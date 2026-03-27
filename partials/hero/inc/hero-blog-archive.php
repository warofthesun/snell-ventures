<?php
/**
 * Blog index or taxonomy/archive hero.
 *
 * @var string $hero_type Either `blog` or `archive`.
 * @var array  $d         Hero data from `client_get_hero_config()['data']`.
 */
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
