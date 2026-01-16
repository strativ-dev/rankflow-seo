<?php
/**
 * Content analyzer.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/includes/core
 * @author     Strativ AB
 */
class RankFlow_SEO_Content_Analyzer
{

	/**
	 * Analyze content.
	 *
	 * @param    string    $content         Content to analyze
	 * @param    string    $focus_keyword   Focus keyword
	 * @return   array
	 */
	public function analyze($content, $focus_keyword = '')
	{
		$analysis = array(
			'word_count' => $this->count_words($content),
			'readability_score' => $this->calculate_readability($content),
			'keyword_density' => 0,
			'keyword_in_title' => false,
			'keyword_in_first_paragraph' => false,
			'keyword_in_headings' => 0,
			'image_alt_tags' => $this->check_image_alt_tags($content),
			'internal_links' => $this->count_internal_links($content),
			'external_links' => $this->count_external_links($content),
			'recommendations' => array(),
		);

		if (!empty($focus_keyword)) {
			$analysis['keyword_density'] = $this->calculate_keyword_density($content, $focus_keyword);
			$analysis['keyword_in_headings'] = $this->count_keyword_in_headings($content, $focus_keyword);
			$analysis = $this->add_keyword_recommendations($analysis, $focus_keyword);
		}

		$analysis = $this->add_general_recommendations($analysis);

		return $analysis;
	}

	/**
	 * Count words in content.
	 *
	 * @param    string    $content    Content
	 * @return   int
	 */
	private function count_words($content)
	{
		$text = wp_strip_all_tags($content);
		return str_word_count($text);
	}

	/**
	 * Calculate readability score (Flesch Reading Ease).
	 *
	 * @param    string    $content    Content
	 * @return   float
	 */
	private function calculate_readability($content)
	{
		$text = wp_strip_all_tags($content);

		$sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
		$words = str_word_count($text);

		if (count($sentences) === 0 || $words === 0) {
			return 0;
		}

		$syllables = $this->count_syllables($text);

		// Flesch Reading Ease formula
		$score = 206.835 - 1.015 * ($words / count($sentences)) - 84.6 * ($syllables / $words);

		return max(0, min(100, $score));
	}

	/**
	 * Count syllables in text.
	 *
	 * @param    string    $text    Text
	 * @return   int
	 */
	private function count_syllables($text)
	{
		$words = str_word_count(strtolower($text), 1);
		$syllables = 0;

		foreach ($words as $word) {
			$syllables += $this->count_word_syllables($word);
		}

		return $syllables;
	}

	/**
	 * Count syllables in a word.
	 *
	 * @param    string    $word    Word
	 * @return   int
	 */
	private function count_word_syllables($word)
	{
		$word = strtolower($word);
		$vowels = array('a', 'e', 'i', 'o', 'u', 'y');
		$syllables = 0;
		$previous_was_vowel = false;

		for ($i = 0; $i < strlen($word); $i++) {
			$is_vowel = in_array($word[$i], $vowels);

			if ($is_vowel && !$previous_was_vowel) {
				$syllables++;
			}

			$previous_was_vowel = $is_vowel;
		}

		// Adjust for silent e
		if (substr($word, -1) === 'e') {
			$syllables--;
		}

		return max(1, $syllables);
	}

	/**
	 * Calculate keyword density.
	 *
	 * @param    string    $content    Content
	 * @param    string    $keyword    Keyword
	 * @return   float
	 */
	private function calculate_keyword_density($content, $keyword)
	{
		$text = wp_strip_all_tags(strtolower($content));
		$keyword = strtolower($keyword);

		$word_count = str_word_count($text);
		$keyword_count = substr_count($text, $keyword);

		if ($word_count === 0) {
			return 0;
		}

		return round(($keyword_count / $word_count) * 100, 2);
	}

