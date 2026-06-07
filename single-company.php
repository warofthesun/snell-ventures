<!--single-company-->
<?php get_header(); ?>
<?php if ( client_get_hero_config()['show'] ) { include get_template_directory() . '/partials/hero/hero.php'; } ?>

<?php
$company_post_id   = get_queried_object_id();
$company_tagline   = '';
$company_logo_id   = 0;
$company_logo_alt  = '';

if ( function_exists( 'get_field' ) && is_singular( 'company' ) ) {
	$tagline = get_field( 'company_tagline', $company_post_id );
	if ( is_string( $tagline ) ) {
		$company_tagline = trim( $tagline );
	}

	$logo = get_field( 'company_logo', $company_post_id );
	if ( is_array( $logo ) && ! empty( $logo['ID'] ) ) {
		$company_logo_id  = (int) $logo['ID'];
		$company_logo_alt = ! empty( $logo['alt'] ) ? (string) $logo['alt'] : get_the_title( $company_post_id );
	} elseif ( is_numeric( $logo ) ) {
		$company_logo_id  = (int) $logo;
		$company_logo_alt = get_the_title( $company_post_id );
	}
}

if ( $company_tagline !== '' || $company_logo_id > 0 ) :
	$intro_text_cols = $company_logo_id > 0 ? '7' : '12';
	?>
	<div class="c-companyIntro wrap">
		<div class="c-companyIntro__grid row">
			<?php if ( $company_logo_id > 0 ) : ?>
				<div class="c-companyIntro__logo col-xs-12 col-sm-5">
					<?php
					echo wp_get_attachment_image(
						$company_logo_id,
						'medium',
						false,
						array(
							'class' => 'c-companyIntro__logoImg',
							'alt'   => $company_logo_alt,
						)
					);
					?>
				</div>
			<?php endif; ?>
			<?php if ( $company_tagline !== '' ) : ?>
				<div class="c-companyIntro__text col-xs-12 col-sm-<?php echo esc_attr( $intro_text_cols ); ?>">
					<h3 class="h3 medium c-companyIntro__heading"><?php echo esc_html( $company_tagline ); ?></h3>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
endif;
?>

			<div id="content">

				<div id="inner-content" class="wrap row">

					<main id="main" class="col-xs-12" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
							 <article id="post-<?php the_ID(); ?>" <?php post_class('cf row'); ?> role="article" itemscope itemprop="blogPost" itemtype="http://schema.org/BlogPosting">

							<section class="entry-content cf <?php if(get_field('include_sidebar_on_blog_posts', 'option')) :  ?>col-xs-12 col-sm-7<?php else : ?>col-xs-12 col-md-10<?php endif; ?>" itemprop="articleBody">
								<?php the_content(); ?>
                			</section>
							<?php if(get_field('include_sidebar_on_blog_posts', 'option')) :  ?>			
							<?php get_sidebar(); ?>
							<?php endif; ?>

                <footer class="article__footer">
                  <?php the_tags( '<p class="tags"><span class="tags-title">' . __( 'Tags:', 'client_theme' ) . '</span> ', ', ', '</p>' ); ?>

                </footer> <?php // end article footer ?>

              </article> <?php // end article ?>

						<?php
						$current_post_type = get_post_type();
						if ( in_array( $current_post_type, array( 'post', 'tribe_events' ), true ) ) {
							// Heading and background from Site Settings > Post Settings (per post type).
							get_template_part( 'template-parts/related-posts' );
						}
						?>

						<?php endwhile; ?>

						<?php else : ?>

							<article id="post-not-found" class="hentry ">
									<header class="article__header">
										<h1><?php _e( 'Oops, Post Not Found!', 'client_theme' ); ?></h1>
									</header>
									<section class="entry-content">
										<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'client_theme' ); ?></p>
									</section>
									<footer class="article__footer">
											<p><?php _e( 'This is the error message in the single.php template.', 'client_theme' ); ?></p>
									</footer>
							</article>

						<?php endif; ?>

					</main>
					

				</div>

			</div>

<?php get_footer(); ?>
