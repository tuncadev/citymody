<?php
namespace Indeed\Ihc\Db;

class Orders
{
    private $id             = 0;
    private $data           = null;

    public function setData( $data = array() )
    {
        if ( !$data ){
            return;
        }
        foreach ( $data as $key => $value ){
            $this->data[ $key ] = $value;
        }
        return $this;
    }

    public function setId( $id=0 )
    {
        $this->id = $id;
        return $this;
    }

    public function fetch()
    {
        global $wpdb;
        $query = $wpdb->prepare( "SELECT id, uid, lid, amount_type, amount_value, automated_payment, status, create_date FROM {$wpdb->prefix}ihc_orders WHERE id=%d;", $this->id );
        $this->data = $wpdb->get_row( $query );
        $this->data = $this->data;
        return $this;
    }

    public function get()
    {
        return $this->data;
    }

    public function save()
    {
        global $wpdb;
        $query = $wpdb->prepare( "SELECT id, uid, lid, amount_type, amount_value, automated_payment, status, create_date FROM {$wpdb->prefix}ihc_orders WHERE id=%d;", $this->id );
        $writeData = $wpdb->get_row( $query );
        if ( $writeData ){
            /// update
            $writeData = (array)$writeData;
            foreach ( $this->data as $key => $value ){
                $writeData[$key] = $value;
            }
            $query = $wpdb->prepare( "UPDATE {$wpdb->prefix}ihc_orders SET
                                          uid=%d,
                                          lid=%d,
                                          amount_type=%s,
                                          amount_value=%s,
                                          automated_payment=%s,
                                          status=%s,
                                          create_date=%s
                                          WHERE id=%d;",
            $writeData['uid'], $writeData['lid'], $writeData['amount_type'], $writeData['amount_value'], $writeData['automated_payment'],
            $writeData['status'], $writeData['create_date'], $writeData['id'] );
            $wpdb->query( $query );
            do_action( 'ump_payment_check', $writeData['id'], 'update' );
            return $writeData['id'];
        } else {
            /// insert

            /// since version 8.6, before we used NOW() function in mysql
            $createDate = indeed_get_current_time_with_timezone();
            if ( isset( $this->data['create_date'] ) && $this->data['create_date'] != '' ){
                $createDate = $this->data['create_date'];
            }

            $query = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}ihc_orders
                                          VALUES( NULL, %d, %d, %s, %s, %d, %s, %s );",
            $this->data['uid'], $this->data['lid'], $this->data['amount_type'], $this->data['amount_value'], $this->data['automated_payment'],
            $this->data['status'], $createDate );
            $wpdb->query( $query );

