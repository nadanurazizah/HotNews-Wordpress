<?php
/**
 * NewsLunar Homepage Sections Manager
 * 
 * Manages modular, draggable, and repeatable homepage sections
 * 
 * @package NewsLunar
 * @since 1.0.0
 */

if ( ! class_exists( 'NewsLunar_Homepage_Sections' ) ) :

class NewsLunar_Homepage_Sections {

	/**
	 * Available section types and their configurations
	 */
	private static $section_types = array(
		'main_banner' => array(
			'label'       => 'Main Banner',
			'description' => 'Hero section with slider, metro, and list panels',
			'icon'        => 'dashicons-slides',
			'repeatable'  => true,
			'has_category' => false, // We'll handle categories per panel
			'has_count'    => false, // We'll handle counts per panel
			'has_offset'   => false, // We'll handle offsets per panel
			'has_panels'   => true,  // Custom flag for panel configuration
		),
		'popular_news' => array(
			'label'       => 'Popular News Articles',
			'description' => 'Display popular or trending posts',
			'icon'        => 'dashicons-star-filled',
			'repeatable'  => true,
			'has_category' => true,
			'has_count'    => true,
			'has_offset'   => true,
		),
		'full_width_widget' => array(
			'label'       => 'Homepage Full-Width Widget Area',
			'description' => 'Widget area with layout options',
			'icon'        => 'dashicons-admin-customizer',
			'repeatable'  => true,
			'has_category' => false,
			'has_count'    => false,
			'has_offset'   => false,
			'has_layout_options' => true,
		),
		'latest_posts' => array(
			'label'       => 'Latest Posts',
			'description' => 'Recent posts with sidebar (non-repeatable)',
			'icon'        => 'dashicons-admin-post',
			'repeatable'  => false,
			'has_category' => false,
			'has_count'    => false,
			'has_offset'   => false,
		),
		'must_read' => array(
			'label'       => 'Must Read',
			'description' => 'Essential reading section',
			'icon'        => 'dashicons-book',
			'repeatable'  => true,
			'has_category' => true,
			'has_count'    => true,
			'has_offset'   => true,
		),
		'in_case_missed' => array(
			'label'       => 'In Case You Missed',
			'description' => 'Catch-up section for older posts',
			'icon'        => 'dashicons-backup',
			'repeatable'  => true,
			'has_category' => true,
			'has_count'    => true,
			'has_offset'   => true,
		),
		'trending_topics' => array(
			'label'       => 'Trending Topics',
			'description' => 'Display multiple categories with custom images',
			'icon'        => 'dashicons-chart-line',
			'repeatable'  => true,
			'has_category' => false,
			'has_count'    => false,
			'has_offset'   => false,
			'has_trending_config' => true, // Custom configuration for trending topics
		),
		'cta_section' => array(
			'label'       => 'CTA Section',
			'description' => 'Call-to-action with image/video, title, description, and button',
			'icon'        => 'dashicons-megaphone',
			'repeatable'  => true,
			'has_category' => false,
			'has_count'    => false,
			'has_offset'   => false,
			'has_cta_fields' => true,
		),
		'marquee_strip' => array(
			'label'       => 'Marquee Strip',
			'description' => 'Scrolling news ticker with featured image, title, and date',
			'icon'        => 'dashicons-media-text',
			'repeatable'  => true,
			'has_category' => true,
			'has_count'    => true,
			'has_offset'   => false,
			'has_marquee_config' => true,
		),
	);

	/**
	 * Initialize the class
	 */
	public static function init() {
		add_action( 'customize_register', array( __CLASS__, 'register_customizer_settings' ) );
		add_action( 'customize_controls_enqueue_scripts', array( __CLASS__, 'enqueue_customizer_scripts' ) );
		add_action( 'widgets_init', array( __CLASS__, 'register_widget_areas' ) );
	}

	/**
	 * Get available section types
	 */
	public static function get_section_types() {
		return apply_filters( 'newslunar_homepage_section_types', self::$section_types );
	}

	/**
	 * Get section type configuration
	 */
	public static function get_section_type( $type ) {
		$types = self::get_section_types();
		return isset( $types[ $type ] ) ? $types[ $type ] : null;
	}

