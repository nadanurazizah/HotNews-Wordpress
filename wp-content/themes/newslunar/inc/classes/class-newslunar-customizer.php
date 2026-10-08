<?php

/* ---------------------------------------------------------------------------------------------
   CUSTOMIZER SETTINGS
   --------------------------------------------------------------------------------------------- */

if ( ! class_exists( 'NewsLunar_Customize' ) ) :
	class NewsLunar_Customize {

		public static function register( $wp_customize ) {

			// Load the upsell control class — available here because customize_register
			// fires after WP_Customize_Control is defined.
			require_once get_template_directory() . '/inc/classes/class-newslunar-upsell-section.php';

			/* ---- PANEL: THEME OPTIONS ---- */

			$wp_customize->add_panel( 'newslunar_panel', array(
				'title'       => __( 'Theme Options', 'newslunar' ),
				'priority'    => 10,
				'capability'  => 'edit_theme_options',
				'description' => __( 'Customize settings for the NewsLunar theme.', 'newslunar' ),
			) );

			/* ---- SECTION: NEWSLUNAR PRO UPSELL ---- */

			$wp_customize->add_section( 'newslunar_pro', array(
				'title'    => __( 'NewsLunar Pro', 'newslunar' ),
				'priority' => 1,
			) );

			$wp_customize->add_setting( 'newslunar_pro_placeholder', array(
				'sanitize_callback' => '__return_false',
			) );

			$wp_customize->add_control(
				new NewsLunar_Upsell_Control(
					$wp_customize,
					'newslunar_pro_placeholder',
					array(
						'section'  => 'newslunar_pro',
						'priority' => 1,
						'pro_url'  => esc_url( apply_filters( 'newslunar_pro_url', 'https://unitedtheme.com/themes/newslunar-pro/' ) ),
						'help_url' => esc_url( apply_filters( 'newslunar_help_url', 'https://unitedtheme.com/submit-a-request/' ) ),
						'features' => apply_filters( 'newslunar_pro_features', array(
							__( 'Multiple Header Layouts', 'newslunar' ),
							__( 'Advanced Typography Controls', 'newslunar' ),
							__( 'Advanced Color Controls', 'newslunar' ),
							__( 'Custom Widget Areas', 'newslunar' ),
							__( 'Dark Mode Support', 'newslunar' ),
							__( 'Color Scheme Options', 'newslunar' ),
							__( 'Priority Support', 'newslunar' ),
						) ),
					)
				)
			);

			/* SECTION: COLOR OPTIONS */

			$wp_customize->add_section( 'newslunar_colors', array(
				'title'      => __( 'Color Options', 'newslunar' ),
				'priority'   => 10,
				'capability' => 'edit_theme_options',
				'panel'      => 'newslunar_panel',
			) );

			/* SETTING: ACCENT COLOR */

			$wp_customize->add_setting( 'accent_color', array(
				'default'           => '#ce242c',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'newslunar_accent_color', array(
				'label'    => __( 'Accent Color', 'newslunar' ),
				'section'  => 'newslunar_colors',
				'settings' => 'accent_color',
				'priority' => 10,
			) ) );

			/* SECTION: TOPBAR OPTIONS */

			$wp_customize->add_section( 'newslunar_topbar', array(
				'title'      => __( 'Topbar Options', 'newslunar' ),
				'priority'   => 15,
				'capability' => 'edit_theme_options',
				'panel'      => 'newslunar_panel',
			) );

			/* SETTING: SHOW TOPBAR */

			$wp_customize->add_setting( 'newslunar_show_topbar', array(
				'default'           => false,
				'type'              => 'theme_mod',
				'sanitize_callback' => 'newslunar_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'newslunar_show_topbar', array(
				'label'    => __( 'Show/Hide Topbar', 'newslunar' ),
				'section'  => 'newslunar_topbar',
				'type'     => 'checkbox',
				'priority' => 10,
			) );

			/* SETTING: SHOW TODAY'S DATE */

			$wp_customize->add_setting( 'newslunar_show_date', array(
				'default'           => false,
				'type'              => 'theme_mod',
				'sanitize_callback' => 'newslunar_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'newslunar_show_date', array(
				'label'    => __( 'Show/Hide Today\'s Date', 'newslunar' ),
				'section'  => 'newslunar_topbar',
				'type'     => 'checkbox',
				'priority' => 20,
			) );

			/* SETTING: SHOW SOCIAL MENU */

			$wp_customize->add_setting( 'newslunar_show_topbar_social', array(
				'default'           => false,
				'type'              => 'theme_mod',
				'sanitize_callback' => 'newslunar_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'newslunar_show_topbar_social', array(
				'label'    => __( 'Show/Hide Social Menu', 'newslunar' ),
				'section'  => 'newslunar_topbar',
				'type'     => 'checkbox',
				'priority' => 30,
			) );

			/* SETTING: DATE FORMAT */

			$date_formats = array_unique( apply_filters( 'date_formats', array( __( 'F j, Y', 'newslunar' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ) ) );
			$format_choices = array(
				'' => sprintf( __( 'Default (%s)', 'newslunar' ), wp_date( 'l, F j, Y' ) ),
			);
			foreach ( $date_formats as $format ) {
				$format_choices[ $format ] = wp_date( $format );
			}

			$wp_customize->add_setting( 'newslunar_date_format', array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( 'newslunar_date_format', array(
				'label'    => __( 'Date Format', 'newslunar' ),
				'section'  => 'newslunar_topbar',
				'type'     => 'select',
				'choices'  => $format_choices,
				'priority' => 40,
			) );

			/* SECTION: SITE NAVIGATION */

			$wp_customize->add_section( 'newslunar_navigation', array(
				'title'      => __( 'Site Navigation', 'newslunar' ),
				'priority'   => 16,
				'capability' => 'edit_theme_options',
				'panel'      => 'newslunar_panel',
			) );

			/* SETTING: ENABLE COLOR MODE SWITCHER */

			$wp_customize->add_setting( 'newslunar_enable_color_mode', array(
				'default'           => true,
				'type'              => 'theme_mod',
				'sanitize_callback' => 'newslunar_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'newslunar_enable_color_mode', array(
				'label'       => __( 'Enable Color Mode Switcher', 'newslunar' ),
				'description' => __( 'Show day/night/system mode toggle button', 'newslunar' ),
				'section'     => 'newslunar_navigation',
				'type'        => 'checkbox',
				'priority'    => 10,
			) );

			/* SETTING: DEFAULT COLOR MODE */

			$wp_customize->add_setting( 'newslunar_default_color_mode', array(
				'default'           => 'light',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'newslunar_sanitize_color_mode',
			) );

			$wp_customize->add_control( 'newslunar_default_color_mode', array(
				'label'    => __( 'Default Color Mode', 'newslunar' ),
				'section'  => 'newslunar_navigation',
				'type'     => 'select',
				'choices'  => array(
					'light'  => __( 'Light Mode', 'newslunar' ),
					'dark'   => __( 'Dark Mode', 'newslunar' ),
					'system' => __( 'System Preference', 'newslunar' ),
				),
				'priority' => 15,
			) );

			/* SETTING: ENABLE RANDOM POST BUTTON */

			$wp_customize->add_setting( 'newslunar_enable_random_post', array(
				'default'           => true,
				'type'              => 'theme_mod',
				'sanitize_callback' => 'newslunar_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'newslunar_enable_random_post', array(
				'label'       => __( 'Enable Random Post Button', 'newslunar' ),
				'description' => __( 'Show random post button in navigation', 'newslunar' ),
				'section'     => 'newslunar_navigation',
				'type'        => 'checkbox',
				'priority'    => 20,
			) );

			/* SETTING: ENABLE CTA BUTTON */

			$wp_customize->add_setting( 'newslunar_enable_cta_button', array(
				'default'           => true,
				'type'              => 'theme_mod',
				'sanitize_callback' => 'newslunar_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'newslunar_enable_cta_button', array(
				'label'       => __( 'Enable CTA Button', 'newslunar' ),
				'description' => __( 'Show call-to-action button in navigation', 'newslunar' ),
				'section'     => 'newslunar_navigation',
				'type'        => 'checkbox',
				'priority'    => 25,
			) );

			/* SETTING: CTA BUTTON LABEL */

			$wp_customize->add_setting( 'newslunar_cta_button_label', array(
				'default'           => __( 'Sign In', 'newslunar' ),
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( 'newslunar_cta_button_label', array(
				'label'    => __( 'CTA Button Label', 'newslunar' ),
				'section'  => 'newslunar_navigation',
				'type'     => 'text',
				'priority' => 30,
			) );

			/* SETTING: CTA BUTTON URL */

			$wp_customize->add_setting( 'newslunar_cta_button_url', array(
				'default'           => '#',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'esc_url_raw',
			) );

			$wp_customize->add_control( 'newslunar_cta_button_url', array(
				'label'    => __( 'CTA Button URL', 'newslunar' ),
				'section'  => 'newslunar_navigation',
				'type'     => 'url',
				'priority' => 35,
			) );


			/* SECTION: GENERAL OPTIONS */

			$wp_customize->add_section( 'newslunar_general', array(
				'title'      => __( 'General', 'newslunar' ),
				'priority'   => 20,
				'capability' => 'edit_theme_options',
				'panel'      => 'newslunar_panel',
			) );

			/* SETTING: SHOW LATEST POSTS TITLE */

			$wp_customize->add_setting( 'newslunar_show_latest_posts_title', array(
				'default'           => true,
				'type'              => 'theme_mod',
				'sanitize_callback' => 'newslunar_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'newslunar_show_latest_posts_title', array(
				'label'    => __( 'Show Latest Posts Section Title', 'newslunar' ),
				'section'  => 'newslunar_general',
				'type'     => 'checkbox',
				'priority' => 5,
			) );

			/* SETTING: ENABLE PRELOADER */

			$wp_customize->add_setting( 'newslunar_enable_preloader', array(
				'default'           => true,
				'type'              => 'theme_mod',
				'sanitize_callback' => 'newslunar_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'newslunar_enable_preloader', array(
				'label'       => __( 'Enable Preloader', 'newslunar' ),
				'description' => __( 'Show a loading animation while the page loads.', 'newslunar' ),
				'section'     => 'newslunar_general',
				'type'        => 'checkbox',
				'priority'    => 10,
			) );

			/* SETTING: PRELOADER STYLE */

			$wp_customize->add_setting( 'newslunar_preloader_style', array(
				'default'           => 'spinner',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'newslunar_sanitize_preloader_style',
			) );

			$wp_customize->add_control( 'newslunar_preloader_style', array(
				'label'       => __( 'Preloader Style', 'newslunar' ),
				'description' => __( 'Choose the loading animation style.', 'newslunar' ),
				'section'     => 'newslunar_general',
				'type'        => 'select',
				'choices'     => array(
					'spinner'   => __( 'Spinner', 'newslunar' ),
					'dots'      => __( 'Dots', 'newslunar' ),
					'pulse'     => __( 'Pulse', 'newslunar' ),
					'bars'      => __( 'Bars', 'newslunar' ),
					'circle'    => __( 'Circle', 'newslunar' ),
				),
				'priority'    => 15,
			) );

			/* SETTING: CUSTOM LOGO */

			// Only display the Customizer section for the newslunar_logo setting if it already has a value.
			// This means that site owners with existing logos can remove them, but new site owners can't add them.
			// Since v2.0.0, the core custom_logo setting (in the Site Identity Customizer panel) should be used instead.
			if ( get_theme_mod( 'newslunar_logo' ) ) {

				$wp_customize->add_setting( 'newslunar_logo', array(
					'sanitize_callback' => 'esc_url_raw',
				) );

				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'newslunar_logo', array(
					'label'    => __( 'Logo', 'newslunar' ),
					'section'  => 'newslunar_general',
					'settings' => 'newslunar_logo',
				) ) );

			}

			/* SECTION: FOOTER OPTIONS */

			$wp_customize->add_section( 'newslunar_footer', array(
				'title'      => __( 'Footer Options', 'newslunar' ),
				'priority'   => 30,
				'capability' => 'edit_theme_options',
				'panel'      => 'newslunar_panel',
			) );

			/* SETTING: NUMBER OF WIDGET AREAS */

			$wp_customize->add_setting( 'newslunar_footer_widgets', array(
				'default'           => 4,
				'type'              => 'theme_mod',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( 'newslunar_footer_widgets', array(
				'label'    => __( 'Number of Widget Areas', 'newslunar' ),
				'section'  => 'newslunar_footer',
				'type'     => 'select',
				'choices'  => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'priority' => 10,
			) );

			/* SETTING: BACK TO TOP BUTTON */

			$wp_customize->add_setting( 'newslunar_back_to_top', array(
				'default'           => true,
				'type'              => 'theme_mod',
				'sanitize_callback' => 'newslunar_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'newslunar_back_to_top', array(
				'label'    => __( 'Back to Top Button', 'newslunar' ),
				'section'  => 'newslunar_footer',
				'type'     => 'checkbox',
				'priority' => 20,
			) );

		}

		public static function header_output() {

			$display_header_text = get_theme_mod( 'display_header_text', true );
			$accent_default      = '#ce242c';
			$accent              = get_theme_mod( 'accent_color', $accent_default );

			$has_custom_accent = ( $accent && $accent != $accent_default );

            if (!$display_header_text || $has_custom_accent) {
                echo '<!-- Customizer CSS -->';
                echo '<style type="text/css">';

                if (!$display_header_text) {
                    echo '.blog-title, .blog-description { display: none; }';
                }

                if ($has_custom_accent) {

                    self::generate_css(':root', '--united-accent-color', $accent);

                }

                echo '</style>';
                echo '<!--/Customizer CSS-->';
            }
				
		}

		public static function generate_css( $selector, $style, $value, $prefix='', $postfix='', $echo=true ) {
			$return = '';
			if ( $value ) {
				$return = sprintf( '%s { %s:%s; }',
					$selector,
					$style,
					$prefix.$value.$postfix
				);
				if ( $echo ) echo $return;
			}
			return $return;
		}

	}

	add_action( 'customize_register', array( 'NewsLunar_Customize', 'register' ) );
	add_action( 'wp_head', array( 'NewsLunar_Customize', 'header_output' ) );
	add_action( 'customize_controls_enqueue_scripts', 'newslunar_customizer_enqueue' );

endif;


/* ---------------------------------------------------------------------------------------------
   ENQUEUE CUSTOMIZER ASSETS
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'newslunar_customizer_enqueue' ) ) :
	/**
	 * Enqueue CSS and JS for the Customizer controls panel.
	 * Use customizer.css / customizer.js to style or script any Customizer UI.
	 */
	function newslunar_customizer_enqueue() {
		$version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style(
			'newslunar-customizer',
			get_theme_file_uri( '/assets/css/customizer.css' ),
			array(),
			$version
		);
	}
endif;

if ( ! function_exists( 'newslunar_sanitize_checkbox' ) ) :
	/**
	 * Sanitize checkbox output.
	 *
	 * @param bool $checked Whether the checkbox is checked.
	 * @return bool Whether the checkbox is checked.
	 */
	function newslunar_sanitize_checkbox( $checked ) {
		return ( ( isset( $checked ) && true == $checked ) ? true : false );
	}
endif;

if ( ! function_exists( 'newslunar_sanitize_preloader_style' ) ) :
	/**
	 * Sanitize preloader style selection.
	 *
	 * @param string $input The preloader style.
	 * @return string The sanitized preloader style.
	 */
	function newslunar_sanitize_preloader_style( $input ) {
		$valid_styles = array( 'spinner', 'dots', 'pulse', 'bars', 'circle' );
		return in_array( $input, $valid_styles, true ) ? $input : 'spinner';
	}
endif;

if ( ! function_exists( 'newslunar_sanitize_color_mode' ) ) :
	/**
	 * Sanitize color mode selection.
	 *
	 * @param string $input The color mode.
	 * @return string The sanitized color mode.
	 */
	function newslunar_sanitize_color_mode( $input ) {
		$valid_modes = array( 'light', 'dark', 'system' );
		return in_array( $input, $valid_modes, true ) ? $input : 'light';
	}
endif;