<?php

namespace WPML\CF7;

use WPML\FP\Lens;
use WPML\FP\Obj;
use WPML\FP\Str;
use WPML\LIB\WP\Hooks;

use function WPML\FP\compose;
use function WPML\FP\spreadArgs;

class Placeholders implements \IWPML_Backend_Action, \IWPML_Frontend_Action {

	const FOR_ATE  = 'placeholder="';
	const ORIGINAL = 'placeholder "';

	/**
	 * @return void
	 */
	public function add_hooks() {
		Hooks::onFilter( 'icl_job_elements', 10, 2 )
			->then( spreadArgs( [ $this, 'fixTagForTranslation' ] ) );
		Hooks::onFilter( 'wpml_encode_custom_field', -PHP_INT_MAX, 2 )
			->then( spreadArgs( [ $this, 'restoreTranslatedTag' ] ) );
	}

	/**
	 * @param array $elements
	 * @param mixed $postId
	 *
	 * @return array
	 */
	public function fixTagForTranslation( $elements, $postId ) {
		if ( Constants::POST_TYPE !== get_post_type( $postId ) ) {
			return $elements;
		}

		$fieldTypes = wp_list_pluck( $elements, 'field_type' );
		$index      = array_search( 'field-_form-0', $fieldTypes, true );
		if ( false !== $index ) {
			// $lensOnDecodedFieldData :: callable -> callable
			$lensOnDecodedFieldData = compose( Obj::lensPath( [ $index, 'field_data' ] ), Lens::isoBase64Decoded() );

			// $addEqualSignOnPlaceholder :: string -> string
			$addEqualSignOnPlaceholder = Str::replace( self::ORIGINAL, self::FOR_ATE );

			return Obj::over( $lensOnDecodedFieldData, $addEqualSignOnPlaceholder, $elements );
		}

		return $elements;
	}

	/**
	 * @param string $value
	 * @param string $name
	 *
	 * @return array
	 */
	public function restoreTranslatedTag( $value, $name ) {
		if ( '_form' === $name ) {
			$value = Str::replace( self::FOR_ATE, self::ORIGINAL, $value );
		}

		return $value;
	}

}
