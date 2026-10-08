/**
 * NewsLunar Tabbed Posts Widget JavaScript
 */

(function($) {
    'use strict';

    var TabbedPosts = {
        
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;

            // Tab click handler
            $(document).on('click', '.newslunar-tabbed-posts .tab-btn', function(e) {
                e.preventDefault();
                
                var $button = $(this);
                var $widget = $button.closest('.newslunar-tabbed-posts');
                var $tabContent = $widget.find('.tab-content');
                var $loading = $widget.find('.tab-loading');
                var tabId = $button.data('tab');
                var categoryId = $button.data('category');
                var widgetId = $widget.data('widget-id');
                
                // Don't reload if already active
                if ($button.hasClass('active')) {
                    return;
                }
                
                // Update active tab button
                $button.siblings().removeClass('active');
                $button.addClass('active');
                
                // Check if tab content already exists
                var $targetPane = $widget.find('#' + tabId);
                if ($targetPane.find('.tabbed-posts-list').length > 0) {
                    // Content exists, just show it
                    $tabContent.find('.tab-pane').removeClass('active');
                    $targetPane.addClass('active');
                    return;
                }
                
                // Show loading
                $loading.show();
                $tabContent.hide();
                
                // AJAX request for tab content
                $.ajax({
                    url: newslunar_tabbed_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'newslunar_get_tab_content',
                        nonce: newslunar_tabbed_ajax.nonce,
                        category_id: categoryId,
                        widget_id: widgetId,
                        posts_per_tab: newslunar_tabbed_ajax.posts_per_tab || 5
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            // Hide loading
                            $loading.hide();
                            $tabContent.show();
                            
                            // Update content
                            $tabContent.find('.tab-pane').removeClass('active');
                            $targetPane.html(response.data).addClass('active');
                        } else {
                            self.handleError($widget, response.data || 'Error loading content');
                        }
                    },
                    error: function() {
                        self.handleError($widget, 'Network error occurred');
                    }
                });
            });
        },

        handleError: function($widget, message) {
            var $loading = $widget.find('.tab-loading');
            var $tabContent = $widget.find('.tab-content');
            
            $loading.hide();
            $tabContent.show();
            
            // Show error message in active tab
            var $activePane = $tabContent.find('.tab-pane.active');
            $activePane.html('<div class="tab-error"><p>' + message + '</p></div>');
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        TabbedPosts.init();
    });

})(jQuery);