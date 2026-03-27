<?php
/**
 * Single post / CPT hero (not pages).
 *
 * @var array $d Hero data from `client_get_hero_config()['data']`.
 */
$d_single = isset( $d['has_image'] ) ? $d : array_merge( $d, array( 'has_image' => ! empty( $d['image_id'] ) ) );
$has_img  = ! empty( $d_single['has_image'] );
$bg_style = '';
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
