<?php
/**
 * Reusable hero partial. Uses client_get_hero_config() for context (page, post, blog, archive).
 * Editor choice on pages via ACF "Hero Style"; automatic type on single post, blog index, archive.
 * Hero Medium is used for the Medium style on pages and for the Events archive (same markup/CSS).
 */
$hero = isset( $hero_config ) ? $hero_config : client_get_hero_config();
if ( empty( $hero['show'] ) ) {
  return;
}
$hero_type = isset( $hero['type'] ) ? $hero['type'] : 'landing';
$d = isset( $hero['data'] ) ? $hero['data'] : array();
?>
<!-- Hero (<?php echo esc_attr( $hero_type ); ?>) -->

<?php if ( $hero_type === 'single' ) : ?>
  <?php
  $d_single  = isset( $d['has_image'] ) ? $d : array_merge( $d, array( 'has_image' => ! empty( $d['image_id'] ) ) );
  $has_img   = ! empty( $d_single['has_image'] );
  $bg_style  = '';
  if ( $has_img && ! empty( $d_single['image_id'] ) ) {
    $img = wp_get_attachment_image_src( (int) $d_single['image_id'], 'hero-bg' );
    if ( $img ) {
      $bg_style = 'background-image: url(' . esc_url( $img[0] ) . ');';
    }
  } else {
    $bg_style = 'background-color: #A1B152;';
  }
  $no_img_mod = $has_img ? '' : ' hero__container--single-no-image';
  ?>
  <div class="hero__container hero__container--single<?php echo esc_attr( $no_img_mod ); ?>">
    <div class="hero__container--single-inner">
      <div class="hero__content hero__content--single-bg" style="<?php echo esc_attr( $bg_style ); ?>"></div>
      <div class="hero__content hero__content--single">
        <div class="hero__headline hero__headline--single">
          <h1 class="hero__title hero__title--single entry-title single-title" itemprop="headline"><?php echo esc_html( $d_single['title'] ); ?></h1>
          <?php if ( ! empty( $d_single['author_link'] ) || ! empty( $d_single['date'] ) ) : ?>
            <p class="hero__meta hero__meta--single byline entry-meta vcard">
              <?php if ( ! empty( $d_single['author_link'] ) ) : ?>
                <span class="hero__meta-item hero__meta-item--author">
                  <i class="fa-regular fa-circle-user hero__meta-icon" aria-hidden="true"></i>
                  <span class="hero__meta-author"><?php echo $d_single['author_link']; ?></span>
                </span>
              <?php endif; ?>
              <?php if ( ! empty( $d_single['author_link'] ) && ! empty( $d_single['date'] ) ) : ?><span class="hero__meta-gap" aria-hidden="true"></span><?php endif; ?>
              <?php if ( ! empty( $d_single['date'] ) ) : ?>
                <span class="hero__meta-item hero__meta-item--date">
                  <i class="fa-regular fa-circle-calendar hero__meta-icon" aria-hidden="true"></i>
                  <time class="updated entry-time" datetime="<?php echo esc_attr( isset( $d_single['date_iso'] ) ? $d_single['date_iso'] : '' ); ?>" itemprop="datePublished"><?php echo esc_html( $d_single['date'] ); ?></time>
                </span>
              <?php endif; ?>
            </p>
          <?php endif; ?>
          <?php if ( ! empty( $d_single['category_terms'] ) && ! is_wp_error( $d_single['category_terms'] ) ) : ?>
            <div class="hero__chips hero__chips--single">
              <?php foreach ( $d_single['category_terms'] as $term ) : ?>
                <span class="hero__chip hero__chip--single"><?php echo esc_html( $term->name ); ?></span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

