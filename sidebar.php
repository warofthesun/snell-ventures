<?php
$client_sidebar_classes = ! empty( $GLOBALS['client_sidebar_wrapper_class'] )
	? $GLOBALS['client_sidebar_wrapper_class']
	: 'sidebar col-xs-12 col-sm-4';
unset( $GLOBALS['client_sidebar_wrapper_class'] );
?>
				<div id="sidebar1" class="<?php echo esc_attr( $client_sidebar_classes ); ?>" role="complementary">

					<?php if ( is_active_sidebar( 'sidebar1' ) ) : ?>

						<?php dynamic_sidebar( 'sidebar1' ); ?>

					<?php else : ?>

						<?php
							/*
							 * This content shows up if there are no widgets defined in the backend.
							*/
						?>

						<div class="no-widgets">
							<p><?php _e( 'This is a widget ready area. Add some and they will appear here.', 'client_theme' );  ?></p>
						</div>

					<?php endif; ?>

				</div>
