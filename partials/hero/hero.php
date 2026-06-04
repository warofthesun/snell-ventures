<?php
/**
 * Reusable hero partial. Uses client_get_hero_config() for context (page, post, blog, archive).
 * Editor choice on pages via ACF "Hero Style"; automatic type on single post, blog index, archive.
 * Hero Medium is used for the Medium style on pages, the Events archive, and single Company (same markup/CSS).
 *
 * Markup for each variant lives in `partials/hero/inc/hero-{type}.php`.
 */
$hero = isset( $hero_config ) ? $hero_config : client_get_hero_config();
if ( empty( $hero['show'] ) ) {
	return;
}
$hero_type = isset( $hero['type'] ) ? $hero['type'] : 'landing';
$d         = isset( $hero['data'] ) ? $hero['data'] : array();

$hero_inc_dir = __DIR__ . '/inc';
?>
<!-- Hero (<?php echo esc_attr( $hero_type ); ?>) -->

<?php
if ( $hero_type === 'single' ) {
	include $hero_inc_dir . '/hero-single.php';
} elseif ( $hero_type === 'post' ) {
	include $hero_inc_dir . '/hero-post.php';
} elseif ( $hero_type === 'blog' || $hero_type === 'archive' ) {
	include $hero_inc_dir . '/hero-blog-archive.php';
} elseif ( $hero_type === 'medium' ) {
	include $hero_inc_dir . '/hero-medium.php';
} elseif ( $hero_type === 'initiative' ) {
	include $hero_inc_dir . '/hero-initiative.php';
} else {
	include $hero_inc_dir . '/hero-landing.php';
}
