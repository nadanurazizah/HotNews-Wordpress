<?php 

if ( ! class_exists( 'NewsLunar_Recent_Posts' ) ) :
	class NewsLunar_Recent_Posts extends WP_Widget {

		function __construct() {
			parent::__construct( 'NewsLunar_Recent_Posts', __( 'Recent Posts', 'newslunar' ), array( 
				'classname' 	=> 'NewsLunar_Recent_Posts', 
				'description' 	=> __( 'Displays recent blog entries.', 'newslunar' ) 
			) );
		}
		
		function widget( $args, $instance ) {
		
			// Outputs the content of the widget
			extract( $args ); // Make before_widget, etc available.
			
			$widget_title = isset( $instance['widget_title'] ) ? apply_filters( 'widget_title', $instance['widget_title'] ) : '';
			$number_of_posts = ! empty( $instance['number_of_posts'] ) ? $instance['number_of_posts'] : 5;
			$selected_category = ! empty( $instance['category'] ) ? $instance['category'] : 0;
			$post_offset = ! empty( $instance['post_offset'] ) ? $instance['post_offset'] : 0;
			
			echo $before_widget;

			if ( $widget_title ) {
				echo $before_title . $widget_title . $after_title;	
			}
			
			?>
			
			<ul class="newslunar-widget-list reset-list-style">
				
				<?php

				$query_args = array(
					'ignore_sticky_posts' => true,
					'post_status'         => 'publish',
					'posts_per_page'      => $number_of_posts,
					'offset'              => $post_offset,
				);
				
				// Add category filter if selected
				if ( $selected_category > 0 ) {
					$query_args['cat'] = $selected_category;
				}

				$recent_posts = get_posts( $query_args );
				
				if ( $recent_posts ) :
					foreach ( $recent_posts as $recent_post ) : 
					
						?>
				
						<li>
							<a href="<?php the_permalink( $recent_post->ID ); ?>" class="group">
								<div class="post-icon">
									<?php 
									if ( $post_thumbnail = get_the_post_thumbnail( $recent_post, 'thumbnail' ) ) {
										echo $post_thumbnail;
									} elseif ( get_post_format( $recent_post ) == 'gallery' ) {
										echo '<i class="bi bi-image"></i>';
									} else {
										echo '<i class="bi bi-journal-richtext"></i>';
									}
									?>
								</div>
								<div class="inner">
									<p class="title"><?php echo get_the_title( $recent_post ); ?></p>
									<p class="meta"><?php echo get_the_time( get_option( 'date_format' ), $recent_post ); ?></p>
								</div>
							</a>
						</li>
				
					<?php 
					endforeach;
				endif; 
				?>
		
			</ul>
					
			<?php
			
			echo $after_widget; 

		}
		
		function update( $new_instance, $old_instance ) {

			$instance = $old_instance;
			
			$instance['widget_title'] = strip_tags( $new_instance['widget_title'] );
			$instance['number_of_posts'] = intval( $new_instance['number_of_posts'] ) ?: 5;
			$instance['category'] = intval( $new_instance['category'] ) ?: 0;
			$instance['post_offset'] = intval( $new_instance['post_offset'] ) ?: 0;
		
			//update and save the widget
			return $instance;

		}
		
		function form( $instance ) {
			
			// Set defaults
			if ( ! isset( $instance['widget_title'] ) ) $instance['widget_title'] = '';
			if ( empty( $instance['number_of_posts'] ) ) $instance['number_of_posts'] = 5;
			if ( ! isset( $instance['category'] ) ) $instance['category'] = 0;
			if ( ! isset( $instance['post_offset'] ) ) $instance['post_offset'] = 0;

			?>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>"><?php _e( 'Title', 'newslunar' ); ?>:
				<input id="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_title' ) ); ?>" type="text" class="widefat" value="<?php echo esc_attr( $instance['widget_title'] ); ?>" /></label>
			</p>
							
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>"><?php _e( 'Number of posts to display', 'newslunar' ); ?>:
				<input id="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_posts' ) ); ?>" type="text" class="widefat" value="<?php echo esc_attr( $instance['number_of_posts'] ); ?>" /></label>
				<small>(<?php _e( 'Defaults to 5 if empty', 'newslunar' ); ?>)</small>
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
				<label for="<?php echo esc_attr( $this->get_field_id( 'post_offset' ) ); ?>"><?php _e( 'Post Offset', 'newslunar' ); ?>:
				<input id="<?php echo esc_attr( $this->get_field_id( 'post_offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_offset' ) ); ?>" type="number" min="0" class="widefat" value="<?php echo esc_attr( $instance['post_offset'] ); ?>" /></label>
				<small>(<?php _e( 'Number of posts to skip. Use 0 to show from the beginning.', 'newslunar' ); ?>)</small>
			</p>
			
			<?php

		}

	}
endif;
