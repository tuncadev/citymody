<?php
namespace Indeed\Ihc\Services;

class ElCheck
{
    /**
     * @var string
     */
    private $purchaseCode         = '';// input from user
    /**
     * @var string
     */
    private $pluginId             = '12159253';
    /**
     * @var string
     */
    private $ajax                 = 'ihc_el_check_get_url_ajax';//
    /**
     * @var string
     */
    private $wpIndeedUrlEndpoint  = 'https://wpindeed.com/wp-content/plugins/wpindeed-envato/input.php'; // wpIndeed Server
    /**
     * @var string
     */
    private $hashOptionName       = 'ihc_license_hash';
    /**
     * @var string
     */
    private $redirectBackUri      = ''; /// dynamicly generated
    /**
     * @var string
     */
    private $confirmationUri      = '';/// dynamicly generated
    /**
     * @var string
     */
    private $ajaxRevoke           = 'ihc_revoke_license';//
    /**
     * @var string
     */
    private $wpIndeedRevokeEndpoint = 'https://wpindeed.com/wp-admin/admin-ajax.php';
    /**
     * @var string
     */
    private $licenseTokenOptionName  = 'ihc_license_token';
    /**
     * @var string
     */
    private $redirectBackPath        = 'admin.php?page=ihc_manage&tab=help';
    /**
     * @var string
     */
    private $gateResponse            = 'ihc_elc_response';
    /**
     * @var string
     */
    private $nonceName               = 'ihc_license_nonce';
    /**
     * @var string
     */
    private $pluginBaseFile          = IHC_PATH . 'indeed-membership-pro.php';

    /**
     * @param none
     * @return none
     */
    public function __construct()
    {
        // ajax to get the link to redirect to envato
        add_action( 'wp_ajax_' . $this->ajax, [ $this, 'ajax' ] );
        // ajax to revoke plugin license
        add_action( 'wp_ajax_' . $this->ajaxRevoke, [ $this, 'revoke' ] );
        // ajax gate for response
        add_action( 'wp_ajax_nopriv_' . $this->gateResponse, [$this, 'response'] );
    		add_action( 'wp_ajax_' . $this->gateResponse, [$this, 'response'] );
        // where to redirect after envato
        $this->redirectBackUri = admin_url( $this->redirectBackPath );
        // where to send confirmation if the license is good
        $this->confirmationUri = admin_url( 'admin-ajax.php?action=' . $this->gateResponse );
    }

