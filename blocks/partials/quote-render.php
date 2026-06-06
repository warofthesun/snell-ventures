<?php
/**
 * Quote band partial — Figma Quote/Block (718:626).
 *
 * @param array $args {
 *   @type array  $slides      Normalized slides with `text`.
 *   @type string $variant     standalone|compact
 *   @type string $layout      standalone|compact|combined — combined uses Figma Group 27 (718:591) quote shapes.
 *   @type bool   $embed       Legacy: compact layout inside another block (no outer breakout).
 *   @type string $section_id  Optional id attribute.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_theme_file_path( 'blocks/partials/quote-resolve.php' );

$slides      = isset( $args['slides'] ) && is_array( $args['slides'] ) ? $args['slides'] : array();
$variant     = isset( $args['variant'] ) ? (string) $args['variant'] : 'standalone';
$layout      = isset( $args['layout'] ) ? (string) $args['layout'] : '';
$embed       = ! empty( $args['embed'] );
$section_id  = isset( $args['section_id'] ) ? (string) $args['section_id'] : '';

if ( $layout === '' ) {
	$layout = $embed ? 'compact' : $variant;
}

if ( empty( $slides ) ) {
	return;
}

$is_slideshow = count( $slides ) > 1;
if ( $is_slideshow && ! is_admin() ) {
	snell_quote_enqueue_view_script();
}

$classes = array(
	'c-quote',
	'c-quote--' . sanitize_html_class( $variant ),
	'c-quote--layout-' . sanitize_html_class( $layout ),
);
if ( $embed || $layout === 'compact' ) {
	$classes[] = 'c-quote--embed';
}
if ( $layout === 'combined' ) {
	$classes[] = 'c-quote--combined';
}
if ( $is_slideshow ) {
	$classes[] = 'c-quote--slideshow';
}

$tag     = ( $embed || $layout === 'compact' || $layout === 'combined' ) ? 'div' : 'section';
$grad_id = 'quoteBlockGrad-' . ( $section_id !== '' ? sanitize_html_class( $section_id ) : wp_unique_id() );
?>

<<?php echo esc_attr( $tag ); ?>
	<?php if ( $section_id !== '' ) : ?>
		id="<?php echo esc_attr( $section_id ); ?>"
	<?php endif; ?>
	class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
	<?php if ( $is_slideshow ) : ?>
		data-quote-slides="<?php echo esc_attr( wp_json_encode( $slides ) ); ?>"
	<?php endif; ?>
>
	<?php if ( $layout === 'compact' ) : ?>
		<div class="c-quote__ratio" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="c-quote__shapes" aria-hidden="true">
		<?php if ( $layout === 'combined' ) : ?>
			<svg class="c-quote__slab" viewBox="0 0 1439.5 988" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true">
				<path d="M0 0L735 154.5L1439.5 67V988L0 741.5V0Z" fill="#12273D"/>
			</svg>
			<svg class="c-quote__gradient" viewBox="0 0 1440 814" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true">
				<defs>
					<linearGradient id="<?php echo esc_attr( $grad_id ); ?>" gradientUnits="userSpaceOnUse" x1="918.995" y1="-141.741" x2="918.995" y2="814">
						<stop offset="0%" stop-color="#12273D"/>
						<stop offset="100%" stop-color="#507A73"/>
					</linearGradient>
				</defs>
				<path d="M0 185.5 L1440 0V814 L0 507 V185.5Z" fill="url(#<?php echo esc_attr( $grad_id ); ?>)"/>
			</svg>
		<?php else : ?>
			<svg class="c-quote__canvas" viewBox="0 0 1450 868.5" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true">
				<path d="M0 0.5L738.5 92L1450 0V868.5L738.5 763.5L0 812Z" fill="#12273D"/>
				<g transform="scale(1.0069444444 1.06695577395)">
					<defs>
						<linearGradient id="<?php echo esc_attr( $grad_id ); ?>" x1="1440" y1="0" x2="0" y2="814" gradientUnits="userSpaceOnUse">
							<stop offset="0%" stop-color="#12273D"/>
							<stop offset="100%" stop-color="#507A73"/>
						</linearGradient>
					</defs>
					<path d="M0 185.5L1440 0V710.5L0 814V507V185.5Z" fill="url(#<?php echo esc_attr( $grad_id ); ?>)"/>
				</g>
			</svg>
		<?php endif; ?>
	</div>

	<div class="c-quote__inner wrap">
		<div class="c-quote__stage" <?php echo $is_slideshow ? 'aria-live="polite"' : ''; ?> role="group" aria-label="<?php esc_attr_e( 'Quote', 'client_theme' ); ?>">
			<?php if ( $is_slideshow ) : ?>
				<div class="c-quote__measure" aria-hidden="true">
					<?php foreach ( $slides as $slide ) : ?>
						<div class="c-quote__text">
							<?php echo wp_kses_post( $slide['text'] ); ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="c-quote__display">
					<div class="c-quote__slideshow">
						<div class="c-quote__text c-quote__text--current">
							<?php echo wp_kses_post( $slides[0]['text'] ); ?>
						</div>
						<div class="c-quote__text c-quote__text--next" aria-hidden="true">
							<?php echo wp_kses_post( $slides[1]['text'] ?? $slides[0]['text'] ); ?>
						</div>
					</div>
				</div>
			<?php else : ?>
				<div class="c-quote__text">
					<?php echo wp_kses_post( $slides[0]['text'] ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</<?php echo esc_attr( $tag ); ?>>
