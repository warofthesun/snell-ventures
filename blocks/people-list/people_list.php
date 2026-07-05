<?php
/**
 * People List (ACF Block)
 *
 * Single selection: Bio/Single/Desktop (gradient rear, solid facet, photo, copy) and
 * Bio/Single/Mobile (665:860) — same field stack; mobile adds Figma Rectangle 36 band + name plate.
 * Multiple selections: Figma Headshot tiles in a 2- or 3-column grid + dialog bio (666:900 / 669:969).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'client_block_inserter_preview' ) && client_block_inserter_preview( $block, 'people-list' ) ) {
	return;
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'people-list-' . $block['id'];
$classes  = array( 'c-peopleList' );
if ( ! empty( $block['className'] ) ) {
	$classes[] = $block['className'];
}

$people = function_exists( 'get_field' ) ? get_field( 'people' ) : array();
if ( ! is_array( $people ) ) {
	$people = array();
}

$person_ids = array();
foreach ( $people as $person ) {
	$pid = is_object( $person ) ? (int) $person->ID : (int) $person;
	if ( $pid ) {
		$person_ids[] = $pid;
	}
}

$people_count = count( $person_ids );
$is_single    = ( 1 === $people_count );

if ( $is_single ) {
	$classes[] = 'c-peopleList--single';
} elseif ( $people_count > 0 ) {
	$classes[] = 'c-peopleList--multi';
	// Figma: "Multiple People List 2up or 4up" vs "3up, 6 and over".
	$classes[] = ( 2 === $people_count || 4 === $people_count ) ? 'c-peopleList--cols-2' : 'c-peopleList--cols-3';
}

/**
 * Bio/Single/Desktop — Figma layer order: gradient = rearmost, solid $primary-700 in front (higher z-index in CSS).
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
			<!--
				Desktop: angled top (Figma desktop). Mobile: horizontal top + shorter slab (see _base.scss).
			-->
			<path class="c-peopleBio__solidPath c-peopleBio__solidPath--desktop" d="M0 8 L100 40 L100 100 L0 100 Z"/>
			<path class="c-peopleBio__solidPath c-peopleBio__solidPath--mobile" d="M0 0 L100 0 L100 100 L0 100 Z"/>
		</svg>
	</div>
	<?php
};
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php if ( ! empty( $person_ids ) ) : ?>
		<ul class="c-peopleList__items">
			<?php foreach ( $person_ids as $person_id ) : ?>
				<?php
				$name = get_the_title( $person_id );

				// group_snell_people_fields → field key field_snell_people_title, name "title".
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

				$email = function_exists( 'get_field' ) ? get_field( 'email', $person_id ) : '';
				$bio         = get_post_field( 'post_content', $person_id );
				$social_rows = function_exists( 'get_field' ) ? get_field( 'social_sites', $person_id ) : array();
				if ( ! is_array( $social_rows ) ) {
					$social_rows = array();
				}
				$heading_id = 'people-bio-heading-' . $person_id;
				?>
				<li class="c-peopleList__item">
					<?php if ( $is_single ) : ?>
						<article class="c-peopleBio" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
							<div class="c-peopleBio__hero">
								<?php
								$people_bio_grad_id = wp_unique_id( 'people-bio-grad-' );
								$people_bio_mband_id = wp_unique_id( 'people-bio-mband-' );
								?>
								<div class="c-peopleBio__stage">
									<?php $client_people_bio_surfaces( $people_bio_grad_id ); ?>

									<div class="c-peopleBio__mobileBand" aria-hidden="true">
										<?php // Figma 665:860 — Rectangle 36: gradient Primary 135°, above photo in stack; path ≈ top edge sloping down-right. ?>
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

							<div class="c-peopleBio__details">
								<?php if ( $email ) : ?>
									<p class="c-peopleBio__emailWrap">
										<a href="mailto:<?php echo esc_attr( $email ); ?>" class="c-peopleBio__email"><?php echo esc_html( $email ); ?></a>
									</p>
								<?php endif; ?>

								<?php if ( ! empty( $social_rows ) ) : ?>
									<div class="c-peopleBio__social">
										<span class="c-peopleBio__socialLabel"><?php esc_html_e( 'Find me on', 'client_theme' ); ?></span>
										<ul class="c-peopleBio__socialList" aria-label="<?php esc_attr_e( 'Social profiles', 'client_theme' ); ?>">
											<?php foreach ( $social_rows as $row ) : ?>
												<?php
												$network = isset( $row['network'] ) ? $row['network'] : '';
												$url     = isset( $row['url'] ) ? $row['url'] : '';
												$icon    = isset( $row['icon'] ) ? $row['icon'] : '';

												if ( ! $url ) {
													continue;
												}

												$icon_html = '';
												if ( 'twitter' === $network ) {
													$icon_html = '<i class="fa-brands fa-x-twitter" aria-hidden="true"></i>';
												} elseif ( 'linkedin' === $network ) {
													$icon_html = '<i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>';
												} elseif ( 'facebook' === $network ) {
													$icon_html = '<i class="fa-brands fa-facebook-f" aria-hidden="true"></i>';
												} elseif ( $icon ) {
													$icon_html = $icon;
												}

												if ( ! $icon_html ) {
													continue;
												}
												?>
												<li class="c-peopleBio__socialItem">
													<a href="<?php echo esc_url( $url ); ?>" class="c-peopleBio__socialLink" target="_blank" rel="noopener noreferrer">
														<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
													</a>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
							</div>
						</article>
					<?php else : ?>
						<?php
						$dialog_id    = $block_id . '-dlg-' . $person_id;
						$dialog_id    = sanitize_title( $dialog_id );
						$dialog_title = 'people-list-dlg-title-' . $person_id;
						$tile_band    = wp_unique_id( 'people-tile-band-' );
						?>
						<button
							type="button"
							class="c-peopleCard c-peopleCard--tile"
							data-people-list-dialog-open
							aria-haspopup="dialog"
							aria-controls="<?php echo esc_attr( $dialog_id ); ?>"
							aria-label="<?php echo esc_attr( sprintf( __( 'Open bio for %s', 'client_theme' ), $name ) ); ?>"
						>
							<span class="c-peopleCard__tileStage">
								<span class="c-peopleCard__tileSolid" aria-hidden="true">
									<svg class="c-peopleCard__tileSolidSvg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none" focusable="false">
										<path class="c-peopleCard__tileSolidPath" d="M0 0 L100 0 L100 100 L0 100 Z"/>
									</svg>
								</span>
								<span class="c-peopleCard__tileBand" aria-hidden="true">
									<svg class="c-peopleCard__tileBandSvg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 342 168" preserveAspectRatio="none" focusable="false">
										<defs>
											<linearGradient id="<?php echo esc_attr( $tile_band ); ?>" gradientUnits="userSpaceOnUse" x1="0" y1="168" x2="342" y2="0">
												<stop offset="0%" stop-color="#12273d"/>
												<stop offset="100%" stop-color="#507a73"/>
											</linearGradient>
										</defs>
										<path fill="url(#<?php echo esc_attr( $tile_band ); ?>)" d="M0 0 L342 48 L342 168 L0 168 Z"/>
									</svg>
								</span>
								<?php if ( has_post_thumbnail( $person_id ) ) : ?>
									<span class="c-peopleCard__tilePhoto">
										<?php echo get_the_post_thumbnail( $person_id, 'client_slider_square', array( 'alt' => '' ) ); ?>
									</span>
								<?php endif; ?>
								<span class="c-peopleCard__tileFoot">
									<span class="c-peopleCard__tileNameplate">
										<span class="c-peopleCard__tileName"><?php echo esc_html( $name ); ?></span>
									</span>
									<?php if ( $acf_title !== '' ) : ?>
										<span class="c-peopleCard__tileRole"><?php echo esc_html( $acf_title ); ?></span>
									<?php endif; ?>
								</span>
								<span class="c-peopleCard__tileHover" aria-hidden="true">
									<span class="c-peopleCard__tileHoverInner">
										<span class="btn btn--primary--solid c-peopleCard__tileCta"><?php esc_html_e( 'Read Bio', 'client_theme' ); ?></span>
									</span>
								</span>
							</span>
						</button>

						<?php $people_dlg_shape_grad = wp_unique_id( 'people-dlg-shape-' ); ?>
						<dialog id="<?php echo esc_attr( $dialog_id ); ?>" class="c-peopleList__dialog" aria-labelledby="<?php echo esc_attr( $dialog_title ); ?>">
							<div class="c-peopleList__dialogShell">
								<div class="c-peopleList__dialogPanel">
									<button type="button" class="c-peopleList__dialogClose" data-people-list-dialog-close aria-label="<?php esc_attr_e( 'Close', 'client_theme' ); ?>">
										<span aria-hidden="true">&times;</span>
									</button>
									<div class="c-peopleList__dialogGrid">
										<div class="c-peopleList__dialogVisual">
											<div class="c-peopleList__dialogPhotoStack">
												<div class="c-peopleList__dialogShape" aria-hidden="true">
													<svg class="c-peopleList__dialogShapeSvg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none" focusable="false">
														<defs>
															<linearGradient id="<?php echo esc_attr( $people_dlg_shape_grad ); ?>" gradientUnits="userSpaceOnUse" x1="8" y1="92" x2="92" y2="8">
																<stop offset="0%" stop-color="#12273d"/>
																<stop offset="100%" stop-color="#507a73"/>
															</linearGradient>
														</defs>
														<path class="c-peopleList__dialogShapePath" fill="url(#<?php echo esc_attr( $people_dlg_shape_grad ); ?>)" d="M0 100 L0 42 Q0 34 8 30 Q11 28 16 28 L84 16 Q92 14 96 18 Q100 22 100 30 L100 100 Z"/>
													</svg>
												</div>
												<?php if ( has_post_thumbnail( $person_id ) ) : ?>
													<div class="c-peopleList__dialogPhotoWrap">
														<?php echo get_the_post_thumbnail( $person_id, 'client_slider_square', array( 'alt' => wp_strip_all_tags( $name ) ) ); ?>
													</div>
												<?php endif; ?>
											</div>
											<?php if ( ! empty( $social_rows ) ) : ?>
												<div class="c-peopleList__dialogSocialOuter">
													<ul class="c-peopleList__dialogSocialRow" aria-label="<?php esc_attr_e( 'Social profiles', 'client_theme' ); ?>">
														<?php foreach ( $social_rows as $row ) : ?>
															<?php
															$network = isset( $row['network'] ) ? $row['network'] : '';
															$url     = isset( $row['url'] ) ? $row['url'] : '';
															$icon    = isset( $row['icon'] ) ? $row['icon'] : '';
															if ( ! $url ) {
																continue;
															}
															$icon_html = '';
															if ( 'twitter' === $network ) {
																$icon_html = '<i class="fa-brands fa-x-twitter" aria-hidden="true"></i>';
															} elseif ( 'linkedin' === $network ) {
																$icon_html = '<i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>';
															} elseif ( 'facebook' === $network ) {
																$icon_html = '<i class="fa-brands fa-facebook-f" aria-hidden="true"></i>';
															} elseif ( $icon ) {
																$icon_html = $icon;
															}
															if ( ! $icon_html ) {
																continue;
															}
															?>
															<li class="c-peopleList__dialogSocialItem">
																<a href="<?php echo esc_url( $url ); ?>" class="c-peopleList__dialogSocialLink" target="_blank" rel="noopener noreferrer">
																	<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
																	<span class="screen-reader-text"><?php echo esc_html( $network ? ucfirst( $network ) : __( 'Social profile', 'client_theme' ) ); ?></span>
																</a>
															</li>
														<?php endforeach; ?>
													</ul>
												</div>
											<?php endif; ?>
										</div>
										<div class="c-peopleList__dialogMain">
											<div class="c-peopleList__dialogHead">
												<h2 id="<?php echo esc_attr( $dialog_title ); ?>" class="c-peopleList__dialogName"><?php echo esc_html( $name ); ?></h2>
												<?php if ( $acf_title !== '' ) : ?>
													<p class="c-peopleList__dialogRole"><?php echo esc_html( $acf_title ); ?></p>
												<?php endif; ?>
											</div>
											<?php if ( $email ) : ?>
												<p class="c-peopleList__dialogEmail">
													<a href="mailto:<?php echo esc_attr( $email ); ?>" class="c-peopleList__dialogEmailLink"><?php echo esc_html( $email ); ?></a>
												</p>
											<?php endif; ?>
											<?php if ( $bio ) : ?>
												<div class="c-peopleList__dialogCopy">
													<div class="c-peopleCard__bio c-peopleList__dialogBio"><?php echo wp_kses_post( wpautop( $bio ) ); ?></div>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
						</dialog>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p class="c-peopleList__empty"><?php esc_html_e( 'No people selected yet.', 'client_theme' ); ?></p>
	<?php endif; ?>
</section>
