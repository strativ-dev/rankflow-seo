<?php
/**
 * SEO Analyzer
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/includes/core
 * @author     Strativ AB
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class RankFlow_SEO_SEO_Analyzer
 */
class RankFlow_SEO_SEO_Analyzer
{

	/**
	 * Analyze SEO for a post.
	 *
	 * @param int    $post_id         Post ID.
	 * @param string $content         Post content.
	 * @param string $title           Post title.
	 * @param string $focus_keyword   Focus keyphrase.
	 * @param string $meta_title      Meta title.
	 * @param string $meta_description Meta description.
	 * @param string $slug            Post slug.
	 * @return array Analysis results with 'problems' and 'good' arrays.
	 */
	public function analyze($post_id, $content = '', $title = '', $focus_keyword = '', $meta_title = '', $meta_description = '', $slug = '')
	{
		$problems = array();
		$good = array();

		// Get post data if not provided.
		if (empty($content) || empty($title)) {
			$post = get_post($post_id);
			if ($post) {
				$content = empty($content) ? $post->post_content : $content;
				$title = empty($title) ? $post->post_title : $title;
				$slug = empty($slug) ? $post->post_name : $slug;
			}
		}

		// Get meta data if not provided.
		if (empty($meta_title)) {
			$meta_title = get_post_meta($post_id, '_rankflow_seo_title', true);
		}
		if (empty($meta_description)) {
			$meta_description = get_post_meta($post_id, '_rankflow_seo_description', true);
		}
		if (empty($focus_keyword)) {
			$focus_keyword = get_post_meta($post_id, '_rankflow_seo_focus_keyword', true);
		}

		$text = wp_strip_all_tags($content);
		$word_count = str_word_count($text);
		$keyword_lower = strtolower($focus_keyword);

		// ========================================
		// Focus Keyphrase Checks
		// ========================================

		// 1. Keyphrase length.
		if (empty($focus_keyword)) {
			$problems[] = array(
				'title' => __('Keyphrase length', 'rankflow-seo'),
				'message' => __('No focus keyphrase was set for this page. Set a keyphrase in order to calculate your SEO score.', 'rankflow-seo'),
			);
		} else {
			$keyword_word_count = str_word_count($focus_keyword);
			if ($keyword_word_count >= 1 && $keyword_word_count <= 4) {
				$good[] = array(
					'title' => __('Keyphrase length', 'rankflow-seo'),
					'message' => __('Good job!', 'rankflow-seo'),
				);
			} elseif ($keyword_word_count > 4) {
				$problems[] = array(
					'title' => __('Keyphrase length', 'rankflow-seo'),
					'message' => __('The keyphrase is over 4 words. Consider using a shorter keyphrase.', 'rankflow-seo'),
				);
			}
		}

		// 2. Keyphrase in SEO title.
		if (!empty($focus_keyword) && !empty($meta_title)) {
			if (false !== stripos($meta_title, $focus_keyword)) {
				// Check if it begins with the keyphrase.
				if (0 === stripos($meta_title, $focus_keyword)) {
					$good[] = array(
						'title' => __('Keyphrase in SEO title', 'rankflow-seo'),
						'message' => __('The SEO title begins with the focus keyphrase. Good job!', 'rankflow-seo'),
					);
				} else {
					$good[] = array(
						'title' => __('Keyphrase in SEO title', 'rankflow-seo'),
						'message' => __('The focus keyphrase appears in the SEO title. Good job!', 'rankflow-seo'),
					);
				}
			} else {
				$problems[] = array(
					'title' => __('Keyphrase in SEO title', 'rankflow-seo'),
					'message' => __('Please add both a keyphrase and an SEO title beginning with the keyphrase.', 'rankflow-seo'),
				);
			}
		} elseif (!empty($focus_keyword) && empty($meta_title)) {
			$problems[] = array(
				'title' => __('Keyphrase in SEO title', 'rankflow-seo'),
				'message' => __('Please add both a keyphrase and an SEO title beginning with the keyphrase.', 'rankflow-seo'),
			);
		}

		// 3. Keyphrase in meta description.
		if (!empty($focus_keyword) && !empty($meta_description)) {
			if (false !== stripos($meta_description, $focus_keyword)) {
				$good[] = array(
					'title' => __('Keyphrase in meta description', 'rankflow-seo'),
					'message' => __('The focus keyphrase appears in the meta description. Good job!', 'rankflow-seo'),
				);
			} else {
				$problems[] = array(
					'title' => __('Keyphrase in meta description', 'rankflow-seo'),
					'message' => __('Please add both a keyphrase and a meta description containing the keyphrase.', 'rankflow-seo'),
				);
			}
		} elseif (!empty($focus_keyword) && empty($meta_description)) {
			$problems[] = array(
				'title' => __('Keyphrase in meta description', 'rankflow-seo'),
				'message' => __('Please add both a keyphrase and a meta description containing the keyphrase.', 'rankflow-seo'),
			);
		}

		// 4. Keyphrase in slug.
		if (!empty($focus_keyword) && !empty($slug)) {
			$slug_check = str_replace('-', ' ', strtolower($slug));
			if (false !== stripos($slug_check, $keyword_lower)) {
				$good[] = array(
					'title' => __('Keyphrase in slug', 'rankflow-seo'),
					'message' => __('The focus keyphrase appears in the slug. Good job!', 'rankflow-seo'),
				);
			} else {
				$problems[] = array(
					'title' => __('Keyphrase in slug', 'rankflow-seo'),
					'message' => __('Please add both a keyphrase and a slug containing the keyphrase.', 'rankflow-seo'),
				);
			}
		} elseif (!empty($focus_keyword) && empty($slug)) {
			$problems[] = array(
				'title' => __('Keyphrase in slug', 'rankflow-seo'),
				'message' => __('Please add both a keyphrase and a slug containing the keyphrase.', 'rankflow-seo'),
			);
		}

		// 5. Keyphrase in introduction.
		if (!empty($focus_keyword) && !empty($content)) {
			$first_paragraph = $this->get_first_paragraph($content);
			if (false !== stripos($first_paragraph, $focus_keyword)) {
				$good[] = array(
					'title' => __('Keyphrase in introduction', 'rankflow-seo'),
					'message' => __('The focus keyphrase appears in the first paragraph. Good job!', 'rankflow-seo'),
				);
			} else {
				$problems[] = array(
					'title' => __('Keyphrase in introduction', 'rankflow-seo'),
					'message' => __('Please add both a keyphrase and an introduction containing the keyphrase.', 'rankflow-seo'),
				);
			}
		}

		// 6. Keyphrase density.
		if (!empty($focus_keyword) && $word_count > 0) {
			$keyword_count = substr_count(strtolower($text), $keyword_lower);
			$density = ($keyword_count / $word_count) * 100;

			if ($density >= 0.5 && $density <= 2.5) {
				$good[] = array(
					'title' => __('Keyphrase density', 'rankflow-seo'),
					'message' => sprintf(
						/* translators: %s: keyphrase density percentage */
						__('The focus keyphrase appears %s%% of the time. Good job!', 'rankflow-seo'),
						number_format($density, 1)
					),
				);
			} elseif ($density < 0.5) {
				$problems[] = array(
					'title' => __('Keyphrase density', 'rankflow-seo'),
					'message' => sprintf(
						/* translators: %s: keyphrase density percentage */
						__('The focus keyphrase appears %s%% of the time. Try to use it more often.', 'rankflow-seo'),
						number_format($density, 1)
					),
				);
			} else {
				$problems[] = array(
					'title' => __('Keyphrase density', 'rankflow-seo'),
					'message' => sprintf(
						/* translators: %s: keyphrase density percentage */
						__('The focus keyphrase appears %s%% of the time. This might be keyword stuffing.', 'rankflow-seo'),
						number_format($density, 1)
					),
				);
			}
		}

		// ========================================
		// Meta Data Checks
		// ========================================

		// 7. Meta description length.
		if (!empty($meta_description)) {
			$desc_length = strlen($meta_description);
			if ($desc_length >= 120 && $desc_length <= 160) {
				$good[] = array(
					'title' => __('Meta description length', 'rankflow-seo'),
					'message' => __('The meta description has an optimal length. Good job!', 'rankflow-seo'),
				);
			} elseif ($desc_length < 120) {
				$problems[] = array(
					'title' => __('Meta description length', 'rankflow-seo'),
					'message' => sprintf(
						/* translators: %d: meta description character count */
						__('The meta description is too short (%d characters). Use at least 120 characters.', 'rankflow-seo'),
						$desc_length
					),
				);
			} else {
				$problems[] = array(
					'title' => __('Meta description length', 'rankflow-seo'),
					'message' => sprintf(
						/* translators: %d: meta description character count */
						__('The meta description is too long (%d characters). Keep it under 160 characters.', 'rankflow-seo'),
						$desc_length
					),
				);
			}
		} else {
			$problems[] = array(
				'title' => __('Meta description length', 'rankflow-seo'),
				'message' => __('No meta description has been specified. Search engines will display copy from the page instead. Make sure to write one!', 'rankflow-seo'),
			);
		}

		// 8. SEO title width.
		if (!empty($meta_title)) {
			$title_length = strlen($meta_title);
			if ($title_length >= 50 && $title_length <= 60) {
				$good[] = array(
					'title' => __('SEO title width', 'rankflow-seo'),
					'message' => __('Good job!', 'rankflow-seo'),
				);
			} elseif ($title_length < 50) {
				$problems[] = array(
					'title' => __('SEO title width', 'rankflow-seo'),
					'message' => sprintf(
						/* translators: %d: SEO title character count */
						__('The SEO title is too short (%d characters). Use between 50-60 characters.', 'rankflow-seo'),
						$title_length
					),
				);
			} else {
				$problems[] = array(
					'title' => __('SEO title width', 'rankflow-seo'),
					'message' => sprintf(
						/* translators: %d: SEO title character count */
						__('The SEO title is too long (%d characters). Keep it under 60 characters.', 'rankflow-seo'),
						$title_length
					),
				);
			}
		}

		// ========================================
		// Content Checks
		// ========================================

		// 9. Text length.
		if ($word_count >= 300) {
			$good[] = array(
				'title' => __('Text length', 'rankflow-seo'),
				'message' => sprintf(
					/* translators: %d: word count */
					__('The text contains %d words. Good job!', 'rankflow-seo'),
					$word_count
				),
			);
		} else {
			$problems[] = array(
				'title' => __('Text length', 'rankflow-seo'),
				'message' => sprintf(
					/* translators: %d: word count */
					__('The text contains %d words. Add more content to improve SEO (recommended: 300+ words).', 'rankflow-seo'),
					$word_count
				),
			);
		}

		// 10. Single H1 title.
		$h1_count = preg_match_all('/<h1[^>]*>/i', $content, $matches);
		if (1 === $h1_count) {
			$good[] = array(
				'title' => __('Single title', 'rankflow-seo'),
				'message' => __("You don't have multiple H1 headings, well done!", 'rankflow-seo'),
			);
		} elseif (0 === $h1_count) {
			$problems[] = array(
				'title' => __('Single title', 'rankflow-seo'),
				'message' => __('No H1 heading found. Add one H1 heading to your content.', 'rankflow-seo'),
			);
		} else {
			$problems[] = array(
				'title' => __('Single title', 'rankflow-seo'),
				'message' => sprintf(
					/* translators: %d: number of H1 headings */
					__('You have %d H1 headings. Use only one H1 heading per page.', 'rankflow-seo'),
					$h1_count
				),
			);
		}

		// 11. Keyphrase in subheadings.
		if (!empty($focus_keyword)) {
			preg_match_all('/<h[2-6][^>]*>(.*?)<\/h[2-6]>/i', $content, $headings);
			$keyword_in_headings = 0;
			foreach ($headings[1] as $heading) {
				if (false !== stripos(wp_strip_all_tags($heading), $focus_keyword)) {
					$keyword_in_headings++;
				}
			}

			$total_subheadings = count($headings[0]);
			if ($total_subheadings > 0 && $keyword_in_headings > 0) {
				$percentage = ($keyword_in_headings / $total_subheadings) * 100;
				if ($percentage >= 30 && $percentage <= 75) {
					$good[] = array(
						'title' => __('Keyphrase in subheading', 'rankflow-seo'),
						'message' => sprintf(
							/* translators: 1: number of subheadings with keyphrase, 2: total number of subheadings */
							__('%1$d of your %2$d subheadings contain the focus keyphrase. Good job!', 'rankflow-seo'),
							$keyword_in_headings,
							$total_subheadings
						),
					);
				} elseif ($percentage > 75) {
					$problems[] = array(
						'title' => __('Keyphrase in subheading', 'rankflow-seo'),
						'message' => __('The focus keyphrase appears in too many subheadings. Use it in fewer subheadings.', 'rankflow-seo'),
					);
				}
			} elseif ($total_subheadings > 0 && 0 === $keyword_in_headings) {
				$problems[] = array(
					'title' => __('Keyphrase in subheading', 'rankflow-seo'),
					'message' => __('Use the focus keyphrase in at least one subheading (H2-H6).', 'rankflow-seo'),
				);
			}
		}

		// ========================================
		// Links Checks
		// ========================================

		// 12. Internal links.
		$internal_links = $this->count_internal_links($content);
		if ($internal_links >= 1) {
			$good[] = array(
				'title' => __('Internal links', 'rankflow-seo'),
				'message' => sprintf(
					/* translators: %d: number of internal links */
					__('You have %d internal link(s). Good job!', 'rankflow-seo'),
					$internal_links
				),
			);
		} else {
			$problems[] = array(
				'title' => __('Internal links', 'rankflow-seo'),
				'message' => __('No internal links appear in this page, make sure to add some!', 'rankflow-seo'),
			);
		}

		// 13. Outbound links.
		$external_links = $this->count_external_links($content);
		if ($external_links >= 1) {
			$good[] = array(
				'title' => __('Outbound links', 'rankflow-seo'),
				'message' => sprintf(
					/* translators: %d: number of outbound links */
					__('You have %d outbound link(s). Good job!', 'rankflow-seo'),
					$external_links
				),
			);
		} else {
			$problems[] = array(
				'title' => __('Outbound links', 'rankflow-seo'),
				'message' => __('No outbound links appear in this page. Add some!', 'rankflow-seo'),
			);
		}

		// 14. Competing links (links with keyphrase as anchor).
		if (!empty($focus_keyword)) {
			$competing_links = $this->count_competing_links($content, $focus_keyword);
			if (0 === $competing_links) {
				$good[] = array(
					'title' => __('Competing links', 'rankflow-seo'),
					'message' => __('There are no links which use your keyphrase or synonym as their anchor text. Nice!', 'rankflow-seo'),
				);
			} else {
				$problems[] = array(
					'title' => __('Competing links', 'rankflow-seo'),
					'message' => sprintf(
						/* translators: %d: number of competing links */
						__('%d link(s) use your keyphrase as anchor text. This could cause competition.', 'rankflow-seo'),
						$competing_links
					),
				);
			}
		}

		// ========================================
		// Image Checks
		// ========================================

		// 15. Images.
		preg_match_all('/<img[^>]+>/i', $content, $images);
		$total_images = count($images[0]);

		if ($total_images > 0) {
			$images_with_alt = 0;
			$images_with_keyword_alt = 0;

			foreach ($images[0] as $img) {
				if (preg_match('/alt=["\']([^"\']*)["\']/', $img, $alt_match)) {
					if (!empty(trim($alt_match[1]))) {
						$images_with_alt++;
						if (!empty($focus_keyword) && false !== stripos($alt_match[1], $focus_keyword)) {
							$images_with_keyword_alt++;
						}
					}
				}
			}

			if ($images_with_alt === $total_images) {
				$good[] = array(
					'title' => __('Images', 'rankflow-seo'),
					'message' => __('Good job!', 'rankflow-seo'),
				);
			} else {
				$missing = $total_images - $images_with_alt;
				$problems[] = array(
					'title' => __('Images', 'rankflow-seo'),
					'message' => sprintf(
						/* translators: %d: number of images missing alt text */
						__('%d image(s) are missing alt text. Add descriptive alt text to improve accessibility and SEO.', 'rankflow-seo'),
						$missing
					),
				);
			}

			// 16. Keyphrase in image alt.
			if (!empty($focus_keyword)) {
				if ($images_with_keyword_alt > 0) {
					$good[] = array(
						'title' => __('Keyphrase in image alt attributes', 'rankflow-seo'),
						'message' => sprintf(
							/* translators: %d: number of images with keyphrase in alt text */
							__('%d image(s) contain the focus keyphrase in the alt text. Good job!', 'rankflow-seo'),
							$images_with_keyword_alt
						),
					);
				} else {
					$problems[] = array(
						'title' => __('Keyphrase in image alt attributes', 'rankflow-seo'),
						'message' => __('This page does not have images, a keyphrase, or both. Add some images with alt attributes that include the keyphrase or synonyms!', 'rankflow-seo'),
					);
				}
			}
		} elseif (!empty($focus_keyword)) {
			$problems[] = array(
				'title' => __('Keyphrase in image alt attributes', 'rankflow-seo'),
				'message' => __('This page does not have images, a keyphrase, or both. Add some images with alt attributes that include the keyphrase or synonyms!', 'rankflow-seo'),
			);
		}

		// 17. Previously used keyphrase (simplified check).
		if (!empty($focus_keyword)) {
			$duplicate = $this->check_duplicate_keyphrase($post_id, $focus_keyword);
			if (!$duplicate) {
				$good[] = array(
					'title' => __('Previously used keyphrase', 'rankflow-seo'),
					'message' => __('You have not used this focus keyphrase before. Good job!', 'rankflow-seo'),
				);
			} else {
				$problems[] = array(
					'title' => __('Previously used keyphrase', 'rankflow-seo'),
					'message' => __("You have used this focus keyphrase before. Please add a focus keyphrase you haven't used before on other content.", 'rankflow-seo'),
				);
			}
		} elseif (empty($focus_keyword)) {
			$problems[] = array(
				'title' => __('Previously used keyphrase', 'rankflow-seo'),
				'message' => __("No focus keyphrase was set for this page. Please add a focus keyphrase you haven't used before on other content.", 'rankflow-seo'),
			);
		}

		return array(
			'problems' => $problems,
			'good' => $good,
		);
	}

