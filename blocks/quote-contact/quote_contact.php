<?php
/**
 * Quote + Contact combined block — Figma Group 27 (718:591).
 *
 * @param array $block The block settings and attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_theme_file_path( 'blocks/partials/quote-resolve.php' );

if ( ! function_exists( 'snell_quote_contact_get_field' ) ) {
	/**
	 * Read cloned ACF fields on the combined block.
	 *
	 * @param string $name Field name.
	 * @return mixed
	 */
	function snell_quote_contact_get_field( $name ) {
		if ( ! function_exists( 'get_field' ) ) {
			return null;
		}

		$value = get_field( $name );
		if ( $value !== null && $value !== false && $value !== '' ) {
			return $value;
		}

		foreach ( array( 'quote', 'contact' ) as $group_key ) {
			$group = get_field( $group_key );
			if ( is_array( $group ) && array_key_exists( $name, $group ) ) {
				return $group[ $name ];
			}
		}

		return $value;
	}
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'quote-contact-' . $block['id'];

$items = snell_quote_contact_get_field( 'quote_items' );
$headline = (string) snell_quote_contact_get_field( 'headline' );
$intro    = (string) snell_quote_contact_get_field( 'intro' );
$form_id  = (int) snell_quote_contact_get_field( 'cf7_form' );

$slides = snell_quote_resolve_slides( $items );
$empty  = empty( $slides ) && $headline === '' && $intro === '' && $form_id <= 0;

if ( is_admin() && $empty ) :
	?>
	<div class="block-placeholder" style="border: 1px dashed #cfd3d7; padding: 1rem; border-radius: .5rem; background: rgba(255,255,255,.8);">
		<strong style="display:block; margin-bottom:.25rem;"><?php esc_html_e( 'Quote + Contact', 'client_theme' ); ?></strong>
		<p style="margin:0;"><?php esc_html_e( 'Add quotes and contact form settings.', 'client_theme' ); ?></p>
	</div>
	<?php
	return;
endif;

if ( $empty ) {
	return;
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="c-quoteContact">
	<div class="c-quoteContact__outer" aria-hidden="true"></div>

	<?php if ( ! empty( $slides ) ) : ?>
		<?php
		get_template_part(
			'blocks/partials/quote-render',
			null,
			array(
				'slides'  => $slides,
				'variant' => 'compact',
				'embed'   => true,
			)
		);
		?>
	<?php endif; ?>

	<?php
	get_template_part(
		'blocks/partials/contact-form-render',
		null,
		array(
			'headline'          => $headline,
			'intro'             => $intro,
			'form_id'           => $form_id,
			'show_accent_angle' => false,
			'embed'             => true,
		)
	);
	?>
</section>
