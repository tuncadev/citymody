<?php
/*
 * Run on public section
 */
namespace UmpESh;

class Main
{
		/**
		 * @var array
		 */
		private $addOnSettings				= [];
		/**
		 * @var string
		 */
		private $view 								= null;

		/**
		 * @param array
		 * @param string
		 * @return none
		 */
    public function __construct( $addOnSettings=[], $viewObject=null )
    {
				$this->addOnSettings 	= $addOnSettings;
				$this->view 					= $viewObject;

	    	// check if magic feat is active filter ...
       	add_filter( 'ihc_is_magic_feat_active_filter', [ $this, 'isMagicFeatActive' ], 1, 2 );

				if ( !get_option( $this->addOnSettings['slug'] . '-enabled' ) ){
						return;
				}

				// css & js
				add_action( 'wp_enqueue_scripts', [ $this, 'styleAndScripts' ] );

				add_shortcode( 'ump-logged-user', [ $this, 'showForLoggedUser'] );
				add_shortcode( 'ump-visitor', [ $this, 'showForVisitor'] );
				add_shortcode( 'ump-account-page-link', [ $this, 'accountPageLink' ] );
				add_shortcode( 'ump-login-page-link', [ $this, 'loginPageLink' ] );
				add_shortcode( 'ump-lost-password-page-link', [ $this, 'lostPassPageLink' ] );
				add_shortcode( 'ump-register-page-link', [ $this, 'registerPageLink' ] );
				add_shortcode( 'ump-subscription-page-link', [ $this, 'subscriptionPageLink' ] );

				$version = indeed_get_plugin_version( IHC_PATH . 'indeed-membership-pro.php');

        if ( version_compare( '9.4.2', $version ) == 1 ){
        		$levels = get_option('ihc_levels');
        } else {
        		$levels = \Indeed\Ihc\Db\Memberships::getAll();
        }
				if ( !$levels ){
						return;
				}
				foreach ( $levels as $lid => $levelData ){

						add_shortcode( 'ump-show-for_' . $levelData['name'], [ $this, 'showForLevelSlug'] );
				}
    }

		/**
		 * @param none
		 * @return none
		 */
		public function styleAndScripts()
		{
				wp_enqueue_style( $this->addOnSettings['slug'] . '-public-style', $this->addOnSettings['dir_url'] . 'assets/css/public.css' );
				wp_enqueue_script( $this->addOnSettings['slug'] . '-public-js', $this->addOnSettings['dir_url'] . 'assets/js/public.js', [], null );
		}

		/**
		 * @param bool
 		 * @param string
		 * @return bool
		 */
    public function isMagicFeatActive( $isActive=false, $type='' )
    {
        if ( $this->addOnSettings['slug'] != $type ){
            return $isActive;
        }
        // check if is active ...
        $settings = ihc_return_meta_arr( $this->addOnSettings['slug'] );
        if ( !empty( $settings[ $this->addOnSettings['slug'] . '-enabled'] ) ){
            return true;
        }
        return false;
    }

		/**
		 * @param array
		 * @param string
		 * @return string
		 */
		public function showForLevelSlug( $attr=[], $content='', $tag='' )
		{

				global $current_user;
				if ( !$tag ){
						return $content;
				}
				$targetLevel = str_replace( 'ump-show-for_', '', $tag );

				if ( !$targetLevel ){
						return $content;
				}
				$lid = $this->getLevelIdBySlug( $targetLevel );
				$uid = isset( $current_user->ID ) ? $current_user->ID : 0;
				$userLevels = \Ihc_Db::get_user_levels( $uid, true );

				foreach ( $userLevels as $userLevelData ){
						if ( $userLevelData['level_id'] == $lid ){
								return $content;
						}
				}
				return '';
		}

		/**
		 * @param string
		 * @return int
		 */
		private function getLevelIdBySlug( $slug='' )
		{
				if ( !$slug ){
						return 0;
				}
				$version = indeed_get_plugin_version( IHC_PATH . 'indeed-membership-pro.php');

        if ( version_compare( '9.4.2', $version ) == 1 ){
        		$levels = get_option('ihc_levels');
        } else {
        		$levels = \Indeed\Ihc\Db\Memberships::getAll();
        }
				foreach ( $levels as $lid => $levelData ){
						if ( $levelData['name'] == $slug ){
								return $lid;
						}
				}
				return 0;
		}

