<?php
/**
 * Template part for Trending Topics Section
 * Displays category boxes with custom images and post counts
 *
 * @package NewsLunar
 */

$section_data = get_query_var( 'section_data' );
$title = ! empty( $section_data['title'] ) ? $section_data['title'] : __( 'Trending Topics', 'newslunar' );
$display_mode = ! empty( $section_data['trending_display_mode'] ) ? $section_data['trending_display_mode'] : 'recent';
$count = ! empty( $section_data['trending_count'] ) ? absint( $section_data['trending_count'] ) : 6;
$count = max( 1, min( 12, $count ) ); // Ensure count is between 1-12

$categories = array();

if ( $display_mode === 'selected' ) {
	// Get selected categories
	$selected_ids = ! empty( $section_data['trending_selected_categories'] ) ? $section_data['trending_selected_categories'] : array();
	
	if ( ! empty( $selected_ids ) ) {
		$categories = get_categories( array(
			'include' => $selected_ids,
			'orderby' => 'include',
			'hide_empty' => false,
			'number' => $count,
		) );
	}
} else {
	// Get recent categories (categories with recent posts)
	$categories = get_categories( array(
		'orderby' => 'count',
		'order'   => 'DESC',
		'hide_empty' => true,
		'number' => $count,
	) );
}

if ( ! empty( $categories ) ) :
?>
<section class="homepage-section trending-topics-section">
	<div class="section-inner">
		
		<?php if ( $title ) : ?>
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		
		<div class="trending-topics-grid" data-count="<?php echo esc_attr( $count ); ?>">
			<?php foreach ( $categories as $category ) : 
				// Get custom category image from term meta
				$category_image = get_term_meta( $category->term_id, 'newslunar_category_image', true );
				
				// Fallback to a placeholder or first post image
				if ( empty( $category_image ) ) {
					$recent_post = get_posts( array(
						'category'    => $category->term_id,
						'numberposts' => 1,
						'post_status' => 'publish',
					) );
					
					if ( ! empty( $recent_post ) && has_post_thumbnail( $recent_post[0]->ID ) ) {
						$category_image = get_the_post_thumbnail_url( $recent_post[0]->ID, 'medium_large' );
					}
				}
				
				$post_count = $category->count;
				$category_link = get_category_link( $category->term_id );
			?>
				<a href="<?php echo esc_url( $category_link ); ?>" class="trending-topic-item">
					<div class="topic-image-wrapper">
						<?php if ( $category_image ) : ?>
							<img src="<?php echo esc_url( $category_image ); ?>" alt="<?php echo esc_attr( $category->name ); ?>" class="topic-image" />
                        <?php else : ?>
                            <?php newslunar_the_fallback_image(); ?>
                        <?php endif; ?>
						<div class="topic-overlay"></div>
					</div>
					
					<div class="topic-content">
						<h3 class="topic-name"><?php echo esc_html( $category->name ); ?></h3>
						<span class="topic-count"><?php echo esc_html( $post_count ); ?> <?php echo _n( 'POST', 'POSTS', $post_count, 'newslunar' ); ?></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
		
	</div>
</section>
<?php
endif;
