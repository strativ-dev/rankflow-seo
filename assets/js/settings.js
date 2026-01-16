/**
 * Settings page functionality
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/assets/js
 */

(function ($) {
	'use strict';

	/**
	 * Settings Handler
	 */
	class SettingsHandler {
		constructor() {
			this.init();
		}

		init() {
			// API provider change
			$('#api_provider').on('change', (e) => this.handleProviderChange(e));

			// Toggle API key visibility
			$('#toggle_api_key').on('click', (e) => this.toggleApiKeyVisibility(e));

			// Reset settings
			$('#reset_settings').on('click', (e) => this.resetSettings(e));

			// Clear cache
			$('#clear_cache').on('click', (e) => this.clearCache(e));

			// Test API connection
			$('#test_api_connection').on('click', (e) => this.testApiConnection(e));

			// Show appropriate instructions
			this.updateApiInstructions();
		}

		/**
		 * Handle API provider change
		 */
		handleProviderChange(e) {
			this.updateApiInstructions();
		}

		/**
		 * Update API instructions based on selected provider
		 */
		updateApiInstructions() {
			const provider = $('#api_provider').val();

			$('.provider-instructions li').hide();
			$(`.provider-instructions li[data-provider="${provider}"]`).show();
		}

		/**
		 * Toggle API key visibility
		 */
		toggleApiKeyVisibility(e) {
			e.preventDefault();

			const input = $('#api_key');
			const button = $(e.currentTarget);

			if (input.attr('type') === 'password') {
				input.attr('type', 'text');
				button.text('Hide');
			} else {
				input.attr('type', 'password');
				button.text('Show');
			}
		}

		/**
		 * Reset settings
		 */
		resetSettings(e) {
			e.preventDefault();

			if (!confirm('Are you sure? This will reset all plugin settings to defaults. This action cannot be undone.')) {
				return;
			}

			const button = $(e.currentTarget);
			button.prop('disabled', true).text('Resetting...');

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'rankflow_seo_reset_settings',
					nonce: aiSeoProData.nonce
				},
				success: (response) => {
					if (response.success) {
						this.showNotice('Settings reset successfully!', 'success');
						setTimeout(() => {
							window.location.reload();
						}, 1500);
					} else {
						this.showNotice('Failed to reset settings.', 'error');
					}
				},
				error: () => {
					this.showNotice('An error occurred.', 'error');
				},
				complete: () => {
					button.prop('disabled', false).text('Reset All Settings');
				}
			});
		}

		/**
		 * Clear cache
		 */
		clearCache(e) {
			e.preventDefault();

			if (!confirm('Are you sure? This will clear all cached API responses.')) {
				return;
			}

			const button = $(e.currentTarget);
			button.prop('disabled', true).text('Clearing...');

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'rankflow_seo_clear_cache',
					nonce: aiSeoProData.nonce
				},
				success: (response) => {
					if (response.success) {
						this.showNotice('Cache cleared successfully!', 'success');
					} else {
						this.showNotice('Failed to clear cache.', 'error');
					}
				},
				error: () => {
					this.showNotice('An error occurred.', 'error');
				},
				complete: () => {
					button.prop('disabled', false).text('Clear All Caches');
				}
			});
		}

		/**
		 * Test API connection
		 */
		testApiConnection(e) {
			e.preventDefault();

			const apiKey = $('#api_key').val();
			const provider = $('#api_provider').val();

			if (!apiKey) {
				this.showNotice('Please enter an API key first.', 'warning');
				return;
			}

			const button = $(e.currentTarget);
			button.prop('disabled', true).text('Testing...');

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'rankflow_seo_test_api',
					nonce: aiSeoProData.nonce,
					api_key: apiKey,
					provider: provider
				},
				success: (response) => {
					if (response.success) {
						this.showNotice('API connection successful!', 'success');
					} else {
						this.showNotice('API connection failed: ' + response.data, 'error');
					}
				},
				error: () => {
					this.showNotice('An error occurred while testing the connection.', 'error');
				},
				complete: () => {
					button.prop('disabled', false).text('Test Connection');
				}
			});
		}

		/**
		 * Show notice
		 */
		showNotice(message, type) {
			const notice = $('<div>')
				.addClass('notice notice-' + type + ' is-dismissible')
				.html('<p>' + message + '</p>');

			$('.wrap h1').after(notice);

			// Auto dismiss after 5 seconds
			setTimeout(() => {
				notice.fadeOut(() => notice.remove());
			}, 5000);
		}
	}

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function () {
		if ($('.rankflow-seo-settings').length) {
			new SettingsHandler();
		}
	});

})(jQuery);