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
 *   @type string $form_plugin contact_form_7|gravity_forms
 *   @type bool   $show_accent_angle
 *   @type bool   $embed
 *   @type string $context     standalone|embed|combined
 *   @type string $section_id
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$headline          = isset( $args['headline'] ) ? (string) $args['headline'] : '';
$intro             = isset( $args['intro'] ) ? (string) $args['intro'] : '';
$form_id           = isset( $args['form_id'] ) ? (int) $args['form_id'] : 0;
$form_plugin       = isset( $args['form_plugin'] ) ? (string) $args['form_plugin'] : '';
$show_accent_angle = array_key_exists( 'show_accent_angle', $args ) ? (bool) $args['show_accent_angle'] : true;
$embed             = ! empty( $args['embed'] );
$context           = isset( $args['context'] ) ? (string) $args['context'] : '';
$section_id        = isset( $args['section_id'] ) ? (string) $args['section_id'] : '';

if ( $form_plugin === '' ) {
	$form_plugin = function_exists( 'client_get_form_plugin' ) ? client_get_form_plugin() : 'contact_form_7';
}

$uses_gf      = ( $form_plugin === 'gravity_forms' );
$plugin_label = $uses_gf
	? __( 'Gravity Forms', 'client_theme' )
	: __( 'Contact Form 7', 'client_theme' );

if ( $context === '' ) {
	$context = $embed ? 'embed' : 'standalone';
}

$classes = array( 'c-contactForm' );
if ( $show_accent_angle ) {
	$classes[] = 'c-contactForm--has-accent';
}
if ( $context === 'embed' ) {
	$classes[] = 'c-contactForm--embed';
}
if ( $context === 'combined' ) {
	$classes[] = 'c-contactForm--combined';
}

/**
 * Output the selected form shortcode / markup.
 *
 * @param int    $form_id Form ID.
 * @param bool   $uses_gf Whether Gravity Forms is active for this block.
 * @param string $plugin_label Human-readable plugin name for admin placeholder.
 */
$snell_render_contact_form = static function ( $form_id, $uses_gf, $plugin_label ) {
	if ( $form_id <= 0 ) {
		if ( is_admin() ) {
			printf(
				'<p class="c-contactForm__placeholder">%s</p>',
				esc_html(
					sprintf(
						/* translators: %s: form plugin name (Contact Form 7 or Gravity Forms) */
						__( 'Select a %s form.', 'client_theme' ),
						$plugin_label
					)
				)
			);
		}
		return;
	}

	if ( $uses_gf ) {
		if ( function_exists( 'gravity_form' ) ) {
			gravity_form( $form_id, false, false, false, null, true );
			return;
		}
		if ( shortcode_exists( 'gravityform' ) ) {
			echo do_shortcode(
				sprintf(
					'[gravityform id="%d" title="false" description="false" ajax="true"]',
					absint( $form_id )
				)
			);
			return;
		}
		if ( is_admin() ) {
			echo '<p class="c-contactForm__placeholder">' . esc_html__( 'Gravity Forms is not active.', 'client_theme' ) . '</p>';
		}
		return;
	}

	if ( function_exists( 'wpcf7_contact_form' ) ) {
		echo do_shortcode( '[contact-form-7 id="' . absint( $form_id ) . '"]' );
		return;
	}

	if ( is_admin() ) {
		echo '<p class="c-contactForm__placeholder">' . esc_html__( 'Contact Form 7 is not active.', 'client_theme' ) . '</p>';
	}
};

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
$tag             = ( $context === 'embed' || $context === 'combined' ) ? 'div' : 'section';
$use_hero        = ( $context === 'standalone' || $context === 'combined' );
?>

<<?php echo esc_attr( $tag ); ?>
	<?php if ( $section_id !== '' ) : ?>
		id="<?php echo esc_attr( $section_id ); ?>"
	<?php endif; ?>
	class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
>
	<?php if ( $use_hero ) : ?>
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
					<?php $snell_render_contact_form( $form_id, $uses_gf, $plugin_label ); ?>
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
				<?php $snell_render_contact_form( $form_id, $uses_gf, $plugin_label ); ?>
			</div>
		</div>
	<?php endif; ?>
</<?php echo esc_attr( $tag ); ?>>
