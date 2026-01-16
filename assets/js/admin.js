/**
 * Admin functionality
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/assets/js
 */

(function ($) {
	'use strict';

	/**
	 * Admin Handler
	 */
	class AdminHandler {
		constructor() {
			this.init();
		}

		init() {
			// Dismiss notices
			$(document).on('click', '.notice-dismiss', (e) => this.dismissNotice(e));

			// Confirm actions
			this.setupConfirmActions();

			// Tooltips
			this.initTooltips();

			// Collapsible sections
			this.initCollapsible();
		}

		/**
		 * Dismiss notice
		 */
		dismissNotice(e) {
			$(e.target).closest('.notice').fadeOut();
		}

		/**
		 * Setup confirm actions
		 */
		setupConfirmActions() {
			$('[data-confirm]').on('click', function (e) {
				const message = $(this).data('confirm');
				if (!confirm(message)) {
					e.preventDefault();
					return false;
				}
			});
		}

		/**
		 * Initialize tooltips
		 */
		initTooltips() {
			$('[data-tooltip]').each(function () {
				const tooltip = $('<span>')
					.addClass('ai-seo-tooltip')
					.text($(this).data('tooltip'));

				$(this).append(tooltip);
			});
		}

		/**
		 * Initialize collapsible sections
		 */
		initCollapsible() {
			$('.collapsible-header').on('click', function () {
				$(this).toggleClass('active');
				$(this).next('.collapsible-content').slideToggle();
			});
		}

		/**
		 * Show loading overlay
		 */
		showLoading(message = 'Processing...') {
			const overlay = $('<div>')
				.addClass('ai-seo-loading-overlay')
				.html(`
					<div class="loading-content">
						<div class="spinner is-active"></div>
						<p>${message}</p>
					</div>
				`);

			$('body').append(overlay);
		}

		/**
		 * Hide loading overlay
		 */
		hideLoading() {
			$('.ai-seo-loading-overlay').fadeOut(() => {
				$('.ai-seo-loading-overlay').remove();
			});
		}

		/**
		 * Copy to clipboard
		 */
		copyToClipboard(text) {
			const temp = $('<textarea>');
			$('body').append(temp);
			temp.val(text).select();
			document.execCommand('copy');
			temp.remove();

			this.showToast('Copied to clipboard!', 'success');
		}

		/**
		 * Show toast notification
		 */
		showToast(message, type = 'info') {
			const toast = $('<div>')
				.addClass('ai-seo-toast toast-' + type)
				.text(message);

			$('body').append(toast);

			setTimeout(() => {
				toast.addClass('show');
			}, 10);

			setTimeout(() => {
				toast.removeClass('show');
				setTimeout(() => toast.remove(), 300);
			}, 3000);
		}
	}

	/**
	 * Utility functions
	 */
	window.AISeoPro = window.AISeoPro || {};

	window.AISeoPro.Utils = {
		/**
		 * Format number with commas
		 */
		formatNumber: function (num) {
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		},

		/**
		 * Truncate text
		 */
		truncate: function (text, length, suffix = '...') {
			if (text.length <= length) return text;
			return text.substring(0, length) + suffix;
		},

		/**
		 * Debounce function
		 */
		debounce: function (func, wait) {
			let timeout;
			return function executedFunction(...args) {
				const later = () => {
					clearTimeout(timeout);
					func(...args);
				};
				clearTimeout(timeout);
				timeout = setTimeout(later, wait);
			};
		},

		/**
		 * Validate email
		 */
		validateEmail: function (email) {
			const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			return re.test(email);
		},

		/**
		 * Validate URL
		 */
		validateUrl: function (url) {
			try {
				new URL(url);
				return true;
			} catch (e) {
				return false;
			}
		}
	};

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function () {
		window.AISeoPro.Admin = new AdminHandler();
	});

})(jQuery);