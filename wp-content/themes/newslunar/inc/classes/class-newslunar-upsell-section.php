<?php
/**
 * NewsLunar Pro — Upsell Control
 *
 * A minimal WP_Customize_Control that renders a feature list and action
 * buttons inside a standard Customizer section.
 *
 * @package NewsLunar
 */

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'NewsLunar_Upsell_Control' ) ) :

	/**
	 * Class NewsLunar_Upsell_Control
	 */
	class NewsLunar_Upsell_Control extends WP_Customize_Control {

		/** @var string Control type. */
		public $type = 'newslunar-upsell';

		/** @var string[] List of premium feature strings. */
		public $features = array();

		/** @var string URL for the "Upgrade to Pro" button. */
		public $pro_url = '';

		/** @var string URL for the "Help Center" button. */
		public $help_url = '';

		/**
		 * Render the control content.
		 */
		public function render_content() {
			?>
			<div class="newslunar-upsell-wrap">

				<?php if ( ! empty( $this->features ) ) : ?>
					<ul class="newslunar-features-list">
						<?php foreach ( $this->features as $feature ) : ?>
							<li class="newslunar-feature-item">
								<span class="dashicons dashicons-yes-alt"></span>
								<?php echo esc_html( $feature ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<div class="newslunar-upsell-actions">
					<?php if ( $this->pro_url ) : ?>
						<a href="<?php echo esc_url( $this->pro_url ); ?>" target="_blank" rel="noopener noreferrer" class="button newslunar-btn-pro">
							<?php esc_html_e( 'Upgrade to Pro', 'newslunar' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $this->help_url ) : ?>
						<a href="<?php echo esc_url( $this->help_url ); ?>" target="_blank" rel="noopener noreferrer" class="button newslunar-btn-help">
							<?php esc_html_e( 'Help Center', 'newslunar' ); ?>
						</a>
					<?php endif; ?>
				</div>

			</div>
			<?php
		}
	}

endif;