	/**
	 * Register widget areas for homepage sections
	 */
	public static function register_widget_areas() {
		// Get sections configuration
		$sections = self::get_sections_config();
		
		$widget_counter = 1;
		
		foreach ( $sections as $index => $section ) {
			if ( $section['type'] === 'full_width_widget' ) {
				// Use section's unique ID instead of index to prevent widget loss on reorder/delete
				$section_id = isset( $section['id'] ) ? $section['id'] : 'section-' . $index;
				
				// Use section title for widget area name if available
				$base_title = ! empty( $section['title'] ) ? 
					$section['title'] : 
					sprintf( __( 'Homepage Widget Area %d', 'newslunar' ), $widget_counter );
				
				// Get layout type (default to 'default' if not set)
				$layout_type = ! empty( $section['layout_type'] ) ? $section['layout_type'] : 'default';
				
				if ( $layout_type === 'default' ) {
					// Single full-width widget area
					register_sidebar( array(
						'name'          => $base_title,
						'id'            => 'homepage-section-widget-' . $section_id,
						'description'   => __( 'Full-width widget area for homepage section.', 'newslunar' ),
						'before_title'  => '<h3 class="widget-title">',
						'after_title'   => '</h3>',
						'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
						'after_widget'  => '</div></div>',
					) );
				} else {
					// Main area + Sidebar layout - register two widget areas
					register_sidebar( array(
						'name'          => $base_title . ' Main Area',
						'id'            => 'homepage-section-widget-' . $section_id . '-main',
						'description'   => sprintf( __( 'Main area widget for "%s" section.', 'newslunar' ), $base_title ),
						'before_title'  => '<h3 class="widget-title">',
						'after_title'   => '</h3>',
						'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
						'after_widget'  => '</div></div>',
					) );
					
					register_sidebar( array(
						'name'          => $base_title . ' Sidebar',
						'id'            => 'homepage-section-widget-' . $section_id . '-sidebar',
						'description'   => sprintf( __( 'Sidebar widget for "%s" section.', 'newslunar' ), $base_title ),
						'before_title'  => '<h3 class="widget-title">',
						'after_title'   => '</h3>',
						'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
						'after_widget'  => '</div></div>',
					) );
				}
				
				$widget_counter++;
			}
		}
	}

	/**
	 * Register customizer settings
	 */
	public static function register_customizer_settings( $wp_customize ) {
		
		// Add Homepage Sections Panel
		// Check if homepage is properly configured with the template
		$page_on_front = get_option( 'page_on_front' );
		$has_homepage_template = false;
		
		if ( $page_on_front ) {
			$page_template = get_post_meta( $page_on_front, '_wp_page_template', true );
			if ( $page_template === 'homepage-sections.php' ) {
				$has_homepage_template = true;
			}
		}
		
		// Build description with conditional info box
		$description = '';
		if ( ! $has_homepage_template ) {
			$description .= '<div style="background:#d1ecf1;border:1px solid #bee5eb;border-radius:4px;padding:12px;margin-bottom:15px;color:#0c5460;"><strong>Setup Required:</strong> Create a new page, assign the "Homepage with Sections" template, then set it as your homepage in Settings → Reading.</div>';
		}
		$description .= __( 'Drag and drop to reorder sections. Add, remove, or configure homepage sections.', 'newslunar' );
		
		$wp_customize->add_section( 'newslunar_homepage_sections', array(
			'title'       => __( 'Homepage Sections', 'newslunar' ),
			'priority'    => 25,
			'capability'  => 'edit_theme_options',
			'description' => $description,
		) );

		// Main setting to store sections configuration
		$wp_customize->add_setting( 'newslunar_homepage_sections_config', array(
			'default'           => self::get_default_sections(),
			'type'              => 'theme_mod',
			'sanitize_callback' => array( __CLASS__, 'sanitize_sections_config' ),
			'transport'         => 'refresh',
		) );

		// Add custom control for sections manager
		$wp_customize->add_control( new NewsLunar_Homepage_Sections_Control(
			$wp_customize,
			'newslunar_homepage_sections_config',
			array(
				'label'       => __( 'Homepage Sections', 'newslunar' ),
				'section'     => 'newslunar_homepage_sections',
				'settings'    => 'newslunar_homepage_sections_config',
				'section_types' => self::get_section_types(),
			)
		) );
	}

