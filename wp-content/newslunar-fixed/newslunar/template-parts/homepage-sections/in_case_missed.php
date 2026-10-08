<?php
/**
 * Template part for In Case You Missed Section
 *
 * @package NewsLunar
 */

$section_data = get_query_var( 'section_data' );
$title = ! empty( $section_data['title'] ) ? $section_data['title'] : __( 'In Case You Missed', 'newslunar' );
$category = ! empty( $section_data['category'] ) ? absint( $section_data['category'] ) : 0;
$count = ! empty( $section_data['count'] ) ? absint( $section_data['count'] ) : 3;
$offset = ! empty( $section_data['offset'] ) ? absint( $section_data['offset'] ) : 0;

// Get posts for the "In Case You Missed" section
$args = array(
	'posts_per_page'      => $count,
	'offset'              => $offset,
	'ignore_sticky_posts' => true,
	'post_status'         => 'publish',
);

if ( $category > 0 ) {
	$args['cat'] = $category;
}

$missed_query = new WP_Query( $args );

if ( $missed_query->have_posts() ) :
?>
<section class="homepage-section recommendation-news-section">
	<div class="section-inner">
		
		<?php if ( $title ) : ?>
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		
		<div class="recommendation-grid">
			<?php while ( $missed_query->have_posts() ) : $missed_query->the_post(); ?>
                <article id="recommendation-post-<?php the_ID(); ?>" <?php post_class('united-article united-article-list'); ?>>


                    <?php if (has_post_thumbnail()) : ?>
                        <div class="entry-image entry-image-thumbnail">
                            <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                <?php the_post_thumbnail('medium', array('alt' => the_title_attribute(array('echo' => false)))); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="entry-details">
							<?php
							$categories = get_the_category();
							if ( ! empty( $categories ) ) :
							?>
								<span class="post-categories"><?php echo esc_html( $categories[0]->name ); ?></span>
							<?php endif; ?>
							
							<h3 class="post-title post-title-small">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>

                            <div class="post-meta">
                                <time class="post-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <i class="bi bi-calendar4" aria-hidden="true"></i>
                                    <?php echo esc_html(get_the_date()); ?>
                                </time>
                            </div>
						</div>

				</article>
			<?php endwhile; ?>
		</div>
		
	</div>
</section>
<?php
	wp_reset_postdata();
endif;
