<?php
/**
 * Upload size ceiling (PHP ini + WordPress filter).
 *
 * WordPress reads upload_max_filesize / post_max_size from the PHP that serves the request.
 * Calling ini_set() from the theme (or even an MU plugin) is best-effort: depending on PHP build
 * and SAPI, those directives may already be fixed for the request or not changeable via ini_set.
 *
 * This repo includes wp-content/mu-plugins/client-upload-limits.php so the bump runs as early as
 * WordPress loads MU plugins. If the admin UI still shows ~2 MB, configure the actual php.ini (or
 * pool snippet) used by your local web stack — same fix applies on production; it is not “host
 * blocking,” it is whatever INI the PHP worker loaded.
 *
 * Earliest in-app option: wp-config.php, immediately before require wp-settings.php:
 *
 *   @ini_set( 'upload_max_filesize', '64M' );
 *   @ini_set( 'post_max_size', '64M' );
 *
 * @package client_theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Raise upload ceiling toward 50 MB (WordPress + PHP). Effective limit is the minimum of this cap
 * and the web PHP INI (ini_set from here may not override it).
 */
define( 'CLIENT_MAX_UPLOAD_BYTES', 50 * 1024 * 1024 );

/**
 * Bump PHP upload-related ini values when possible.
 */
function client_bump_php_upload_ini() {
	@ini_set( 'upload_max_filesize', '50M' );
	@ini_set( 'post_max_size', '55M' );
}

// Run as soon as the theme loads this file.
client_bump_php_upload_ini();
add_action( 'init', 'client_bump_php_upload_ini', 1 );
add_action( 'admin_init', 'client_bump_php_upload_ini', 0 );

/**
 * WordPress passes $size = min(upload_max_filesize, post_max_size) from INI *before* filters run.
 * Bump INI first, then read fresh values. If another filter (e.g. multisite) lowered $size below what
 * PHP now reports, use the higher effective PHP cap up to CLIENT_MAX_UPLOAD_BYTES.
 *
 * @param int $size    Size in bytes after prior filters.
 * @param int $u_bytes upload_max_filesize in bytes (snapshot; may be stale).
 * @param int $p_bytes post_max_size in bytes (snapshot; may be stale).
 * @return int
 */
function client_upload_size_limit( $size, $u_bytes, $p_bytes ) {
	client_bump_php_upload_ini();

	$u = wp_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
	$p = wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) );
	if ( $u < 1 || $p < 1 ) {
		return (int) $size;
	}

	$php_cap = (int) min( $u, $p );
	$theme   = (int) min( CLIENT_MAX_UPLOAD_BYTES, $php_cap );

	$size = (int) $size;
	if ( $size > 0 && $size < $php_cap ) {
		return (int) min( CLIENT_MAX_UPLOAD_BYTES, $php_cap );
	}

	return (int) min( $theme, $size > 0 ? $size : $theme );
}

add_filter( 'upload_size_limit', 'client_upload_size_limit', 999999, 3 );
