<?php
/**
 * Contact Form block — Figma Contact Form/Block (718:569).
 *
 * Form plugin (Contact Form 7 or Gravity Forms) is chosen in Site Settings → Form Plugin Selection.
 *
 * @param array $block The block settings and attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'client_block_inserter_preview' ) && client_block_inserter_preview( $block, 'contact-form' ) ) {
	return;
}

$uses_gf  = function_exists( 'client_uses_gravity_forms' ) && client_uses_gravity_forms();
$plugin_label = $uses_gf
	? __( 'Gravity Forms', 'client_theme' )
	: __( 'Contact Form 7', 'client_theme' );

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'contact-form-' . $block['id'];
$headline = function_exists( 'get_field' ) ? (string) get_field( 'headline' ) : '';
$intro    = function_exists( 'get_field' ) ? (string) get_field( 'intro' ) : '';
$form_id  = 0;
if ( function_exists( 'get_field' ) ) {
	$form_id = $uses_gf ? (int) get_field( 'gf_form' ) : (int) get_field( 'cf7_form' );
}
$accent = function_exists( 'get_field' ) ? (bool) get_field( 'show_accent_angle' ) : true;

if ( is_admin() && $form_id <= 0 && $headline === '' && $intro === '' ) :
	?>
	<div class="block-placeholder" style="border: 1px dashed #cfd3d7; padding: 1rem; border-radius: .5rem; background: rgba(255,255,255,.8);">
		<strong style="display:block; margin-bottom:.25rem;"><?php esc_html_e( 'Contact Form', 'client_theme' ); ?></strong>
		<p style="margin:0;">
			<?php
			printf(
				/* translators: %s: form plugin name (Contact Form 7 or Gravity Forms) */
				esc_html__( 'Add intro copy and select a %s form.', 'client_theme' ),
				esc_html( $plugin_label )
			);
			?>
		</p>
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
		'form_plugin'       => $uses_gf ? 'gravity_forms' : 'contact_form_7',
		'show_accent_angle' => $accent,
		'section_id'        => $block_id,
	)
);
