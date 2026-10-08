<?php
/**
 * Template part for Main Banner Section
 * 3-Panel Layout: Slider | Metro Style | List Style
 *
 * @package NewsLunar
 */
$section_data = get_query_var('section_data');
// Panel 1: Slider - ALWAYS 4 POSTS
$panel1_title = !empty($section_data['panel1_title']) ? $section_data['panel1_title'] : __('Main Story', 'newslunar');
$panel1_category = !empty($section_data['panel1_category']) ? absint($section_data['panel1_category']) : 0;
$panel1_count = 2;
$panel1_offset = !empty($section_data['panel1_offset']) ? absint($section_data['panel1_offset']) : 0;
// Panel 2: Metro
$panel2_title = !empty($section_data['panel2_title']) ? $section_data['panel2_title'] : __("Editor's Picks", 'newslunar');
$panel2_category = !empty($section_data['panel2_category']) ? absint($section_data['panel2_category']) : 0;
$panel2_count = 2;
$panel2_offset = !empty($section_data['panel2_offset']) ? absint($section_data['panel2_offset']) : 0;
// Panel 3: List
$panel3_title = !empty($section_data['panel3_title']) ? $section_data['panel3_title'] : __('Trending Story', 'newslunar');
$panel3_category = !empty($section_data['panel3_category']) ? absint($section_data['panel3_category']) : 0;
$panel3_count = !empty($section_data['panel3_count']) ? absint($section_data['panel3_count']) : 5;
$panel3_offset = !empty($section_data['panel3_offset']) ? absint($section_data['panel3_offset']) : 0;
?>
<section class="homepage-section main-banner-section">
    <div class="section-inner">
        <div class="main-banner-container">
            <!-- Panel 1: Slider -->
            <div class="banner-panel banner-panel-highlight">
                <?php
                $highlight_args = array(
                    'posts_per_page' => $panel1_count,
                    'offset' => $panel1_offset,
                    'ignore_sticky_posts' => true,
                    'post_status' => 'publish',
                );
                if ($panel1_category > 0) {
                    $highlight_args['cat'] = $panel1_category;
                }
                $highlight_query = new WP_Query($highlight_args);
                if ($highlight_query->have_posts()) :
                    ?>
                    <header class="section-header">
                        <h2 class="section-title"><?php echo esc_html($panel1_title); ?></h2>
                    </header>
                    <div class="prime-content">
                        <?php 
                        $post_index = 0;
                        while ($highlight_query->have_posts()) :
                            $highlight_query->the_post();
                            $post_index++;

                            if ($post_index === 1) :
                                // First post - Metro style
                        ?>
                            <article id="metro-post-<?php the_ID(); ?>" <?php post_class('united-article united-article-metro'); ?>>
                                <div class="entry-image entry-image-large">
                                    <a class="post-thumbnail" href="<?php the_permalink(); ?>"
                                       aria-hidden="true" tabindex="-1">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('medium_large'); ?>
                                        <?php else : ?>
                                            <?php newslunar_the_fallback_image(); ?>
                                        <?php endif; ?>
                                        <div class="thumbnail-has-overlay"></div>
                                        <?php
                                        $categories = get_the_category();
                                        if (!empty($categories)) :
                                            ?>
                                            <span class="post-categories post-category-badge"><?php echo esc_html($categories[0]->name); ?></span>
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div class="entry-details">
                                    <h3 class="post-title post-title-large">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <div class="entry-excerpt">
                                        <?php newslunar_the_custom_excerpt( get_the_ID(), 20 ); ?>
                                    </div>
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
                            else :
                                // Remaining posts - List style
                        ?>
                            <article id="list-post-<?php the_ID(); ?>" <?php post_class('united-article united-article-list'); ?>>
                                <div class="entry-image entry-image-small">
                                    <a class="post-thumbnail" href="<?php the_permalink(); ?>"
                                       aria-hidden="true" tabindex="-1">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('medium'); ?>
                                        <?php else : ?>
                                            <?php newslunar_the_fallback_image(); ?>
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div class="entry-details">
                                    <h3 class="post-title post-title-big">
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
                        <?php 
                            endif;
                        endwhile; 
                        ?>
                    </div>
                    <?php
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <!-- Panel 2: Metro Style -->
            <div class="banner-panel banner-metro-panel">
                <?php
                $metro_args = array(
                    'posts_per_page' => $panel2_count,
                    'offset' => $panel2_offset,
                    'ignore_sticky_posts' => true,
                    'post_status' => 'publish',
                );
                if ($panel2_category > 0) {
                    $metro_args['cat'] = $panel2_category;
                }
                $metro_query = new WP_Query($metro_args);
                if ($metro_query->have_posts()) :
                    ?>
                    <header class="section-header">
                        <h2 class="section-title"><?php echo esc_html($panel2_title); ?></h2>
                    </header>
                    <div class="metro-grid">
                        <?php
                        $post_index = 0;
                        while ($metro_query->have_posts()) :
                            $metro_query->the_post();
                            $post_index++;
                            ?>
                            <article id="metro-post-<?php the_ID(); ?>" <?php post_class('united-article united-article-grid'); ?>>
                                <div class="entry-image entry-image-small">
                                    <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('medium'); ?>
                                        <?php else : ?>
                                            <?php newslunar_the_fallback_image(); ?>
                                        <?php endif; ?>
                                        <?php
                                        $categories = get_the_category();
                                        if (!empty($categories)) :
                                            ?>
                                            <span class="post-categories post-category-badge"><?php echo esc_html($categories[0]->name); ?></span>
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div class="entry-details">

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
                    <?php
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <!-- Panel 3: List Style -->
            <div class="banner-panel banner-list-panel">
                <?php
                $list_args = array(
                    'posts_per_page' => $panel3_count,
                    'offset' => $panel3_offset,
                    'ignore_sticky_posts' => true,
                    'post_status' => 'publish',
                );
                if ($panel3_category > 0) {
                    $list_args['cat'] = $panel3_category;
                }
                $list_query = new WP_Query($list_args);
                if ($list_query->have_posts()) :
                    ?>
                    <header class="section-header">
                        <h2 class="section-title"><?php echo esc_html($panel3_title); ?></h2>
                    </header>
                    <div class="list-content">
                        <?php
                        $list_index = 0;
                        while ($list_query->have_posts()) :
                            $list_query->the_post();
                            $list_index++;
                            ?>
                            <article id="banner-list-<?php the_ID(); ?>" <?php post_class('united-article united-article-list has-border-bottom united-banner-list'); ?>>
                                <div class="list-number"><?php echo $list_index; ?></div>
                                <div class="entry-image entry-image-thumbnail">
                                    <a class="post-thumbnail" href="<?php the_permalink(); ?>"
                                       aria-hidden="true" tabindex="-1">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('medium'); ?>
                                        <?php else : ?>
                                            <?php newslunar_the_fallback_image(); ?>
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div class="entry-details">
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
                    <?php
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </div>
</section>
