<?php
/**
 * Company Grid (ACF Block)
 *
 * Figma Card/Portfolio Company (234:619): featured image, alternating gradient overlay,
 * logo, tagline, and CTA linking to the company single.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'company-grid-' . $block['id'];
$classes  = array( 'c-companyGrid' );
if ( ! empty( $block['className'] ) ) {
	$classes[] = $block['className'];
}

$companies = function_exists( 'get_field' ) ? get_field( 'companies' ) : array();
if ( ! is_array( $companies ) ) {
	$companies = array();
}

$company_ids = array();
foreach ( $companies as $company ) {
	$cid = is_object( $company ) ? (int) $company->ID : (int) $company;
	if ( $cid ) {
		$company_ids[] = $cid;
	}
}

$columns = function_exists( 'get_field' ) ? get_field( 'columns' ) : '2';
if ( '3' !== $columns ) {
	$columns = '2';
}

$classes[] = 'c-companyGrid--cols-' . $columns;

$cols_int = (int) $columns;

/**
 * Resolve an ACF image field to attachment ID.
 *
 * @param mixed $image ACF image value.
 * @return int
 */
$client_resolve_acf_image_id = static function ( $image ) {
	if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
		return (int) $image['ID'];
	}
	if ( is_numeric( $image ) ) {
		return (int) $image;
	}
	return 0;
};

/**
 * Resolve alt text from an ACF image field.
 *
 * @param mixed  $image        ACF image value.
 * @param string $fallback_alt Fallback alt string.
 * @return string
 */
$client_resolve_acf_image_alt = static function ( $image, $fallback_alt ) {
	if ( is_array( $image ) && ! empty( $image['alt'] ) ) {
		return (string) $image['alt'];
	}
	return $fallback_alt;
};

/**
 * Pick grid card logo ID and alt text (dark-background logo preferred).
 *
 * @param int    $company_id Company post ID.
 * @param string $name       Company title for alt fallback.
 * @return array{ id: int, alt: string }
 */
$client_company_grid_logo = static function ( $company_id, $name ) use ( $client_resolve_acf_image_id, $client_resolve_acf_image_alt ) {
	$result = array(
		'id'  => 0,
		'alt' => $name,
	);

	if ( ! function_exists( 'get_field' ) ) {
		return $result;
	}

	$logo         = get_field( 'company_logo', $company_id );
	$logo_on_dark = get_field( 'company_logo_on_dark', $company_id );
	$logo_id      = $client_resolve_acf_image_id( $logo );
	$logo_dark_id = $client_resolve_acf_image_id( $logo_on_dark );

	if ( $logo_dark_id ) {
		$result['id']  = $logo_dark_id;
		$result['alt'] = $client_resolve_acf_image_alt( $logo_on_dark, $name );
		return $result;
	}

	if ( $logo_id ) {
		$result['id']  = $logo_id;
		$result['alt'] = $client_resolve_acf_image_alt( $logo, $name );
	}

	return $result;
};
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php if ( ! empty( $company_ids ) ) : ?>
		<ul class="c-companyGrid__items">
			<?php foreach ( $company_ids as $index => $company_id ) : ?>
				<?php
				$name     = get_the_title( $company_id );
				$permalink = get_permalink( $company_id );

				$tagline = '';
				if ( function_exists( 'get_field' ) ) {
					$t = get_field( 'company_tagline', $company_id );
					if ( is_string( $t ) ) {
						$tagline = trim( $t );
					}
				}

				$grid_logo = $client_company_grid_logo( $company_id, $name );
				$logo_id   = $grid_logo['id'];
				$logo_alt  = $grid_logo['alt'];

				$row = (int) floor( $index / $cols_int );
				$col = $index % $cols_int;
				$overlay_class = ( ( $row + $col ) % 2 === 0 )
					? 'c-companyCard--overlay-primary'
					: 'c-companyCard--overlay-secondary';

				$card_classes = array( 'c-companyCard', $overlay_class );
				?>
				<li class="c-companyGrid__item">
					<article class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>">
						<div class="c-companyCard__media" aria-hidden="true">
							<?php if ( has_post_thumbnail( $company_id ) ) : ?>
								<div class="c-companyCard__bg">
									<?php
									echo get_the_post_thumbnail(
										$company_id,
										'hero-bg',
										array(
											'class' => 'c-companyCard__bgImg',
											'alt'   => '',
										)
									);
									?>
								</div>
							<?php endif; ?>
							<div class="c-companyCard__overlay"></div>
						</div>

						<div class="c-companyCard__inner">
							<?php if ( $logo_id > 0 ) : ?>
								<div class="c-companyCard__logo">
									<?php
									echo wp_get_attachment_image(
										$logo_id,
										'medium',
										false,
										array(
											'class' => 'c-companyCard__logoImg',
											'alt'   => $logo_alt,
										)
									);
									?>
								</div>
							<?php endif; ?>

							<?php if ( $tagline !== '' ) : ?>
								<p class="c-companyCard__tagline h3 medium"><?php echo esc_html( $tagline ); ?></p>
							<?php endif; ?>

							<?php if ( $permalink ) : ?>
								<a href="<?php echo esc_url( $permalink ); ?>" class="c-companyCard__cta">
									<?php esc_html_e( 'Read Their Story', 'client_theme' ); ?>
								</a>
							<?php endif; ?>
						</div>
					</article>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<div class="block-placeholder" style="border: 1px dashed #cfd3d7; padding: 1rem; border-radius: .5rem; background: rgba(255,255,255,.8);">
			<p><?php esc_html_e( 'Select companies to display in this grid.', 'client_theme' ); ?></p>
		</div>
	<?php endif; ?>
</section>
