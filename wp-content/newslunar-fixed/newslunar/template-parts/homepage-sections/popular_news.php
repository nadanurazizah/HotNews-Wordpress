<?php
/**
 * Template part for Popular News Section
 *
 * @package NewsLunar
 */

$section_data = get_query_var( 'section_data' );
$title = ! empty( $section_data['title'] ) ? $section_data['title'] : __( 'Popular News', 'newslunar' );
$category = ! empty( $section_data['category'] ) ? absint( $section_data['category'] ) : 0;
$count = ! empty( $section_data['count'] ) ? absint( $section_data['count'] ) : 4;
$offset = ! empty( $section_data['offset'] ) ? absint( $section_data['offset'] ) : 0;

$args = array(
	'posts_per_page'      => $count,
	'offset'              => $offset,
	'ignore_sticky_posts' => true,
	'post_status'         => 'publish',
	'orderby'             => 'comment_count', // Popular by comments
	'order'               => 'DESC',
);

if ( $category > 0 ) {
	$args['cat'] = $category;
}

$popular_query = new WP_Query( $args );

if ( $popular_query->have_posts() ) :
?>
<section class="homepage-section popular-news-section">
	<div class="section-inner">
		
		<?php if ( $title ) : ?>
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		
		<div class="popular-news-grid">
			<?php while ( $popular_query->have_posts() ) : $popular_query->the_post(); ?>
                <article id="popular-post-<?php the_ID(); ?>" <?php post_class('united-article united-article-grid popular-item'); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
                    <div class="entry-image entry-image-small">
                        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
							<?php the_post_thumbnail( 'medium' ); ?>
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
						
						<h3 class="post-title post-title-medium">
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
			<?php endwhile; ?>
		</div>
		
	</div>
</section>
<?php
	wp_reset_postdata();
endif;
