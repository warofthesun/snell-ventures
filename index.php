<!--index-->
<?php get_header(); ?>
<?php
$client_blog_sidebar = function_exists( 'client_get_blog_index_sidebar_config' ) ? client_get_blog_index_sidebar_config() : array( 'active' => false, 'position' => 'right' );
$client_blog_sb_active = ! empty( $client_blog_sidebar['active'] );
$client_blog_sb_left   = $client_blog_sb_active && isset( $client_blog_sidebar['position'] ) && 'left' === $client_blog_sidebar['position'];
?>
<?php include get_template_directory() . '/partials/hero/hero.php'; ?>
			<div id="content">

				<div id="inner-content" class="wrap row<?php echo $client_blog_sb_active ? ' client-blog-index--sidebar' : ''; ?>">

						<main id="main" class="col-xs-12 row" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

							<div class="c-storiesPage">
							<?php
							$client_featured_post_id = function_exists( 'client_get_blog_featured_post_id' ) ? (int) client_get_blog_featured_post_id() : 0;
							if ( $client_featured_post_id ) {
								include get_template_directory() . '/partials/blog-stories-featured.php';
							}
							?>

							<div class="c-storiesIndex<?php echo $client_blog_sb_active ? ' c-storiesIndex--with-sidebar' : ''; ?>">
							<?php if ( have_posts() ) : ?>
								<div id="client-stories-header" class="c-storiesDivider">
									<span class="c-storiesDivider__label"><?php esc_html_e( 'Stories', 'client_theme' ); ?></span>
									<span class="c-storiesDivider__line" aria-hidden="true"></span>
								</div>
								<?php if ( $client_blog_sb_active ) : ?>
								<div class="c-storiesIndexLayout row c-storiesIndexLayout--with-sidebar<?php echo $client_blog_sb_left ? ' c-storiesIndexLayout--sidebar-left' : ''; ?>">
									<div class="c-storiesIndex__gridCol col-xs-12 col-md-8 col-lg-9">
								<?php endif; ?>
								<div class="c-posts__grid">
									<?php
									while ( have_posts() ) :
										the_post();
										if ( function_exists( 'client_render_blog_post_card' ) ) {
											client_render_blog_post_card( $GLOBALS['post'] );
										}
									endwhile;
									?>
								</div>
								<?php client_page_navi(); ?>
								<?php if ( $client_blog_sb_active ) : ?>
									</div>
									<?php
									$GLOBALS['client_sidebar_wrapper_class'] = 'sidebar col-xs-12 col-md-4 col-lg-3';
									get_sidebar();
									?>
								</div>
								<?php endif; ?>

							<?php elseif ( $client_featured_post_id ) : ?>
								<?php if ( $client_blog_sb_active ) : ?>
								<div id="client-stories-header" class="c-storiesDivider">
									<span class="c-storiesDivider__label"><?php esc_html_e( 'Stories', 'client_theme' ); ?></span>
									<span class="c-storiesDivider__line" aria-hidden="true"></span>
								</div>
								<div class="c-storiesIndexLayout row c-storiesIndexLayout--with-sidebar<?php echo $client_blog_sb_left ? ' c-storiesIndexLayout--sidebar-left' : ''; ?>">
									<div class="c-storiesIndex__gridCol col-xs-12 col-md-8 col-lg-9">
										<p class="c-storiesIndex__empty"><?php esc_html_e( 'No additional posts yet.', 'client_theme' ); ?></p>
									</div>
									<?php
									$GLOBALS['client_sidebar_wrapper_class'] = 'sidebar col-xs-12 col-md-4 col-lg-3';
									get_sidebar();
									?>
								</div>
								<?php else : ?>
								<p class="c-storiesIndex__empty"><?php esc_html_e( 'No additional posts yet.', 'client_theme' ); ?></p>
								<?php endif; ?>

							<?php else : ?>

								<?php if ( $client_blog_sb_active ) : ?>
								<div class="c-storiesIndexLayout row c-storiesIndexLayout--with-sidebar<?php echo $client_blog_sb_left ? ' c-storiesIndexLayout--sidebar-left' : ''; ?>">
									<div class="c-storiesIndex__gridCol col-xs-12 col-md-8 col-lg-9">
								<?php endif; ?>
									<article id="post-not-found" class="hentry ">
											<header class="article__header">
												<h1><?php esc_html_e( 'Oops, Post Not Found!', 'client_theme' ); ?></h1>
										</header>
											<section class="entry-content">
												<p><?php esc_html_e( 'Uh Oh. Something is missing. Try double checking things.', 'client_theme' ); ?></p>
										</section>
										<footer class="article__footer">
												<p><?php esc_html_e( 'This is the error message in the index.php template.', 'client_theme' ); ?></p>
										</footer>
									</article>
								<?php if ( $client_blog_sb_active ) : ?>
									</div>
									<?php
									$GLOBALS['client_sidebar_wrapper_class'] = 'sidebar col-xs-12 col-md-4 col-lg-3';
									get_sidebar();
									?>
								</div>
								<?php endif; ?>

							<?php endif; ?>
							</div>
							</div>

						</main>

				</div>

			</div>

<?php get_footer(); ?>
