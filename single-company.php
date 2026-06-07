<!--single-company-->
<?php get_header(); ?>
<?php if ( client_get_hero_config()['show'] ) { include get_template_directory() . '/partials/hero/hero.php'; } ?>

<?php
$company_post_id   = get_queried_object_id();
$company_tagline   = '';
$company_synopsis  = '';

if ( function_exists( 'get_field' ) && is_singular( 'company' ) ) {
	$tagline = get_field( 'company_tagline', $company_post_id );
	if ( is_string( $tagline ) ) {
		$company_tagline = trim( $tagline );
	}

	$synopsis = get_field( 'company_synopsis', $company_post_id );
	if ( is_string( $synopsis ) && trim( wp_strip_all_tags( $synopsis ) ) !== '' ) {
		$company_synopsis = $synopsis;
	}
}

if ( $company_tagline !== '' || $company_synopsis !== '' ) :
	?>
	<div class="c-companyIntro wrap">
		<div class="c-companyIntro__grid row">
			<div class="c-companyIntro__text col-xs-12">
				<?php if ( $company_tagline !== '' ) : ?>
					<h4 class="h4 medium c-companyIntro__heading<?php echo $company_synopsis !== '' ? ' c-companyIntro__heading--hasSynopsis' : ''; ?>"><?php echo esc_html( $company_tagline ); ?></h4>
				<?php endif; ?>
				<?php if ( $company_synopsis !== '' ) : ?>
					<div class="c-companyIntro__synopsis"><?php echo wp_kses_post( $company_synopsis ); ?></div>
				<?php endif; ?>
			</div>
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
