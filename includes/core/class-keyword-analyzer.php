<?php
/**
 * Keyword analyzer.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes/core
 * @author     Strativ AB
 */
class AI_SEO_Pro_Keyword_Analyzer
{

	/**
	 * Analyze keyword usage in content.
	 *
	 * @param    string    $content    Content
	 * @param    string    $keyword    Keyword
	 * @return   array
	 */
	public function analyze($content, $keyword)
	{
		if (empty($keyword)) {
			return array(
				'density' => 0,
				'count' => 0,
				'in_title' => false,
				'in_first_para' => false,
				'in_headings' => 0,
				'in_url' => false,
				'prominence' => 0,
			);
		}

		$analysis = array();
		$text = wp_strip_all_tags(strtolower($content));
		$keyword_lower = strtolower($keyword);

		// Keyword density
		$analysis['density'] = $this->calculate_density($text, $keyword_lower);

		// Keyword count
		$analysis['count'] = substr_count($text, $keyword_lower);

		// Keyword in headings
		$analysis['in_headings'] = $this->count_in_headings($content, $keyword_lower);

		// Keyword in first paragraph
		$analysis['in_first_para'] = $this->is_in_first_paragraph($content, $keyword_lower);

		// Keyword prominence (position in content)
		$analysis['prominence'] = $this->calculate_prominence($text, $keyword_lower);

		// Recommendations
		$analysis['recommendations'] = $this->get_recommendations($analysis);

		return $analysis;
	}

	/**
	 * Calculate keyword density.
	 *
	 * @param    string    $text       Text
	 * @param    string    $keyword    Keyword
	 * @return   float
	 */
	private function calculate_density($text, $keyword)
	{
		$word_count = str_word_count($text);
		$keyword_count = substr_count($text, $keyword);

		if ($word_count === 0) {
			return 0;
		}

		return round(($keyword_count / $word_count) * 100, 2);
	}

	/**
	 * Count keyword in headings.
	 *
	 * @param    string    $content    Content
	 * @param    string    $keyword    Keyword
	 * @return   int
	 */
	private function count_in_headings($content, $keyword)
	{
		preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/i', $content, $matches);

		$count = 0;
		foreach ($matches[1] as $heading) {
			$heading_text = strtolower(wp_strip_all_tags($heading));
			if (strpos($heading_text, $keyword) !== false) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Check if keyword is in first paragraph.
	 *
	 * @param    string    $content    Content
	 * @param    string    $keyword    Keyword
	 * @return   bool
	 */
	private function is_in_first_paragraph($content, $keyword)
	{
		preg_match('/<p[^>]*>(.*?)<\/p>/is', $content, $matches);

		if (empty($matches[1])) {
			// No <p> tags, check first 150 words
			$text = wp_strip_all_tags($content);
			$words = explode(' ', $text);
			$first_para = implode(' ', array_slice($words, 0, 150));
			return strpos(strtolower($first_para), $keyword) !== false;
		}

		$first_para = strtolower(wp_strip_all_tags($matches[1]));
		return strpos($first_para, $keyword) !== false;
	}

	/**
	 * Calculate keyword prominence.
	 *
	 * @param    string    $text       Text
	 * @param    string    $keyword    Keyword
	 * @return   int
	 */
	private function calculate_prominence($text, $keyword)
	{
		$position = strpos($text, $keyword);

		if ($position === false) {
			return 0;
		}

		$total_length = strlen($text);

		if ($total_length === 0) {
			return 0;
		}

		// Calculate position as percentage (lower is better)
		$position_percent = ($position / $total_length) * 100;

		// Convert to score (0-100, higher is better)
		return max(0, 100 - $position_percent);
	}

	/**
	 * Get recommendations based on analysis.
	 *
	 * @param    array    $analysis    Analysis data
	 * @return   array
	 */
	private function get_recommendations($analysis)
	{
		$recommendations = array();

		// Density recommendations
		if ($analysis['density'] < 0.5) {
			$recommendations[] = array(
				'type' => 'warning',
				'message' => sprintf(
					/* translators: %s: keyword density percentage */
					__('Keyword density is low (%s%%). Try to include the focus keyword more naturally in your content.', 'ai-seo-pro'),
					number_format($analysis['density'], 2)
				),
			);
		} elseif ($analysis['density'] > 2.5) {
			$recommendations[] = array(
				'type' => 'error',
				'message' => sprintf(
					/* translators: %s: keyword density percentage */
					__('Keyword density is too high (%s%%). This might be considered keyword stuffing.', 'ai-seo-pro'),
					number_format($analysis['density'], 2)
				),
			);
		} else {
			$recommendations[] = array(
				'type' => 'success',
				'message' => sprintf(
					/* translators: %s: keyword density percentage */
					__('Keyword density is good (%s%%).', 'ai-seo-pro'),
					number_format($analysis['density'], 2)
				),
			);
		}

		// Headings recommendations
		if ($analysis['in_headings'] === 0) {
			$recommendations[] = array(
				'type' => 'warning',
				'message' => __('Focus keyword not found in any headings. Use it in at least one heading (H1-H6).', 'ai-seo-pro'),
			);
		} else {
			$recommendations[] = array(
				'type' => 'success',
				'message' => sprintf(
					/* translators: %d: number of headings containing the keyword */
					__('Focus keyword appears in %d heading(s).', 'ai-seo-pro'),
					$analysis['in_headings']
				),
			);
		}

		// First paragraph recommendation
		if (!$analysis['in_first_para']) {
			$recommendations[] = array(
				'type' => 'warning',
				'message' => __('Focus keyword not found in the first paragraph. Include it early in your content.', 'ai-seo-pro'),
			);
		} else {
			$recommendations[] = array(
				'type' => 'success',
				'message' => __('Focus keyword appears in the first paragraph.', 'ai-seo-pro'),
			);
		}

		return $recommendations;
	}

	/**
	 * Extract related keywords from content.
	 *
	 * @param    string    $content    Content
	 * @param    int       $limit      Number of keywords to return
	 * @return   array
	 */
	public function extract_related_keywords($content, $limit = 10)
	{
		$text = wp_strip_all_tags(strtolower($content));

		// Remove common stop words
		$stop_words = array('the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from', 'is', 'are', 'was', 'were', 'be', 'been', 'this', 'that', 'these', 'those');

		// Get all words
		$words = str_word_count($text, 1);

		// Filter out stop words and short words
		$words = array_filter($words, function ($word) use ($stop_words) {
			return strlen($word) > 3 && !in_array($word, $stop_words, true);
		});

		// Count word frequency
		$word_freq = array_count_values($words);

		// Sort by frequency
		arsort($word_freq);

		// Get top keywords
		return array_slice(array_keys($word_freq), 0, $limit);
	}
}