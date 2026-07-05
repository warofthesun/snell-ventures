<?php
/**
 * People Highlight (ACF Block)
 *
 * Single person: Figma Bio/Single/Desktop (461:5671) — headshot, name, role, excerpt.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'client_block_inserter_preview' ) && client_block_inserter_preview( $block, 'people-highlight' ) ) {
	return;
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'people-highlight-' . $block['id'];
$classes  = array( 'c-peopleHighlight' );
if ( ! empty( $block['className'] ) ) {
	$classes[] = $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$classes[] = 'align' . $block['align'];
}

$person    = function_exists( 'get_field' ) ? get_field( 'person' ) : null;
$person_id = 0;

if ( is_object( $person ) && isset( $person->ID ) ) {
	$person_id = (int) $person->ID;
} elseif ( is_array( $person ) && ! empty( $person ) ) {
	$first = reset( $person );
	$person_id = is_object( $first ) ? (int) $first->ID : (int) $first;
} elseif ( is_numeric( $person ) ) {
	$person_id = (int) $person;
}

$is_editor = is_admin();

if ( $is_editor && ! $person_id ) :
	?>
	<div class="block-placeholder" style="border: 1px dashed #cfd3d7; padding: 1rem; border-radius: .5rem; background: rgba(255,255,255,.8);">
		<strong style="display:block; margin-bottom:.25rem;"><?php esc_html_e( 'People Highlight', 'client_theme' ); ?></strong>
		<p style="margin:0;"><?php esc_html_e( 'Select a person to highlight.', 'client_theme' ); ?></p>
	</div>
	<?php
	return;
endif;

if ( ! $person_id ) :
	?>
	<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<p class="c-peopleHighlight__empty"><?php esc_html_e( 'No person selected yet.', 'client_theme' ); ?></p>
	</section>
	<?php
	return;
endif;

$name = get_the_title( $person_id );

$acf_title = '';
if ( function_exists( 'get_field' ) ) {
	$t = get_field( 'title', $person_id );
	if ( is_string( $t ) && $t !== '' ) {
		$acf_title = $t;
	}
}

$bio_excerpt = '';
if ( function_exists( 'get_field' ) ) {
	$be = get_field( 'bio_excerpt', $person_id );
	if ( is_string( $be ) && $be !== '' ) {
		$bio_excerpt = $be;
	}
}

$heading_id = 'people-highlight-heading-' . $person_id;

/**
 * Bio/Single/Desktop — Figma layer order: gradient rearmost, solid $primary-700 in front.
 */
$client_people_bio_surfaces = static function ( $gradient_id ) {
	?>
	<div class="c-peopleBio__angle" aria-hidden="true">
		<svg class="c-peopleBio__angleSvg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none" focusable="false">
			<defs>
				<linearGradient id="<?php echo esc_attr( $gradient_id ); ?>" gradientUnits="userSpaceOnUse" x1="8" y1="92" x2="92" y2="8">
					<stop offset="0%" stop-color="#12273d"/>
					<stop offset="100%" stop-color="#507a73"/>
				</linearGradient>
			</defs>
			<path fill="url(#<?php echo esc_attr( $gradient_id ); ?>)" d="M0 56 L100 14 L100 100 L0 100 Z"/>
		</svg>
	</div>
	<div class="c-peopleBio__solid" aria-hidden="true">
		<svg class="c-peopleBio__solidSvg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none" focusable="false">
			<path class="c-peopleBio__solidPath c-peopleBio__solidPath--desktop" d="M0 8 L100 40 L100 100 L0 100 Z"/>
			<path class="c-peopleBio__solidPath c-peopleBio__solidPath--mobile" d="M0 0 L100 0 L100 100 L0 100 Z"/>
		</svg>
	</div>
	<?php
};

$people_bio_grad_id  = wp_unique_id( 'people-highlight-grad-' );
$people_bio_mband_id = wp_unique_id( 'people-highlight-mband-' );
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<article class="c-peopleBio" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
		<div class="c-peopleBio__hero">
			<div class="c-peopleBio__stage">
				<?php $client_people_bio_surfaces( $people_bio_grad_id ); ?>

				<div class="c-peopleBio__mobileBand" aria-hidden="true">
					<svg class="c-peopleBio__mobileBandSvg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 342 168" preserveAspectRatio="none" focusable="false">
						<defs>
							<linearGradient id="<?php echo esc_attr( $people_bio_mband_id ); ?>" gradientUnits="userSpaceOnUse" x1="0" y1="168" x2="342" y2="0">
								<stop offset="0%" stop-color="#12273d"/>
								<stop offset="100%" stop-color="#507a73"/>
							</linearGradient>
						</defs>
						<path fill="url(#<?php echo esc_attr( $people_bio_mband_id ); ?>)" d="M0 0 L342 168 L342 168 L0 168 Z"/>
					</svg>
				</div>

				<?php if ( has_post_thumbnail( $person_id ) ) : ?>
					<div class="c-peopleBio__photo">
						<?php echo get_the_post_thumbnail( $person_id, 'client_slider_square', array( 'alt' => wp_strip_all_tags( $name ) ) ); ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="c-peopleBio__intro">
				<div class="c-peopleBio__heading">
					<div class="c-peopleBio__nameplate">
						<h4 id="<?php echo esc_attr( $heading_id ); ?>" class="h4 c-peopleBio__name">
							<?php echo esc_html( $name ); ?>
						</h4>
					</div>
					<?php if ( $acf_title !== '' ) : ?>
						<h5 class="h5 bold c-peopleBio__personalTitle"><?php echo esc_html( $acf_title ); ?></h5>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $bio_excerpt !== '' ) : ?>
				<div class="c-peopleBio__excerpt">
					<?php echo wp_kses_post( $bio_excerpt ); ?>
				</div>
			<?php endif; ?>
		</div>
	</article>
</section>
