/**
 * Character counter functionality
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/assets/js
 */

(function ($) {
	'use strict';

	/**
	 * Character Counter Class
	 */
	class CharacterCounter {
		constructor(input, counterElement, maxLength) {
			this.input = $(input);
			this.counter = $(counterElement);
			this.maxLength = maxLength;

			this.init();
		}

		init() {
			// Update counter on input
			this.input.on('input', () => this.update());

			// Initial update
			this.update();
		}

		update() {
			const length = this.input.val().length;
			const remaining = this.maxLength - length;

			// Update text
			this.counter.text(`${length} / ${this.maxLength}`);

			// Update color based on length
			this.counter.removeClass('good warning error');

			if (length === 0) {
				this.counter.addClass('error');
			} else if (length < this.maxLength * 0.8) {
				this.counter.addClass('good');
			} else if (length <= this.maxLength) {
				this.counter.addClass('warning');
			} else {
				this.counter.addClass('error');
			}
		}

		getLength() {
			return this.input.val().length;
		}

		getStatus() {
			const length = this.getLength();

			if (length === 0) return 'empty';
			if (length < this.maxLength * 0.8) return 'good';
			if (length <= this.maxLength) return 'warning';
			return 'error';
		}
	}

	/**
	 * Initialize character counters
	 */
	function initCharacterCounters() {
		// Initialize all counters
		$('.character-counter').each(function () {
			const counter = $(this);
			const fieldId = counter.data('field');
			const maxLength = counter.data('max');
			const input = $('#' + fieldId);

			if (input.length) {
				new CharacterCounter(input, counter, maxLength);
			}
		});
	}

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function () {
		initCharacterCounters();
	});

	// Export for use in other modules
	window.RankflowSeo = window.RankflowSeo || {};
	window.RankflowSeo.CharacterCounter = CharacterCounter;

})(jQuery);