<?php elseif ( $hero_type === 'post' ) : ?>
  <div class="hero__container hero__container--post">
    <div class="hero__post row wrap">
      <div class="hero__post-text col-xs-12 col-md-6">
        <?php if ( ! empty( $d['categories'] ) ) : ?>
          <p class="post-category"><?php echo $d['categories']; ?></p>
        <?php endif; ?>
        <h1 class="entry-title single-title" itemprop="headline" rel="bookmark"><?php echo esc_html( $d['title'] ); ?></h1>
        <p class="byline entry-meta vcard">
          <?php
          $datetime = isset( $d['date_iso'] ) ? $d['date_iso'] : '';
          printf(
            __( 'Posted', 'client_theme' ) . ' %1$s %2$s',
            '<time class="updated entry-time" datetime="' . esc_attr( $datetime ) . '" itemprop="datePublished">' . esc_html( $d['date'] ) . '</time>',
            '<span class="by">' . __( 'by', 'client_theme' ) . '</span> <span class="entry-author author" itemprop="author">' . $d['author_link'] . '</span>'
          );
          ?>
        </p>
      </div>
      <div class="hero__post-image col-xs-12 col-md-6">
        <div class="hero--image">
          <?php
          if ( ! empty( $d['image_id'] ) ) {
            echo wp_get_attachment_image( (int) $d['image_id'], 'gallery-image' );
          }
          ?>
        </div>
      </div>
    </div>
  </div>

<?php elseif ( $hero_type === 'blog' || $hero_type === 'archive' ) : ?>
  <?php
  $bg_url = '';
  if ( ! empty( $d['image_id'] ) ) {
    $img = wp_get_attachment_image_src( (int) $d['image_id'], 'hero-bg' );
    $bg_url = $img ? $img[0] : '';
  }
  ?>
  <div class="hero__container hero__container--<?php echo esc_attr( $hero_type ); ?>">
    <div class="hero__container--inner">
      <?php if ( $bg_url ) : ?>
        <div class="hero__content hero__content--image col-xs-12" style="background-image: url('<?php echo esc_url( $bg_url ); ?>');"></div>
      <?php endif; ?>
      <div class="hero__content hero__content--text col-xs-12">
        <h1 class="hero__title page-title"><?php echo $d['title']; ?></h1>
        <?php if ( $hero_type === 'archive' && ! empty( $d['description'] ) ) : ?>
          <div class="hero__description taxonomy-description"><?php echo $d['description']; ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

<?php elseif ( $hero_type === 'medium' ) : ?>
  <?php
  // Hero Medium: image or solid color background; no logo. (Events archive uses same markup.)
  $medium_bg_style = '';
  if ( isset( $d['background_type'] ) && $d['background_type'] === 'color' && ! empty( $d['background_color'] ) ) {
    $medium_bg_style = 'background-color: ' . esc_attr( $d['background_color'] ) . ';';
  } elseif ( ! empty( $d['background_image'] ) ) {
    $img = wp_get_attachment_image_src( (int) $d['background_image'], 'hero-bg' );
    if ( $img ) {
      $medium_bg_style = 'background-image: url(' . esc_url( $img[0] ) . ');';
    }
  }
  ?>
  <?php
  $is_small            = isset( $d['size'] ) && $d['size'] === 'small';
  $is_events_archive   = is_post_type_archive( 'tribe_events' ) && ! is_singular( 'tribe_events' );
  // Full-height medium uses --medium; Hero Small on pages uses --small only for headline/title.
  $use_medium_text_classes = ! $is_small || $is_events_archive;
  $text_mode               = isset( $d['text_color'] ) ? (string) $d['text_color'] : '';
  $text_dark               = $text_mode === 'dark';
  ?>
  <div
    class="<?php echo $is_small ? 'hero__container hero__container--small' : 'hero__container hero__container--medium'; ?>">
    <div class="hero__container--inner">
      <?php if ( $medium_bg_style ) : ?>
        <div class="hero__content hero__content--bg col-xs-12" style="<?php echo $medium_bg_style; ?>"></div>
      <?php endif; ?>

      <div class="hero__content hero__content--text<?php echo $is_small ? ' hero__content--small' : ' hero__content--medium'; ?> col-xs-12">
        <div class="hero__headline <?php echo $use_medium_text_classes ? 'hero__headline--medium' : 'hero__headline--small'; ?>">
          <?php if ( ! empty( $d['headline_text'] ) ) : ?>
            <h1 class="hero__title <?php echo $use_medium_text_classes ? 'hero__title--medium' : 'hero__title--small'; ?><?php echo $text_dark ? ' dark' : ''; ?>"><?php echo esc_html( $d['headline_text'] ); ?></h1>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