	/**
	 * Get default sections configuration
	 */
	public static function get_default_sections() {
		return json_encode( array(
			array(
				'id'       => 'section_' . uniqid(),
				'type'     => 'main_banner',
				'enabled'  => true,
				'title'    => 'Main Banner',
				'category' => 0,
				'count'    => 5,
				'offset'   => 0,
			),
			array(
				'id'       => 'section_' . uniqid(),
				'type'     => 'popular_news',
				'enabled'  => true,
				'title'    => 'Popular News',
				'category' => 0,
				'count'    => 4,
				'offset'   => 0,
			),
			array(
				'id'       => 'section_' . uniqid(),
				'type'     => 'latest_posts',
				'enabled'  => true,
				'title'    => 'Latest Posts',
			),
		) );
	}

	/**
	 * Sanitize sections configuration
	 */
	public static function sanitize_sections_config( $value ) {
		if ( empty( $value ) ) {
			return self::get_default_sections();
		}

		// If it's already a string, decode it
		$sections = is_string( $value ) ? json_decode( $value, true ) : $value;
		
		if ( ! is_array( $sections ) ) {
			return self::get_default_sections();
		}

		$sanitized = array();
		$section_types = self::get_section_types();

		foreach ( $sections as $section ) {
			if ( ! isset( $section['type'] ) || ! isset( $section_types[ $section['type'] ] ) ) {
				continue;
			}

			$sanitized_section = array(
				'id'      => sanitize_text_field( $section['id'] ),
				'type'    => sanitize_text_field( $section['type'] ),
				'enabled' => ! empty( $section['enabled'] ),
			);

			// Sanitize title if present
			if ( isset( $section['title'] ) ) {
				$sanitized_section['title'] = sanitize_text_field( $section['title'] );
			}

			// Sanitize category if applicable
			if ( $section_types[ $section['type'] ]['has_category'] && isset( $section['category'] ) ) {
				$sanitized_section['category'] = absint( $section['category'] );
			}

			// Sanitize count if applicable
			if ( $section_types[ $section['type'] ]['has_count'] && isset( $section['count'] ) ) {
				$sanitized_section['count'] = absint( $section['count'] );
			}

			// Sanitize offset if applicable
			if ( $section_types[ $section['type'] ]['has_offset'] && isset( $section['offset'] ) ) {
				$sanitized_section['offset'] = absint( $section['offset'] );
			}

			// Sanitize panel data for main_banner
			if ( $section['type'] === 'main_banner' && $section_types[ $section['type'] ]['has_panels'] ) {
				// Panel 1
				if ( isset( $section['panel1_title'] ) ) {
					$sanitized_section['panel1_title'] = sanitize_text_field( $section['panel1_title'] );
				}
				if ( isset( $section['panel1_category'] ) ) {
					$sanitized_section['panel1_category'] = absint( $section['panel1_category'] );
				}
				if ( isset( $section['panel1_offset'] ) ) {
					$sanitized_section['panel1_offset'] = absint( $section['panel1_offset'] );
				}

				// Panel 2
				if ( isset( $section['panel2_title'] ) ) {
					$sanitized_section['panel2_title'] = sanitize_text_field( $section['panel2_title'] );
				}
				if ( isset( $section['panel2_category'] ) ) {
					$sanitized_section['panel2_category'] = absint( $section['panel2_category'] );
				}
				if ( isset( $section['panel2_count'] ) ) {
					$sanitized_section['panel2_count'] = absint( $section['panel2_count'] );
				}
				if ( isset( $section['panel2_offset'] ) ) {
					$sanitized_section['panel2_offset'] = absint( $section['panel2_offset'] );
				}

				// Panel 3
				if ( isset( $section['panel3_title'] ) ) {
					$sanitized_section['panel3_title'] = sanitize_text_field( $section['panel3_title'] );
				}
				if ( isset( $section['panel3_category'] ) ) {
					$sanitized_section['panel3_category'] = absint( $section['panel3_category'] );
				}
				if ( isset( $section['panel3_count'] ) ) {
					$sanitized_section['panel3_count'] = absint( $section['panel3_count'] );
				}
				if ( isset( $section['panel3_offset'] ) ) {
					$sanitized_section['panel3_offset'] = absint( $section['panel3_offset'] );
				}
			}

			// Sanitize CTA section fields
			if ( $section['type'] === 'cta_section' && isset( $section_types[ $section['type'] ]['has_cta_fields'] ) ) {
				if ( isset( $section['cta_media_type'] ) ) {
					$sanitized_section['cta_media_type'] = sanitize_text_field( $section['cta_media_type'] );
				}
				if ( isset( $section['cta_image'] ) ) {
					$sanitized_section['cta_image'] = esc_url_raw( $section['cta_image'] );
				}
				if ( isset( $section['cta_video_url'] ) ) {
					$sanitized_section['cta_video_url'] = esc_url_raw( $section['cta_video_url'] );
				}
				if ( isset( $section['cta_title'] ) ) {
					$sanitized_section['cta_title'] = sanitize_text_field( $section['cta_title'] );
				}
				if ( isset( $section['cta_description'] ) ) {
					$sanitized_section['cta_description'] = wp_kses_post( $section['cta_description'] );
				}
				if ( isset( $section['cta_button_label'] ) ) {
					$sanitized_section['cta_button_label'] = sanitize_text_field( $section['cta_button_label'] );
				}
				if ( isset( $section['cta_button_url'] ) ) {
					$sanitized_section['cta_button_url'] = esc_url_raw( $section['cta_button_url'] );
				}
				if ( isset( $section['cta_reverse_order'] ) ) {
					$sanitized_section['cta_reverse_order'] = (bool) $section['cta_reverse_order'];
				}
				if ( isset( $section['cta_background_media'] ) ) {
					$sanitized_section['cta_background_media'] = (bool) $section['cta_background_media'];
				}
			}

			// Sanitize trending topics fields
			if ( $section['type'] === 'trending_topics' && isset( $section_types[ $section['type'] ]['has_trending_config'] ) ) {
				if ( isset( $section['trending_display_mode'] ) ) {
					$sanitized_section['trending_display_mode'] = sanitize_text_field( $section['trending_display_mode'] );
				}
				if ( isset( $section['trending_count'] ) ) {
					$sanitized_section['trending_count'] = max( 1, min( 12, absint( $section['trending_count'] ) ) );
				}
				if ( isset( $section['trending_selected_categories'] ) ) {
					$categories = is_array( $section['trending_selected_categories'] ) ? $section['trending_selected_categories'] : array();
					$sanitized_section['trending_selected_categories'] = array_map( 'absint', $categories );
				}
			}

			// Sanitize marquee strip fields
			if ( $section['type'] === 'marquee_strip' && isset( $section_types[ $section['type'] ]['has_marquee_config'] ) ) {
				if ( isset( $section['marquee_date_format'] ) ) {
					$sanitized_section['marquee_date_format'] = sanitize_text_field( $section['marquee_date_format'] );
				}
				if ( isset( $section['marquee_autoplay'] ) ) {
					$sanitized_section['marquee_autoplay'] = (bool) $section['marquee_autoplay'];
				}
				if ( isset( $section['marquee_speed'] ) ) {
					$sanitized_section['marquee_speed'] = max( 1, min( 10, absint( $section['marquee_speed'] ) ) );
				}
				if ( isset( $section['marquee_direction'] ) ) {
					$sanitized_section['marquee_direction'] = sanitize_text_field( $section['marquee_direction'] );
				}
			}

			// Sanitize layout options fields
			if ( $section['type'] === 'full_width_widget' && isset( $section_types[ $section['type'] ]['has_layout_options'] ) ) {
				if ( isset( $section['layout_type'] ) ) {
					$allowed_layouts = array( 'default', 'main_sidebar', 'sidebar_main' );
					$sanitized_section['layout_type'] = in_array( $section['layout_type'], $allowed_layouts ) ? 
						$section['layout_type'] : 'default';
				}
			}

			$sanitized[] = $sanitized_section;
		}

		return json_encode( $sanitized );
	}

