<?php
/**
 * Initiative hero (logo or text headline, optional gradient overlay).
 *
 * @var array $d Hero data from `client_get_hero_config()['data']`.
 */
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
