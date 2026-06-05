<?php
/**
 * Contact form band partial — Figma Contact Form/Block (206:439).
 *
 * Layer order (rearmost → front): gradient accent angle, solid $primary-700 panel, content.
 *
 * @param array $args {
 *   @type string $headline
 *   @type string $intro
 *   @type int    $form_id
 *   @type bool   $show_accent_angle
 *   @type bool   $embed
 *   @type string $section_id
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$headline          = isset( $args['headline'] ) ? (string) $args['headline'] : '';
$intro             = isset( $args['intro'] ) ? (string) $args['intro'] : '';
$form_id           = isset( $args['form_id'] ) ? (int) $args['form_id'] : 0;
$show_accent_angle = array_key_exists( 'show_accent_angle', $args ) ? (bool) $args['show_accent_angle'] : true;
$embed             = ! empty( $args['embed'] );
$section_id        = isset( $args['section_id'] ) ? (string) $args['section_id'] : '';

$classes = array( 'c-contactForm' );
if ( $show_accent_angle ) {
	$classes[] = 'c-contactForm--has-accent';
}
if ( $embed ) {
	$classes[] = 'c-contactForm--embed';
}

/**
 * Decorative surfaces — same stacking model as People Highlight (.c-peopleBio__angle / __solid).
 *
 * @param string $gradient_id Unique SVG gradient id.
 * @param bool   $show_angle  Whether to render the rear gradient wedge.
 */
$snell_contact_form_surfaces = static function ( $gradient_id, $show_angle ) {
	if ( $show_angle ) :
		?>
		<div class="c-contactForm__angle" aria-hidden="true">
			<svg class="c-contactForm__angleSvg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1438 420" preserveAspectRatio="none" focusable="false">
				<defs>
					<linearGradient id="<?php echo esc_attr( $gradient_id ); ?>" gradientUnits="userSpaceOnUse" x1="719" y1="0" x2="851" y2="502">
						<stop offset="0%" stop-color="#12273d"/>
						<stop offset="100%" stop-color="#507a73"/>
					</linearGradient>
				</defs>
				<path fill="url(#<?php echo esc_attr( $gradient_id ); ?>)" d="M0 100.5L1438 0V420H0V239.5Z"/>
			</svg>
		</div>
		<?php
	endif;
	?>
	<div class="c-contactForm__solid" aria-hidden="true">
		<svg class="c-contactForm__solidSvg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1438 1013" preserveAspectRatio="none" focusable="false">
			<path class="c-contactForm__solidPath c-contactForm__solidPath--desktop" d="M0 305.5L1438 0V1013H0V305.5Z"/>
			<path class="c-contactForm__solidPath c-contactForm__solidPath--mobile" d="M0 0H1438V1013H0V0Z"/>
		</svg>
	</div>
	<?php
};

$contact_grad_id = wp_unique_id( 'contact-form-grad-' );
$tag             = $embed ? 'div' : 'section';
?>

<<?php echo esc_attr( $tag ); ?>
	<?php if ( $section_id !== '' ) : ?>
		id="<?php echo esc_attr( $section_id ); ?>"
	<?php endif; ?>
	class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
>
	<?php if ( ! $embed ) : ?>
		<div class="c-contactForm__hero">
			<div class="c-contactForm__stage">
				<?php $snell_contact_form_surfaces( $contact_grad_id, $show_accent_angle ); ?>
			</div>

			<div class="c-contactForm__content">
				<?php if ( $headline !== '' || $intro !== '' ) : ?>
					<div class="c-contactForm__intro">
						<?php if ( $headline !== '' ) : ?>
							<h2 class="c-contactForm__headline"><?php echo esc_html( $headline ); ?></h2>
						<?php endif; ?>
						<?php if ( $intro !== '' ) : ?>
							<div class="c-contactForm__body"><?php echo wp_kses_post( $intro ); ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="c-contactForm__formWrap">
					<?php if ( $form_id > 0 && function_exists( 'wpcf7_contact_form' ) ) : ?>
						<?php echo do_shortcode( '[contact-form-7 id="' . absint( $form_id ) . '"]' ); ?>
					<?php elseif ( is_admin() ) : ?>
						<p class="c-contactForm__placeholder"><?php esc_html_e( 'Select a Contact Form 7 form.', 'client_theme' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php else : ?>
		<div class="c-contactForm__content wrap">
			<?php if ( $headline !== '' || $intro !== '' ) : ?>
				<div class="c-contactForm__intro">
					<?php if ( $headline !== '' ) : ?>
						<h2 class="c-contactForm__headline"><?php echo esc_html( $headline ); ?></h2>
					<?php endif; ?>
					<?php if ( $intro !== '' ) : ?>
						<div class="c-contactForm__body"><?php echo wp_kses_post( $intro ); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="c-contactForm__formWrap">
				<?php if ( $form_id > 0 && function_exists( 'wpcf7_contact_form' ) ) : ?>
					<?php echo do_shortcode( '[contact-form-7 id="' . absint( $form_id ) . '"]' ); ?>
				<?php elseif ( is_admin() ) : ?>
					<p class="c-contactForm__placeholder"><?php esc_html_e( 'Select a Contact Form 7 form.', 'client_theme' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
</<?php echo esc_attr( $tag ); ?>>
