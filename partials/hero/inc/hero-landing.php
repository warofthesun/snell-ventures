<?php
/**
 * Home / landing page hero + optional Page Intro (ACF `landing_page_hero_options`).
 *
 * @var array $d Hero data from `client_get_hero_config()['data']`.
 */
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
$landing_headline      = preg_replace( '/<\/?p[^>]*>/i', '', $landing_headline );
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
	// ACF (acf-json/group_68260ada5ea86.json): Include Page Intro Block, Page Intro Header, Page Intro.
	$include_page_intro = ! empty( $lpho['include_page_intro_block'] );
	$page_intro_header    = isset( $lpho['page_intro_header'] ) ? $lpho['page_intro_header'] : '';
	$page_intro_header    = is_string( $page_intro_header ) ? trim( $page_intro_header ) : '';
	$page_intro_body      = isset( $lpho['page_intro'] ) ? $lpho['page_intro'] : '';
	$page_intro_body      = is_string( $page_intro_body ) ? trim( $page_intro_body ) : '';
	$show_page_intro      = $include_page_intro && ( $page_intro_header !== '' || $page_intro_body !== '' );
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
