<?php
/**
 * Helper functions for Homepage Sections
 *
 * @package NewsLunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if homepage sections are enabled
 */
function newslunar_has_homepage_sections() {
	$sections = NewsLunar_Homepage_Sections::get_sections_config();
	return ! empty( $sections );
}

/**
 * Get specific section by ID
 */
function newslunar_get_section_by_id( $section_id ) {
	$sections = NewsLunar_Homepage_Sections::get_sections_config();
	
	foreach ( $sections as $section ) {
		if ( isset( $section['id'] ) && $section['id'] === $section_id ) {
			return $section;
		}
	}
	
	return null;
}

/**
 * Get all sections of a specific type
 */
function newslunar_get_sections_by_type( $type ) {
	$sections = NewsLunar_Homepage_Sections::get_sections_config();
	$filtered = array();
	
	foreach ( $sections as $section ) {
		if ( isset( $section['type'] ) && $section['type'] === $type ) {
			$filtered[] = $section;
		}
	}
	
	return $filtered;
}

/**
 * Check if a section type exists
 */
function newslunar_section_type_exists( $type ) {
	$sections = NewsLunar_Homepage_Sections::get_sections_config();
	
	foreach ( $sections as $section ) {
		if ( isset( $section['type'] ) && $section['type'] === $type ) {
			return true;
		}
	}
	
	return false;
}

/**
 * Count enabled sections
 */
function newslunar_count_enabled_sections() {
	$sections = NewsLunar_Homepage_Sections::get_sections_config();
	$count = 0;
	
	foreach ( $sections as $section ) {
		if ( ! empty( $section['enabled'] ) ) {
			$count++;
		}
	}
	
	return $count;
}

/**
 * Get posts for a section (for use in custom templates)
 */
function newslunar_get_section_posts( $section_data ) {
	$category = ! empty( $section_data['category'] ) ? absint( $section_data['category'] ) : 0;
	$count = ! empty( $section_data['count'] ) ? absint( $section_data['count'] ) : 5;
	$offset = ! empty( $section_data['offset'] ) ? absint( $section_data['offset'] ) : 0;
	$orderby = ! empty( $section_data['orderby'] ) ? sanitize_text_field( $section_data['orderby'] ) : 'date';
	
	$args = array(
		'posts_per_page'      => $count,
		'offset'              => $offset,
		'ignore_sticky_posts' => true,
		'post_status'         => 'publish',
		'orderby'             => $orderby,
	);
	
	if ( $category > 0 ) {
		$args['cat'] = $category;
	}
	
	return new WP_Query( $args );
}

/**
 * Render section title with customization options
 */
function newslunar_render_section_title( $title, $args = array() ) {
	$defaults = array(
		'tag'   => 'h2',
		'class' => 'section-title',
		'icon'  => '',
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	if ( empty( $title ) ) {
		return;
	}
	
	$output = '<' . esc_attr( $args['tag'] ) . ' class="' . esc_attr( $args['class'] ) . '">';
	
	if ( ! empty( $args['icon'] ) ) {
		$output .= '<i class="bi bi-' . esc_attr( $args['icon'] ) . '"></i> ';
	}
	
	$output .= esc_html( $title );
	$output .= '</' . esc_attr( $args['tag'] ) . '>';
	
	echo $output;
}

/**
 * Get section wrapper classes
 */
function newslunar_get_section_classes( $section_data ) {
	$classes = array( 'homepage-section' );
	
	if ( ! empty( $section_data['type'] ) ) {
		$classes[] = sanitize_html_class( $section_data['type'] ) . '-section';
	}
	
	if ( ! empty( $section_data['id'] ) ) {
		$classes[] = 'section-' . sanitize_html_class( $section_data['id'] );
	}
	
	// Allow filtering
	$classes = apply_filters( 'newslunar_section_classes', $classes, $section_data );
	
	return implode( ' ', array_unique( $classes ) );
}

/**
 * Check if section should display based on conditions
 */
function newslunar_should_display_section( $section_data ) {
	// Check if enabled
	if ( empty( $section_data['enabled'] ) ) {
		return false;
	}
	
	// Check if section type exists
	$type_config = NewsLunar_Homepage_Sections::get_section_type( $section_data['type'] );
	if ( ! $type_config ) {
		return false;
	}
	
	// Allow filtering
	return apply_filters( 'newslunar_should_display_section', true, $section_data );
}

/**
 * Get category name from ID
 */
function newslunar_get_category_name( $category_id ) {
	if ( empty( $category_id ) ) {
		return __( 'All Categories', 'newslunar' );
	}
	
	$category = get_category( $category_id );
	
	return $category ? $category->name : __( 'All Categories', 'newslunar' );
}

/**
 * Get all used post IDs from sections to avoid duplicates
 */
function newslunar_get_used_post_ids( $exclude_section_id = '' ) {
	$sections = NewsLunar_Homepage_Sections::get_sections_config();
	$used_ids = array();
	
	foreach ( $sections as $section ) {
		// Skip the current section
		if ( ! empty( $exclude_section_id ) && $section['id'] === $exclude_section_id ) {
			continue;
		}
		
		// Skip disabled sections
		if ( empty( $section['enabled'] ) ) {
			continue;
		}
		
		// Get posts for this section
		$section_posts = newslunar_get_section_posts( $section );
		
		if ( $section_posts->have_posts() ) {
			while ( $section_posts->have_posts() ) {
				$section_posts->the_post();
				$used_ids[] = get_the_ID();
			}
			wp_reset_postdata();
		}
	}
	
	return array_unique( $used_ids );
}

/**
 * Debug function - output section configuration
 */
function newslunar_debug_sections() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	$sections = NewsLunar_Homepage_Sections::get_sections_config();
	
	echo '<pre style="background: #f5f5f5; padding: 20px; margin: 20px; border: 1px solid #ddd;">';
	echo '<strong>Homepage Sections Configuration:</strong>' . "\n\n";
	print_r( $sections );
	echo '</pre>';
}

/**
 * Get section template path
 */
function newslunar_get_section_template_path( $type ) {
	$template = 'template-parts/homepage-sections/' . $type . '.php';
	$template_path = get_template_directory() . '/' . $template;
	
	if ( file_exists( $template_path ) ) {
		return $template_path;
	}
	
	// Check child theme
	$child_template_path = get_stylesheet_directory() . '/' . $template;
	if ( file_exists( $child_template_path ) ) {
		return $child_template_path;
	}
	
	return false;
}

/**
 * Enqueue section-specific styles or scripts
 */
function newslunar_enqueue_section_assets( $section_type ) {
	$asset_file = get_template_directory() . '/assets/css/sections/' . $section_type . '.css';
	
	if ( file_exists( $asset_file ) ) {
		wp_enqueue_style(
			'newslunar-section-' . $section_type,
			get_template_directory_uri() . '/assets/css/sections/' . $section_type . '.css',
			array( 'newslunar_style' ),
			wp_get_theme()->get( 'Version' )
		);
	}
}
