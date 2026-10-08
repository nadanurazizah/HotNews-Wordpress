<?php

if ( ! class_exists( 'NewsLunar_Interactive_Banner' ) ) :
	class NewsLunar_Interactive_Banner extends WP_Widget {

		function __construct() {
			parent::__construct( 'NewsLunar_Interactive_Banner', __( 'NewsLunar: Interactive Banner', 'newslunar' ), array(
				'classname' 	=> 'NewsLunar_Interactive_Banner', 
				'description' 	=> __( 'Interactive banner with hover-changing background images and post titles.', 'newslunar' ) 
			) );
		}
		
		function widget( $args, $instance ) {
		
			// Outputs the content of the widget
			extract( $args ); // Make before_widget, etc available.
			
			$widget_title = isset( $instance['widget_title'] ) ? apply_filters( 'widget_title', $instance['widget_title'] ) : '';
			$number_of_posts = ! empty( $instance['number_of_posts'] ) ? max( 2, min( 5, intval( $instance['number_of_posts'] ) ) ) : 4;
			$selected_category = ! empty( $instance['category'] ) ? $instance['category'] : 0;
			$post_offset = ! empty( $instance['post_offset'] ) ? $instance['post_offset'] : 0;
			$banner_height = ! empty( $instance['banner_height'] ) ? $instance['banner_height'] : 480;
			$break_banner_tablet = isset( $instance['break_banner_tablet'] ) ? (bool) $instance['break_banner_tablet'] : false;
			$break_banner_mobile = isset( $instance['break_banner_mobile'] ) ? (bool) $instance['break_banner_mobile'] : false;
			
			// Generate unique widget ID
			$widget_id = $this->id;



			echo $before_widget;

			if ( $widget_title ) {
				echo $before_title . $widget_title . $after_title;	
			}

			?>
			
			<div class="newslunar-slider-banner" 
				data-widget-id="<?php echo esc_attr( $widget_id ); ?>" 
				data-post-count="<?php echo esc_attr( $number_of_posts ); ?>" 
				data-break-tablet="<?php echo $break_banner_tablet ? 'true' : 'false'; ?>" 
				data-break-mobile="<?php echo $break_banner_mobile ? 'true' : 'false'; ?>" 
				style="height: <?php echo esc_attr( $banner_height ) . 'px'; ?>">

				<?php
				
				$query_args = array(
					'ignore_sticky_posts' => true,
					'post_status'         => 'publish',
					'posts_per_page'      => $number_of_posts,
					'offset'              => $post_offset,
					'meta_query'          => array(
						array(
							'key' => '_thumbnail_id',
							'compare' => 'EXISTS'
						)
					)
				);
				
				// Add category filter if selected
				if ( $selected_category > 0 ) {
					$query_args['cat'] = $selected_category;
				}

				$banner_posts = get_posts( $query_args );
				
				if ( $banner_posts ) :
					$first_post = $banner_posts[0];
					?>
					

					<div class="banner-backgrounds">
						<?php foreach ( $banner_posts as $index => $post ) : 
							$is_active = $index === 0 ? 'active' : '';
							?>
							<div class="banner-bg <?php echo esc_attr( $is_active ); ?>" 
								data-post-id="<?php echo esc_attr( $post->ID ); ?>" 
								data-post-index="<?php echo esc_attr( $index ); ?>" 
								style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( $post->ID, 'full' ) ?: '' ); ?>')">
								<div class="banner-overlay"></div>
							</div>
						<?php endforeach; ?>
					</div>


					<div class="banner-content">

						
						<div class="banner-posts">
							<?php foreach ( $banner_posts as $index => $banner_post ) : 
								$post_categories = get_the_category( $banner_post->ID );
								$primary_category = ! empty( $post_categories ) ? $post_categories[0] : null;
								$is_active = $index === 0 ? 'active' : '';
								$comment_count = get_comments_number( $banner_post->ID );
								$comments_open = comments_open( $banner_post->ID );
								$post_thumbnail_url = get_the_post_thumbnail_url( $banner_post->ID, 'full' );
								?>
								
								<div class="banner-post-item <?php echo esc_attr( $is_active ); ?>" 
									data-post-id="<?php echo esc_attr( $banner_post->ID ); ?>" 
									data-post-index="<?php echo esc_attr( $index ); ?>"
									style="background-image: url('<?php echo esc_url( $post_thumbnail_url ?: '' ); ?>')">

									<div class="individual-banner-overlay"></div>

									<div class="banner-post-content">
										<?php if ( $primary_category ) : ?>
											<span class="post-categories">
												<?php echo esc_html( $primary_category->name ); ?>
											</span>
										<?php endif; ?>
										
										<h3 class="post-title post-title-medium">
											<a href="<?php echo esc_url( get_permalink( $banner_post->ID ) ); ?>">
												<?php echo esc_html( get_the_title( $banner_post ) ); ?>
											</a>
										</h3>

										<div class="post-meta">
											<time class="post-date" datetime="<?php echo esc_attr( get_the_date( 'c', $banner_post->ID ) ); ?>">
												<i class="bi bi-calendar4" aria-hidden="true"></i>
												<?php echo esc_html( get_the_date( '', $banner_post->ID ) ); ?>
											</time>
											<?php if ( $comments_open || $comment_count > 0 ) : ?>
												<span class="post-comments">
													<i class="bi bi-chat-left-text" aria-hidden="true"></i>
													<a href="<?php echo esc_url( get_comments_link( $banner_post->ID ) ); ?>">
														<?php 
														if ( $comment_count == 0 ) {
															echo esc_html__( '0 Comments', 'newslunar' );
														} elseif ( $comment_count == 1 ) {
															echo esc_html__( '1 Comment', 'newslunar' );
														} else {
															printf( esc_html__( '%s Comments', 'newslunar' ), number_format_i18n( $comment_count ) );
														}
														?>
													</a>
												</span>
											<?php endif; ?>
										</div>
									</div>
								</div>
								
							<?php endforeach; ?>
						</div>
					</div>
					
				<?php else : ?>
					<div class="banner-no-posts">
						<p><?php _e( 'No posts with featured images found.', 'newslunar' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
					
			<?php
			
			echo $after_widget; 

		}
		
		function update( $new_instance, $old_instance ) {

			$instance = $old_instance;
			
			$instance['widget_title'] = strip_tags( $new_instance['widget_title'] );
			$instance['number_of_posts'] = max( 2, min( 5, intval( $new_instance['number_of_posts'] ) ) ) ?: 4;
			$instance['category'] = intval( $new_instance['category'] ) ?: 0;
			$instance['post_offset'] = intval( $new_instance['post_offset'] ) ?: 0;
			$instance['banner_height'] = intval( $new_instance['banner_height'] ) ?: 480;
			$instance['break_banner_tablet'] = isset( $new_instance['break_banner_tablet'] ) ? (bool) $new_instance['break_banner_tablet'] : false;
			$instance['break_banner_mobile'] = isset( $new_instance['break_banner_mobile'] ) ? (bool) $new_instance['break_banner_mobile'] : false;
		
			//update and save the widget
			return $instance;

		}
		
		function form( $instance ) {
			
			// Set defaults
			if ( ! isset( $instance['widget_title'] ) ) $instance['widget_title'] = '';
			if ( empty( $instance['number_of_posts'] ) ) $instance['number_of_posts'] = 4;
			if ( ! isset( $instance['category'] ) ) $instance['category'] = 0;
			if ( ! isset( $instance['post_offset'] ) ) $instance['post_offset'] = 0;
			if ( empty( $instance['banner_height'] ) ) $instance['banner_height'] = 480;
			if ( ! isset( $instance['break_banner_tablet'] ) ) $instance['break_banner_tablet'] = false;
			if ( ! isset( $instance['break_banner_mobile'] ) ) $instance['break_banner_mobile'] = false;

			?>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>"><?php _e( 'Widget Title (Optional)', 'newslunar' ); ?>:</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_title' ) ); ?>" type="text" class="widefat" value="<?php echo esc_attr( $instance['widget_title'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>"><?php _e( 'Number of posts', 'newslunar' ); ?>:</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_posts' ) ); ?>" type="number" min="2" max="5" class="widefat" value="<?php echo esc_attr( $instance['number_of_posts'] ); ?>" />
				<small><?php _e( 'Posts with featured images only. Min: 2, Max: 5.', 'newslunar' ); ?></small>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php _e( 'Category', 'newslunar' ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" class="widefat">
					<option value="0"<?php selected( $instance['category'], 0 ); ?>><?php _e( 'All Categories', 'newslunar' ); ?></option>
					<?php
					$categories = get_categories();
					foreach ( $categories as $category ) {
						echo '<option value="' . esc_attr( $category->term_id ) . '"' . selected( $instance['category'], $category->term_id, false ) . '>' . esc_html( $category->name ) . '</option>';
					}
					?>
				</select>
			</p>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'post_offset' ) ); ?>"><?php _e( 'Post Offset', 'newslunar' ); ?>:</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'post_offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_offset' ) ); ?>" type="number" min="0" class="widefat" value="<?php echo esc_attr( $instance['post_offset'] ); ?>" />
				<small><?php _e( 'Number of posts to skip.', 'newslunar' ); ?></small>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'banner_height' ) ); ?>"><?php _e( 'Banner Height (px)', 'newslunar' ); ?>:</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'banner_height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'banner_height' ) ); ?>" type="number" min="200" max="800" class="widefat" value="<?php echo esc_attr( $instance['banner_height'] ); ?>" />
			</p>

			<p>
				<input id="<?php echo esc_attr( $this->get_field_id( 'break_banner_tablet' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'break_banner_tablet' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['break_banner_tablet'], true ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_id( 'break_banner_tablet' ) ); ?>"><?php _e( 'Separate banner views on tablet', 'newslunar' ); ?></label>
				<br>
				<small><?php _e( 'Display individual articles with their own background image on tablet devices (≥768px and ≤1024px).', 'newslunar' ); ?></small>
			</p>

			<p>
				<input id="<?php echo esc_attr( $this->get_field_id( 'break_banner_mobile' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'break_banner_mobile' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['break_banner_mobile'], true ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_id( 'break_banner_mobile' ) ); ?>"><?php _e( 'Separate banner views on mobile', 'newslunar' ); ?></label>
				<br>
				<small><?php _e( 'Display individual articles with their own background image on mobile devices (≤767px).', 'newslunar' ); ?></small>
			</p>
			
			<?php

		}

	}
endif;