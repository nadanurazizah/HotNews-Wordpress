<?php
/**
 * Template part for Latest Posts Section
 * This is the existing .news-posts section with sidebar
 * Non-repeatable section
 *
 * @package NewsLunar
 */

$section_data = get_query_var( 'section_data' );
$title = ! empty( $section_data['title'] ) ? $section_data['title'] : __( 'Latest Posts', 'newslunar' );

// Get current page number
// Use 'page' for static front page, 'paged' for blog index
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

// Query for latest posts
$latest_posts_args = array(
	'post_type'           => 'post',
	'posts_per_page'      => get_option( 'posts_per_page' ), // Use WordPress reading settings
	'paged'               => $paged, // Add pagination support
	'post_status'         => 'publish',
	'ignore_sticky_posts' => false,
);

$latest_posts_query = new WP_Query( $latest_posts_args );
?>

<section class="homepage-section latest-posts-section">
	<div class="wrapper section-inner group news-posts">
		
		<div class="content">
			
			<?php if ( $title && get_theme_mod( 'newslunar_show_latest_posts_title', true ) ) : ?>
				<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			
			<?php if ( is_active_sidebar( 'homepage-before-post' ) ) : ?>
				<div class="homepage-widget-area widget-area-before">
					<?php dynamic_sidebar( 'homepage-before-post' ); ?>
				</div>
			<?php endif; ?>
			
			<?php if ( $latest_posts_query->have_posts() ) : ?>
				<div class="posts" id="posts">
					<?php 
					while ( $latest_posts_query->have_posts() ) : 
						$latest_posts_query->the_post(); 
						get_template_part( 'content', get_post_format() );
					endwhile; 
					?>
				</div><!-- .posts -->
			<?php else : ?>
				<div class="posts" id="posts">
					<p><?php _e( 'No posts found.', 'newslunar' ); ?></p>
				</div>
			<?php endif; ?>
			
			<?php if ( is_active_sidebar( 'homepage-after-post' ) ) : ?>
				<div class="homepage-widget-area widget-area-after">
					<?php dynamic_sidebar( 'homepage-after-post' ); ?>
				</div>
			<?php endif; ?>
			
			<?php
			// Use standard WordPress pagination
			if ( $latest_posts_query->max_num_pages > 1 ) :
				// Set global $wp_query to our custom query temporarily for pagination
				global $wp_query;
				$temp_query = $wp_query;
				$wp_query = $latest_posts_query;
				
				// Use the theme's standard pagination template
				get_template_part( 'pagination' );
				
				// Restore original $wp_query
				$wp_query = $temp_query;
			endif;
			
			wp_reset_postdata();
			?>
			
		</div><!-- .content -->
		
		<?php get_sidebar(); ?>
		
	</div><!-- .wrapper.section-inner -->
</section>