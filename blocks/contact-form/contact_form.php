<?php
/**
 * Contact Form block — Figma Contact Form/Block (718:569).
 *
 * @param array $block The block settings and attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'client_block_inserter_preview' ) && client_block_inserter_preview( $block, 'contact-form' ) ) {
	return;
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'contact-form-' . $block['id'];
$headline = function_exists( 'get_field' ) ? (string) get_field( 'headline' ) : '';
$intro    = function_exists( 'get_field' ) ? (string) get_field( 'intro' ) : '';
$form_id  = function_exists( 'get_field' ) ? (int) get_field( 'cf7_form' ) : 0;
$accent   = function_exists( 'get_field' ) ? (bool) get_field( 'show_accent_angle' ) : true;

if ( is_admin() && $form_id <= 0 && $headline === '' && $intro === '' ) :
	?>
	<div class="block-placeholder" style="border: 1px dashed #cfd3d7; padding: 1rem; border-radius: .5rem; background: rgba(255,255,255,.8);">
		<strong style="display:block; margin-bottom:.25rem;"><?php esc_html_e( 'Contact Form', 'client_theme' ); ?></strong>
		<p style="margin:0;"><?php esc_html_e( 'Add intro copy and select a Contact Form 7 form.', 'client_theme' ); ?></p>
	</div>
	<?php
	return;
endif;

get_template_part(
	'blocks/partials/contact-form-render',
	null,
	array(
		'headline'          => $headline,
		'intro'             => $intro,
		'form_id'           => $form_id,
		'show_accent_angle' => $accent,
		'section_id'        => $block_id,
	)
);
