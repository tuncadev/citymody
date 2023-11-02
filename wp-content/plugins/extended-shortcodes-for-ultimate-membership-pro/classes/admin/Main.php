<?php
/*
 * This class will run only on Admin section
 */
namespace UmpESh\Admin;

class Main
{
    /**
		 * @var array
		 */
		private $addOnSettings			  = [];
		/**
		 * @var string
		 */
		private $view 								= null;

    /**
     * @param array
		 * @param object
     * @return none
     */
    public function __construct( $settings=[], $viewObject=null )
    {
        $this->addOnSettings 		= $settings;
				$this->view 						= $viewObject;
        // admin settings page
        add_filter( 'ihc_magic_feature_list', [ $this, 'addMagicFeatureItem' ], 1, 1 );
        // settings page
        add_action( 'ump_print_admin_page', [ $this, 'printSettingsPage' ], 1, 1 );
        // style & js stuff
        add_action( 'admin_enqueue_scripts', [ $this, 'styleAndScripts' ] );

				//extra actions on Admin settings page
				add_action( 'ump_addon_action_before_print_admin_settings', [ $this, 'beforePrintSettings' ] );
    }

    /**
		 * @param array
		 * @return array
		 */
    public function addMagicFeatureItem( $items=[] )
    {
        $items[ $this->addOnSettings['slug'] ] = array(
                'label'						=> esc_html__( $this->addOnSettings['name'], 'extended-shortcodes-for-ultimate-membership-pro' ),
                'link' 						=> (IHCACTIVATEDMODE) ? admin_url('admin.php?page=ihc_manage&tab=' . $this->addOnSettings['slug'] ) : '',
                'icon'						=> 'fa-code',
                'extra_class' 		=> 'ihc-extra-extension-box iump-' . $this->addOnSettings['slug'] . '-box',
								'description'			=> esc_html__('Extras functionality provided based on additional dedicated Shortcodes', 'extended-shortcodes-for-ultimate-membership-pro'),
                'enabled'					=> ihc_is_magic_feat_active( $this->addOnSettings['slug'] ),
        );

        return $items;
    }

    /**
		 * @param string
		 * @return none
		 */
    public function printSettingsPage( $tab='' )
    {
        if ( $tab != $this->addOnSettings['slug'] ){
            return;
        }
				do_action( 'ump_addon_action_before_print_admin_settings' );

        ihc_save_update_metas( $this->addOnSettings['slug'] );//save update metas
        $data = ihc_return_meta_arr( $this->addOnSettings['slug'] );
				$data['plugin_slug'] = $this->addOnSettings['slug'];
				$data['lang'] = $this->addOnSettings['slug'];
				$data['name'] = $this->addOnSettings['name'];
				$data['description'] = $this->addOnSettings['description'];
				$data['shortcodes'] = [
																[
																		'shortcode'			=> 'ump-logged-user',
																		'attributes'		=> [],
																		'what_it_does'	=> esc_html__( 'Show the content only for registered users.', 'extended-shortcodes-for-ultimate-membership-pro' ),
																		'single'				=> false,
																],
																[
																		'shortcode'			=> 'ump-visitor',
																		'attributes'		=> [],
																		'what_it_does'	=> esc_html__( 'Show the content only for unregistered users.', 'extended-shortcodes-for-ultimate-membership-pro' ),
																		'single'				=> false,
																],
																[
																		'shortcode'			=> 'ump-login-page-link',
																		'attributes'		=> [],
																		'what_it_does'	=> esc_html__( 'Show link to login page.', 'extended-shortcodes-for-ultimate-membership-pro' ),
																		'single'				=> true,
																],
																[
																		'shortcode'			=> 'ump-account-page-link',
																		'attributes'		=> [],
																		'what_it_does'	=> esc_html__( 'Show link to account page.', 'extended-shortcodes-for-ultimate-membership-pro' ),
																		'single'				=> true,
																],
																[
																		'shortcode'			=> 'ump-lost-password-page-link',
																		'attributes'		=> [],
																		'what_it_does'	=> esc_html__( 'Show link to Lost Password page.', 'extended-shortcodes-for-ultimate-membership-pro' ),
																		'single'				=> true,
																],
																[
																		'shortcode'			=> 'ump-register-page-link',
																		'attributes'		=> [],
																		'what_it_does'	=> esc_html__( 'Show link to Register page.', 'extended-shortcodes-for-ultimate-membership-pro' ),
																		'single'				=> true,
																],
																[
																		'shortcode'			=> 'ump-subscription-page-link',
																		'attributes'		=> [],
																		'what_it_does'	=> esc_html__( 'Show link to Subscription page.', 'extended-shortcodes-for-ultimate-membership-pro' ),
																		'single'				=> true,
																],
				];

				$version = indeed_get_plugin_version( IHC_PATH . 'indeed-membership-pro.php');
				if ( version_compare( '9.4.2', $version ) == 1 ){
						$levels = get_option('ihc_levels');
				} else {
						$levels = \Indeed\Ihc\Db\Memberships::getAll();
				}

				if ( $levels ){

						foreach ( $levels as $lid => $levelData ){
								$data['shortcodes'][] = [
										'shortcode'			=> 'ump-show-for_' . $levelData['name'],
										'attributes'		=> [],
										'what_it_does'	=> esc_html__( 'Show the content only for ', 'extended-shortcodes-for-ultimate-membership-pro' ) . $levelData['name'] . esc_html__( ' subscription.', 'extended-shortcodes-for-ultimate-membership-pro'),
										'single'				=> false,
								];
						}
				}
        $string = $this->view->setTemplate( $this->addOnSettings['dir_path'] . 'views/admin.php' )
                  			->setContentData( $data )
                  			->getOutput();
				$allowedHtml = [
												'label' 	=> [ 'class' => [] ],
												'div' 		=> [ 'class' => [] ],
												'h3'			=> [ 'class' => [] ],
												'h2'			=> [],
												'ul'			=> [ 'class' => [] ],
												'li'			=> [],
												'p'				=> [],
												'input'		=> [
																				'class' 			=> [],
																				'id' 					=> [],
																				'type' 				=> [],
																				'name' 				=> [],
																				'value' 			=> [],
																				'onclick' 		=> [],
																				'onClick'			=> [],
																				'checked' 		=> []
												],
												'form'		=> [
																				'action' => [],
																				'method' => []
												],
												'table'		=> [ 'class' => [] ],
												'thead'		=> [],
												'tr'			=> [ 'class' => [] ],
												'th'			=> [],
												'td'			=> [],


				];
				echo wp_kses( $string, $allowedHtml );
    }

    /**
		 * @param none
		 * @return none
		 */
    public function styleAndScripts()
    {
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'ihc_manage' ){
            wp_enqueue_style( $this->addOnSettings['slug'] . '-admin-style', $this->addOnSettings['dir_url'] . 'assets/css/admin.css' );
        	  wp_enqueue_script( $this->addOnSettings['slug'] . '-admin-js', $this->addOnSettings['dir_url'] . 'assets/js/admin.js', [], null );
					//	wp_enqueue_style( $this->addOnSettings['slug'] . '-admin-font-style', $this->addOnSettings['dir_url'] . 'assets/css/all.min.css' );
						wp_enqueue_script( $this->addOnSettings['slug'] . '-admin-font-js', $this->addOnSettings['dir_url'] . 'assets/js/all.min.js', [], null );
				}
    }

		/**
		 * @param none
		 * @return none
		 */
		public function beforePrintSettings()
		{
				// Add your custom code and functionality here
		}

}
