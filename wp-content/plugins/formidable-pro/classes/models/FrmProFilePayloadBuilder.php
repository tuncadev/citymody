<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProFilePayloadBuilder {

	/**
	 * @var int $id the id of the file attachment we're building a payload for.
	 */
	private $id;

	/**
	 * @param array|string|false $size the specific image size we're accessing.
	 */
	private $size;

	/**
	 * @var string $url the unprotected file url (used if the file isn't protected).
	 */
	private $url;

	/**
	 * @param int               $id the id of the attachment.
	 * @param string|int[]|bool $size the size of the image.
	 * @param string|false      $url if false one will be automatically generated.
	 */
	public function __construct( $id, $size, $url ) {
		$this->id   = $id;
		$this->size = $size;
		$this->maybe_generate_url( $url );
	}

	/**
	 * @param string|false $url
	 */
	private function maybe_generate_url( $url ) {
		if ( $url ) {
			$this->set_url( $url );
		} else {
			$this->generate_url();
		}
	}

	/**
	 * Set the url value based off of id and size.
	 */
	private function generate_url() {
		if ( ! $this->size && wp_attachment_is_image( $this->id ) ) {
			$this->size = 'full';
		}

		if ( $this->size ) {
			$src = wp_get_attachment_image_src( $this->id, $this->size );
			if ( $src ) {
				$this->url = reset( $src );
			}
		}

		if ( ! isset( $this->url ) ) {
			$this->url = wp_get_attachment_url( $this->id );
		}
	}

	/**
	 * Set url value
	 *
	 * @param string $url
	 */
	private function set_url( $url ) {
		$this->url = $url;
	}

	/**
	 * Get url value
	 *
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * @param string $protocol either 'http' or 'https'
	 * @param bool   $leave_size_out_of_payload if true payload will omit size, used to confirm a full size image url match.
	 * @return string the protected file url in either ?frm_file=payload or /file_file/payload format.
	 */
	public function get_protected_url( $protocol, $leave_size_out_of_payload ) {
		$attached_file = get_attached_file( $this->id );
		$filename      = basename( $attached_file );
		$raw           = "id:{$this->id}|filename:{$filename}";

		if ( $this->size && ! $leave_size_out_of_payload ) {
			if ( is_string( $this->size ) ) {
				$raw .= "|size:{$this->size}";
			} elseif ( is_array( $this->size ) && 2 === count( $this->size ) ) {
				list( $width, $height ) = $this->size;
				if ( is_numeric( $width ) && is_numeric( $height ) ) {
					$raw .= "|size:{$width}x{$height}";
				}
			}
		}

		$scheme   = self::maybe_is_ssl() ? 'https' : 'http';
		$home_url = home_url( '', $scheme );

		return $home_url . $protocol . base64_encode( $raw );
	}

	/**
	 * Determine if the server should serve files over https
	 * First, check for specific $_SERVER variables
	 * If none of the special cases match, use the WordPress function if applicable
	 *
	 * @return bool
	 */
	private static function maybe_is_ssl() {
		if ( self::headers_include_cloudflare_ssl_scheme() ) {
			return true;
		}

		if ( self::headers_include_https_proxy() ) {
			return true;
		}

		return function_exists( 'is_ssl' ) ? is_ssl() : false;
	}

	/**
	 * @return bool
	 */
	private static function headers_include_cloudflare_ssl_scheme() {
		$cloudflare_visitor = FrmAppHelper::get_server_value( 'HTTP_CF_VISITOR' );

		if ( ! $cloudflare_visitor ) {
			return false;
		}

		$cloudflare_visitor = json_decode( $cloudflare_visitor );
		return isset( $cloudflare_visitor->scheme ) && 'https' === $cloudflare_visitor->scheme;
	}

	/**
	 * @return bool
	 */
	private static function headers_include_https_proxy() {
		return ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'];
	}
}

