/**
 * Content analyzer functionality
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/assets/js
 */

(function($) {
	'use strict';

	/**
	 * Content Analyzer Class
	 */
	class ContentAnalyzer {
		constructor() {
			this.analyzing = false;
		}

		/**
		 * Analyze content
		 */
		analyze(postId, content, focusKeyword) {
			if (this.analyzing) {
				return Promise.reject('Analysis already in progress');
			}

			this.analyzing = true;

			return $.ajax({
				url: aiSeoProData.ajaxUrl,
				type: 'POST',
				data: {
					action: 'ai_seo_analyze_content',
					nonce: aiSeoProData.nonce,
					post_id: postId,
					content: content,
					focus_keyword: focusKeyword
				}
			}).always(() => {
				this.analyzing = false;
			});
		}

		/**
		 * Count words in text
		 */
		countWords(text) {
			text = this.stripHtml(text);
			const words = text.match(/\b\w+\b/g);
			return words ? words.length : 0;
		}

		/**
		 * Calculate keyword density
		 */
		calculateKeywordDensity(text, keyword) {
			if (!keyword) return 0;

			text = this.stripHtml(text).toLowerCase();
			keyword = keyword.toLowerCase();

			const wordCount = this.countWords(text);
			const keywordCount = (text.match(new RegExp(keyword, 'g')) || []).length;

			return wordCount > 0 ? (keywordCount / wordCount * 100).toFixed(2) : 0;
		}

		/**
		 * Check keyword in headings
		 */
		checkKeywordInHeadings(content, keyword) {
			if (!keyword) return 0;

			const headings = content.match(/<h[1-6][^>]*>.*?<\/h[1-6]>/gi) || [];
			keyword = keyword.toLowerCase();

			let count = 0;
			headings.forEach(heading => {
				const text = this.stripHtml(heading).toLowerCase();
				if (text.indexOf(keyword) !== -1) {
					count++;
				}
			});

			return count;
		}

		/**
		 * Count images
		 */
		countImages(content) {
			const images = content.match(/<img[^>]+>/gi) || [];
			return images.length;
		}

		/**
		 * Check images with alt tags
		 */
		checkImageAltTags(content) {
			const images = content.match(/<img[^>]+>/gi) || [];
			let withAlt = 0;

			images.forEach(img => {
				if (/alt=["'][^"']*["']/i.test(img)) {
					withAlt++;
				}
			});

			return {
				total: images.length,
				withAlt: withAlt,
				withoutAlt: images.length - withAlt
			};
		}

		/**
		 * Count links
		 */
		countLinks(content) {
			const links = content.match(/<a[^>]+href=["']([^"']+)["']/gi) || [];
			const siteUrl = window.location.origin;

			let internal = 0;
			let external = 0;

			links.forEach(link => {
				const hrefMatch = link.match(/href=["']([^"']+)["']/i);
				if (hrefMatch) {
					const href = hrefMatch[1];
					if (href.indexOf(siteUrl) === 0 || href.indexOf('/') === 0) {
						internal++;
					} else if (href.indexOf('http') === 0) {
						external++;
					}
				}
			});

			return { internal, external, total: internal + external };
		}

		/**
		 * Strip HTML tags
		 */
		stripHtml(html) {
			const tmp = document.createElement('DIV');
			tmp.innerHTML = html;
			return tmp.textContent || tmp.innerText || '';
		}

		/**
		 * Get quick analysis (without AJAX)
		 */
		getQuickAnalysis(content, focusKeyword) {
			const wordCount = this.countWords(content);
			const keywordDensity = this.calculateKeywordDensity(content, focusKeyword);
			const keywordInHeadings = this.checkKeywordInHeadings(content, focusKeyword);
			const imageData = this.checkImageAltTags(content);
			const linkData = this.countLinks(content);

			return {
				wordCount,
				keywordDensity,
				keywordInHeadings,
				images: imageData,
				links: linkData
			};
		}
	}

	// Export for use in other modules
	window.AISeoPro = window.AISeoPro || {};
	window.AISeoPro.ContentAnalyzer = ContentAnalyzer;

})(jQuery);