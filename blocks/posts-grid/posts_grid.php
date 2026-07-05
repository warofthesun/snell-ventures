<?php
/**
 * Posts Grid (ACF Block)
 */

$block_id = !empty($block['anchor']) ? $block['anchor'] : 'posts-grid-' . $block['id'];

if ( function_exists( 'client_block_inserter_preview' ) && client_block_inserter_preview( $block, 'posts-grid' ) ) {
	return;
}

$classes = ['c-posts'];
if (!empty($block['className'])) $classes[] = $block['className'];

$post_type     = get_field('post_type') ?: 'post';
$per_page      = (int) (get_field('posts_per_page') ?: 6);
$background    = get_field('background');
$bg_image      = get_field('background_image');

$heading   = get_field('section_heading');

if ( ! $background ) {
	$background = ( ! empty( $bg_image ) && is_array( $bg_image ) ) ? 'image' : 'none';
}
if ( ! in_array( $background, array( 'none', 'image', 'solid_color' ), true ) ) {
	$background = 'none';
}

$has_bg = ( $background === 'image' || $background === 'solid_color' );
$show_accent_angle = true;
if ( $background === 'solid_color' ) {
	$accent_field = get_field( 'show_accent_angle' );
	if ( in_array( $accent_field, array( false, 0, '0' ), true ) ) {
		$show_accent_angle = false;
	}
}
if ( $has_bg ) {
	$classes[] = 'c-posts--has-bg';
	if ( $background === 'image' ) {
		$classes[] = 'c-posts--bg-image';
	} elseif ( $background === 'solid_color' ) {
		$classes[] = 'c-posts--bg-solid';
		if ( $show_accent_angle ) {
			$classes[] = 'c-posts--has-accent';
		}
	}
}

$bg_url = '';
if ( $background === 'image' && ! empty( $bg_image ) && is_array( $bg_image ) ) {
	$bg_url = esc_url( $bg_image['sizes']['large'] ?? $bg_image['url'] );
}

// Hard cap logic (max 6 total cards)
$requested = (int) get_field('posts_per_page');
if ($requested <= 0) $requested = 6;
$cap = min($requested, 6);

// Sticky handling: only applies to the default "post" post type
$sticky_ids = ($post_type === 'post') ? get_option('sticky_posts') : [];
$sticky_ids = is_array($sticky_ids) ? array_values(array_filter(array_map('intval', $sticky_ids))) : [];

$posts_to_render = [];

// Special handling for Events: only upcoming or ongoing events.
if ($post_type === 'tribe_events') {
  $now = current_time('Y-m-d H:i:s');
  $q = new WP_Query([
    'post_type'      => 'tribe_events',
    'post_status'    => 'publish',
    'posts_per_page' => $cap,
    // Order by start date, but include events whose end date is in the future
    'meta_key'       => '_EventStartDate',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => [
      [
        'key'     => '_EventEndDate',
        'value'   => $now,
        'compare' => '>=',
        'type'    => 'DATETIME',
      ],
    ],
    'no_found_rows'  => true,
  ]);

  $posts_to_render = $q->posts;
} elseif ($post_type === 'post' && !empty($sticky_ids)) {
  // 1) Pull sticky posts first (counting toward the cap)
  $sticky_query = new WP_Query([
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => $cap,
    'post__in'            => $sticky_ids,
    'orderby'             => 'post__in',
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
  ]);

  $sticky_posts = $sticky_query->posts;
  $sticky_count = count($sticky_posts);
  $remaining    = max(0, $cap - $sticky_count);

  // 2) Fill the remaining slots with newest non-sticky posts
  $normal_posts = [];
  if ($remaining > 0) {
    $normal_query = new WP_Query([
      'post_type'           => 'post',
      'post_status'         => 'publish',
      'posts_per_page'      => $remaining,
      'post__not_in'        => $sticky_ids,
      'orderby'             => 'date',
      'order'               => 'DESC',
      'ignore_sticky_posts' => true,
      'no_found_rows'       => true,
    ]);

    $normal_posts = $normal_query->posts;
  }

  $posts_to_render = array_merge($sticky_posts, $normal_posts);
} else {
  // Non-default post types (or no sticky posts): simple capped query
  $q = new WP_Query([
    'post_type'      => $post_type,
    'post_status'    => 'publish',
    'posts_per_page' => $cap,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
  ]);

  $posts_to_render = $q->posts;
}

