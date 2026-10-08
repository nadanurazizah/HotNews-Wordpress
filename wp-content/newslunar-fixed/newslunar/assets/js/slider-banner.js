/**
 * NewsLunar NewsLunar: Interactive Banner Widget
 */

(function($) {
    'use strict';

    var SliderBanner = {
        
        init: function() {
            this.bindEvents();
            this.setupAutoSlider();
        },

        bindEvents: function() {
            var self = this;

            // Post item hover - change background
            $(document).on('mouseenter', '.banner-post-item', function() {
                var $item = $(this);
                var $banner = $item.closest('.newslunar-slider-banner');
                var postId = $item.data('post-id');
                
                self.changeBackground($banner, postId, $item);
            });

            // Auto-pause on hover
            $(document).on('mouseenter', '.newslunar-slider-banner', function() {
                var $banner = $(this);
                self.pauseAutoSlider($banner);
            });

            $(document).on('mouseleave', '.newslunar-slider-banner', function() {
                var $banner = $(this);
                self.resumeAutoSlider($banner);
            });

            // Keyboard navigation
            $(document).on('keydown', '.banner-post-item', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    $(this).find('a')[0].click();
                }
            });
        },

        changeBackground: function($banner, postId, $item) {
            // Update background
            $banner.find('.banner-bg').removeClass('active');
            $banner.find('.banner-bg[data-post-id="' + postId + '"]').addClass('active');
            
            // Update active post item
            $banner.find('.banner-post-item').removeClass('active');
            $item.addClass('active');
        },

        setupAutoSlider: function() {
            var self = this;
            
            $('.newslunar-slider-banner').each(function() {
                var $banner = $(this);
                var $posts = $banner.find('.banner-post-item');
                
                if ($posts.length <= 1) return;
                
                var currentIndex = 0;
                var totalPosts = $posts.length;
                
                var autoSlide = function() {
                    if ($banner.data('paused')) return;
                    
                    currentIndex = (currentIndex + 1) % totalPosts;
                    var $nextPost = $posts.eq(currentIndex);
                    var nextPostId = $nextPost.data('post-id');
                    
                    self.changeBackground($banner, nextPostId, $nextPost);
                };
                
                // Store interval ID
                var intervalId = setInterval(autoSlide, 4000);
                $banner.data('interval-id', intervalId);
            });
        },

        pauseAutoSlider: function($banner) {
            $banner.data('paused', true);
        },

        resumeAutoSlider: function($banner) {
            $banner.data('paused', false);
        },

        // Method to manually navigate (for potential future use)
        goToSlide: function($banner, index) {
            var $posts = $banner.find('.banner-post-item');
            var $targetPost = $posts.eq(index);
            
            if ($targetPost.length) {
                var postId = $targetPost.data('post-id');
                this.changeBackground($banner, postId, $targetPost);
            }
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        SliderBanner.init();
    });

    // Reinitialize for dynamically added widgets (AJAX, etc.)
    $(document).on('widget-added widget-updated', function() {
        SliderBanner.setupAutoSlider();
    });

})(jQuery);