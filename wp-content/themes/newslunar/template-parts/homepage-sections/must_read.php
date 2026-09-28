<?php
/**
 * Template part for Must Read Section
 *
 * @package NewsLunar
 */

$section_data = get_query_var( 'section_data' );
$title = ! empty( $section_data['title'] ) ? $section_data['title'] : __( 'Must Read', 'newslunar' );
$category = ! empty( $section_data['category'] ) ? absint( $section_data['category'] ) : 0;
$count = ! empty( $section_data['count'] ) ? absint( $section_data['count'] ) : 6;
$offset = ! empty( $section_data['offset'] ) ? absint( $section_data['offset'] ) : 0;

$args = array(
	'posts_per_page'      => $count,
	'offset'              => $offset,
	'ignore_sticky_posts' => false, // Include sticky posts
	'post_status'         => 'publish',
);

if ( $category > 0 ) {
	$args['cat'] = $category;
}

$must_read_query = new WP_Query( $args );

if ( $must_read_query->have_posts() ) :
?>
<section class="homepage-section must-read-section">
	<div class="section-inner">
		
		<?php if ( $title ) : ?>
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		
		<div class="must-read-grid">
			<?php 
			$post_index = 0;
			
			while ( $must_read_query->have_posts() ) : 
				$must_read_query->the_post(); 
				
				// Every 3 posts is one row
				// Row pattern: [Large 50%] [Small 25%] [Small 25%]
				// Alternate rows: [Small 25%] [Small 25%] [Large 50%]
				
				$row_number = floor( $post_index / 3 );
				$position_in_row = $post_index % 3;
				
				// Determine if this row is reversed (odd rows)
				$is_reversed_row = ( $row_number % 2 === 1 );
				
				// In normal rows: position 0 is large
				// In reversed rows: position 2 is large
				if ( $is_reversed_row ) {
					$is_large = ( $position_in_row === 2 );
				} else {
					$is_large = ( $position_in_row === 0 );
				}
				
				$article_class = $is_large ? 'united-article-metro must-read-article large' : 'united-article-grid must-read-article small';
                $image_class = $is_large ? 'entry-image-big' : 'entry-image-small';
                $title_class = $is_large ? 'post-title-big' : 'post-title-medium';

				
				// Get category
				$categories = get_the_category();
				$category_name = ! empty( $categories ) ? $categories[0]->name : '';
			?>
                <article id="must-read-post-<?php the_ID(); ?>" <?php post_class( 'united-article ' . $article_class ); ?>>
					
					<?php if ( has_post_thumbnail() ) : ?>
                    <div class="entry-image <?php echo esc_attr( $image_class ); ?>">
                        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
							<?php
							// Use larger image for large articles
							$image_size = $is_large ? 'medium_large' : 'medium';
							the_post_thumbnail( $image_size );
							?>
                            <div class="thumbnail-has-overlay"></div>
							<?php if ( $category_name ) : ?>
								<span class="post-categories post-category-badge">
									<?php echo esc_html( strtoupper( $category_name ) ); ?>
								</span>
							<?php endif; ?>
						</a>
                    </div>
					<?php endif; ?>
					
					<div class="entry-details">

						<h3 class="post-title <?php echo esc_attr( $title_class ); ?>">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>


                        <div class="post-meta">
                            <time class="post-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <i class="bi bi-calendar4" aria-hidden="true"></i>
                                <?php echo esc_html(get_the_date()); ?>
                            </time>
                            <?php if (comments_open() || get_comments_number()) : ?>
                                <span class="post-comments">
                                                <i class="bi bi-chat-left-text" aria-hidden="true"></i>
                                                <?php
                                                comments_popup_link(
                                                    esc_html__('0 Comments', 'newslunar'),
                                                    esc_html__('1 Comment', 'newslunar'),
                                                    esc_html__('% Comments', 'newslunar')
                                                );
                                                ?>
                                            </span>
                            <?php endif; ?>
                        </div>
					</div>
				</article>
			<?php 
				$post_index++;
			endwhile; 
			?>
		</div>
		
	</div>
</section>
<?php
	wp_reset_postdata();
endif;
