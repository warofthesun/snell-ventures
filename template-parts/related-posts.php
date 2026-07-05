<?php
/**
 * Related posts section: same post type, reuse posts-grid card markup and background.
 * Heading and background image come from the Post Settings options page (Site Settings > Post Settings),
 * per post type, via client_get_related_posts_settings(). When no background image is set, uses solid
 * $primary-700 (footer navy). When a background image is set, a single flat semi-transparent
 * $primary-700 overlay sits on top.
 *
 * @param array $args Optional. post_id, post_type, limit (default 3), heading, background_image_url.
 */
$args = wp_parse_args( isset( $args ) ? $args : array(), array(
	'post_id'   => get_the_ID(),
	'post_type' => get_post_type(),
	'limit'     => 3,
	'heading'   => '',
	'background_image_url' => '',
) );
$post_id   = (int) $args['post_id'];
$post_type = $args['post_type'] ? $args['post_type'] : get_post_type( $post_id );
$limit     = max( 1, min( 10, (int) $args['limit'] ) );

if ( ! $args['heading'] && function_exists( 'client_get_related_posts_settings' ) ) {
	$settings = client_get_related_posts_settings( $post_type );
	$heading  = $settings['heading'];
	$bg_url   = $settings['background_image_url'];
} else {
	$heading = $args['heading'] ? $args['heading'] : __( 'Related Posts', 'client_theme' );
	$bg_url  = $args['background_image_url'] ? $args['background_image_url'] : '';
}

$related = function_exists( 'client_get_related_posts' ) ? client_get_related_posts( $post_id, $post_type, $limit ) : array();
if ( empty( $related ) ) {
	return;
}

$section_id = 'related-posts-' . $post_id;
$has_bg_img = ! empty( $bg_url );
?>
<section id="<?php echo esc_attr( $section_id ); ?>" class="c-posts c-posts--related alignfull">
	<h2 class="c-posts__heading c-posts__heading--related"><?php echo esc_html( $heading ); ?></h2>
	<!-- Background: image with flat overlay, or solid footer navy when no image -->
	<div class="c-posts__bg c-posts__bg--related" aria-hidden="true">
		<?php if ( $has_bg_img ) : ?>
			<div class="c-posts__bgImage" style="background-image:url('<?php echo esc_url( $bg_url ); ?>')"></div>
		<?php endif; ?>
		<div class="c-posts__overlay" aria-hidden="true"></div>
	</div>

	<div class="c-posts__inner l-container wrap">
		<?php
		$grid_count = count( $related );
		$grid_class = ( $grid_count <= 2 ) ? ' c-posts__grid--count-' . $grid_count : '';
		?>
		<div class="c-posts__grid<?php echo esc_attr( $grid_class ); ?>">
			<?php foreach ( $related as $p ) : ?>
				<?php
				$p_id      = $p->ID;
				$date      = ( $p->post_type === 'tribe_events' && function_exists( 'tribe_get_start_date' ) )
					? ( tribe_get_start_date( $p_id, false, 'm/d/y' ) ?: get_the_date( 'm/d/y', $p_id ) )
					: get_the_date( 'm/d/y', $p_id );
				$title     = get_the_title( $p_id );
				$permalink = get_permalink( $p_id );
				$img_url   = get_the_post_thumbnail_url( $p_id, 'post-card' );

				$categories = get_the_category( $p_id );
				$tags       = get_the_tags( $p_id );
				$chips      = array();
				if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
					foreach ( $categories as $cat ) {
						$chips[] = $cat->name;
					}
				}
				if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
					foreach ( $tags as $tag ) {
						$chips[] = $tag->name;
					}
				}
				$chips = array_values( array_unique( array_filter( $chips ) ) );
				$chips = array_slice( $chips, 0, 3 );
				?>
				<article class="c-postCard">
					<a class="c-postCard__link" href="<?php echo esc_url( $permalink ); ?>">
						<div class="c-postCard__media">
							<?php if ( $img_url ) : ?>
								<img class="c-postCard__img" src="<?php echo esc_url( $img_url ); ?>" alt="" loading="lazy" />
							<?php endif; ?>
						</div>
						<div class="c-postCard__body">
							<h6 class="c-postCard__meta"><?php echo esc_html( $date ); ?></h6>
							<h3 class="c-postCard__title"><?php echo esc_html( $title ); ?></h3>
							<?php if ( ! empty( $chips ) ) : ?>
								<div class="c-postCard__chips">
									<?php foreach ( $chips as $chip ) : ?>
										<span class="c-chip"><?php echo esc_html( $chip ); ?></span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							<span class="c-postCard__cta"><?php esc_html_e( 'Read Now', 'client_theme' ); ?></span>
						</div>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
