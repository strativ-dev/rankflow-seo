/**
 * Meta box functionality with Tabs and Analysis
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/assets/js
 */

(function ($) {
	'use strict';

	/**
	 * Meta Box Handler
	 */
	class MetaBoxHandler {
		constructor() {
			this.generating = false;
			this.init();
		}

		init() {
			// Tab switching
			$('.rankflow-seo-tab-btn').on('click', (e) => this.switchTab(e));

			// Accordion toggle
			$('.accordion-toggle').on('click', (e) => this.toggleAccordion(e));

			// Generate button click
			$('#rankflow_seo_generate_now').on('click', (e) => this.generateMeta(e));

			// Auto-generate toggle
			$('#rankflow_seo_auto_generate').on('change', (e) => this.handleAutoGenerateToggle(e));

			// Update preview on input
			$('#rankflow_seo_title, #rankflow_seo_description').on('input', () => this.updatePreview());

			// Focus keyword analysis
			$('#rankflow_seo_focus_keyword').on('blur', () => this.analyzeFocusKeyword());

			// Real-time analysis on content change
			this.initRealTimeAnalysis();

			// Initial preview update
			this.updatePreview();
		}

		/**
		 * Switch between tabs
		 */
		switchTab(e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			const tabId = button.data('tab');

			// Update button states
			$('.rankflow-seo-tab-btn').removeClass('active');
			button.addClass('active');

			// Update content states
			$('.rankflow-seo-tab-content').removeClass('active');
			$(`.rankflow-seo-tab-content[data-tab="${tabId}"]`).addClass('active');
		}

		/**
		 * Toggle accordion
		 */
		toggleAccordion(e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			const accordionId = button.data('accordion');
			const content = $(`#${accordionId}`);

			button.toggleClass('active');

			// Update icon direction
			const icon = button.find('.toggle-icon');
			if (button.hasClass('active')) {
				icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
			} else {
				icon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
			}

			content.slideToggle(300);
		}

		/**
		 * Get editor content - Works with all WordPress editors
		 */
		getEditorContent() {
			let content = '';

			// Try Gutenberg (Block Editor) first
			if (wp && wp.data && wp.data.select('core/editor')) {
				try {
					content = wp.data.select('core/editor').getEditedPostContent();
					if (content) {
						return content;
					}
				} catch (e) {
					// Gutenberg not available, fall through to Classic Editor.
				}
			}

			// Try Classic Editor (TinyMCE)
			if (typeof tinymce !== 'undefined') {
				if (tinymce.activeEditor && tinymce.activeEditor.getContent) {
					content = tinymce.activeEditor.getContent();
					if (content) {
						return content;
					}
				}

				const editor = tinymce.get('content');
				if (editor && editor.getContent) {
					content = editor.getContent();
					if (content) {
						return content;
					}
				}
			}

			// Fallback to textarea
			content = $('#content').val();
			if (content) {
				return content;
			}

			content = $('#wp-content-editor-container textarea').val();
			return content || '';
		}

		/**
		 * Get post title
		 */
		getPostTitle() {
			// Try Gutenberg
			if (wp && wp.data && wp.data.select('core/editor')) {
				try {
					const title = wp.data.select('core/editor').getEditedPostAttribute('title');
					if (title) return title;
				} catch (e) { }
			}

			// Fallback to classic editor
			return $('#title').val() || '';
		}

		/**
		 * Generate meta tags with AI
		 */
		generateMeta(e) {
			e.preventDefault();

			if (this.generating) {
				return;
			}

			// Get checkbox values
			const generateTitle = $('#ai_generate_title').is(':checked');
			const generateDescription = $('#ai_generate_description').is(':checked');
			const generateKeywords = $('#ai_generate_keywords').is(':checked');

			// Validate at least one is checked
			if (!generateTitle && !generateDescription && !generateKeywords) {
				this.showNotice('Please select at least one field to generate.', 'error');
				return;
			}

			const button = $('#rankflow_seo_generate_now');
			const spinner = button.next('.spinner');

			const title = this.getPostTitle();
			const content = this.getEditorContent();

			if (!title) {
				this.showNotice('Please add a title first.', 'error');
				return;
			}

			if (!content || content.trim().length < 10) {
				this.showNotice('Please add content first. Content should be at least 10 characters.', 'error');
				return;
			}

			// Confirm if fields already have content
			let confirmNeeded = false;
			let fieldsToReplace = [];

			if (generateTitle && $('#rankflow_seo_title').val()) {
				confirmNeeded = true;
				fieldsToReplace.push('title');
			}
			if (generateDescription && $('#rankflow_seo_description').val()) {
				confirmNeeded = true;
				fieldsToReplace.push('description');
			}
			if (generateKeywords && $('#rankflow_seo_keywords').val()) {
				confirmNeeded = true;
				fieldsToReplace.push('keywords');
			}

			if (confirmNeeded) {
				const fieldsText = fieldsToReplace.join(', ');
				if (!confirm(`This will replace existing ${fieldsText}. Continue?`)) {
					return;
				}
			}

			const focusKeyword = $('#rankflow_seo_focus_keyword').val();

			// Show loading state
			this.generating = true;
			button.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> ' + rankflowSeoData.strings.generating);
			spinner.addClass('is-active');

			// Make AJAX request
			$.ajax({
				url: rankflowSeoData.ajaxUrl,
				type: 'POST',
				data: {
					action: 'rankflow_seo_generate_meta',
					nonce: rankflowSeoData.nonce,
					post_id: rankflowSeoData.postId,
					title: title,
					content: content,
					focus_keyword: focusKeyword,
					generate_title: generateTitle,
					generate_description: generateDescription,
					generate_keywords: generateKeywords
				},
				success: (response) => {
					if (response.success) {
						const data = response.data.data || response.data;

						if (generateTitle && data.title) {
							$('#rankflow_seo_title').val(data.title);
						}
						if (generateDescription && data.description) {
							$('#rankflow_seo_description').val(data.description);
						}
						if (generateKeywords && data.keywords) {
							$('#rankflow_seo_keywords').val(data.keywords);
						}

						// Update character counters
						$('#rankflow_seo_title, #rankflow_seo_description').trigger('input');

						// Update preview
						this.updatePreview();

						// Trigger analysis update
						this.triggerAnalysisUpdate();

						const message = response.data.message || rankflowSeoData.strings.success;
						this.showNotice(message, 'success');
					} else {
						const errorMsg = response.data.message || response.data || rankflowSeoData.strings.error;
						this.showNotice(errorMsg, 'error');
					}
				},
				error: (xhr, status, error) => {
						this.showNotice('Error: ' + error, 'error');
				},
				complete: () => {
					this.generating = false;
					button.prop('disabled', false).html('<span class="dashicons dashicons-superhero"></span> Generate Now with AI');
					spinner.removeClass('is-active');
				}
			});
		}

		/**
		 * Handle auto-generate toggle
		 */
		handleAutoGenerateToggle(e) {
			const checked = $(e.target).is(':checked');
			const title = $('#rankflow_seo_title').val();

			if (checked && !title) {
				const confirmMsg = 'Auto-generate is enabled. Meta tags will be generated automatically when you save. Would you like to generate them now instead?';

				if (confirm(confirmMsg)) {
					$('#rankflow_seo_generate_now').click();
				}
			}
		}

		/**
		 * Update search preview
		 */
		updatePreview() {
			const title = $('#rankflow_seo_title').val() || this.getPostTitle() || 'Page Title';
			const description = $('#rankflow_seo_description').val() || 'Your page description will appear here...';

			$('.preview-title').text(title);
			$('.preview-description').text(description);
		}

		/**
		 * Analyze focus keyword
		 */
		analyzeFocusKeyword() {
			const keyword = $('#rankflow_seo_focus_keyword').val();
			if (!keyword) return;

			this.triggerAnalysisUpdate();
		}

		/**
		 * Initialize real-time analysis
		 */
		initRealTimeAnalysis() {
			// Debounce function
			const debounce = (func, wait) => {
				let timeout;
				return function executedFunction(...args) {
					const later = () => {
						clearTimeout(timeout);
						func(...args);
					};
					clearTimeout(timeout);
					timeout = setTimeout(later, wait);
				};
			};

			// Listen for content changes
			const debouncedAnalysis = debounce(() => this.triggerAnalysisUpdate(), 1500);

			// For Gutenberg
			if (wp && wp.data && wp.data.subscribe) {
				let previousContent = '';
				wp.data.subscribe(() => {
					const currentContent = this.getEditorContent();
					if (currentContent !== previousContent) {
						previousContent = currentContent;
						debouncedAnalysis();
					}
				});
			}

			// For Classic Editor
			if (typeof tinymce !== 'undefined') {
				$(document).on('tinymce-editor-init', (event, editor) => {
					editor.on('keyup change', debouncedAnalysis);
				});
			}

			// For focus keyword and meta fields
			$('#rankflow_seo_focus_keyword, #rankflow_seo_title, #rankflow_seo_description').on('input', debouncedAnalysis);
		}

		/**
		 * Trigger analysis update via AJAX
		 */
		triggerAnalysisUpdate() {
			const postId = rankflowSeoData.postId;
			const focusKeyword = $('#rankflow_seo_focus_keyword').val();
			const content = this.getEditorContent();
			const title = this.getPostTitle();
			const metaTitle = $('#rankflow_seo_title').val();
			const metaDescription = $('#rankflow_seo_description').val();
			const slug = $('input[name="post_name"]').val() || '';

			if (!postId) return;

			$.ajax({
				url: rankflowSeoData.ajaxUrl,
				type: 'POST',
				data: {
					action: 'rankflow_seo_update_analysis',
					nonce: rankflowSeoData.nonce,
					post_id: postId,
					focus_keyword: focusKeyword,
					content: content,
					title: title,
					meta_title: metaTitle,
					meta_description: metaDescription,
					slug: slug
				},
				success: (response) => {
					if (response.success) {
						this.updateAnalysisDisplay(response.data);
					}
				}
			});
		}

		/**
		 * Update analysis display
		 */
		updateAnalysisDisplay(data) {
			// Update SEO Analysis
			if (data.seo_analysis) {
				this.renderAnalysisSection('#seo-analysis', data.seo_analysis);
				this.updateTabBadge('seo', data.seo_analysis);
				this.updateAccordionSummary('seo-analysis', data.seo_analysis);
			}

			// Update Readability Analysis
			if (data.readability_analysis) {
				this.renderAnalysisSection('#readability-analysis', data.readability_analysis);
				this.updateTabBadge('readability', data.readability_analysis);
				this.updateAccordionSummary('readability-analysis', data.readability_analysis);

				// Update readability score
				if (data.readability_analysis.flesch_score !== undefined) {
					this.updateReadabilityScore(data.readability_analysis);
				}
			}

			// Update SEO Score if provided
			if (data.seo_score !== undefined) {
				this.updateSeoScore(data.seo_score);
			}
		}

		/**
		 * Render analysis section
		 */
		renderAnalysisSection(selector, analysis) {
			const container = $(selector);
			let html = '';

			// Problems
			if (analysis.problems && analysis.problems.length > 0) {
				html += '<div class="analysis-group problems-group">';
				html += `<h5 class="group-title"><span class="dashicons dashicons-warning"></span>Problems (${analysis.problems.length})</h5>`;
				html += '<ul class="analysis-list">';
				analysis.problems.forEach(item => {
					html += `<li class="analysis-item problem">
						<span class="item-indicator"></span>
						<span class="item-title">${this.escapeHtml(item.title)}:</span>
						<span class="item-message">${this.escapeHtml(item.message)}</span>
					</li>`;
				});
				html += '</ul></div>';
			}

			// Good results
			if (analysis.good && analysis.good.length > 0) {
				html += '<div class="analysis-group good-group">';
				html += `<h5 class="group-title"><span class="dashicons dashicons-yes-alt"></span>Good results (${analysis.good.length})</h5>`;
				html += '<ul class="analysis-list">';
				analysis.good.forEach(item => {
					html += `<li class="analysis-item good">
						<span class="item-indicator"></span>
						<span class="item-title">${this.escapeHtml(item.title)}:</span>
						<span class="item-message">${this.escapeHtml(item.message)}</span>
					</li>`;
				});
				html += '</ul></div>';
			}

			if (!html) {
				html = '<p class="no-analysis">Enter a focus keyphrase and content to see analysis.</p>';
			}

			container.html(html);
		}

		/**
		 * Update tab badge
		 */
		updateTabBadge(tabName, analysis) {
			const tab = $(`.rankflow-seo-tab-btn[data-tab="${tabName}"]`);
			tab.find('.tab-badge').remove();

			const problemCount = analysis.problems ? analysis.problems.length : 0;
			if (problemCount > 0) {
				tab.append(`<span class="tab-badge badge-problems">${problemCount}</span>`);
			}
		}

		/**
		 * Update accordion summary
		 */
		updateAccordionSummary(accordionId, analysis) {
			const toggle = $(`.accordion-toggle[data-accordion="${accordionId}"]`);
			const summary = toggle.find('.analysis-summary');

			let html = '';
			const problems = analysis.problems ? analysis.problems.length : 0;
			const good = analysis.good ? analysis.good.length : 0;

			if (problems > 0) {
				html += `<span class="summary-problems">${problems} problem${problems > 1 ? 's' : ''}</span>`;
			}
			if (good > 0) {
				html += `<span class="summary-good">${good} good result${good > 1 ? 's' : ''}</span>`;
			}

			summary.html(html);
		}

		/**
		 * Update SEO score display
		 */
		updateSeoScore(score) {
			const status = this.getScoreStatus(score);
			const section = $('.seo-score-section');

			section.find('.score-number').text(score);
			section.find('.score-circle').css('border-color', status.color);
			section.find('.score-status').text(status.label).css('color', status.color);
		}

		/**
		 * Update readability score display
		 */
		updateReadabilityScore(analysis) {
			const score = Math.round(analysis.flesch_score);
			const status = this.getReadabilityStatus(score);
			const section = $('.readability-score-section');

			section.find('.score-number').text(score);
			section.find('.score-circle').css('border-color', status.color);
			section.find('.score-status').text(status.label).css('color', status.color);

			if (analysis.grade_level) {
				section.find('.grade-level').text(analysis.grade_level);
			}
		}

		/**
		 * Get score status
		 */
		getScoreStatus(score) {
			if (score >= 80) {
				return { status: 'good', label: 'Excellent', color: '#46b450' };
			} else if (score >= 60) {
				return { status: 'ok', label: 'Good', color: '#ffb900' };
			} else if (score >= 40) {
				return { status: 'needs_improvement', label: 'Needs Improvement', color: '#f56e28' };
			} else {
				return { status: 'poor', label: 'Poor', color: '#dc3232' };
			}
		}

		/**
		 * Get readability status
		 */
		getReadabilityStatus(score) {
			if (score >= 60) {
				return { status: 'good', label: 'Good', color: '#46b450' };
			} else if (score >= 40) {
				return { status: 'ok', label: 'OK', color: '#ffb900' };
			} else {
				return { status: 'needs_improvement', label: 'Needs Improvement', color: '#dc3232' };
			}
		}

		/**
		 * Show notice
		 */
		showNotice(message, type) {
			$('.rankflow-seo-metabox .notice').remove();

			const notice = $('<div>')
				.addClass('notice notice-' + type + ' is-dismissible')
				.html('<p>' + message + '</p>')
				.css({
					'margin': '10px 0',
					'padding': '10px 15px'
				});

			$('.rankflow-seo-metabox').prepend(notice);

			if (type === 'success') {
				setTimeout(() => {
					notice.fadeOut(() => notice.remove());
				}, 5000);
			}
		}

		/**
		 * Escape HTML
		 */
		escapeHtml(text) {
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}
	}

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function () {
		if ($('.rankflow-seo-metabox').length) {
			new MetaBoxHandler();
					}
	});

})(jQuery);