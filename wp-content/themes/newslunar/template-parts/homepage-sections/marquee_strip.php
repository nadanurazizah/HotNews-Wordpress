<?php
/**
 * Template part for Marquee Strip Section
 * Scrolling news ticker with featured image, title, and date
 *
 * @package NewsLunar
 */

$section_data = get_query_var( 'section_data' );

// Get marquee fields
$title = ! empty( $section_data['title'] ) ? $section_data['title'] : __( 'Breaking News', 'newslunar' );
$category = ! empty( $section_data['category'] ) ? absint( $section_data['category'] ) : 0;
$count = ! empty( $section_data['count'] ) ? absint( $section_data['count'] ) : 10;
$date_format = ! empty( $section_data['marquee_date_format'] ) ? $section_data['marquee_date_format'] : 'relative';
$autoplay = ! empty( $section_data['marquee_autoplay'] );
$speed = ! empty( $section_data['marquee_speed'] ) ? absint( $section_data['marquee_speed'] ) : 5;
$direction = ! empty( $section_data['marquee_direction'] ) ? $section_data['marquee_direction'] : 'ltr';

// Query posts
$args = array(
	'posts_per_page'      => $count,
	'ignore_sticky_posts' => true,
	'post_status'         => 'publish',
	'orderby'             => 'date',
	'order'               => 'DESC',
);

if ( $category > 0 ) {
	$args['cat'] = $category;
}

$marquee_query = new WP_Query( $args );

if ( $marquee_query->have_posts() ) :
	
	// Generate unique ID for this marquee instance
	$marquee_id = 'marquee-' . uniqid();
	
	// Calculate animation duration based on speed (1-10 scale)
	$base_duration = 60; // Base 60 seconds
	$duration = $base_duration - ( ( $speed - 1 ) * 5 ); // Speed 1=55s, Speed 10=15s
	
	// Direction classes
	$direction_class = $direction === 'rtl' ? 'marquee-rtl' : 'marquee-ltr';
?>
<section class="homepage-section marquee-strip-section">
	<div class="section-inner">
		
		<?php if ( $title ) : ?>
			<header class="section-header">
				<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>

					<?php if ( $autoplay ) : ?>
						<button type="button" class="marquee-pause-btn" data-target="<?php echo esc_attr( $marquee_id ); ?>">
							<i class="bi bi-pause-fill"></i>
						</button>
					<?php endif; ?>

			</header>
		<?php endif; ?>
		
		<div class="marquee-container <?php echo esc_attr( $direction_class ); ?>" 
		     id="<?php echo esc_attr( $marquee_id ); ?>"
		     data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
		     data-speed="<?php echo esc_attr( $speed ); ?>"
		     data-direction="<?php echo esc_attr( $direction ); ?>"
		     data-duration="<?php echo esc_attr( $duration ); ?>">
			
			<div class="marquee-track">
				<?php 
				// Duplicate content for seamless loop
				for ( $loop = 0; $loop < 2; $loop++ ) : 
					while ( $marquee_query->have_posts() ) : 
						$marquee_query->the_post(); 
						
						// Format date based on setting
						if ( $date_format === 'relative' ) {
							$formatted_date = human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'newslunar' );
						} else {
							$formatted_date = get_the_date();
						}
				?>
					<div class="marquee-item">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="marquee-image">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'thumbnail' ); ?>
								</a>
							</div>
						<?php endif; ?>
						
						<div class="marquee-content">
							<h3 class="post-title post-title-small marquee-post-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>

                            <time class="post-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <i class="bi bi-calendar4" aria-hidden="true"></i>
                                <?php echo esc_html( $formatted_date ); ?>
                            </time>

						</div>
					</div>
				<?php 
					endwhile;
					// Reset for second loop
					if ( $loop === 0 ) {
						$marquee_query->rewind_posts();
					}
				endfor; 
				?>
			</div>
			
		</div>
		
	</div>
</section>

<?php
	wp_reset_postdata();
endif;