<?php

if ( ! class_exists( 'NewsLunar_Tabbed_Posts' ) ) :
	class NewsLunar_Tabbed_Posts extends WP_Widget {

		function __construct() {
			parent::__construct( 'NewsLunar_Tabbed_Posts', __( 'NewsLunar: Post Tabs', 'newslunar' ), array( 
				'classname' 	=> 'NewsLunar_Tabbed_Posts', 
				'description' 	=> __( 'Displays featured posts in tabbed interface with AJAX loading.', 'newslunar' ) 
			) );
		}
		
		function widget( $args, $instance ) {
		
			// Outputs the content of the widget
			extract( $args ); // Make before_widget, etc available.
			
			$widget_title = isset( $instance['widget_title'] ) ? apply_filters( 'widget_title', $instance['widget_title'] ) : 'Featured News';
			$tab1_name = ! empty( $instance['tab1_name'] ) ? $instance['tab1_name'] : 'Science';
			$tab1_category = ! empty( $instance['tab1_category'] ) ? $instance['tab1_category'] : 0;
			$tab2_name = ! empty( $instance['tab2_name'] ) ? $instance['tab2_name'] : 'Fashion';
			$tab2_category = ! empty( $instance['tab2_category'] ) ? $instance['tab2_category'] : 0;
			$tab3_name = ! empty( $instance['tab3_name'] ) ? $instance['tab3_name'] : 'Sports';
			$tab3_category = ! empty( $instance['tab3_category'] ) ? $instance['tab3_category'] : 0;
			$posts_per_tab = ! empty( $instance['posts_per_tab'] ) ? $instance['posts_per_tab'] : 5;
			
			// Generate unique widget ID for AJAX
			$widget_id = $this->id;
			
			echo $before_widget;

			?>
			
			<div class="newslunar-tabbed-posts" data-widget-id="<?php echo esc_attr( $widget_id ); ?>">
				<header class="widget-header">
					<h3 class="widget-title">
						<?php echo esc_html( $widget_title ); ?>
					</h3>
					<div class="tab-nav">
						<button class="tab-btn transparent-button active" data-tab="tab1" data-category="<?php echo esc_attr( $tab1_category ); ?>">
							<?php echo esc_html( $tab1_name ); ?>
						</button>
						<button class="tab-btn transparent-button" data-tab="tab2" data-category="<?php echo esc_attr( $tab2_category ); ?>">
							<?php echo esc_html( $tab2_name ); ?>
						</button>
						<button class="tab-btn transparent-button" data-tab="tab3" data-category="<?php echo esc_attr( $tab3_category ); ?>">
							<?php echo esc_html( $tab3_name ); ?>
						</button>
					</div>
				</header>
				
				<div class="tab-content">
					<div class="tab-pane active" id="tab1">
						<?php echo $this->get_tab_content( $tab1_category, $posts_per_tab, true ); ?>
					</div>
					<div class="tab-pane" id="tab2">
						<!-- Content loaded via AJAX -->
					</div>
					<div class="tab-pane" id="tab3">
						<!-- Content loaded via AJAX -->
					</div>
				</div>
				
				<div class="tab-loading" style="display: none;">
					<div class="loading-spinner"></div>
					<span><?php _e( 'Loading...', 'newslunar' ); ?></span>
				</div>
			</div>
					
			<?php
			
			echo $after_widget; 

		}
		
		/**
		 * Get tab content HTML
		 */
		private function get_tab_content( $category_id, $posts_count, $is_featured = false ) {
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
				
				if ( $index === 0 && $is_featured ) {
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
		
		function update( $new_instance, $old_instance ) {

			$instance = $old_instance;
			
			$instance['widget_title'] = strip_tags( $new_instance['widget_title'] );
			$instance['tab1_name'] = sanitize_text_field( $new_instance['tab1_name'] );
			$instance['tab1_category'] = intval( $new_instance['tab1_category'] );
			$instance['tab2_name'] = sanitize_text_field( $new_instance['tab2_name'] );
			$instance['tab2_category'] = intval( $new_instance['tab2_category'] );
			$instance['tab3_name'] = sanitize_text_field( $new_instance['tab3_name'] );
			$instance['tab3_category'] = intval( $new_instance['tab3_category'] );
			$instance['posts_per_tab'] = intval( $new_instance['posts_per_tab'] ) ?: 5;
		
			//update and save the widget
			return $instance;

		}
		
		function form( $instance ) {
			
			// Set defaults
			if ( ! isset( $instance['widget_title'] ) ) $instance['widget_title'] = 'Featured News';
			if ( ! isset( $instance['tab1_name'] ) ) $instance['tab1_name'] = 'Science';
			if ( ! isset( $instance['tab1_category'] ) ) $instance['tab1_category'] = 0;
			if ( ! isset( $instance['tab2_name'] ) ) $instance['tab2_name'] = 'Fashion';
			if ( ! isset( $instance['tab2_category'] ) ) $instance['tab2_category'] = 0;
			if ( ! isset( $instance['tab3_name'] ) ) $instance['tab3_name'] = 'Sports';
			if ( ! isset( $instance['tab3_category'] ) ) $instance['tab3_category'] = 0;
			if ( empty( $instance['posts_per_tab'] ) ) $instance['posts_per_tab'] = 5;

			?>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>"><?php _e( 'Widget Title', 'newslunar' ); ?>:</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_title' ) ); ?>" type="text" class="widefat" value="<?php echo esc_attr( $instance['widget_title'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'posts_per_tab' ) ); ?>"><?php _e( 'Posts per tab', 'newslunar' ); ?>:</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'posts_per_tab' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'posts_per_tab' ) ); ?>" type="number" min="3" max="10" class="widefat" value="<?php echo esc_attr( $instance['posts_per_tab'] ); ?>" />
				<small><?php _e( 'First post will be featured (large), others will be regular size.', 'newslunar' ); ?></small>
			</p>
			
			<hr>
			<h4><?php _e( 'Tab 1 Settings', 'newslunar' ); ?></h4>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'tab1_name' ) ); ?>"><?php _e( 'Tab 1 Name', 'newslunar' ); ?>:</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'tab1_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab1_name' ) ); ?>" type="text" class="widefat" value="<?php echo esc_attr( $instance['tab1_name'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'tab1_category' ) ); ?>"><?php _e( 'Tab 1 Category', 'newslunar' ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'tab1_category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab1_category' ) ); ?>" class="widefat">
					<option value="0"<?php selected( $instance['tab1_category'], 0 ); ?>><?php _e( 'All Categories', 'newslunar' ); ?></option>
					<?php
					$categories = get_categories();
					foreach ( $categories as $category ) {
						echo '<option value="' . esc_attr( $category->term_id ) . '"' . selected( $instance['tab1_category'], $category->term_id, false ) . '>' . esc_html( $category->name ) . '</option>';
					}
					?>
				</select>
			</p>

			<hr>
			<h4><?php _e( 'Tab 2 Settings', 'newslunar' ); ?></h4>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'tab2_name' ) ); ?>"><?php _e( 'Tab 2 Name', 'newslunar' ); ?>:</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'tab2_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab2_name' ) ); ?>" type="text" class="widefat" value="<?php echo esc_attr( $instance['tab2_name'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'tab2_category' ) ); ?>"><?php _e( 'Tab 2 Category', 'newslunar' ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'tab2_category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab2_category' ) ); ?>" class="widefat">
					<option value="0"<?php selected( $instance['tab2_category'], 0 ); ?>><?php _e( 'All Categories', 'newslunar' ); ?></option>
					<?php
					foreach ( $categories as $category ) {
						echo '<option value="' . esc_attr( $category->term_id ) . '"' . selected( $instance['tab2_category'], $category->term_id, false ) . '>' . esc_html( $category->name ) . '</option>';
					}
					?>
				</select>
			</p>

			<hr>
			<h4><?php _e( 'Tab 3 Settings', 'newslunar' ); ?></h4>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'tab3_name' ) ); ?>"><?php _e( 'Tab 3 Name', 'newslunar' ); ?>:</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'tab3_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab3_name' ) ); ?>" type="text" class="widefat" value="<?php echo esc_attr( $instance['tab3_name'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'tab3_category' ) ); ?>"><?php _e( 'Tab 3 Category', 'newslunar' ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'tab3_category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab3_category' ) ); ?>" class="widefat">
					<option value="0"<?php selected( $instance['tab3_category'], 0 ); ?>><?php _e( 'All Categories', 'newslunar' ); ?></option>
					<?php
					foreach ( $categories as $category ) {
						echo '<option value="' . esc_attr( $category->term_id ) . '"' . selected( $instance['tab3_category'], $category->term_id, false ) . '>' . esc_html( $category->name ) . '</option>';
					}
					?>
				</select>
			</p>
			
			<?php

		}

	}
endif;