	/**
	 * Get first paragraph from content.
	 *
	 * @param string $content HTML content.
	 * @return string
	 */
	private function get_first_paragraph($content)
	{
		// Try to find first <p> tag.
		if (preg_match('/<p[^>]*>(.*?)<\/p>/is', $content, $match)) {
			return wp_strip_all_tags($match[1]);
		}

		// Fallback: first 150 words.
		$text = wp_strip_all_tags($content);
		$words = explode(' ', $text);
		return implode(' ', array_slice($words, 0, 150));
	}

	/**
	 * Count internal links.
	 *
	 * @param string $content HTML content.
	 * @return int
	 */
	private function count_internal_links($content)
	{
		$site_url = get_site_url();
		preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $content, $links);

		$count = 0;
		foreach ($links[1] as $link) {
			if (0 === strpos($link, $site_url) || (0 === strpos($link, '/') && 0 !== strpos($link, '//'))) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Count external links.
	 *
	 * @param string $content HTML content.
	 * @return int
	 */
	private function count_external_links($content)
	{
		$site_url = get_site_url();
		preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $content, $links);

		$count = 0;
		foreach ($links[1] as $link) {
			if (0 === strpos($link, 'http') && false === strpos($link, $site_url)) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Count competing links (links with keyphrase as anchor text).
	 *
	 * @param string $content HTML content.
	 * @param string $keyword Focus keyword.
	 * @return int
	 */
	private function count_competing_links($content, $keyword)
	{
		preg_match_all('/<a[^>]*>(.*?)<\/a>/i', $content, $links);

		$count = 0;
		foreach ($links[1] as $anchor) {
			$anchor_text = wp_strip_all_tags($anchor);
			if (false !== stripos($anchor_text, $keyword)) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Check if keyphrase is used in another post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $keyword Focus keyword.
	 * @return bool
	 */
	private function check_duplicate_keyphrase($post_id, $keyword)
	{
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Simple meta lookup for duplicate check.
		$result = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->postmeta} 
				WHERE meta_key = '_rankflow_seo_focus_keyword' 
				AND meta_value = %s 
				AND post_id != %d",
				$keyword,
				$post_id
			)
		);

		return $result > 0;
	}
}