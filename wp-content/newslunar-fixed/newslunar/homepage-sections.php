<?php
/**
 * Template Name: Homepage with Sections
 * Template for homepage with customizable sections
 *
 * @package NewsLunar
 */

get_header(); 

// Check if we're on a paged view (page 2, 3, etc.)
// Use 'page' for static front page, 'paged' for blog index
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

// If on page 2+, only show Latest Posts section
if ( $paged > 1 ) :
	// Find and render only the latest_posts section
	$sections = NewsLunar_Homepage_Sections::get_sections_config();
	$latest_posts_section = null;
	
	foreach ( $sections as $section ) {
		if ( $section['type'] === 'latest_posts' && ! empty( $section['enabled'] ) ) {
			$latest_posts_section = $section;
			break;
		}
	}
	
	if ( $latest_posts_section ) {
		NewsLunar_Homepage_Sections::render_section( $latest_posts_section );
	} else {
		// Fallback if no latest posts section configured
		?>
		<div class="wrapper section-inner group news-posts">
			<div class="content">
				<?php if ( have_posts() ) : ?>
					<div class="posts" id="posts">
						<?php 
						while ( have_posts() ) : 
							the_post(); 
							get_template_part( 'content', get_post_format() );
						endwhile; 
						?>
					</div>
					<?php get_template_part( 'pagination' ); ?>
				<?php endif; ?>
			</div>
			<?php get_sidebar(); ?>
		</div>
		<?php
	}
	
else :
	// Page 1: Show all enabled sections
	$sections = NewsLunar_Homepage_Sections::get_sections_config();
	
	if ( ! empty( $sections ) ) :
		foreach ( $sections as $section ) :
			if ( ! empty( $section['enabled'] ) ) {
				NewsLunar_Homepage_Sections::render_section( $section );
			}
		endforeach;
	else :
		// Fallback to default content if no sections configured
		?>
		<div class="wrapper section-inner group news-posts">
			<div class="content">
				<?php if ( have_posts() ) : ?>
					<div class="posts" id="posts">
						<?php 
						while ( have_posts() ) : 
							the_post(); 
							get_template_part( 'content', get_post_format() );
						endwhile; 
						?>
					</div>
					<?php get_template_part( 'pagination' ); ?>
				<?php endif; ?>
			</div>
			<?php get_sidebar(); ?>
		</div>
		<?php
	endif;
	
endif;

get_footer();
