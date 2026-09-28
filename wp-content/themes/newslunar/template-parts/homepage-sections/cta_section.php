<?php
/**
 * Template part for CTA Section
 * Call-to-action with image/video, title, description, and button
 *
 * @package NewsLunar
 */

$section_data = get_query_var( 'section_data' );

// Get CTA fields
$title = ! empty( $section_data['title'] ) ? $section_data['title'] : '';
$media_type = ! empty( $section_data['cta_media_type'] ) ? $section_data['cta_media_type'] : 'image';
$image_url = ! empty( $section_data['cta_image'] ) ? $section_data['cta_image'] : '';
$video_url = ! empty( $section_data['cta_video_url'] ) ? $section_data['cta_video_url'] : '';
$cta_title = ! empty( $section_data['cta_title'] ) ? $section_data['cta_title'] : '';
$cta_description = ! empty( $section_data['cta_description'] ) ? $section_data['cta_description'] : '';
$button_label = ! empty( $section_data['cta_button_label'] ) ? $section_data['cta_button_label'] : __( 'Learn More', 'newslunar' );
$button_url = ! empty( $section_data['cta_button_url'] ) ? $section_data['cta_button_url'] : '#';

// Layout options
$reverse_order = ! empty( $section_data['cta_reverse_order'] );
$background_media = ! empty( $section_data['cta_background_media'] );

// Helper function to get YouTube embed URL
function newslunar_get_youtube_embed_url( $url ) {
	// Extract video ID from various YouTube URL formats
	$video_id = '';
	
	if ( preg_match( '/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id ) ) {
		$video_id = $id[1];
	} elseif ( preg_match( '/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id ) ) {
		$video_id = $id[1];
	} elseif ( preg_match( '/youtu\.be\/([^\&\?\/]+)/', $url, $id ) ) {
		$video_id = $id[1];
	}
	
	if ( $video_id ) {
		return 'https://www.youtube.com/embed/' . $video_id . '?rel=0&showinfo=0';
	}
	
	return '';
}

// Helper function to get YouTube thumbnail
function newslunar_get_youtube_thumbnail( $url ) {
	$video_id = '';
	
	if ( preg_match( '/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id ) ) {
		$video_id = $id[1];
	} elseif ( preg_match( '/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id ) ) {
		$video_id = $id[1];
	} elseif ( preg_match( '/youtu\.be\/([^\&\?\/]+)/', $url, $id ) ) {
		$video_id = $id[1];
	}
	
	if ( $video_id ) {
		return 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg';
	}
	
	return '';
}

// Skip section if no content
if ( empty( $cta_title ) && empty( $cta_description ) ) {
	return;
}

// Determine container class
$container_class = 'cta-container';
if ( $reverse_order ) {
	$container_class .= ' cta-reverse';
}
if ( $background_media ) {
	$container_class .= ' cta-background';
}

// Background media setup
$background_style = '';
$background_video = '';
if ( $background_media ) {
	if ( $media_type === 'video' && ! empty( $video_url ) ) {
		// Check if it's a YouTube URL or direct video file
		if ( strpos( $video_url, 'youtube.com' ) !== false || strpos( $video_url, 'youtu.be' ) !== false ) {
			// Use YouTube thumbnail for background since YouTube can't autoplay as background
			$thumbnail_url = newslunar_get_youtube_thumbnail( $video_url );
			if ( $thumbnail_url ) {
				$background_style = 'background-image: url(' . esc_url( $thumbnail_url ) . '); background-size: cover; background-position: center; background-repeat: no-repeat;';
			}
		} else {
			// Direct video file - use as HTML5 background video
			$background_video = $video_url;
		}
	} elseif ( ! empty( $image_url ) ) {
		$background_style = 'background-image: url(' . esc_url( $image_url ) . '); background-size: cover; background-position: center; background-repeat: no-repeat;';
	}
}
?>
<section class="homepage-section cta-section" <?php echo $background_style ? 'style="' . esc_attr( $background_style ) . '"' : ''; ?>>
	<?php if ( $background_media ) : ?>
		<?php if ( ! empty( $background_video ) ) : ?>
			<!-- HTML5 Background Video -->
			<video class="cta-background-video" autoplay muted loop playsinline>
				<source src="<?php echo esc_url( $background_video ); ?>" type="video/mp4">
				<!-- Fallback for unsupported video -->
			</video>
		<?php endif; ?>
		<div class="cta-overlay"></div>
	<?php endif; ?>
	
	<div class="section-inner">
		
		<div class="<?php echo esc_attr( $container_class ); ?>">
			
			<?php if ( ! $background_media ) : ?>
				<!-- Media Column (only show if not background) -->
				<div class="cta-media">
					<?php if ( $media_type === 'video' && ! empty( $video_url ) ) : 
						$embed_url = newslunar_get_youtube_embed_url( $video_url );
						if ( $embed_url ) :
					?>
						<div class="cta-video-wrapper">
							<iframe 
								src="<?php echo esc_url( $embed_url ); ?>" 
								frameborder="0" 
								allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
								allowfullscreen
								class="cta-video"
							></iframe>
						</div>
					<?php 
						endif;
					elseif ( ! empty( $image_url ) ) : 
					?>
						<div class="cta-image-wrapper">
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $cta_title ); ?>" class="cta-image" />
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			
			<!-- Content Column -->
			<div class="cta-content">
				<?php if ( $title ) : ?>
					<span class="cta-label"><?php echo esc_html( $title ); ?></span>
				<?php endif; ?>
				
				<?php if ( $cta_title ) : ?>
					<h2 class="post-title post-title-medium"><?php echo esc_html( $cta_title ); ?></h2>
				<?php endif; ?>
				
				<?php if ( $cta_description ) : ?>
                    <?php echo wp_kses_post( wpautop( $cta_description ) ); ?>
				<?php endif; ?>
				
				<?php if ( $button_url && $button_url !== '#' ) : ?>
					<a href="<?php echo esc_url( $button_url ); ?>" class="united-button">
						<?php echo esc_html( $button_label ); ?>
						<i class="bi bi-arrow-right"></i>
					</a>
				<?php endif; ?>
			</div>
			
		</div>
		
	</div>
</section>