	/**
	 * Get sections configuration for display
	 */
	public static function get_sections_config() {
		$config = get_theme_mod( 'newslunar_homepage_sections_config', self::get_default_sections() );
		$sections = json_decode( $config, true );
		
		return is_array( $sections ) ? $sections : array();
	}

	/**
	 * Enqueue customizer scripts
	 */
	public static function enqueue_customizer_scripts() {
		$theme_version = wp_get_theme( 'newslunar' )->get( 'Version' );
		
		wp_enqueue_script(
			'newslunar-homepage-sections-customizer',
			get_template_directory_uri() . '/assets/js/homepage-sections-customizer.js',
			array( 'jquery', 'jquery-ui-sortable', 'customize-controls' ),
			$theme_version,
			true
		);

		wp_enqueue_style(
			'newslunar-homepage-sections-customizer',
			get_template_directory_uri() . '/assets/css/homepage-sections-customizer.css',
			array(),
			$theme_version
		);
		
		// Localize script data (since control enqueue() method isn't automatically called)
		$section_types = self::get_section_types();
		
		wp_localize_script( 'newslunar-homepage-sections-customizer', 'newslunarSectionTypes', array(
			'types' => $section_types,
			'i18n' => array(
				'edit'            => __( 'Edit', 'newslunar' ),
				'remove'          => __( 'Remove', 'newslunar' ),
				'toggle'          => __( 'Toggle', 'newslunar' ),
				'title'           => __( 'Title', 'newslunar' ),
				'category'        => __( 'Select Category', 'newslunar' ),
				'allCategories'   => __( 'All Categories', 'newslunar' ),
				'postCount'       => __( 'Number of Posts', 'newslunar' ),
				'postOffset'      => __( 'Post Offset', 'newslunar' ),
				'enabled'         => __( 'Enabled', 'newslunar' ),
				'confirmRemove'   => __( 'Are you sure you want to remove this section?', 'newslunar' ),
			),
		) );
		
		// Get categories for JavaScript
		$categories = get_categories( array( 'hide_empty' => false ) );
		$categories_data = array();
		foreach ( $categories as $cat ) {
			$categories_data[] = array(
				'id'   => $cat->term_id,
				'name' => $cat->name,
			);
		}
		wp_localize_script( 'newslunar-homepage-sections-customizer', 'newslunarCategories', $categories_data );
	}

