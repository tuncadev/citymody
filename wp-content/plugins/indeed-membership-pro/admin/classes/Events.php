<?php
namespace Indeed\Ihc\Admin;

class Events
{
    /**
     * @param none
     * @return none
     */
    public function __construct()
    {
        add_action( 'ihc_action_after_delete_membership', [ $this, 'onDeleteMembership' ], 1, 2 );
        add_action( 'ihc_action_admin_save_membership', [ $this, 'saveStripeConnectProduct' ], 1, 1 );
    }

    /**
     * It will remove the post restrictions for this level.
     * @param int
     * @param bool
     * @return none
     */
    public function onDeleteMembership( $membershipId=null, $processDone=true )
    {
        global $wpdb;
        if ( !$processDone ){
            return;
        }
        $query = "
        SELECT DISTINCT(a.post_id) as ID
        	FROM {$wpdb->postmeta} a
        	INNER JOIN {$wpdb->posts} b
        	ON a.post_id=b.ID
        	INNER JOIN {$wpdb->postmeta} c
        	ON c.post_id=a.post_id
        	WHERE 1=1
        	AND
        	(
        			(
        					( a.meta_key='ihc_mb_type' AND a.meta_value='show' )
        					AND
        					( c.meta_key='ihc_mb_who' AND FIND_IN_SET($membershipId, c.meta_value) )
        			)
        			OR
        			(
        				( a.meta_key='ihc_mb_type' AND a.meta_value='block' )
        				AND
        				( c.meta_key='ihc_mb_who' AND ( FIND_IN_SET($membershipId, c.meta_value)  ) )
        			)
        	)
        ";

        $posts = $wpdb->get_results( $query );

        if ( !$posts ){
            return;
        }

        foreach ( $posts as $postData ){
        		$postSettings = get_post_meta( $postData->ID, 'ihc_mb_who', true );
        		$levelIds = explode( ',', $postSettings );
        		$key = array_search ( $membershipId , $levelIds );
        		if ( $key !== false ){
        				unset( $levelIds[ $key ] );
        		}
        		$levelIds = implode( ',', $levelIds );
        		update_post_meta( $postData->ID, 'ihc_mb_who', $levelIds );
        }
    }

    public function saveStripeConnectProduct( $data=[] )
    {
        $enabled = get_option('ihc_stripe_connect_status');
        if ( $enabled === false || $enabled === null || $enabled == 0 ){
            return;
        }
        if ( !class_exists('\Stripe\StripeClient') ){
            require_once IHC_PATH . 'classes/gateways/libraries/stripe-sdk/init.php';
        }
        try {
            if ( get_option( 'ihc_stripe_connect_live_mode' ) ){
                $key = get_option( 'ihc_stripe_connect_client_secret' );
                if ( $key === '' || $key === false || $key === null ){
                    return;
                }
                $stripe = new \Stripe\StripeClient( $key );
                $productId = \Indeed\Ihc\Db\Memberships::getOneMeta( $data['level_id'], 'ihc_stripe_product_id' );
                $metaName = 'ihc_stripe_product_id';
            } else {
                $key = get_option( 'ihc_stripe_connect_test_client_secret' );
                if ( $key === '' || $key === false || $key === null ){
                    return;
                }
                $stripe = new \Stripe\StripeClient( $key );
                $productId = \Indeed\Ihc\Db\Memberships::getOneMeta( $data['level_id'], 'ihc_stripe_product_id-test' );
                $metaName = 'ihc_stripe_product_id-test';
            }
            $statementDescriptor = get_option( 'ihc_stripe_connect_descriptor' );
            if ( $statementDescriptor === false ){
                $statementDescriptor = get_option( 'blogname' );
            }
            $statementDescriptor = substr( $statementDescriptor, 0, 21 );

            if ( $productId === null ){
                // create
                $productParams = [
                  'name'                  => $data['label']
                ];
                if ( $data['short_description'] !== '' ){
                    $productParams['description'] = $data['short_description'];
                }
                if ( $statementDescriptor !== '' ){
                    $productParams['statement_descriptor'] = $statementDescriptor;
                }
                $product = $stripe->products->create( $productParams );

                $productId = isset( $product->id ) ? $product->id : '';
                \Indeed\Ihc\Db\Memberships::saveMeta( $data['level_id'], $metaName, $productId );
            } else {
                // update if it's case
                $product = $stripe->products->retrieve( $productId );
                if ( $product->name !== $data['label'] ){
                    // update name
                    $stripe->products->update(
                      $productId,
                      [ 'name' => $data['label'] ]
                    );
                }
                if ( $product->description !== $data['description'] ){
                    // update description
                    $stripe->products->update(
                      $productId,
                      [ 'description' => $data['description'] ]
                    );
                }
                if ( $product->statement_descriptor !== $statementDescriptor ){
                    // update statement_descriptor
                    $stripe->products->update(
                      $productId,
                      [ 'statement_descriptor' => $statementDescriptor ]
                    );
                }
            }
        } catch ( \Exception $e ){

        }


    }

}
