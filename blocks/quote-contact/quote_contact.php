<?php
/**
 * Quote + Contact combined block — Figma Group 27 (718:591).
 *
 * Layer order (rear → front): quote outer slab, quote gradient, quote text,
 * contact solid panel + content (same surfaces as standalone contact; no accent angle).
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
if ( ! is_array( $items ) || $items === array() ) {
	$quote_group = function_exists( 'get_field' ) ? get_field( 'quote' ) : null;
	if ( is_array( $quote_group ) && ! empty( $quote_group['quote_items'] ) ) {
		$items = $quote_group['quote_items'];
	}
}

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
	<div class="c-quoteContact__stack">
		<?php if ( ! empty( $slides ) ) : ?>
			<div class="c-quoteContact__quote">
				<?php
				get_template_part(
					'blocks/partials/quote-render',
					null,
					array(
						'slides'  => $slides,
						'variant' => 'compact',
						'layout'  => 'combined',
					)
				);
				?>
			</div>
		<?php endif; ?>

		<div class="c-quoteContact__contact">
			<?php
			get_template_part(
				'blocks/partials/contact-form-render',
				null,
				array(
					'headline'          => $headline,
					'intro'             => $intro,
					'form_id'           => $form_id,
					'show_accent_angle' => false,
					'context'           => 'combined',
				)
			);
			?>
		</div>
	</div>
</section>
