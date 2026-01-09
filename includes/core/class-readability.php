<?php
/**
 * Readability analyzer.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes/core
 * @author     Strativ AB
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

class AI_SEO_Pro_Readability
{

	/**
	 * Analyze readability.
	 *
	 * @param string $content Content
	 * @return array
	 */
	public function analyze($content)
	{
		$text = wp_strip_all_tags($content);

		$analysis = array(
			'flesch_score' => $this->calculate_flesch_reading_ease($text),
			'avg_sentence_length' => $this->get_average_sentence_length($text),
			'avg_word_length' => $this->get_average_word_length($text),
			'paragraph_count' => $this->count_paragraphs($content),
			'long_sentences' => $this->count_long_sentences($text),
			'passive_voice' => $this->detect_passive_voice($text),
			'transition_words' => $this->count_transition_words($text),
		);

		$analysis['grade_level'] = $this->get_grade_level($analysis['flesch_score']);
		$analysis['recommendations'] = $this->get_recommendations($analysis);

		return $analysis;
	}

	/**
	 * Calculate Flesch Reading Ease score.
	 *
	 * @param string $text Text
	 * @return float
	 */
	private function calculate_flesch_reading_ease($text)
	{
		$sentences = $this->count_sentences($text);
		$words = str_word_count($text);

		if ($sentences === 0 || $words === 0) {
			return 0;
		}

		$syllables = $this->count_syllables($text);

		// Flesch Reading Ease formula
		$score = 206.835 - 1.015 * ($words / $sentences) - 84.6 * ($syllables / $words);

		return max(0, min(100, round($score, 1)));
	}

	/**
	 * Count sentences.
	 *
	 * @param string $text Text
	 * @return int
	 */
	private function count_sentences($text)
	{
		$sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
		return count($sentences);
	}

	/**
	 * Count syllables.
	 *
	 * @param string $text Text
	 * @return int
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
	 * @param string $word Word
	 * @return int
	 */
	private function count_word_syllables($word)
	{
		$word = strtolower($word);
		$vowels = array('a', 'e', 'i', 'o', 'u', 'y');
		$syllables = 0;
		$previous_was_vowel = false;

		for ($i = 0; $i < strlen($word); $i++) {
			$is_vowel = in_array($word[$i], $vowels, true);

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
	 * Get average sentence length.
	 *
	 * @param string $text Text
	 * @return float
	 */
	private function get_average_sentence_length($text)
	{
		$sentences = $this->count_sentences($text);
		$words = str_word_count($text);

		if ($sentences === 0) {
			return 0;
		}

		return round($words / $sentences, 1);
	}

	/**
	 * Get average word length.
	 *
	 * @param string $text Text
	 * @return float
	 */
	private function get_average_word_length($text)
	{
		$words = str_word_count($text, 1);
		$total_length = 0;

		foreach ($words as $word) {
			$total_length += strlen($word);
		}

		if (count($words) === 0) {
			return 0;
		}

		return round($total_length / count($words), 1);
	}

	/**
	 * Count paragraphs.
	 *
	 * @param string $content Content
	 * @return int
	 */
	private function count_paragraphs($content)
	{
		$paragraphs = preg_split('/<\/?p[^>]*>/', $content, -1, PREG_SPLIT_NO_EMPTY);
		$paragraphs = array_filter(
			$paragraphs,
			function ($p) {
				return trim(strip_tags($p)) !== '';
			}
		);

		return count($paragraphs);
	}

	/**
	 * Count long sentences.
	 *
	 * @param string $text Text
	 * @return int
	 */
	private function count_long_sentences($text)
	{
		$sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
		$long_count = 0;

		foreach ($sentences as $sentence) {
			$word_count = str_word_count($sentence);
			if ($word_count > 20) {
				$long_count++;
			}
		}

		return $long_count;
	}

	/**
	 * Detect passive voice (simple detection).
	 *
	 * @param string $text Text
	 * @return int
	 */
	private function detect_passive_voice($text)
	{
		$passive_indicators = array('was', 'were', 'been', 'being', 'is', 'are');
		$count = 0;

		foreach ($passive_indicators as $indicator) {
			$count += substr_count(strtolower($text), ' ' . $indicator . ' ');
		}

		return $count;
	}

	/**
	 * Count transition words.
	 *
	 * @param string $text Text
	 * @return int
	 */
	private function count_transition_words($text)
	{
		$transitions = array('however', 'therefore', 'furthermore', 'moreover', 'nevertheless', 'consequently', 'additionally', 'meanwhile', 'otherwise', 'likewise');
		$count = 0;

		foreach ($transitions as $transition) {
			$count += substr_count(strtolower($text), $transition);
		}

		return $count;
	}

	/**
	 * Get grade level from Flesch score.
	 *
	 * @param float $score Flesch score
	 * @return string
	 */
	private function get_grade_level($score)
	{
		if ($score >= 90) {
			return __('5th grade (Very Easy)', 'ai-seo-pro');
		} elseif ($score >= 80) {
			return __('6th grade (Easy)', 'ai-seo-pro');
		} elseif ($score >= 70) {
			return __('7th grade (Fairly Easy)', 'ai-seo-pro');
		} elseif ($score >= 60) {
			return __('8th-9th grade (Standard)', 'ai-seo-pro');
		} elseif ($score >= 50) {
			return __('10th-12th grade (Fairly Difficult)', 'ai-seo-pro');
		} elseif ($score >= 30) {
			return __('College (Difficult)', 'ai-seo-pro');
		} else {
			return __('College Graduate (Very Difficult)', 'ai-seo-pro');
		}
	}

	/**
	 * Get recommendations.
	 *
	 * @param array $analysis Analysis data
	 * @return array
	 */
	private function get_recommendations($analysis)
	{
		$recommendations = array();

		// Flesch score recommendations		

		if ($analysis['flesch_score'] < 60) {

			$score = number_format($analysis['flesch_score'], 1);

			$recommendations[] = array(
				'type' => 'warning',
				'message' => sprintf(
					/* translators: %s: readability score number */
					esc_html__('Readability score is %s. Consider using shorter sentences and simpler words.', 'ai-seo-pro'),
					$score
				),
			);
		}

		// Sentence length recommendations
		if ($analysis['avg_sentence_length'] > 20) {
			$sentence_length = number_format($analysis['avg_sentence_length'], 1);
			$recommendations[] = array(
				'type' => 'warning',
				'message' => sprintf(
					/* translators: %s: average sentence length number */
					esc_html__('Average sentence length is %s words. Try to use shorter sentences (under 20 words).', 'ai-seo-pro'),
					$sentence_length
				),
			);
		}

		// Long sentences
		if ($analysis['long_sentences'] > 3) {
			$recommendations[] = array(
				'type' => 'info',
				'message' => sprintf(
					/* translators: %d: number of long sentences */
					esc_html__('%d sentences are very long. Consider breaking them up.', 'ai-seo-pro'),
					$analysis['long_sentences']
				),
			);
		}

		// Transition words
		if ($analysis['transition_words'] < 2) {
			$recommendations[] = array(
				'type' => 'info',
				'message' => __('Use more transition words to improve flow (however, therefore, furthermore, etc.).', 'ai-seo-pro'),
			);
		}

		return $recommendations;
	}
}