	/**
	 * Count keyword occurrences in headings.
	 *
	 * @param    string    $content    Content
	 * @param    string    $keyword    Keyword
	 * @return   int
	 */
	private function count_keyword_in_headings($content, $keyword)
	{
		preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/i', $content, $matches);

		$count = 0;
		foreach ($matches[1] as $heading) {
			if (stripos($heading, $keyword) !== false) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Check image alt tags.
	 *
	 * @param    string    $content    Content
	 * @return   array
	 */
	private function check_image_alt_tags($content)
	{
		preg_match_all('/<img[^>]+>/i', $content, $images);

		$total = count($images[0]);
		$with_alt = 0;

		foreach ($images[0] as $img) {
			if (preg_match('/alt=["\'][^"\']*["\']/i', $img)) {
				$with_alt++;
			}
		}

		return array(
			'total' => $total,
			'with_alt' => $with_alt,
			'without_alt' => $total - $with_alt,
		);
	}

	/**
	 * Count internal links.
	 *
	 * @param    string    $content    Content
	 * @return   int
	 */
	private function count_internal_links($content)
	{
		$site_url = get_site_url();
		preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $content, $links);

		$count = 0;
		foreach ($links[1] as $link) {
			if (strpos($link, $site_url) === 0 || strpos($link, '/') === 0) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Count external links.
	 *
	 * @param    string    $content    Content
	 * @return   int
	 */
	private function count_external_links($content)
	{
		$site_url = get_site_url();
		preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $content, $links);

		$count = 0;
		foreach ($links[1] as $link) {
			if (strpos($link, 'http') === 0 && strpos($link, $site_url) === false) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Add keyword-based recommendations.
	 *
	 * @param    array     $analysis    Analysis data
	 * @param    string    $keyword     Keyword
	 * @return   array
	 */
	private function add_keyword_recommendations($analysis, $keyword)
	{
		if ($analysis['keyword_density'] < 0.5) {
			$analysis['recommendations'][] = array(
				'type' => 'warning',
				/* translators: 1: keyword density percentage, 2: focus keyword */
				'message' => sprintf(__('Keyword density is low (%1$.2f%%). Try to include "%2$s" more naturally in your content.', 'rankflow-seo'), $analysis['keyword_density'], $keyword),
			);
		} elseif ($analysis['keyword_density'] > 2.5) {
			$analysis['recommendations'][] = array(
				'type' => 'error',
				/* translators: %s: keyword density percentage */
				'message' => sprintf(__('Keyword density is too high (%s%%). This might be seen as keyword stuffing.', 'rankflow-seo'), number_format($analysis['keyword_density'], 2)),
			);
		}

		if ($analysis['keyword_in_headings'] === 0) {
			$analysis['recommendations'][] = array(
				'type' => 'warning',
				/* translators: %s: focus keyword */
				'message' => sprintf(__('Focus keyword "%s" not found in any headings. Use it in at least one heading.', 'rankflow-seo'), $keyword),
			);
		}

		return $analysis;
	}

	/**
	 * Add general recommendations.
	 *
	 * @param    array    $analysis    Analysis data
	 * @return   array
	 */
	private function add_general_recommendations($analysis)
	{
		if ($analysis['word_count'] < 300) {
			$analysis['recommendations'][] = array(
				'type' => 'warning',
				/* translators: %d: word count */
				'message' => sprintf(__('Content is short (%d words). Aim for at least 300 words for better SEO.', 'rankflow-seo'), $analysis['word_count']),
			);
		}

		if ($analysis['readability_score'] < 60) {
			$analysis['recommendations'][] = array(
				'type' => 'info',
				/* translators: %s: readability score */
				'message' => sprintf(__('Readability score is %s. Consider using shorter sentences and simpler words.', 'rankflow-seo'), number_format($analysis['readability_score'], 1)),
			);
		}

		if ($analysis['image_alt_tags']['without_alt'] > 0) {
			$analysis['recommendations'][] = array(
				'type' => 'warning',
				/* translators: %d: number of images missing alt text */
				'message' => sprintf(__('%d image(s) missing alt text. Add descriptive alt text to all images.', 'rankflow-seo'), $analysis['image_alt_tags']['without_alt']),
			);
		}

		if ($analysis['internal_links'] === 0) {
			$analysis['recommendations'][] = array(
				'type' => 'info',
				'message' => __('No internal links found. Link to related content on your site.', 'rankflow-seo'),
			);
		}

		return $analysis;
	}
}