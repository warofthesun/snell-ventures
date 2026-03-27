<?php
/**
 * Legacy post hero layout (split text + image).
 *
 * @var array $d Hero data from `client_get_hero_config()['data']`.
 */
?>
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