		/**
		 * @param array
		 * @param string
		 * @return string
		 */
		public function showForLoggedUser( $attr=[], $content='', $tag='' )
		{
				global $current_user;
				if ( empty( $current_user->ID ) ){
						return '';
				}
				return $content;
		}

		/**
		 * @param array
		 * @param string
		 * @return string
		 */
		public function showForVisitor( $attr=[], $content='', $tag='' )
		{
				global $current_user;
				if ( empty( $current_user->ID ) ){
						return $content;
				}
				return '';
		}

		/**
		 * @param none
		 * @return string
		 */
		public function loginPageLink()
		{
				global $indeed_db, $current_user;
				if ( empty( $current_user->ID ) ){
						$link = get_option( 'ihc_general_login_default_page' );
						if ( !$link ){
								return '';
						}
						$link = get_permalink( $link );
						if ( !$link ){
								return '';
						}
						$string = "<a href='$link' >" . __( 'Login Page', 'extended-shortcodes-for-ultimate-membership-pro' )  . "</a>";
						$allowedHtml = [
											'a' 			=> [
																			'href'		=> [],
											],
						];
						return wp_kses( $string, $allowedHtml );
				}
				return '';
		}

		/**
		 * @param none
		 * @return string
		 */
		public function accountPageLink()
		{
				global $indeed_db, $current_user;
				if ( empty( $current_user->ID ) ){
						return '';
				}
				$link = get_option( 'ihc_general_user_page' );
				if ( !$link ){
						return '';
				}
				$link = get_permalink( $link );
				if ( !$link ){
						return '';
				}
				$string = "<a href='$link' >" . __( 'Account Page', 'extended-shortcodes-for-ultimate-membership-pro' )  . "</a>";
				$allowedHtml = [
									'a' 			=> [
																	'href'		=> [],
									],
				];
				return wp_kses( $string, $allowedHtml );
		}

		/**
		 * @param none
		 * @return string
		 */
		public function lostPassPageLink()
		{
				global $indeed_db, $current_user;
				if ( !empty( $current_user->ID ) ){
						return '';
				}
				$link = get_option( 'ihc_general_lost_pass_page' );
				if ( !$link ){
						return '';
				}
				$link = get_permalink( $link );
				if ( !$link ){
						return '';
				}
				$string =  "<a href='$link' >" . __( 'Lost Password', 'extended-shortcodes-for-ultimate-membership-pro' )  . "</a>";
				$allowedHtml = [
									'a' 			=> [
																	'href'		=> [],
									],
				];
				return wp_kses( $string, $allowedHtml );
		}

		/**
		 * @param none
		 * @return string
		 */
		public function registerPageLink()
		{
				global $indeed_db, $current_user;
				if ( !empty( $current_user->ID ) ){
						return '';
				}
				$link = get_option( 'ihc_general_register_default_page' );
				if ( !$link ){
						return '';
				}
				$link = get_permalink( $link );
				if ( !$link ){
						return '';
				}
				$string = "<a href='$link' >" . __( 'Register Page', 'extended-shortcodes-for-ultimate-membership-pro' )  . "</a>";
				$allowedHtml = [
									'a' 			=> [
																	'href'		=> [],
									],
				];
				return wp_kses( $string, $allowedHtml );
		}

		/**
		 * @param none
		 * @return string
		 */
		public function subscriptionPageLink()
		{
				global $indeed_db, $current_user;
				if ( empty( $current_user->ID ) ){
						return '';
				}
				$link = get_option( 'ihc_subscription_plan_page' );
				if ( !$link ){
						return '';
				}
				$link = get_permalink( $link );
				if ( !$link ){
						return '';
				}
				$string = "<a href='$link' >" . __( 'Subscription Page', 'extended-shortcodes-for-ultimate-membership-pro' )  . "</a>";
				$allowedHtml = [
									'a' 			=> [
																	'href'		=> [],
									],
				];
				return wp_kses( $string, $allowedHtml );
		}


}
