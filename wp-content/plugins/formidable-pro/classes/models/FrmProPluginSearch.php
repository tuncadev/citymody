<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProPluginSearch {

	/**
	 * @param array $addons
	 * @return array
	 */
	public static function inject_search_suggestion( $addons ) {
		$slug = 'memberpress';
		$addons['memberpress'] = array(
			'id'           => $slug,
			'name'         => 'MemberPress',
			'excerpt'      => 'The all-in-one membership plugin for WordPress. Build top-level WordPress membership sites, create and sell online courses, and sell digital downloads securely.',
			'link'         => 'https://formidableforms.com/go/memberpress/',
			'search_terms' => 'membership members member course',
			'external'     => true,
			'slug'         => $slug,
			'plugin'       => $slug . '/' . $slug . '.php',
			'version'      => '',
			'author'       => '<a href="https://formidableforms.com/go/memberpress/">MemberPress</a>',
			'icons'        => array(
				'svg' => FrmProAppHelper::plugin_url() . '/images/mp-icon.svg',
			),
		);
		return $addons;
	}
}