<?php elseif ( $hero_type === 'initiative' ) : ?>
  <?php
  $init_bg_style = '';
  if ( isset( $d['background_type'] ) && $d['background_type'] === 'color' && ! empty( $d['background_color'] ) ) {
    $init_bg_style = 'background-color: ' . esc_attr( $d['background_color'] ) . ';';
  } elseif ( ! empty( $d['background_image'] ) ) {
    $img = wp_get_attachment_image_src( (int) $d['background_image'], 'hero-bg' );
    if ( $img ) {
      $init_bg_style = 'background-image: url(' . esc_url( $img[0] ) . ');';
    }
  }
  $gradient_overlay = ! empty( $d['gradient_overlay'] );
  ?>
  <div class="hero__container hero__container--initiative<?php echo $gradient_overlay ? ' hero__container--gradient-overlay' : ''; ?>">
    <div class="hero__container--inner">
      <?php if ( $init_bg_style ) : ?>
        <div class="hero__content hero__content--bg col-xs-12" style="<?php echo $init_bg_style; ?>"></div>
      <?php endif; ?>

      <div class="hero__content hero__content--text hero__content--initiative col-xs-12">
        <div class="hero__headline hero__headline--initiative">
          <?php if ( ! empty( $d['headline_type'] ) && $d['headline_type'] === 'logo' && ! empty( $d['logo_id'] ) ) : ?>
            <?php echo wp_get_attachment_image( (int) $d['logo_id'], 'large', false, array( 'class' => 'hero__logo' ) ); ?>
          <?php else : ?>
            <?php
            $ht = isset( $d['headline_text'] ) ? $d['headline_text'] : '';
            if ( is_array( $ht ) ) {
              $ht = isset( $ht['field_68225159eee20'] ) ? $ht['field_68225159eee20'] : ( isset( $ht['hero_headline'] ) ? $ht['hero_headline'] : '' );
            }
            $ht = is_string( $ht ) ? trim( $ht ) : '';
            if ( $ht === '' ) {
              $ht = get_the_title();
            }
            ?>
            <h1 class="hero__title hero__title--initiative"><?php echo wp_kses_post( $ht ); ?></h1>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