// Only show "view more" button if there are more items than we're displaying.
if ( $post_type === 'tribe_events' ) {
  $now_for_count = current_time('Y-m-d H:i:s');
  $count_query = new WP_Query([
    'post_type'      => 'tribe_events',
    'post_status'    => 'publish',
    'posts_per_page' => $cap + 1,
    'fields'         => 'ids',
    'meta_key'       => '_EventStartDate',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => [
      [
        'key'     => '_EventEndDate',
        'value'   => $now_for_count,
        'compare' => '>=',
        'type'    => 'DATETIME',
      ],
    ],
    'no_found_rows'  => true,
  ]);
} else {
  $count_query = new WP_Query([
    'post_type'      => $post_type,
    'post_status'    => 'publish',
    'posts_per_page' => $cap + 1,
    'fields'         => 'ids',
    'no_found_rows'  => true,
  ]);
}
$has_more_posts = count($count_query->posts) > $cap;

$no_events_message = '';
if ( $post_type === 'tribe_events' && empty( $posts_to_render ) && function_exists( 'get_field' ) ) {
  $no_events_message = get_field( 'no_upcoming_events_message', 'post-settings' );
}
$show_no_events = ( $post_type === 'tribe_events' && empty( $posts_to_render ) && $no_events_message !== '' && $no_events_message !== false );
if ( $show_no_events ) {
  $classes[] = 'c-posts--no-events';
}
?>

