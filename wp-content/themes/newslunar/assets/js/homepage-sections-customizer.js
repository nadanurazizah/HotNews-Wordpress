/**
 * NewsLunar Homepage Sections Customizer
 * Handles drag-and-drop, add, remove, and edit functionality
 */

(function($) {
	'use strict';

	var HomepageSectionsManager = {

		init: function() {
			this.bindEvents();
			this.renderSections();
		},

		bindEvents: function() {
			var self = this;

			// Add new section
			$(document).on('click', '.add-section-button', function(e) {
				e.preventDefault();
				self.showSectionTypeSelector();
			});

			// Select section type
			$(document).on('click', '.section-type-item', function() {
				var type = $(this).data('type');
				self.addSection(type);
				$('.section-type-overlay').remove();
				$(document).off('keyup.section-overlay');
			});

			// Toggle section
			$(document).on('click', '.section-toggle', function(e) {
				e.preventDefault();
				var $item = $(this).closest('.section-item');
				var sectionId = $item.data('id');
				var isCollapsing = !$item.hasClass('collapsed');
				
				$item.toggleClass('collapsed');
				
				// Save collapsed state to localStorage
				localStorage.setItem('newslunar_section_collapsed_' + sectionId, isCollapsing);
			});

			// Remove section
			$(document).on('click', '.section-remove', function(e) {
				e.preventDefault();
				if (confirm(newslunarSectionTypes.i18n.confirmRemove)) {
					var $item = $(this).closest('.section-item');
					var sectionId = $item.data('id');
					
					// Clean up localStorage for this section
					localStorage.removeItem('newslunar_section_collapsed_' + sectionId);
					
					$item.remove();
					self.updateSectionsData();
				}
			});

			// Enable/disable section
			$(document).on('change', '.section-enabled-checkbox', function() {
				self.updateSectionsData();
			});

			// Update on field change
			$(document).on('change keyup', '.section-field', function() {
				self.updateSectionsData();
			});

			// CTA Media Type Toggle
			$(document).on('change', '.cta-media-type', function() {
				var $item = $(this).closest('.section-item');
				var mediaType = $(this).val();
				
				if (mediaType === 'video') {
					$item.find('.image-field').hide();
					$item.find('.video-field').show();
				} else {
					$item.find('.image-field').show();
					$item.find('.video-field').hide();
				}
				
				self.updateSectionsData();
			});

			// Trending Topics Display Mode Toggle
			$(document).on('change', '.trending-display-mode', function() {
				var $item = $(this).closest('.section-item');
				var displayMode = $(this).val();
				
				if (displayMode === 'selected') {
					$item.find('.selected-categories-field').show();
				} else {
					$item.find('.selected-categories-field').hide();
				}
				
				self.updateSectionsData();
			});

			// Trending Topics Category Checkboxes
			$(document).on('change', '.trending-category-checkbox', function() {
				self.updateSectionsData();
			});

			// Image Upload Button
			$(document).on('click', '.upload-image-button', function(e) {
				e.preventDefault();
				var $button = $(this);
				var $input = $button.siblings('.cta-image');
				var $preview = $button.siblings('.image-preview');
				
				// Create WordPress media uploader
				var mediaUploader = wp.media({
					title: 'Select Image',
					button: {
						text: 'Use This Image'
					},
					multiple: false
				});
				
				mediaUploader.on('select', function() {
					var attachment = mediaUploader.state().get('selection').first().toJSON();
					$input.val(attachment.url).trigger('change');
					
					if ($preview.length) {
						$preview.find('img').attr('src', attachment.url);
					} else {
						$button.after('<div class="image-preview"><img src="' + attachment.url + '" style="max-width:200px;display:block;margin-top:10px;" /></div>');
					}
				});
				
				mediaUploader.open();
			});

			// Make sections sortable
			this.initSortable();
		},

		initSortable: function() {
			var self = this;

			$('.sections-list').sortable({
				handle: '.section-handle',
				placeholder: 'section-placeholder',
				items: '.section-item',
				tolerance: 'pointer',
				update: function() {
					self.updateSectionsData();
				}
			});
		},

		renderSections: function() {
			var sectionsData = $('.sections-data-input').val();
			var sections = [];

			try {
				sections = JSON.parse(sectionsData);
			} catch (e) {
				sections = [];
			}

			var $container = $('.sections-list');
			$container.empty();

			if (sections && sections.length) {
				sections.forEach(function(section, index) {
					var $sectionHtml = $(this.renderSectionItem(section, index));
					$container.append($sectionHtml);
				}.bind(this));
			}

			this.initSortable();
		},

		renderSectionItem: function(section, index) {
			var type = newslunarSectionTypes.types[section.type];
			if (!type) return '';

			var enabled = section.enabled !== false;
			var title = section.title || type.label;
			
			// Get collapsed state from localStorage or default (first section expanded, rest collapsed)
			var storageKey = 'newslunar_section_collapsed_' + section.id;
			var isCollapsed = localStorage.getItem(storageKey);
			
			// If no stored preference, collapse all except first section
			if (isCollapsed === null) {
				isCollapsed = (typeof index !== 'undefined' && index > 0);
			} else {
				isCollapsed = isCollapsed === 'true';
			}

			var html = '<div class="section-item' + (enabled ? '' : ' section-disabled') + (isCollapsed ? ' collapsed' : '') + '" data-id="' + section.id + '" data-type="' + section.type + '">';
			html += '  <div class="section-header">';
			html += '    <span class="section-handle dashicons dashicons-move"></span>';
			html += '    <span class="section-icon dashicons ' + type.icon + '"></span>';
			html += '    <span class="section-title-display">' + this.escapeHtml(title) + '</span>';
			html += '    <div class="section-actions">';
			html += '      <button type="button" class="section-toggle" title="' + newslunarSectionTypes.i18n.toggle + '">';
			html += '        <span class="dashicons dashicons-arrow-up-alt2"></span>';
			html += '      </button>';
			html += '      <button type="button" class="section-remove" title="' + newslunarSectionTypes.i18n.remove + '">';
			html += '        <span class="dashicons dashicons-trash"></span>';
			html += '      </button>';
			html += '    </div>';
			html += '  </div>';

			html += '  <div class="section-content">';
			
			// Enabled checkbox
			html += '    <label class="section-control">';
			html += '      <input type="checkbox" class="section-enabled-checkbox" ' + (enabled ? 'checked' : '') + ' />';
			html += '      <span>' + newslunarSectionTypes.i18n.enabled + '</span>';
			html += '    </label>';

			// Title field
			html += '    <label class="section-control">';
			html += '      <span class="section-label">' + newslunarSectionTypes.i18n.title + '</span>';
			html += '      <input type="text" class="section-field section-title" value="' + this.escapeHtml(title) + '" />';
			html += '    </label>';

			// Special handling for Main Banner with 3 panels
			if (type.has_panels && section.type === 'main_banner') {
				html += this.renderMainBannerPanels(section);
			} else if (type.has_cta_fields && section.type === 'cta_section') {
				// CTA Section Fields
				html += this.renderCTASectionFields(section);
			} else if (type.has_trending_config && section.type === 'trending_topics') {
				// Trending Topics Fields
				html += this.renderTrendingTopicsFields(section);
			} else if (type.has_marquee_config && section.type === 'marquee_strip') {
				// Marquee Strip Fields
				html += this.renderMarqueeStripFields(section);
			} else if (type.has_layout_options && section.type === 'full_width_widget') {
				// Layout Options Fields
				html += this.renderLayoutOptionsFields(section);
			} else {
				// Regular section fields
				if (type.has_category) {
					html += this.renderCategoryField(section.category || 0);
				}

				if (type.has_count) {
					// Set different default counts for different section types
					var defaultCount = (section.type === 'must_read' || section.type === 'in_case_missed') ? 3 : 4;
					html += this.renderCountField(section.count || defaultCount);
				}

				if (type.has_offset) {
					html += this.renderOffsetField(section.offset || 0);
				}
			}

			html += '  </div>';
			html += '</div>';

			return html;
		},

		renderMainBannerPanels: function(section) {
			var html = '<div class="main-banner-panels">';
			
			// Panel 1: Slider
			html += '<div class="panel-group">';
			html += '  <h4 class="united-panel-title">Panel 1: Slider (4 Posts)</h4>';
			html += '  <label class="section-control">';
			html += '    <span class="section-label">Slider Title</span>';
			html += '    <input type="text" class="section-field panel1-title" value="' + this.escapeHtml(section.panel1_title || 'Main Story') + '" />';
			html += '  </label>';
			html += this.renderCategoryField(section.panel1_category || 0, 'panel1-category');
			html += this.renderOffsetField(section.panel1_offset || 0, 'panel1-offset');
			html += '</div>';

			// Panel 2: Metro Style (FIXED AT 2 POSTS)
			html += '<div class="panel-group">';
			html += '  <h4 class="united-panel-title">Panel 2: Metro Style</h4>';
			html += '  <label class="section-control">';
			html += '    <span class="section-label">Metro Title</span>';
			html += '    <input type="text" class="section-field panel2-title" value="' + this.escapeHtml(section.panel2_title || "Editor\'s Picks") + '" />';
			html += '  </label>';
			html += this.renderCategoryField(section.panel2_category || 0, 'panel2-category');
			html += this.renderOffsetField(section.panel2_offset || 0, 'panel2-offset');
			html += '</div>';

			// Panel 3: List Style
			html += '<div class="panel-group">';
			html += '  <h4 class="united-panel-title">Panel 3: List Style</h4>';
			html += '  <label class="section-control">';
			html += '    <span class="section-label">List Title</span>';
			html += '    <input type="text" class="section-field panel3-title" value="' + this.escapeHtml(section.panel3_title || 'Trending Story') + '" />';
			html += '  </label>';
			html += this.renderCategoryField(section.panel3_category || 0, 'panel3-category');
			html += this.renderCountField(section.panel3_count || 5, 'panel3-count');
			html += this.renderOffsetField(section.panel3_offset || 0, 'panel3-offset');
			html += '</div>';

			html += '</div>';
			return html;
		},

		renderCTASectionFields: function(section) {
			var html = '<div class="cta-section-fields">';
			
			// Media Type
			html += '<label class="section-control">';
			html += '  <span class="section-label">Media Type</span>';
			html += '  <select class="section-field cta-media-type">';
			html += '    <option value="image"' + ((section.cta_media_type || 'image') === 'image' ? ' selected' : '') + '>Image</option>';
			html += '    <option value="video"' + (section.cta_media_type === 'video' ? ' selected' : '') + '>YouTube Video</option>';
			html += '  </select>';
			html += '</label>';
			
			// Image Upload
			html += '<div class="media-field image-field" style="' + (section.cta_media_type === 'video' ? 'display:none;' : '') + '">';
			html += '  <label class="section-control">';
			html += '    <span class="section-label">Image</span>';
			html += '    <div class="image-upload-wrapper">';
			html += '      <input type="hidden" class="section-field cta-image" value="' + this.escapeHtml(section.cta_image || '') + '" />';
			html += '      <button type="button" class="button upload-image-button">Select Image</button>';
			if (section.cta_image) {
				html += '      <div class="image-preview"><img src="' + this.escapeHtml(section.cta_image) + '" style="max-width:200px;display:block;margin-top:10px;" /></div>';
			}
			html += '    </div>';
			html += '  </label>';
			html += '</div>';
			
			// Video URL
			html += '<div class="media-field video-field" style="' + (section.cta_media_type === 'video' ? '' : 'display:none;') + '">';
			html += '  <label class="section-control">';
			html += '    <span class="section-label">Video URL</span>';
			html += '    <input type="url" class="section-field cta-video-url" placeholder="https://www.youtube.com/watch?v=... or direct .mp4 URL" value="' + this.escapeHtml(section.cta_video_url || '') + '" />';
			html += '    <span class="description" style="font-size:12px;color:#666;">YouTube URL or direct video file (.mp4, .webm). Direct files support background autoplay.</span>';
			html += '  </label>';
			html += '</div>';
			
			// CTA Title
			html += '<label class="section-control">';
			html += '  <span class="section-label">CTA Heading</span>';
			html += '  <input type="text" class="section-field cta-title" value="' + this.escapeHtml(section.cta_title || '') + '" />';
			html += '</label>';
			
			// Description
			html += '<label class="section-control">';
			html += '  <span class="section-label">Description</span>';
			html += '  <textarea class="section-field cta-description" rows="4">' + this.escapeHtml(section.cta_description || '') + '</textarea>';
			html += '</label>';
			
			// Button Label
			html += '<label class="section-control">';
			html += '  <span class="section-label">Button Label</span>';
			html += '  <input type="text" class="section-field cta-button-label" value="' + this.escapeHtml(section.cta_button_label || 'Learn More') + '" />';
			html += '</label>';
			
			// Button URL
			html += '<label class="section-control">';
			html += '  <span class="section-label">Button URL</span>';
			html += '  <input type="url" class="section-field cta-button-url" placeholder="https://" value="' + this.escapeHtml(section.cta_button_url || '') + '" />';
			html += '</label>';
			
			// Layout Options
			html += '<div class="cta-layout-options" style="border-top:1px solid #ddd;padding-top:15px;margin-top:15px;">';
			html += '  <h4 style="margin:0 0 10px 0;font-size:13px;font-weight:600;">Layout Options</h4>';
			
			// Reverse Order
			html += '  <label class="section-control">';
			html += '    <input type="checkbox" class="section-field cta-reverse-order"' + (section.cta_reverse_order ? ' checked' : '') + ' /> ';
			html += '    <span>Reverse Order (Content | Media)</span>';
			html += '    <span class="description" style="display:block;font-size:12px;color:#666;margin-top:5px;">Switch position of content and media columns</span>';
			html += '  </label>';
			
			// Background Media
			html += '  <label class="section-control">';
			html += '    <input type="checkbox" class="section-field cta-background-media"' + (section.cta_background_media ? ' checked' : '') + ' /> ';
			html += '    <span>Use Media as Background</span>';
			html += '    <span class="description" style="display:block;font-size:12px;color:#666;margin-top:5px;">Display media as section background. Direct video files (.mp4, .webm) will auto-play muted and loop. YouTube videos use thumbnail as background.</span>';
			html += '  </label>';
			
			html += '</div>';
			
			html += '</div>';
			return html;
		},

		renderTrendingTopicsFields: function(section) {
			var html = '<div class="trending-topics-fields">';
			
			// Display Mode
			html += '<label class="section-control">';
			html += '  <span class="section-label">Display Mode</span>';
			html += '  <select class="section-field trending-display-mode">';
			html += '    <option value="recent"' + ((section.trending_display_mode || 'recent') === 'recent' ? ' selected' : '') + '>Recent Categories (with new posts)</option>';
			html += '    <option value="selected"' + (section.trending_display_mode === 'selected' ? ' selected' : '') + '>Selected Categories</option>';
			html += '  </select>';
			html += '</label>';
			
			// Number of Categories
			html += '<label class="section-control">';
			html += '  <span class="section-label">Number of Categories</span>';
			html += '  <input type="number" class="section-field trending-count" min="1" max="12" value="' + (section.trending_count || 6) + '" />';
			html += '  <span class="description" style="font-size:12px;color:#666;">Maximum 12 categories</span>';
			html += '</label>';
			
			// Selected Categories (only show when mode is 'selected')
			html += '<div class="selected-categories-field" style="' + (section.trending_display_mode === 'selected' ? '' : 'display:none;') + '">';
			html += '  <label class="section-control">';
			html += '    <span class="section-label">Select Categories</span>';
			html += '    <div class="categories-checkboxes" style="max-height:200px;overflow-y:auto;border:1px solid #ddd;padding:10px;background:#fff;">';
			
			// Add checkboxes for each category
			if (typeof newslunarCategories !== 'undefined') {
				var selectedCategories = section.trending_selected_categories || [];
				newslunarCategories.forEach(function(cat) {
					var isChecked = selectedCategories.indexOf(cat.id) !== -1;
					html += '      <label style="display:block;margin-bottom:5px;">';
					html += '        <input type="checkbox" class="trending-category-checkbox" value="' + cat.id + '"' + (isChecked ? ' checked' : '') + ' /> ';
					html += '        ' + this.escapeHtml(cat.name);
					html += '      </label>';
				}.bind(this));
			}
			
			html += '    </div>';
			html += '    <span class="description" style="font-size:12px;color:#666;">Select which categories to display</span>';
			html += '  </label>';
			html += '</div>';
			
			html += '</div>';
			return html;
		},

		renderMarqueeStripFields: function(section) {
			var html = '<div class="marquee-strip-fields">';
			
			// Standard fields (using existing methods)
			html += this.renderCategoryField(section.category || 0);
			html += this.renderCountField(section.count || 10);
			
			// Date Format
			html += '<label class="section-control">';
			html += '  <span class="section-label">Date Format</span>';
			html += '  <select class="section-field marquee-date-format">';
			html += '    <option value="relative"' + ((section.marquee_date_format || 'relative') === 'relative' ? ' selected' : '') + '>Relative (5 hours ago)</option>';
			html += '    <option value="regular"' + (section.marquee_date_format === 'regular' ? ' selected' : '') + '>Regular (December 15, 2024)</option>';
			html += '  </select>';
			html += '</label>';
			
			// Autoplay
			html += '<label class="section-control">';
			html += '  <input type="checkbox" class="section-field marquee-autoplay"' + (section.marquee_autoplay ? ' checked' : '') + ' /> ';
			html += '  <span>Enable Autoplay</span>';
			html += '  <span class="description" style="display:block;font-size:12px;color:#666;margin-top:5px;">Automatically scroll the marquee</span>';
			html += '</label>';
			
			// Speed
			html += '<label class="section-control">';
			html += '  <span class="section-label">Speed</span>';
			html += '  <input type="range" class="section-field marquee-speed" min="1" max="10" value="' + (section.marquee_speed || 5) + '" />';
			html += '  <div style="display:flex;justify-content:space-between;font-size:12px;color:#666;margin-top:5px;">';
			html += '    <span>Slow</span><span>Fast</span>';
			html += '  </div>';
			html += '</label>';
			
			// Direction (RTL Support)
			html += '<label class="section-control">';
			html += '  <span class="section-label">Direction</span>';
			html += '  <select class="section-field marquee-direction">';
			html += '    <option value="ltr"' + ((section.marquee_direction || 'ltr') === 'ltr' ? ' selected' : '') + '>Left to Right</option>';
			html += '    <option value="rtl"' + (section.marquee_direction === 'rtl' ? ' selected' : '') + '>Right to Left</option>';
			html += '  </select>';
			html += '</label>';
			
			html += '</div>';
			return html;
		},

		renderCategoryField: function(categoryValue, customClass) {
			var fieldClass = customClass || 'section-category';
			var html = '<label class="section-control">';
			html += '  <span class="section-label">' + newslunarSectionTypes.i18n.category + '</span>';
			html += '  <select class="section-field ' + fieldClass + '">';
			html += '    <option value="0"' + (categoryValue == 0 ? ' selected' : '') + '>' + newslunarSectionTypes.i18n.allCategories + '</option>';
			
			if (typeof newslunarCategories !== 'undefined') {
				newslunarCategories.forEach(function(cat) {
					html += '<option value="' + cat.id + '"' + (categoryValue == cat.id ? ' selected' : '') + '>' + this.escapeHtml(cat.name) + '</option>';
				}.bind(this));
			}
			
			html += '  </select>';
			html += '</label>';
			return html;
		},

		renderCountField: function(countValue, customClass) {
			var fieldClass = customClass || 'section-count';
			var html = '<label class="section-control">';
			html += '  <span class="section-label">' + newslunarSectionTypes.i18n.postCount + '</span>';
			html += '  <input type="number" class="section-field ' + fieldClass + '" min="1" max="50" value="' + countValue + '" />';
			html += '</label>';
			return html;
		},

		renderOffsetField: function(offsetValue, customClass) {
			var fieldClass = customClass || 'section-offset';
			var html = '<label class="section-control">';
			html += '  <span class="section-label">' + newslunarSectionTypes.i18n.postOffset + '</span>';
			html += '  <input type="number" class="section-field ' + fieldClass + '" min="0" max="100" value="' + offsetValue + '" />';
			html += '</label>';
			return html;
		},

		renderLayoutOptionsFields: function(section) {
			var html = '<div class="layout-options-fields">';
			
			// Layout Type Selection
			html += '<h4 class="section-group-title">Layout Options</h4>';
			html += '<label class="section-control">';
			html += '  <span class="section-label">Layout Type</span>';
			html += '  <select class="section-field layout-type">';
			
			var layoutValue = section.layout_type || 'default';
			html += '    <option value="default"' + (layoutValue === 'default' ? ' selected' : '') + '>Default Layout - Full Width</option>';
			html += '    <option value="main_sidebar"' + (layoutValue === 'main_sidebar' ? ' selected' : '') + '>Main Area + Sidebar</option>';
			html += '    <option value="sidebar_main"' + (layoutValue === 'sidebar_main' ? ' selected' : '') + '>Sidebar + Main Area</option>';
			html += '  </select>';
			html += '</label>';
			
			html += '</div>';
			return html;
		},

		showSectionTypeSelector: function() {
			// Remove existing selector if any
			$('.section-type-overlay').remove();

			// Get template
			var template = $('#tmpl-homepage-section-types').html();
			
			if (!template) {
				console.error('Section type template not found');
				return;
			}
			
			// Create overlay
			var $overlay = $('<div class="section-type-overlay"></div>');
			var $selector = $(template);
			
			$overlay.append($selector);
			
			// Append to Customizer controls or body
			if ($('#customize-controls').length) {
				$('#customize-controls').append($overlay);
			} else {
				$('body').append($overlay);
			}

			// Close on overlay click
			$overlay.on('click', function(e) {
				if ($(e.target).hasClass('section-type-overlay')) {
					$overlay.remove();
				}
			});
			
			// Close on escape key
			$(document).on('keyup.section-overlay', function(e) {
				if (e.keyCode === 27) { // ESC key
					$overlay.remove();
					$(document).off('keyup.section-overlay');
				}
			});
		},

		addSection: function(type) {
			var typeConfig = newslunarSectionTypes.types[type];
			if (!typeConfig) return;

			// Check if section is non-repeatable and already exists
			if (!typeConfig.repeatable) {
				var exists = $('.section-item[data-type="' + type + '"]').length > 0;
				if (exists) {
					alert('This section type can only be added once.');
					return;
				}
			}

			var newSection = {
				id: 'section_' + Date.now(),
				type: type,
				enabled: true,
				title: typeConfig.label
			};

			// Add default values for Main Banner panels
			if (typeConfig.has_panels && type === 'main_banner') {
				newSection.panel1_title = 'Main Story';
				newSection.panel1_category = 0;
				newSection.panel1_offset = 0;
				
				newSection.panel2_title = "Editor's Picks";
				newSection.panel2_category = 0;
				newSection.panel2_count = 2;
				newSection.panel2_offset = 0;
				
				newSection.panel3_title = 'Trending Story';
				newSection.panel3_category = 0;
				newSection.panel3_count = 5;
				newSection.panel3_offset = 0;
			} else if (typeConfig.has_cta_fields && type === 'cta_section') {
				// Add default values for CTA Section
				newSection.cta_media_type = 'image';
				newSection.cta_image = '';
				newSection.cta_video_url = '';
				newSection.cta_title = '';
				newSection.cta_description = '';
				newSection.cta_button_label = 'Learn More';
				newSection.cta_button_url = '';
				newSection.cta_reverse_order = false;
				newSection.cta_background_media = false;
			} else if (typeConfig.has_trending_config && type === 'trending_topics') {
				// Add default values for Trending Topics
				newSection.trending_display_mode = 'recent';
				newSection.trending_count = 6;
				newSection.trending_selected_categories = [];
			} else if (typeConfig.has_marquee_config && type === 'marquee_strip') {
				// Add default values for Marquee Strip
				newSection.marquee_date_format = 'relative';
				newSection.marquee_autoplay = true;
				newSection.marquee_speed = 5;
				newSection.marquee_direction = 'ltr';
			} else if (typeConfig.has_layout_options && type === 'full_width_widget') {
				// Add default values for Layout Options
				newSection.layout_type = 'default';
			} else {
				// Add default values based on type configuration
				if (typeConfig.has_category) {
					newSection.category = 0;
				}
				if (typeConfig.has_count) {
					// Set different default counts for different section types
					if (type === 'must_read' || type === 'in_case_missed') {
						newSection.count = 3;
					} else {
						newSection.count = 4;
					}
				}
				if (typeConfig.has_offset) {
					newSection.offset = 0;
				}
			}

			var $newItem = $(this.renderSectionItem(newSection));
			$('.sections-list').append($newItem);
			
			// New sections should start expanded (remove collapsed class if present)
			$newItem.removeClass('collapsed');
			
			this.updateSectionsData();
		},

		cleanupLocalStorage: function() {
			// Get current section IDs
			var currentSectionIds = [];
			$('.section-item').each(function() {
				currentSectionIds.push($(this).data('id'));
			});
			
			// Clean up localStorage for removed sections
			for (var i = 0; i < localStorage.length; i++) {
				var key = localStorage.key(i);
				if (key && key.startsWith('newslunar_section_collapsed_')) {
					var sectionId = key.replace('newslunar_section_collapsed_', '');
					if (currentSectionIds.indexOf(sectionId) === -1) {
						localStorage.removeItem(key);
						i--; // Decrement counter since we removed an item
					}
				}
			}
		},

		updateSectionsData: function() {
			var sections = [];

			$('.section-item').each(function() {
				var $item = $(this);
				var type = $item.data('type');
				var typeConfig = newslunarSectionTypes.types[type];

				var section = {
					id: $item.data('id'),
					type: type,
					enabled: $item.find('.section-enabled-checkbox').is(':checked'),
					title: $item.find('.section-title').val()
				};

				// Handle Main Banner panels
				if (typeConfig.has_panels && type === 'main_banner') {
					section.panel1_title = $item.find('.panel1-title').val();
					section.panel1_category = parseInt($item.find('.panel1-category').val()) || 0;
					section.panel1_offset = parseInt($item.find('.panel1-offset').val()) || 0;
					
					section.panel2_title = $item.find('.panel2-title').val();
					section.panel2_category = parseInt($item.find('.panel2-category').val()) || 0;
					section.panel2_count = 2;
					section.panel2_offset = parseInt($item.find('.panel2-offset').val()) || 0;
					
					section.panel3_title = $item.find('.panel3-title').val();
					section.panel3_category = parseInt($item.find('.panel3-category').val()) || 0;
					section.panel3_count = parseInt($item.find('.panel3-count').val()) || 5;
					section.panel3_offset = parseInt($item.find('.panel3-offset').val()) || 0;
				} else if (typeConfig.has_cta_fields && type === 'cta_section') {
					// Handle CTA Section fields
					section.cta_media_type = $item.find('.cta-media-type').val() || 'image';
					section.cta_image = $item.find('.cta-image').val() || '';
					section.cta_video_url = $item.find('.cta-video-url').val() || '';
					section.cta_title = $item.find('.cta-title').val() || '';
					section.cta_description = $item.find('.cta-description').val() || '';
					section.cta_button_label = $item.find('.cta-button-label').val() || 'Learn More';
					section.cta_button_url = $item.find('.cta-button-url').val() || '';
					section.cta_reverse_order = $item.find('.cta-reverse-order').is(':checked');
					section.cta_background_media = $item.find('.cta-background-media').is(':checked');
				} else if (typeConfig.has_trending_config && type === 'trending_topics') {
					// Handle Trending Topics fields
					section.trending_display_mode = $item.find('.trending-display-mode').val() || 'recent';
					section.trending_count = parseInt($item.find('.trending-count').val()) || 6;
					
					// Collect selected categories
					var selectedCategories = [];
					$item.find('.trending-category-checkbox:checked').each(function() {
						selectedCategories.push(parseInt($(this).val()));
					});
					section.trending_selected_categories = selectedCategories;
				} else if (typeConfig.has_marquee_config && type === 'marquee_strip') {
					// Handle Marquee Strip fields
					section.marquee_date_format = $item.find('.marquee-date-format').val() || 'relative';
					section.marquee_autoplay = $item.find('.marquee-autoplay').is(':checked');
					section.marquee_speed = parseInt($item.find('.marquee-speed').val()) || 5;
					section.marquee_direction = $item.find('.marquee-direction').val() || 'ltr';
				} else if (typeConfig.has_layout_options && type === 'full_width_widget') {
					// Handle Layout Options fields
					section.layout_type = $item.find('.layout-type').val() || 'default';
				} else {
					// Add fields based on type configuration
					if (typeConfig.has_category) {
						section.category = parseInt($item.find('.section-category').val()) || 0;
					}
					if (typeConfig.has_count) {
						section.count = parseInt($item.find('.section-count').val()) || 5;
					}
					if (typeConfig.has_offset) {
						section.offset = parseInt($item.find('.section-offset').val()) || 0;
					}
				}

				sections.push(section);
			});

			var jsonData = JSON.stringify(sections);
			$('.sections-data-input').val(jsonData).trigger('change');
			
			// Clean up orphaned localStorage entries
			this.cleanupLocalStorage();
		},

		escapeHtml: function(text) {
			var map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};
			return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
		}
	};

	// Initialize when customizer is ready
	wp.customize.bind('ready', function() {
		HomepageSectionsManager.init();
	});

})(jQuery);