<?php else : ?>
  <?php
  // Landing: background from hero config (featured image + optional hero_image fallback); copy + CTAs from Hero Options (ACF group).
  $bg_url = '';
  if ( ! empty( $d['image_id'] ) ) {
    $img = wp_get_attachment_image_src( (int) $d['image_id'], 'hero-bg' );
    $bg_url = $img ? $img[0] : '';
  }
  $landing_internal = ! is_front_page();

  $lpho = array();
  if ( function_exists( 'get_field' ) ) {
    $hero_page = get_queried_object();
    $hero_pid  = ( $hero_page && isset( $hero_page->ID ) ) ? (int) $hero_page->ID : 0;
    if ( $hero_pid > 0 ) {
      $g = get_field( 'landing_page_hero_options', $hero_pid );
      if ( is_array( $g ) ) {
        $lpho = $g;
      }
    }
  }

  $preheader_raw = isset( $lpho['hero_preheader'] ) ? $lpho['hero_preheader'] : '';
  $preheader_raw = is_string( $preheader_raw ) ? trim( $preheader_raw ) : '';

  $landing_headline = '';
  if ( isset( $lpho['hero_headline'] ) && $lpho['hero_headline'] !== '' && $lpho['hero_headline'] !== null ) {
    $landing_headline = $lpho['hero_headline'];
  } elseif ( isset( $d['headline'] ) ) {
    $landing_headline = $d['headline'];
  }
  if ( is_array( $landing_headline ) ) {
    $landing_headline = isset( $landing_headline['field_68225159eee20'] ) ? $landing_headline['field_68225159eee20'] : ( isset( $landing_headline['hero_headline'] ) ? $landing_headline['hero_headline'] : '' );
  }
  $landing_headline = is_string( $landing_headline ) ? trim( $landing_headline ) : '';
  if ( $landing_headline === '' ) {
    $landing_headline = get_the_title();
  }
  $landing_headline = preg_replace( '/<\/?p[^>]*>/i', '', $landing_headline );
  $landing_headline_html = wp_kses_post( $landing_headline );

  $ctas = array();
  if ( ! empty( $lpho['hero_cta'] ) && is_array( $lpho['hero_cta'] ) ) {
    foreach ( $lpho['hero_cta'] as $row ) {
      if ( ! is_array( $row ) ) {
        continue;
      }
      $link = isset( $row['hero_cta_button'] ) ? $row['hero_cta_button'] : null;
      if ( is_array( $link ) && ! empty( $link['url'] ) ) {
        $t = isset( $link['target'] ) ? (string) $link['target'] : '_self';
        if ( $t === '' ) {
          $t = '_self';
        }
        $ctas[] = array(
          'url'    => $link['url'],
          'title'  => isset( $link['title'] ) ? $link['title'] : '',
          'target' => $t,
        );
      }
    }
  }
  if ( empty( $ctas ) && ! empty( $d['ctas'] ) && is_array( $d['ctas'] ) ) {
    $ctas = $d['ctas'];
  }
  $ctas = array_slice( $ctas, 0, 2 );

  $bg_attr = '';
  if ( $bg_url !== '' ) {
    $bg_attr = 'background-image: url(' . esc_url( $bg_url ) . ');';
  }
  ?>
  <div class="hero__container hero__container--landing<?php echo $landing_internal ? ' hero__container--landing-internal' : ''; ?>">
    <div class="hero__container--inner">
      <div class="hero__media">
        <div class="hero__content hero__content--landing-bg"<?php echo $bg_attr !== '' ? ' style="' . esc_attr( $bg_attr ) . '"' : ''; ?>></div>
        <div class="hero__overlay" aria-hidden="true"></div>
      </div>
      <div class="hero__content hero__content--landing">
        <div class="hero__text-block hero__text-block--landing">
          <?php if ( $preheader_raw !== '' ) : ?>
            <h2 class="hero"><?php echo wp_kses_post( $preheader_raw ); ?></h2>
          <?php endif; ?>
          <h1 class="hero"><?php echo $landing_headline_html; ?></h1>
          <?php if ( ! empty( $ctas ) ) : ?>
            <div class="hero__actions c-button-pair c-button-pair--primary">
              <?php foreach ( $ctas as $i => $cta ) : ?>
                <?php
                $btn_class  = ( $i === 0 ) ? 'c-button-pair__button c-button-pair__button--solid' : 'c-button-pair__button c-button-pair__button--outline';
                $cta_target = isset( $cta['target'] ) ? (string) $cta['target'] : '_self';
                if ( $cta_target !== '_blank' && $cta_target !== '_self' ) {
                  $cta_target = '_self';
                }
                $cta_rel = ( $cta_target === '_blank' ) ? ' rel="noopener noreferrer"' : '';
                ?>
                <a class="<?php echo esc_attr( $btn_class ); ?>" href="<?php echo esc_url( $cta['url'] ); ?>" target="<?php echo esc_attr( $cta_target ); ?>"<?php echo $cta_rel; ?>><?php echo esc_html( isset( $cta['title'] ) ? $cta['title'] : '' ); ?></a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php
    // ACF (acf-json/group_68260ada5ea86.json): Landing Page Hero Options — Include Page Intro Block, Page Intro Header, Page Intro.
    $include_page_intro = ! empty( $lpho['include_page_intro_block'] );
    $page_intro_header  = isset( $lpho['page_intro_header'] ) ? $lpho['page_intro_header'] : '';
    $page_intro_header  = is_string( $page_intro_header ) ? trim( $page_intro_header ) : '';
    $page_intro_body    = isset( $lpho['page_intro'] ) ? $lpho['page_intro'] : '';
    $page_intro_body    = is_string( $page_intro_body ) ? trim( $page_intro_body ) : '';
    $show_page_intro    = $include_page_intro && ( $page_intro_header !== '' || $page_intro_body !== '' );
    if ( $show_page_intro ) :
      ?>
    <div class="hero__page-intro">
      <?php if ( $page_intro_header !== '' ) : ?>
        <h4 class="intro__heading"><?php echo wp_kses_post( $page_intro_header ); ?></h4>
      <?php endif; ?>
      <?php if ( $page_intro_body !== '' ) : ?>
        <div class="hero__page-intro__body"><?php echo wp_kses_post( $page_intro_body ); ?></div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