<section id="<?= esc_attr($block_id); ?>" class="<?= esc_attr(implode(' ', $classes)); ?>  alignfull">
  <?php if ( ! $show_no_events && $has_bg ) :
    if ( $background === 'solid_color' && $show_accent_angle ) :
      $posts_grad_id = wp_unique_id( 'posts-grid-grad-' );
  ?>
  <div class="c-posts__stage" aria-hidden="true">
    <div class="c-posts__angle" aria-hidden="true">
      <svg class="c-posts__angleSvg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1438 420" preserveAspectRatio="none" focusable="false">
        <defs>
          <linearGradient id="<?php echo esc_attr( $posts_grad_id ); ?>" gradientUnits="userSpaceOnUse" x1="719" y1="0" x2="851" y2="502">
            <stop offset="0%" stop-color="#12273d"/>
            <stop offset="100%" stop-color="#507a73"/>
          </linearGradient>
        </defs>
        <path fill="url(#<?php echo esc_attr( $posts_grad_id ); ?>)" d="M0 100.5L1438 0V420H0V239.5Z"/>
      </svg>
    </div>
    <div class="c-posts__solid" aria-hidden="true">
      <svg class="c-posts__solidSvg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1438 1013" preserveAspectRatio="none" focusable="false">
        <path class="c-posts__solidPath c-posts__solidPath--desktop" d="M0 305.5L1438 0V1013H0V305.5Z"/>
        <path class="c-posts__solidPath c-posts__solidPath--mobile" d="M0 0H1438V1013H0V0Z"/>
      </svg>
    </div>
  </div>
  <?php elseif ( $background === 'image' || $background === 'solid_color' ) : ?>
  <div class="c-posts__bg" aria-hidden="true">
    <?php if ( $background === 'image' && $bg_url ) : ?>
      <div class="c-posts__bgImage" style="background-image:url('<?php echo $bg_url; ?>')"></div>
    <?php endif; ?>
    <div class="c-posts__overlay" aria-hidden="true"></div>
  </div>
  <?php endif; ?>
  <?php endif; ?>

  <!-- Foreground content -->
  <div class="c-posts__inner l-container wrap">

    <?php if (!empty($posts_to_render)): ?>
      <?php $grid_count = count($posts_to_render); $grid_class = ($grid_count <= 2) ? ' c-posts__grid--count-' . $grid_count : ''; ?>
      <div class="c-posts__grid<?= esc_attr($grid_class); ?>">
        <?php foreach ($posts_to_render as $p): ?>
          <?php if ( $p->post_type === 'post' && function_exists( 'client_render_blog_post_card' ) ) : ?>
            <?php client_render_blog_post_card( $p, array( 'show_cta' => true ) ); ?>
          <?php else : ?>
          <?php
            // Use explicit post IDs so we don't depend on global $post
            $post_id  = $p->ID;
            $date     = ( $p->post_type === 'tribe_events' && function_exists( 'tribe_get_start_date' ) )
              ? ( tribe_get_start_date( $post_id, false, 'm/d/y' ) ?: get_the_date( 'm/d/y', $post_id ) )
              : get_the_date( 'm/d/y', $post_id );
            $title    = get_the_title($post_id);
            // For events, allow external override URL; otherwise use the normal permalink.
            if ( $p->post_type === 'tribe_events' && function_exists( 'client_get_event_external_url' ) ) {
              $permalink = client_get_event_external_url( $post_id );
              if ( ! $permalink ) {
                $permalink = get_permalink( $post_id );
              }
            } else {
              $permalink = get_permalink( $post_id );
            }

            $img_url = get_the_post_thumbnail_url($post_id, 'post-card');

            // Default WP taxonomies (will be empty for CPTs that don't use them)
            $categories = get_the_category($post_id);
            $tags       = get_the_tags($post_id);

            // Build chip list: Categories first, then Tags
            $chips = [];

            if (!empty($categories) && !is_wp_error($categories)) {
              foreach ($categories as $cat) {
                $chips[] = $cat->name;
              }
            }

            if (!empty($tags) && !is_wp_error($tags)) {
              foreach ($tags as $tag) {
                $chips[] = $tag->name;
              }
            }

            // De-dupe + limit
            $chips = array_values(array_unique(array_filter($chips)));
            $chips = array_slice($chips, 0, 3);
          ?>

          <article class="c-postCard">
            <a class="c-postCard__link" href="<?= esc_url($permalink); ?>"<?php if ( $p->post_type === 'tribe_events' ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>>
              <div class="c-postCard__media">
                <?php if ($img_url): ?>
                  <img class="c-postCard__img" src="<?= esc_url($img_url); ?>" alt="" loading="lazy" />
                <?php endif; ?>
              </div>

              <div class="c-postCard__body">
                <h6 class="c-postCard__meta"><?= esc_html( $date ); ?></h6>
                <h3 class="c-postCard__title"><?= esc_html( $title ); ?></h3>
                <?php if ( ! empty( $chips ) ) : ?>
                  <div class="c-postCard__chips">
                    <?php foreach ( $chips as $chip ) : ?>
                      <span class="c-chip"><?= esc_html( $chip ); ?></span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
                <span class="c-postCard__cta">read more</span>
              </div>
            </a>
          </article>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <?php if ( $show_no_events && $no_events_message ) : ?>
        <div class="c-posts__no-events">
          <div class="c-posts__no-events-inner"><h4 class="c-posts__no-events-title"><?php echo wp_kses_post( $no_events_message ); ?></h4></div>
        </div>
      <?php else : ?>
        <p>No posts found.</p>
      <?php endif; ?>
    <?php endif; ?>
    <?php if ($has_more_posts): $partial_path = get_theme_file_path('/partials/button_pair.php'); include $partial_path; endif; ?>
  </div>
</section>