<article id="post-<?php the_ID(); ?>" <?php post_class( 'post united-article united-article-grid' ); ?>>

	<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
	
		<div class="entry-image entry-image-medium">
			
			<?php if ( is_sticky() ) : ?>
				<span class="sticky-tag">
                    <i class="bi bi-pin-angle-fill"></i>
					<span class="screen-reader-text"><?php _e( 'Sticky post', 'newslunar' ); ?></span>
				</span>
			<?php endif; ?>

			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
			
		</div><!-- .post-image -->
			
	<?php endif; ?>
	
	<div class="entry-details">
							
		<?php if ( has_category() ) : ?>
			<span class="post-categories"><?php the_category( ', ' ); ?></span>
		<?php endif; ?>
		
		<?php if ( get_the_title() ) : ?>
		    <h2 class="post-title post-title-medium"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php endif; ?>
		
		<p class="post-meta">
			<a href="<?php the_permalink(); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a> 
			<?php 
			if ( comments_open() ) {
				echo " &mdash; ";
				comments_popup_link( __( '0 Comments', 'newslunar' ), __( '1 Comment', 'newslunar' ), __( '% Comments' , 'newslunar' ) );
			} 
			?>
		</p>
		
	</div><!-- .entry-details -->
						
</article><!-- .post -->