<?php
/**
 * SEO score calculator.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes/core
 * @author     Strativ AB
 */
class AI_SEO_Pro_SEO_Score
{

	/**
	 * Calculate SEO score for a post.
	 *
	 * @param    int    $post_id    Post ID
	 * @return   int                Score (0-100)
	 */
	public function calculate_score($post_id)
	{
		$score = 0;
		$post = get_post($post_id);

		if (!$post) {
			return 0;
		}

		// Meta title (15 points)
		$score += $this->score_meta_title($post_id);

		// Meta description (15 points)
		$score += $this->score_meta_description($post_id);

		// Content length (10 points)
		$score += $this->score_content_length($post->post_content);

		// Focus keyword (20 points)
		$score += $this->score_focus_keyword($post_id, $post->post_content);

		// Headings (10 points)
		$score += $this->score_headings($post->post_content);

		// Images (10 points)
		$score += $this->score_images($post->post_content);

		// Links (10 points)
		$score += $this->score_links($post->post_content);

		// Readability (10 points)
		$score += $this->score_readability($post->post_content);

		return min(100, $score);
	}

	/**
	 * Score meta title.
	 *
	 * @param    int    $post_id    Post ID
	 * @return   int
	 */
	private function score_meta_title($post_id)
	{
		$title = get_post_meta($post_id, '_ai_seo_title', true);

		if (empty($title)) {
			return 0;
		}

		$length = strlen($title);

		if ($length >= 50 && $length <= 60) {
			return 15;
		} elseif ($length >= 40 && $length <= 70) {
			return 10;
		} elseif ($length > 0) {
			return 5;
		}

		return 0;
	}

	/**
	 * Score meta description.
	 *
	 * @param    int    $post_id    Post ID
	 * @return   int
	 */
	private function score_meta_description($post_id)
	{
		$description = get_post_meta($post_id, '_ai_seo_description', true);

		if (empty($description)) {
			return 0;
		}

		$length = strlen($description);

		if ($length >= 150 && $length <= 160) {
			return 15;
		} elseif ($length >= 120 && $length <= 170) {
			return 10;
		} elseif ($length > 0) {
			return 5;
		}

		return 0;
	}

	/**
	 * Score content length.
	 *
	 * @param    string    $content    Content
	 * @return   int
	 */
	private function score_content_length($content)
	{
		$word_count = str_word_count(wp_strip_all_tags($content));

		if ($word_count >= 1000) {
			return 10;
		} elseif ($word_count >= 500) {
			return 7;
		} elseif ($word_count >= 300) {
			return 5;
		}

		return 0;
	}

	/**
	 * Score focus keyword usage.
	 *
	 * @param    int       $post_id    Post ID
	 * @param    string    $content    Content
	 * @return   int
	 */
	private function score_focus_keyword($post_id, $content)
	{
		$keyword = get_post_meta($post_id, '_ai_seo_focus_keyword', true);

		if (empty($keyword)) {
			return 0;
		}

		$score = 0;
		$keyword_lower = strtolower($keyword);
		$content_lower = strtolower(wp_strip_all_tags($content));

		// Keyword in meta title
		$meta_title = get_post_meta($post_id, '_ai_seo_title', true);
		if (!empty($meta_title) && stripos($meta_title, $keyword) !== false) {
			$score += 5;
		}

		// Keyword in meta description
		$meta_desc = get_post_meta($post_id, '_ai_seo_description', true);
		if (!empty($meta_desc) && stripos($meta_desc, $keyword) !== false) {
			$score += 5;
		}

		// Keyword in content
		$keyword_count = substr_count($content_lower, $keyword_lower);
		if ($keyword_count >= 3 && $keyword_count <= 10) {
			$score += 5;
		} elseif ($keyword_count > 0) {
			$score += 2;
		}

		// Keyword in headings
		preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/i', $content, $matches);
		foreach ($matches[1] as $heading) {
			if (stripos($heading, $keyword) !== false) {
				$score += 5;
				break;
			}
		}

		return min(20, $score);
	}

	/**
	 * Score headings usage.
	 *
	 * @param    string    $content    Content
	 * @return   int
	 */
	private function score_headings($content)
	{
		$h1_count = preg_match_all('/<h1[^>]*>/i', $content);
		$h2_count = preg_match_all('/<h2[^>]*>/i', $content);
		$h3_count = preg_match_all('/<h3[^>]*>/i', $content);

		$score = 0;

		// Proper H1 usage (only one)
		if ($h1_count === 1) {
			$score += 3;
		}

		// Good use of H2
		if ($h2_count >= 2) {
			$score += 4;
		} elseif ($h2_count >= 1) {
			$score += 2;
		}

		// Use of H3
		if ($h3_count >= 1) {
			$score += 3;
		}

		return min(10, $score);
	}

	/**
	 * Score images.
	 *
	 * @param    string    $content    Content
	 * @return   int
	 */
	private function score_images($content)
	{
		preg_match_all('/<img[^>]+>/i', $content, $images);

		$total = count($images[0]);

		if ($total === 0) {
			return 0;
		}

		$with_alt = 0;
		foreach ($images[0] as $img) {
			if (preg_match('/alt=["\'][^"\']+["\']/i', $img)) {
				$with_alt++;
			}
		}

		$percentage = ($with_alt / $total) * 100;

		if ($percentage === 100) {
			return 10;
		} elseif ($percentage >= 75) {
			return 7;
		} elseif ($percentage >= 50) {
			return 5;
		}

		return 2;
	}

	/**
	 * Score internal and external links.
	 *
	 * @param    string    $content    Content
	 * @return   int
	 */
	private function score_links($content)
	{
		$site_url = get_site_url();
		preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $content, $links);

		$internal = 0;
		$external = 0;

		foreach ($links[1] as $link) {
			if (strpos($link, $site_url) === 0 || strpos($link, '/') === 0) {
				$internal++;
			} elseif (strpos($link, 'http') === 0) {
				$external++;
			}
		}

		$score = 0;

		if ($internal >= 2) {
			$score += 5;
		} elseif ($internal >= 1) {
			$score += 3;
		}

		if ($external >= 1) {
			$score += 5;
		}

		return min(10, $score);
	}

	/**
	 * Score readability.
	 *
	 * @param    string    $content    Content
	 * @return   int
	 */
	private function score_readability($content)
	{
		$text = wp_strip_all_tags($content);
		$sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
		$words = str_word_count($text);

		if (count($sentences) === 0 || $words === 0) {
			return 0;
		}

		$avg_sentence_length = $words / count($sentences);

		if ($avg_sentence_length <= 20) {
			return 10;
		} elseif ($avg_sentence_length <= 25) {
			return 7;
		} elseif ($avg_sentence_length <= 30) {
			return 5;
		}

		return 2;
	}

	/**
	 * Get score status.
	 *
	 * @param    int    $score    Score
	 * @return   array
	 */
	public function get_score_status($score)
	{
		if ($score >= 80) {
			return array(
				'status' => 'good',
				'label' => __('Excellent', 'ai-seo-pro'),
				'color' => '#46b450',
			);
		} elseif ($score >= 60) {
			return array(
				'status' => 'ok',
				'label' => __('Good', 'ai-seo-pro'),
				'color' => '#ffb900',
			);
		} elseif ($score >= 40) {
			return array(
				'status' => 'needs_improvement',
				'label' => __('Needs Improvement', 'ai-seo-pro'),
				'color' => '#f56e28',
			);
		} else {
			return array(
				'status' => 'poor',
				'label' => __('Poor', 'ai-seo-pro'),
				'color' => '#dc3232',
			);
		}
	}
}