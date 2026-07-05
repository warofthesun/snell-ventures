<?php
/**
 * Company Bio (ACF Block)
 *
 * Figma Company/Bio (733:1039): representative image with angled gradient, frosted logo,
 * company name, tagline, synopsis, stats, and CTA.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'client_block_inserter_preview' ) && client_block_inserter_preview( $block, 'company-bio' ) ) {
	return;
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'company-bio-' . $block['id'];
$classes  = array( 'c-companyBio' );

if ( ! empty( $block['className'] ) ) {
	$classes[] = $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$classes[] = 'align' . $block['align'];
}

$company_id = function_exists( 'get_field' ) ? get_field( 'company' ) : 0;
if ( is_object( $company_id ) && isset( $company_id->ID ) ) {
	$company_id = (int) $company_id->ID;
} else {
	$company_id = (int) $company_id;
}

$image_position = function_exists( 'get_field' ) ? get_field( 'image_position' ) : 'left';
$image_position = ( $image_position === 'right' ) ? 'right' : 'left';
$classes[]      = 'c-companyBio--image' . ucfirst( $image_position );

$is_editor = is_admin();

if ( $is_editor && ! $company_id ) :
	?>
	<div class="block-placeholder" style="border: 1px dashed #cfd3d7; padding: 1rem; border-radius: .5rem; background: rgba(255,255,255,.8);">
		<strong style="display:block; margin-bottom:.25rem;"><?php esc_html_e( 'Company Bio', 'client_theme' ); ?></strong>
		<p style="margin:0;"><?php esc_html_e( 'Select a company to display.', 'client_theme' ); ?></p>
	</div>
	<?php
	return;
endif;

$bio = function_exists( 'client_get_company_bio_data' ) ? client_get_company_bio_data( $company_id ) : null;

if ( ! $bio ) :
	?>
	<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<p class="c-companyBio__empty"><?php esc_html_e( 'No company selected yet.', 'client_theme' ); ?></p>
	</section>
	<?php
	return;
endif;

$heading_id   = 'company-bio-heading-' . $company_id;
$shape_mirror = ( $image_position === 'left' );
$grad_id      = 'company-bio-grad-' . ( ! empty( $block['id'] ) ? (string) (int) $block['id'] : wp_unique_id() );

$has_visual = $bio['rep_image']['id'] > 0 || $bio['logo']['id'] > 0;
$has_header = $bio['name'] !== '' || $bio['tagline'] !== '';
$has_stats  = ! empty( $bio['stats'] );
$has_body   = $bio['synopsis'] !== '' || $has_stats || $bio['permalink'] !== '';
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="c-companyBio__inner wrap">
		<div class="c-companyBio__layout row">
			<?php if ( $has_visual ) : ?>
				<div class="c-companyBio__visualCol col-xs-12 col-md-6">
					<div class="c-companyBio__visual<?php echo $shape_mirror ? ' c-companyBio__visual--mirror' : ''; ?>">
						<div class="c-companyBio__visualShape" aria-hidden="true">
							<div class="c-companyBio__visualShapeInner">
								<svg class="c-companyBio__visualShapeSvg" viewBox="0 0 629 449" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
									<defs>
										<linearGradient id="<?php echo esc_attr( $grad_id ); ?>" x1="401.422" y1="-78.1841" x2="401.422" y2="449" gradientUnits="userSpaceOnUse">
											<stop offset="0" stop-color="#12273D"/>
											<stop offset="1" stop-color="#507A73"/>
										</linearGradient>
									</defs>
									<path fill="url(#<?php echo esc_attr( $grad_id ); ?>)" d="M0 256.037L629 0V449H0V256.037Z"/>
								</svg>
							</div>
						</div>

						<div class="c-companyBio__visualFrame">
							<?php if ( $bio['rep_image']['id'] > 0 ) : ?>
								<div class="c-companyBio__photo">
									<?php
									echo wp_get_attachment_image(
										$bio['rep_image']['id'],
										'client_text_image_square',
										false,
										array(
											'class' => 'c-companyBio__photoImg',
											'alt'   => $bio['rep_image']['alt'],
										)
									);
									?>
								</div>
							<?php endif; ?>

							<?php if ( $bio['logo']['id'] > 0 ) : ?>
								<div class="c-companyBio__logoPlate">
									<?php
									echo wp_get_attachment_image(
										$bio['logo']['id'],
										'medium',
										false,
										array(
											'class' => 'c-companyBio__logoImg',
											'alt'   => $bio['logo']['alt'],
										)
									);
									?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $has_header || $has_body ) : ?>
				<div class="c-companyBio__contentCol col-xs-12 col-md-6">
					<div class="c-companyBio__content">
						<?php if ( $has_header ) : ?>
							<div class="c-companyBio__header">
								<span class="c-companyBio__accent" aria-hidden="true"></span>
								<div class="c-companyBio__titles">
									<?php if ( $bio['name'] !== '' ) : ?>
										<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="h3 medium c-companyBio__name"><?php echo esc_html( $bio['name'] ); ?></h2>
									<?php endif; ?>
									<?php if ( $bio['tagline'] !== '' ) : ?>
										<p class="h5 bold c-companyBio__tagline"><?php echo esc_html( $bio['tagline'] ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $bio['synopsis'] !== '' ) : ?>
							<div class="c-companyBio__synopsis"><?php echo wp_kses_post( $bio['synopsis'] ); ?></div>
						<?php endif; ?>

						<?php if ( $has_stats ) : ?>
							<div class="c-companyBio__stats">
								<?php foreach ( $bio['stats'] as $stat ) : ?>
									<div class="c-companyBio__stat">
										<p class="c-companyBio__statValue"><?php echo esc_html( (string) (int) $stat['value'] ); ?></p>
										<?php if ( ! empty( $stat['lines'] ) && is_array( $stat['lines'] ) ) : ?>
											<div class="c-companyBio__statLabel">
												<?php foreach ( $stat['lines'] as $line ) : ?>
													<?php if ( (string) $line !== '' ) : ?>
														<p class="c-companyBio__statLabelLine"><?php echo esc_html( $line ); ?></p>
													<?php endif; ?>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if ( $bio['permalink'] !== '' ) : ?>
							<a href="<?php echo esc_url( $bio['permalink'] ); ?>" class="c-companyBio__cta">
								<?php esc_html_e( 'Read Their Story', 'client_theme' ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
