<?php
/**
 * Quote block — Figma Quote/Block (718:626).
 *
 * @param array $block The block settings and attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'client_block_inserter_preview' ) && client_block_inserter_preview( $block, 'quote' ) ) {
	return;
}

require_once get_theme_file_path( 'blocks/partials/quote-resolve.php' );

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'quote-' . $block['id'];
$slides = snell_quote_resolve_slides( function_exists( 'get_field' ) ? get_field( 'quote_items' ) : array() );

if ( is_admin() && empty( $slides ) ) :
	?>
	<div class="block-placeholder" style="border: 1px dashed #cfd3d7; padding: 1rem; border-radius: .5rem; background: rgba(255,255,255,.8);">
		<strong style="display:block; margin-bottom:.25rem;"><?php esc_html_e( 'Quote', 'client_theme' ); ?></strong>
		<p style="margin:0;"><?php esc_html_e( 'Add quotes from the library or enter custom text.', 'client_theme' ); ?></p>
	</div>
	<?php
	return;
endif;

if ( empty( $slides ) ) {
	return;
}

get_template_part(
	'blocks/partials/quote-render',
	null,
	array(
		'slides'             => $slides,
		'variant'            => 'standalone',
		'section_id'         => $block_id,
		'remove_background'  => function_exists( 'get_field' ) ? (bool) get_field( 'remove_background' ) : false,
	)
);
