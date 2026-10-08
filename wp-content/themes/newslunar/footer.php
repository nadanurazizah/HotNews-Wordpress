		</main><!-- #site-content -->

		<footer id="colophon" class="site-footer">
			<?php
			$footer_widgets = get_theme_mod( 'newslunar_footer_widgets', 4 );
			
			// Check if any of the active footer areas have widgets.
			$has_active_widgets = false;
			for ( $i = 1; $i <= $footer_widgets; $i++ ) {
				if ( is_active_sidebar( 'footer-' . $i ) ) {
					$has_active_widgets = true;
					break;
				}
			}
			
			if ( $has_active_widgets ) :
			?>
				<div class="footer-widgets">
					<div class="section-inner">
						<div class="footer-widgets-grid footer-widgets-columns-<?php echo esc_attr( $footer_widgets ); ?>">
							<?php for ( $i = 1; $i <= $footer_widgets; $i++ ) : ?>
								<div class="footer-widget-column widget-area">
									<?php dynamic_sidebar( 'footer-' . $i ); ?>
								</div>
							<?php endfor; ?>
						</div><!-- .footer-widgets-grid -->
					</div><!-- .section-inner -->
				</div><!-- .footer-widgets -->
			<?php endif; ?>

			<div class="credits">
				<div class="section-inner">

					<?php if ( get_theme_mod( 'newslunar_back_to_top', true ) ) : ?>
						<a href="#" class="to-the-top">
							<i class="bi bi-chevron-compact-up"></i>
							<span class="screen-reader-text"><?php _e( 'To the top', 'newslunar' ); ?></span>
						</a>
					<?php endif; ?>

                    <p class="copyright">&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo wp_kses_post( get_bloginfo( 'title' ) ); ?></a></p>

                    <p class="attribution"><?php printf( __( 'Theme by %s', 'newslunar' ), '<a href="https://unitedtheme.com/">UnitedTheme</a>' ); ?></p>

                </div><!-- .section-inner -->
            </div>
		</footer><!-- .credits -->

		<?php wp_footer(); ?>

	</body>
	
</html>