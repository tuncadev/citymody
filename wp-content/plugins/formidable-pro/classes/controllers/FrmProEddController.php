<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProEddController extends FrmAddon {

	public $plugin_file;
	public $plugin_name = 'Formidable Pro';
	public $download_id = 93790;
	private $pro_cred_store  = 'frmpro-credentials';
	private $pro_auth_store  = 'frmpro-authorized';
	public $pro_wpmu_store  = 'frmpro-wpmu-sitewide';
	private $pro_wpmu = false;

	public function __construct() {
		$this->version = FrmProDb::$plug_version;
		$this->set_download();

		if ( $this->get_license() && is_multisite() && get_site_option( $this->pro_wpmu_store ) ) {
			$this->pro_wpmu = true;
		}

		global $frm_vars;
		$frm_vars['pro_is_authorized'] = $this->pro_is_authorized();

		parent::__construct();

		if ( is_admin() ) {
			add_action( 'frm_license_error', array( &$this, 'maybe_clear_license' ) );
		}
	}

	public static function load_hooks() {
		// don't use the addons page
	}

	/**
	 * @since 3.0
	 */
	private function set_download() {
		$this->plugin_file = FrmProAppHelper::plugin_path() . '/formidable-pro.php';
	}

	public function set_license( $license ) {
		update_option( $this->pro_cred_store, array( 'license' => $license ) );
	}

	public function get_license() {
		if ( is_multisite() && get_site_option( $this->pro_wpmu_store ) ) {
			$creds = get_site_option( $this->pro_cred_store );
		} else {
			$creds = get_option( $this->pro_cred_store );
		}

		$license = '';
		if ( $creds && is_array( $creds ) && isset( $creds['license'] ) ) {
			$license = $creds['license'];
			if ( strpos( $license, '-' ) ) {
				// this is a fix for licenses saved in the past
				$license = strtoupper( $license );
			}
		}

		if ( empty( $license ) ) {
			$license = $this->activate_defined_license();
		}

		return $license;
	}

	public function get_defined_license() {
		return defined( 'FRM_PRO_LICENSE' ) ? FRM_PRO_LICENSE : false;
	}

	public function clear_license() {
		delete_option( $this->pro_cred_store );
		delete_option( $this->pro_auth_store );
		delete_site_option( $this->pro_cred_store );
		delete_site_option( $this->pro_auth_store );
		parent::clear_license();
	}

	public function set_active( $is_active ) {
		$is_active = ( $is_active == 'valid' );
		$creds = $this->get_pro_cred_form_vals();

		if ( is_multisite() ) {
			update_site_option( $this->pro_wpmu_store, $creds['wpmu'] );
		}

		if ( $creds['wpmu'] ) {
			update_site_option( $this->pro_cred_store, $creds );
			update_site_option( $this->pro_auth_store, $is_active );
		} else {
			update_option( $this->pro_auth_store, $is_active );
		}

		// update style sheet to make sure pro css is included
		$frm_style = new FrmStyle();
		$frm_style->update( 'default' );

		parent::set_active( $is_active );

		// The child class crease the option we don't need.
		delete_option( $this->option_name . 'active' );
	}

	private function get_pro_cred_form_vals() {
		$license = isset( $_POST['license'] ) ? sanitize_text_field( $_POST['license'] ) : $this->get_license();
		$wpmu = isset( $_POST['wpmu'] ) ? absint( $_POST['wpmu'] ) : $this->pro_wpmu;

		return compact('license', 'wpmu');
	}

	public function pro_is_authorized() {
		$license = $this->get_license();
		if ( empty( $license ) ) {
			return false;
		}

		if ( is_multisite() && $this->pro_wpmu ) {
			$authorized = get_site_option( $this->pro_auth_store );
		} else {
			$authorized = get_option( $this->pro_auth_store );
		}

		return $authorized;
	}

	public function pro_is_installed_and_authorized() {
		return $this->pro_is_authorized();
	}

	/**
	 * @return void
	 */
	public function pro_cred_form() {
		global $frm_vars;

		$config_license = $this->get_defined_license();
		$authorized     = $frm_vars['pro_is_authorized'];

		$license_type = FrmProAddonsController::get_readable_license_type();

		?>

<div id="frm_license_top" class="<?php echo esc_attr( $authorized ? 'frm_authorized_box' : 'frm_unauthorized_box' ); ?>">
	<p id="frm-connect-btns" class="frm-show-unauthorized">
		<?php if ( is_callable( 'FrmProAddonsController::connect_link' ) ) { ?>
		<a href="<?php echo esc_url( FrmProAddonsController::connect_link() ); ?>" class="button-primary frm-button-primary frm-button-sm">
		<?php } else { ?>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=formidable-settings' ) ); ?>" target="_blank" class="button-primary frm-button-primary frm-button-sm" id="frm-settings-connect-btn">
		<?php } ?>
			<?php esc_html_e( 'Connect an Account', 'formidable' ); ?>
		</a>
		or
		<a href="<?php echo esc_url( FrmAppHelper::make_affiliate_url( FrmAppHelper::admin_upgrade_link( 'settings-license' ) ) ); ?>" target="_blank" class="button frm-button-secondary frm-button-sm">
			<?php esc_html_e( 'Get Formidable Now', 'formidable' ); ?>
		</a>
	</p>

	<div class="frm-show-authorized">
		<p>You're using Formidable Forms <?php echo esc_html( $license_type ); ?>. Enjoy! 🙂</p>
		<?php if ( 'Elite' !== $license_type ) { ?>
		<p style="font-size:1.1em">
			To <b>unlock more features</b> consider <a href="<?php echo esc_url( FrmAppHelper::make_affiliate_url( FrmAppHelper::admin_upgrade_link( 'settings-upgrade', 'account/downloads/' ) ) ); ?>">upgrading to the Elite plan</a>.
		</p>
		<?php } ?>
	</div>
	<?php $this->display_form(); ?>

	<?php if ( ! $config_license ) { ?>
		<a href="#" id="frm_deauthorize_link" class="frm-show-authorized" data-plugin="<?php echo esc_attr( $this->plugin_slug ); ?>">
			<?php esc_html_e( 'Disconnect this site', 'formidable-pro' ); ?>
		</a>
		<span class="frm-show-authorized">|</span>
		<a href="#" id="frm_reconnect_link" class="frm-show-authorized">
			<?php esc_html_e( 'Check now for a recent upgrade or renewal', 'formidable' ); ?>
		</a>
	<?php } ?>
</div>

<div class="frm_pro_license_msg frm_hidden"></div>
<div class="clear"></div>

		<?php
	}

	/**
	 * This is the view for the license form
	 */
	public function display_form() {
		global $frm_vars;

		$authorized = $frm_vars['pro_is_authorized'];

		if ( $authorized ) {
			$placeholder = __( 'Verify a different license key', 'formidable-pro' );
		} else {
			$placeholder = __( 'Enter your license key here', 'formidable-pro' );
		}
		?>
<div id="pro_cred_form" class="frm_grid_container frm-show-unauthorized frm_hidden">

	<p class="frm9 frm_form_field frm-license-input">
		<input type="text" name="proplug-license" value="" placeholder="<?php echo esc_attr( $placeholder ); ?>" id="edd_<?php echo esc_attr( $this->plugin_slug ); ?>_license_key" />
		<span class="frm-show-authorized">
			<?php esc_html_e( 'License is active', 'formidable-pro' ); ?>
			<?php FrmProAppHelper::icon_by_class( 'frm_icon_font frm_check1_icon' ); ?>
		</span>
	</p>
	<p class="frm3 frm_form_field">
		<button class="button-secondary frm-button-secondary frm_authorize_link" data-plugin="<?php echo esc_attr( $this->plugin_slug ); ?>" type="button">
			<?php esc_attr_e( 'Save License', 'formidable-pro' ); ?>
		</button>
	</p>
	<?php
	if ( is_multisite() ) {
			$creds = $this->get_pro_cred_form_vals();
			?>
		<br/>
		<label for="proplug-wpmu">
			<input type="checkbox" value="1" name="proplug-wpmu" id="proplug-wpmu" <?php checked( $creds['wpmu'], 1 ); ?> />
			<?php esc_html_e( 'Use this license to enable Formidable Pro site-wide', 'formidable-pro' ); ?>
		</label>
			<?php } ?>
</div>
<p class="frm-show-unauthorized">
	<a href="#" id="frm-manual-key" data-frmhide="#frm-manual-key" data-frmshow="#pro_cred_form">
		<?php esc_html_e( 'Click to enter a license key manually', 'formidable-pro' ); ?>
	</a>
</p>
<?php
	}
}