            do_action( 'ihc_action_after_order_placed', $this->data['uid'], $this->data['lid'] );
            do_action( 'ump_payment_check', $wpdb->insert_id, 'insert' );
            return $wpdb->insert_id;
        }

    }

    public function getStatus()
    {
        return isset( $this->data->status ) ? $this->data->status : false;
    }

    public function update( $colName='', $value='' )
    {
        global $wpdb;
        if ( !$colName || !$value || empty($this->id) ){
            return false;
        }
        $colName = sanitize_text_field( $colName );
        $queryString = $wpdb->prepare( "UPDATE {$wpdb->prefix}ihc_orders SET $colName=%s WHERE id=%d;", $value, $this->id );

        $result = $wpdb->query( $queryString );
        do_action( 'ump_payment_check', $this->id, 'update' );
        return $result;
    }

    /**
     * @param int
     * @param int
     * @return none
     */
    public function getCountInInterval( $start=0, $end=0  )
    {
        global $wpdb;
        $query = $wpdb->prepare( "SELECT COUNT( id ) FROM {$wpdb->prefix}ihc_orders
                                      WHERE
                                      IFNULL( UNIX_TIMESTAMP( create_date ), 0 ) > %d
                                      AND
                                      IFNULL( UNIX_TIMESTAMP( create_date ), 0 ) < %d
                                      AND
                                      status='Completed';", $start, $end );
        $count = $wpdb->get_var( $query );
        if ( $count == false ){
            return 0;
        }
        return $count;
    }

    /**
     * @param none
     * @return none
     */
    public function getCountAll()
    {
        global $wpdb;
        //No query parameters required, Safe query. prepare() method without parameters can not be called
        $query = "SELECT COUNT( id ) FROM {$wpdb->prefix}ihc_orders ;";
        $count = $wpdb->get_var( $query );
        if ( $count == false ){
            return 0;
        }
        return $count;
    }

    /**
     * @param none
     * @return none
     */
    public function getTotalAmount()
    {
        global $wpdb;
        //No query parameters required, Safe query. prepare() method without parameters can not be called
        $query = "SELECT SUM( amount_value ) FROM {$wpdb->prefix}ihc_orders ;";
        $data = $wpdb->get_var( $query );
        if ( $data == false ){
            return 0;
        }
        return $data;
    }

    /**
     * @param none
     * @return none
     */
    public function getLastOrders( $limit=5 )
    {
        global $wpdb;
        $query = $wpdb->prepare( "SELECT uid, lid, amount_type, amount_value, create_date
                                        FROM {$wpdb->prefix}ihc_orders
                                        ORDER BY create_date DESC LIMIT %d;", $limit );
        $data = $wpdb->get_results( $query );
        if ( $data == false ){
            return [];
        }
        return $data;
    }

    /**
     * @param none
     * @return none
     */
    public function getTotalAmountInInterval( $start=0, $end=0 )
    {
      global $wpdb;
      $query = $wpdb->prepare( "SELECT SUM( amount_value ) FROM {$wpdb->prefix}ihc_orders
                                    WHERE
                                    IFNULL( UNIX_TIMESTAMP( create_date ), 0 ) > %d
                                    AND
                                    IFNULL( UNIX_TIMESTAMP( create_date ), 0 ) < %d
                                    AND status='Completed';", $start, $end );
      return $wpdb->get_var( $query );
    }

    public function getFirstOrderDaysPassed()
    {
        global $wpdb;
        //No query parameters required, Safe query. prepare() method without parameters can not be called
        $query = "SELECT UNIX_TIMESTAMP() - UNIX_TIMESTAMP(create_date) FROM {$wpdb->prefix}ihc_orders
                                    WHERE
                                    IFNULL( UNIX_TIMESTAMP( create_date ), 0 ) > 0
                                    ORDER BY create_date
                                    ASC
                                    LIMIT 1;
        ";
        $days = $wpdb->get_var( $query );
        if ( $days > 0 ){
            $days = $days / (24 * 60 * 60);
            return (int)$days;
        }
        return 0;
    }

    /**
     * @param none
     * @return none
     */
    public function getTotalAmountInLastTime( $startTime=0, $groupBy='days' )
    {
        global $wpdb;
        switch ( $groupBy ){
            case 'days':
              //No query parameters required, Safe query. prepare() method without parameters can not be called
              $query = "SELECT DATE_FORMAT( create_date, '%Y-%m-%d' ) as the_time, SUM(amount_value) as sum_value
              																	FROM {$wpdb->prefix}ihc_orders ";
              break;
            case 'weeks':
              //No query parameters required, Safe query. prepare() method without parameters can not be called
              $query = "SELECT DATE_FORMAT( create_date, 'week %U' ) as the_time, SUM(amount_value) as sum_value
              																	FROM {$wpdb->prefix}ihc_orders ";
              break;
            case 'months':
              //No query parameters required, Safe query. prepare() method without parameters can not be called
              $query = "SELECT DATE_FORMAT( create_date, '%M %Y' ) as the_time, SUM(amount_value) as sum_value
                                                FROM {$wpdb->prefix}ihc_orders ";
              break;
            case 'years':
              //No query parameters required, Safe query. prepare() method without parameters can not be called
              $query = "SELECT DATE_FORMAT( create_date, '%Y' ) as the_time, SUM(amount_value) as sum_value
                                                FROM {$wpdb->prefix}ihc_orders ";
              break;
        }

        $query .= $wpdb->prepare( " WHERE
                                          IFNULL( UNIX_TIMESTAMP( create_date ), 0 ) > %d
        																	GROUP BY the_time
                                          ORDER BY create_date ASC;", $startTime );
        $data = $wpdb->get_results( $query );
        return $data;
    }
}