    /**
     * @param none
     * @return none
     */
    public function ajax()
    {
        // check if the call was made by admin
        if ( !current_user_can( 'manage_options' ) ){
            die;
        }
        // check nonce
        if ( empty($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field($_POST['nonce']), $this->nonceName ) ){
            die;
        }
        // no empty purchase code
        if ( empty( $_POST['s'] ) ){
            die;
        }
        // save purchase code in db
        $OldLogs = new \Indeed\Ihc\OldLogs();
        $OldLogs->IUCP( sanitize_text_field( $_POST['s'] ) );

        // redirect link to envato
        echo $this->getRedirectLinkToWpIndeed( sanitize_text_field( $_POST['s'] ) );
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function response()
    {
        // check token
        if ( !isset( $_POST['token'] ) ){
            return;
        }
        $dbToken = get_option( $this->licenseTokenOptionName );
        if ( $dbToken != (string)$_POST['token'] ){
            return; // wrong token
        }
        update_option( $this->licenseTokenOptionName , '' );

        $response = isset( $_POST['response'] ) ? (int)$_POST['response'] : 0;
        if ( $response > 0 && !empty( $_POST['hash'] ) ){
            $OldLogs = new \Indeed\Ihc\OldLogs();
            $OldLogs->SCS( 0 );
            update_option( $this->hashOptionName, sanitize_text_field( $_POST['hash'] ) );
            update_option( md5('ihclsm'), 0 );
            return;
        }
        $OldLogs = new \Indeed\Ihc\OldLogs();
        $OldLogs->WECP();
    }

    /**
     * @param none
     * @return int
     */
    public function responseFromGet()
    {
        // check token
        if ( !isset( $_GET['token'] ) ){
            return 0;
        }
        $dbToken = get_option( $this->licenseTokenOptionName );
        if ( $dbToken != (string)$_GET['token'] ){
            return 0; // wrong token
        }
        update_option( $this->licenseTokenOptionName , '' );

        $response = isset( $_GET['response'] ) ? (int)$_GET['response'] : 0;
        if ( $response > 0 ){
            $OldLogs = new \Indeed\Ihc\OldLogs();
            $OldLogs->SCS( 0 );
            update_option( md5('ihclsm'), 0 );
            return 1;
        }
        $OldLogs = new \Indeed\Ihc\OldLogs();
        $OldLogs->WECP();
        update_option( md5('ihclsm'), 0 );
        return 0;
    }

    /**
     * @param string
     * @return string
     */
    public function getRedirectLinkToWpIndeed( $purchaseCode='' )
    {
        if ( !$purchaseCode ){
            return;
        }

        // generate token
        $token = bin2hex( random_bytes( 20 ) ) . md5( time() );
        update_option( $this->licenseTokenOptionName, $token );

        // save purchase code into db
        $OldLogs = new \Indeed\Ihc\OldLogs();
        $OldLogs->IUCP( $purchaseCode );

        $url = add_query_arg( [
                          'indeed-auth-app'       => 'check_license',
                          'purchase_code'         => $purchaseCode,
                          'plugin_id'             => $this->pluginId,
                          'return_url'            => urlencode($this->redirectBackUri),
                          'confirmation_url'      => urlencode($this->confirmationUri),
                          'reference'             => get_option('siteurl'),
                          'version'               => $this->pluginVersion(),
                          'token'                 => $token,
        ], $this->wpIndeedUrlEndpoint );
        return $url;
    }

    /**
     * @param int
     * @return string
     */
    public function responseCodeToMessage( $code=0, $errorClass='', $successClass='', $langCode=null )
    {
        if ( isset( $_GET['response_message'] ) ){
            $class = ( $code > 0 ) ? $successClass : $errorClass;
            return "<div class='$class'>" . urldecode( stripslashes($_GET['response_message']) ) . "</div>";
        }
        switch ( $code ){
            case 1:
              return "<div class='$successClass'>" . esc_html__('Your plugin has been successfully activated the License.', $langCode ) . "</div>";
              break;
            case 0:
              return "<div class='$errorClass'>" . esc_html__('Bad input data. Please try again later!', $langCode ) . "</div>";
              break;
            case -1:
              return "<div class='$errorClass'>" . esc_html__('Envato API Server may be done for a moment. Please try again later', $langCode ) . "</div>";
              break;
            case -2:
              return "<div class='$errorClass'>" . esc_html__('Submitted Purchase Code is invalid.', $langCode ) . "</div>";
              break;
            case -3:
              return "<div class='$errorClass'>" . esc_html__('Submitted Purchase Code does not match with Current product.', $langCode ) . "</div>";
              break;
        }
    }

    /**
     * @param none
     * @return bool
     */
    public function revoke()
    {
        if ( !current_user_can( 'manage_options' ) ){
            die;
        }
        if ( empty($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field($_POST['nonce']), $this->nonceName ) ){
            die;
        }

        $this->doRevoke();

        echo 1;
        die;
    }

    public function doRevoke()
    {
        $OldLogs = new \Indeed\Ihc\OldLogs();
        $p = $OldLogs->GCP();
        $referrence = get_option( 'siteurl' );
        $OldLogs->WECP();
        update_option( md5('ihclsm'), 0 );
        update_option( $this->hashOptionName, 0 );
        $OldLogs->ECP();
        $header= [
          'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
          'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0',
          'Accept: */*',
          'Accept-Language: en-US,en;q=0.5',
          'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
          'X-Requested-With: XMLHttpRequest',
        ];
        $builder= http_build_query([
           'action'         => "wpindeed_envato_revoke_license",
           'purchase_code'  => $p,
           'referrence'     => $referrence,
        ]);


        $ch = curl_init( $this->wpIndeedRevokeEndpoint );
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $builder);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        $res = curl_exec($ch);
        curl_close($ch);
    }

    /**
     * @param none
     * @return string
     */
    private function pluginVersion()
    {
    		require_once ABSPATH . 'wp-admin/includes/plugin.php';
    		$pluginData = get_plugin_data( $this->pluginBaseFile, false, false );
    		return $pluginData['Version'];
    }
}
