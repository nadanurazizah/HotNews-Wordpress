/**
 * Debug helper for Homepage Sections Customizer
 * Add this temporarily to diagnose issues
 */

(function($) {
	'use strict';

	// Wait for customizer to be ready
	wp.customize.bind('ready', function() {
		
		console.log('=== Homepage Sections Debug ===');
		
		// Check if template exists
		setTimeout(function() {
			var template = $('#tmpl-homepage-section-types');
			console.log('Template found:', template.length > 0);
			if (template.length > 0) {
				console.log('Template content length:', template.html().length);
			}
			
			// Check if section types data is loaded
			if (typeof newslunarSectionTypes !== 'undefined') {
				console.log('Section types loaded:', Object.keys(newslunarSectionTypes.types || {}).length);
				console.log('Available types:', Object.keys(newslunarSectionTypes.types || {}));
			} else {
				console.error('newslunarSectionTypes is not defined!');
			}
			
			// Check if categories data is loaded
			if (typeof newslunarCategories !== 'undefined') {
				console.log('Categories loaded:', newslunarCategories.length);
			} else {
				console.warn('newslunarCategories is not defined');
			}
			
			// Check if button exists
			var button = $('.add-section-button');
			console.log('Add button found:', button.length > 0);
			
			// Test button click
			if (button.length > 0) {
				console.log('Button is clickable');
			}
			
		}, 1000);
		
	});
	
})(jQuery);
