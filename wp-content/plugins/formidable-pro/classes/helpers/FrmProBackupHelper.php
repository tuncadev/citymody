<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProBackupHelper {

	/**
	 * @param array
	 */
	private static $temporarily_unprotected_formidable_files;

	/**
	 * Remove files protection before Updraft Plus plugin starts the backup process.
	 *
	 * @param $boot
	 * @param $backup_files
	 *
	 * @return $boot
	 */
	public static function on_updraft_plus_boot( $boot, $backup_files ) {
		if ( ! $backup_files ) {
			return $boot;
		}
		self::remove_formidable_files_protection();

		return $boot;
	}

	/**
	 * Lock files back after Updraft Plus has finished taking backup.
	 */
	public static function protect_files_on_backup_complete( $delete_jobdata ) {
		self::restore_files_protection();

		return $delete_jobdata;
	}

	/**
	 * Before Offload Media plugin attempts to access files for uploading, remove the file protection.
	 *
	 * @param bool   $is_private
	 * @param string $object_key
	 * @param object $as3cf_item The item being uploaded.
	 *
	 * @return bool $is_private
	 */
	public static function before_as3cf_upload_object( $is_private, $object_key, $as3cf_item ) {
		self::$temporarily_unprotected_formidable_files = array();

		foreach ( $as3cf_item->objects() as $object_key => $object ) {
			$source_path = $as3cf_item->full_source_path( $object_key );
			self::remove_single_file_protection( $source_path );
		}

		return $is_private;
	}

	/**
	* After Offload Media plugin has uploaded a file, lock it back.
	*/
	public static function after_as3cf_upload_object() {
		self::restore_files_protection();
	}

	private static function remove_formidable_files_protection() {
		if ( ! is_array( self::$temporarily_unprotected_formidable_files ) ) {
			self::$temporarily_unprotected_formidable_files = array();
		}

		$formidable_uploads = glob( wp_upload_dir()['basedir'] . '/formidable/*/*.*' );
		foreach ( $formidable_uploads as $upload ) {
			self::remove_single_file_protection( $upload );
		}
	}

	public static function restore_files_protection() {
		if ( ! empty( self::$temporarily_unprotected_formidable_files ) ) {
			foreach ( self::$temporarily_unprotected_formidable_files as $file ) {
				FrmProFileField::chmod( $file, 0200 );
			}
		}
	}

	private static function remove_single_file_protection( $file ) {
		if ( 0200 === FrmProFileField::get_chmod( array( 'file' => $file ) ) ) {
			self::$temporarily_unprotected_formidable_files[] = $file;
			FrmProFileField::chmod( $file, 0644 );
		}
	}
}