	/**
	 * Render a section on the homepage
	 */
	public static function render_section( $section ) {
		if ( empty( $section['enabled'] ) ) {
			return;
		}

		$type = $section['type'];
		$template_name = 'template-parts/homepage-sections/' . $type . '.php';
		$template_path = get_template_directory() . '/' . $template_name;

		if ( file_exists( $template_path ) ) {
			// Make section data available to template
			set_query_var( 'section_data', $section );
			get_template_part( 'template-parts/homepage-sections/' . $type );
		} else {
			// Fallback hook for custom implementations
			do_action( 'newslunar_render_homepage_section_' . $type, $section );
		}
	}

}

// Initialize
NewsLunar_Homepage_Sections::init();

endif;


/**
 * Custom Customizer Control for Homepage Sections
 */
if ( class_exists( 'WP_Customize_Control' ) ) :

class NewsLunar_Homepage_Sections_Control extends WP_Customize_Control {

	public $type = 'homepage_sections';
	public $section_types = array();

	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		
		if ( isset( $args['section_types'] ) ) {
			$this->section_types = $args['section_types'];
		}
	}

	public function render_content() {
		$sections = json_decode( $this->value(), true );
		if ( ! is_array( $sections ) ) {
			$sections = array();
		}
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
		</label>

		<div class="homepage-sections-container">
			<div class="sections-list" data-sections='<?php echo esc_attr( $this->value() ); ?>'>
				<!-- Sections will be rendered by JavaScript -->
			</div>

			<button type="button" class="button add-section-button">
				<?php esc_html_e( 'Add New Section', 'newslunar' ); ?>
			</button>
		</div>

		<input type="hidden" class="sections-data-input" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" />

		<script type="text/template" id="tmpl-homepage-section-types">
			<div class="section-type-selector">
				<h3><?php esc_html_e( 'Select Section Type', 'newslunar' ); ?></h3>
				<div class="section-types-grid">
					<?php foreach ( $this->section_types as $type => $config ) : ?>
						<div class="section-type-item" data-type="<?php echo esc_attr( $type ); ?>">
							<span class="dashicons <?php echo esc_attr( $config['icon'] ); ?>"></span>
							<span class="section-type-label"><?php echo esc_html( $config['label'] ); ?></span>
							<p class="section-type-desc"><?php echo esc_html( $config['description'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</script>
		<?php
	}
}

endif;
