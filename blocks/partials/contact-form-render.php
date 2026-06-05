<?php
/**
 * Contact form band partial — Figma Contact Form/Block (718:569).
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

$accent_svg = get_theme_file_uri( 'library/images/contact-form-accent-angle.svg' );
$tag        = $embed ? 'div' : 'section';
?>

<<?php echo esc_attr( $tag ); ?>
	<?php if ( $section_id !== '' ) : ?>
		id="<?php echo esc_attr( $section_id ); ?>"
	<?php endif; ?>
	class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
>
	<?php if ( $show_accent_angle ) : ?>
		<div class="c-contactForm__accent" aria-hidden="true">
			<img class="c-contactForm__accentImg" src="<?php echo esc_url( $accent_svg ); ?>" alt="">
		</div>
	<?php endif; ?>

	<div class="c-contactForm__panel" aria-hidden="true"></div>

	<div class="c-contactForm__inner wrap">
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
</<?php echo esc_attr( $tag ); ?>>
