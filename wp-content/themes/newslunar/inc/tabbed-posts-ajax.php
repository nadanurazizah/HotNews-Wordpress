<?php
/**
 * AJAX Handler for Tabbed Posts Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class NewsLunar_Tabbed_Posts_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_newslunar_get_tab_content', array( $this, 'get_tab_content' ) );
		add_action( 'wp_ajax_nopriv_newslunar_get_tab_content', array( $this, 'get_tab_content' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and localize AJAX data
	 */
	public function enqueue_scripts() {
		// Only enqueue if tabbed posts widget is active
		if ( is_active_widget( false, false, 'NewsLunar_Tabbed_Posts', true ) ) {
			// Enqueue CSS
			wp_enqueue_style(
				'newslunar-tabbed-posts-css',
				get_theme_file_uri( '/assets/css/tabbed-posts.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);

			// Enqueue JavaScript
			wp_enqueue_script(
				'newslunar-tabbed-posts',
				get_theme_file_uri( '/assets/js/tabbed-posts.js' ),
				array( 'jquery' ),
				wp_get_theme()->get( 'Version' ),
				true
			);

			wp_localize_script( 'newslunar-tabbed-posts', 'newslunar_tabbed_ajax', array(
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'newslunar_tabbed_nonce' ),
				'posts_per_tab' => 5, // Default, can be overridden per widget
			) );
		}
	}

	/**
	 * AJAX handler for getting tab content
	 */
	public function get_tab_content() {
		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'newslunar_tabbed_nonce' ) ) {
			wp_send_json_error( 'Invalid nonce' );
		}

		$category_id = intval( $_POST['category_id'] );
		$posts_per_tab = intval( $_POST['posts_per_tab'] ) ?: 5;

		$content = $this->generate_tab_content( $category_id, $posts_per_tab );
		
		if ( $content ) {
			wp_send_json_success( $content );
		} else {
			wp_send_json_error( 'No content found' );
		}
	}

	/**
	 * Generate tab content HTML
	 */
	private function generate_tab_content( $category_id, $posts_count ) {
		$query_args = array(
			'ignore_sticky_posts' => true,
			'post_status'         => 'publish',
			'posts_per_page'      => $posts_count,
		);
		
		// Add category filter if selected
		if ( $category_id > 0 ) {
			$query_args['cat'] = $category_id;
		}

		$posts = get_posts( $query_args );
		
		if ( empty( $posts ) ) {
			return '<p class="no-posts">' . __( 'No posts found.', 'newslunar' ) . '</p>';
		}
		
		$html = '<div class="tabbed-posts-list">';
		
		foreach ( $posts as $index => $post ) {
			$post_categories = get_the_category( $post->ID );
			$primary_category = ! empty( $post_categories ) ? $post_categories[0] : null;
			$author_name = get_the_author_meta( 'display_name', $post->post_author );
			$post_date = get_the_date( 'F j, Y', $post );
			
			if ( $index === 0 ) {
				// Featured post (large - left side)
				$html .= '<div class="united-article united-article-grid featured-post">';
				$html .= '<div class="entry-image">';
				if ( has_post_thumbnail( $post->ID ) ) {
					$html .= get_the_post_thumbnail( $post->ID, 'medium_large' );
				} else {
					$html .= '<div class="no-image"><i class="bi bi-image"></i></div>';
				}
				$html .= '</div>';
				$html .= '<div class="entry-details">';
				if ( $primary_category ) {
					$html .= '<span class="post-categories">' . esc_html( strtoupper( $primary_category->name ) ) . '</span>';
				}
				$html .= '<h3 class="post-title post-title-big"><a href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post ) . '</a></h3>';
				$html .= '<div class="post-meta">';
				$html .= '<span class="author">' . esc_html( $author_name ) . '</span>';
				$html .= '<span class="date">' . esc_html( $post_date ) . '</span>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
			} else if ( $index === 1 ) {
				// Start regular posts container
				$html .= '<div class="regular-posts-container">';
				$html .= '<div class="united-article united-article-list regular-post">';
				$html .= '<div class="entry-image entry-image-thumbnail">';
                $html .= '<a class="post-thumbnail" href="' . get_permalink( $post->ID ) . '" aria-hidden="true" tabindex="-1">';
				if ( has_post_thumbnail( $post->ID ) ) {
					$html .= get_the_post_thumbnail( $post->ID, 'medium' );
				} else {
					$html .= '<div class="no-image"><i class="bi bi-image"></i></div>';
				}
                $html .= '</a>';
                $html .= '</div>';
				$html .= '<div class="entry-details">';
				$html .= '<h3 class="post-title post-title-small"><a href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post ) . '</a></h3>';
				$html .= '<div class="post-meta">';
				$html .= '<span class="post-date">' . esc_html( $post_date ) . '</span>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
			} else {
				// Continue regular posts
				$html .= '<div class="united-article united-article-list regular-post">';
				$html .= '<div class="entry-image entry-image-thumbnail">';
                $html .= '<a class="post-thumbnail" href="' . get_permalink( $post->ID ) . '" aria-hidden="true" tabindex="-1">';
				if ( has_post_thumbnail( $post->ID ) ) {
					$html .= get_the_post_thumbnail( $post->ID, 'medium' );
				} else {
					$html .= '<div class="no-image"><i class="bi bi-image"></i></div>';
				}
                $html .= '</a>';
				$html .= '</div>';
				$html .= '<div class="entry-details">';
				$html .= '<h3 class="post-title post-title-small"><a href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post ) . '</a></h3>';
				$html .= '<div class="post-meta">';
				$html .= '<span class="post-date">' . esc_html( $post_date ) . '</span>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
			}
		}
		
		// Close regular posts container if it was opened
		if ( count( $posts ) > 1 ) {
			$html .= '</div>'; // Close regular-posts-container
		}
		
		$html .= '</div>'; // Close tabbed-posts-list
		return $html;
	}
}

// Initialize the AJAX handler
new NewsLunar_Tabbed_Posts_Ajax();