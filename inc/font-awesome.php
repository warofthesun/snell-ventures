<?php
/**
 * Font Awesome (ACF Font Awesome plugin) integration.
 *
 * Keeps the ACF icon picker aligned with the theme kit URL and FA7 chooser
 * family/style strings used for Brands icons.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default Font Awesome kit script URL (override via client_font_awesome_kit_url).
 *
 * @return string
 */
function client_default_font_awesome_kit_url() {
	return 'https://kit.fontawesome.com/059e62f330.js';
}

/**
 * Kit token from the theme kit URL (e.g. 059e62f330).
 *
 * @return string|false
 */
function client_get_font_awesome_kit_token() {
	$url = apply_filters( 'client_font_awesome_kit_url', client_default_font_awesome_kit_url() );
	if ( ! is_string( $url ) || $url === '' ) {
		return false;
	}

	if ( preg_match( '#kit\.fontawesome\.com/([A-Za-z0-9]+)\.js#', $url, $m ) ) {
		return $m[1];
	}

	return false;
}

/**
 * Tell ACF Font Awesome which kit to use (admin picker + admin enqueue).
 *
 * @param string|false $token Existing token.
 * @return string|false
 */
function client_acffa_fa_kit_token( $token = false ) {
	$from_theme = client_get_font_awesome_kit_token();
	return $from_theme ? $from_theme : $token;
}
add_filter( 'ACFFA_fa_kit_token', 'client_acffa_fa_kit_token' );

/**
 * FA7 Icon Chooser reports Brands as classic_brands and/or brands_solid.
 * Ensure brands-only fields match either form so the picker is not empty.
 *
 * @param array $icon_sets Icon set slugs.
 * @return array
 */
function client_acffa_expand_brands_icon_sets( $icon_sets ) {
	if ( ! is_array( $icon_sets ) || empty( $icon_sets ) ) {
		return $icon_sets;
	}

	$brands_aliases = array( 'brands', 'classic_brands', 'brands_solid' );
	if ( ! array_intersect( $icon_sets, $brands_aliases ) ) {
		return $icon_sets;
	}

	return array_values( array_unique( array_merge( $icon_sets, $brands_aliases ) ) );
}
add_filter( 'ACFFA_standardize_icon_set_family_style', 'client_acffa_expand_brands_icon_sets', 20 );

/**
 * Keep Site Settings field group aligned with acf-json when the DB copy is stale.
 *
 * @param array $group Field group.
 * @return array
 */
function client_fix_site_settings_field_group( $group ) {
	if ( ! is_array( $group ) || ( $group['key'] ?? '' ) !== 'group_69a4e5ae0b03d' ) {
		return $group;
	}

	$json_path = get_template_directory() . '/acf-json/group_69a4e5ae0b03d.json';
	if ( ! file_exists( $json_path ) ) {
		return $group;
	}

	$json = json_decode( (string) file_get_contents( $json_path ), true );
	if ( ! is_array( $json ) || empty( $json['fields'] ) ) {
		return $group;
	}

	$json_mtime = (int) filemtime( $json_path );
	$db_post    = function_exists( 'acf_get_field_group_post' ) ? acf_get_field_group_post( 'group_69a4e5ae0b03d' ) : null;
	$db_mtime   = is_object( $db_post ) ? (int) strtotime( (string) $db_post->post_modified_gmt . ' GMT' ) : 0;

	if ( $json_mtime <= $db_mtime ) {
		return $group;
	}

	$group['fields']   = $json['fields'];
	$group['modified'] = $json['modified'] ?? $json_mtime;

	return $group;
}
add_filter( 'acf/load_field_group/key=group_69a4e5ae0b03d', 'client_fix_site_settings_field_group', 30 );
