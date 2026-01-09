<?php
/**
 * Readability Analyzer - Returns structured analysis with problems and good results.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes/core
 * @author     Strativ AB
 */

if (!defined('ABSPATH')) {
	exit;
}

class AI_SEO_Pro_Readability_Analyzer
{

	/**
	 * Transition words list.
	 */
	private $transition_words = array(
		'accordingly',
		'additionally',
		'afterward',
		'afterwards',
		'albeit',
		'also',
		'although',
		'altogether',
		'another',
		'basically',
		'because',
		'before',
		'besides',
		'but',
		'certainly',
		'chiefly',
		'comparatively',
		'concurrently',
		'consequently',
		'contrarily',
		'conversely',
		'correspondingly',
		'despite',
		'doubtedly',
		'during',
		'e.g.',
		'earlier',
		'emphatically',
		'equally',
		'especially',
		'eventually',
		'evidently',
		'explicitly',
		'finally',
		'firstly',
		'following',
		'formerly',
		'forthwith',
		'fourthly',
		'further',
		'furthermore',
		'generally',
		'hence',
		'henceforth',
		'however',
		'i.e.',
		'identically',
		'importantly',
		'in addition',
		'in conclusion',
		'in contrast',
		'in fact',
		'in other words',
		'in particular',
		'in summary',
		'in the meantime',
		'incidentally',
		'including',
		'indeed',
		'instead',
		'irrespective',
		'lastly',
		'later',
		'lest',
		'likewise',
		'markedly',
		'meanwhile',
		'moreover',
		'namely',
		'nevertheless',
		'nonetheless',
		'nor',
		'notably',
		'notwithstanding',
		'obviously',
		'occasionally',
		'on the contrary',
		'on the other hand',
		'once',
		'originally',
		'otherwise',
		'overall',
		'particularly',
		'presently',
		'previously',
		'primarily',
		'provided that',
		'rather',
		'regardless',
		'secondly',
		'shortly',
		'significantly',
		'similarly',
		'simultaneously',
		'since',
		'so',
		'soon',
		'specifically',
		'still',
		'straightaway',
		'subsequently',
		'such as',
		'summarizing',
		'than',
		'that is',
		'that is to say',
		'the',
		'then',
		'thereafter',
		'therefore',
		'thereupon',
		'thirdly',
		'though',
		'thus',
		'till',
		'to summarize',
		'too',
		'undeniably',
		'undoubtedly',
		'unless',
		'unlike',
		'unquestionably',
		'until',
		'when',
		'whenever',
		'whereas',
		'while',
		'yet',
	);

	/**
	 * Passive voice indicators.
	 */
	private $passive_indicators = array(
		'is being',
		'are being',
		'was being',
		'were being',
		'has been',
		'have been',
		'had been',
		'will be',
		'will have been',
		'is',
		'are',
		'was',
		'were',
		'been',
		'being',
	);

	/**
	 * Past participles for passive detection.
	 */
	private $past_participles_regex = '/(ed|en|n|t)$/i';

	/**
	 * Analyze readability.
	 *
	 * @param string $content Post content
	 * @return array Analysis results with 'problems', 'good', 'flesch_score', 'grade_level'
	 */
	public function analyze($content)
	{
		$problems = array();
		$good = array();

		$text = wp_strip_all_tags($content);

		// Calculate basic metrics
		$sentences = $this->get_sentences($text);
		$sentence_count = count($sentences);
		$word_count = str_word_count($text);
		$paragraph_count = $this->count_paragraphs($content);

		if ($word_count < 10) {
			return array(
				'problems' => array(
					array(
						'title' => __('Content length', 'ai-seo-pro'),
						'message' => __('Add more content to analyze readability.', 'ai-seo-pro'),
					),
				),
				'good' => array(),
				'flesch_score' => 0,
				'grade_level' => '',
			);
		}

		// Calculate Flesch Reading Ease
		$syllable_count = $this->count_syllables($text);
		$flesch_score = $this->calculate_flesch_score($word_count, $sentence_count, $syllable_count);
		$grade_level = $this->get_grade_level($flesch_score);

		// ========================================
		// 1. Sentence Length
		// ========================================
		$long_sentences = $this->count_long_sentences($sentences);
		$long_sentence_percentage = ($sentence_count > 0) ? ($long_sentences / $sentence_count) * 100 : 0;

		if ($long_sentence_percentage <= 25) {
			$good[] = array(
				'title' => __('Sentence length', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %s: percentage of sentences with more than 20 words */
					__('%s%% of the sentences contain more than 20 words, which is less than or equal to the recommended maximum of 25%%. Good job!', 'ai-seo-pro'),
					number_format($long_sentence_percentage, 1)
				),
			);
		} else {
			$problems[] = array(
				'title' => __('Sentence length', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %s: percentage of sentences with more than 20 words */
					__('%s%% of the sentences contain more than 20 words, which is more than the recommended maximum of 25%%. Try to shorten the sentences.', 'ai-seo-pro'),
					number_format($long_sentence_percentage, 1)
				),
			);
		}

		// ========================================
		// 2. Paragraph Length
		// ========================================
		$long_paragraphs = $this->count_long_paragraphs($content);

		if ($long_paragraphs === 0) {
			$good[] = array(
				'title' => __('Paragraph length', 'ai-seo-pro'),
				'message' => __('None of the paragraphs are too long. Good job!', 'ai-seo-pro'),
			);
		} else {
			$problems[] = array(
				'title' => __('Paragraph length', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %d: number of paragraphs with more than 150 words */
					__('%d of the paragraphs contain more than 150 words. Shorten them!', 'ai-seo-pro'),
					$long_paragraphs
				),
			);
		}

		// ========================================
		// 3. Subheading Distribution
		// ========================================
		$subheading_analysis = $this->analyze_subheading_distribution($content);

		if ($subheading_analysis['status'] === 'good') {
			$good[] = array(
				'title' => __('Subheading distribution', 'ai-seo-pro'),
				'message' => __('You are using subheadings properly and the text structure looks great!', 'ai-seo-pro'),
			);
		} elseif ($subheading_analysis['status'] === 'warning') {
			$problems[] = array(
				'title' => __('Subheading distribution', 'ai-seo-pro'),
				'message' => $subheading_analysis['message'],
			);
		} else {
			$problems[] = array(
				'title' => __('Subheading distribution', 'ai-seo-pro'),
				'message' => $subheading_analysis['message'],
			);
		}

		// ========================================
		// 4. Transition Words
		// ========================================
		$transition_percentage = $this->calculate_transition_word_percentage($sentences);

		if ($transition_percentage >= 30) {
			$good[] = array(
				'title' => __('Transition words', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %s: percentage of sentences containing transition words */
					__('%s%% of the sentences contain transition words, which is great!', 'ai-seo-pro'),
					number_format($transition_percentage, 1)
				),
			);
		} elseif ($transition_percentage >= 20) {
			$problems[] = array(
				'title' => __('Transition words', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %s: percentage of sentences containing transition words */
					__('Only %s%% of the sentences contain transition words, which is not enough. Use more of them.', 'ai-seo-pro'),
					number_format($transition_percentage, 1)
				),
			);
		} else {
			$problems[] = array(
				'title' => __('Transition words', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %s: percentage of sentences containing transition words */
					__('Only %s%% of the sentences contain transition words. This is far below the recommended 30%%. Add more to improve flow.', 'ai-seo-pro'),
					number_format($transition_percentage, 1)
				),
			);
		}

		// ========================================
		// 5. Passive Voice
		// ========================================
		$passive_percentage = $this->calculate_passive_voice_percentage($sentences);

		if ($passive_percentage <= 10) {
			$good[] = array(
				'title' => __('Passive voice', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %s: percentage of sentences containing passive voice */
					__('%s%% of the sentences contain passive voice, which is below the recommended maximum of 10%%. Good job!', 'ai-seo-pro'),
					number_format($passive_percentage, 1)
				),
			);
		} else {
			$problems[] = array(
				'title' => __('Passive voice', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %s: percentage of sentences containing passive voice */
					__('%s%% of the sentences contain passive voice, which is more than the recommended maximum of 10%%. Try to use their active counterparts.', 'ai-seo-pro'),
					number_format($passive_percentage, 1)
				),
			);
		}

		// ========================================
		// 6. Consecutive Sentences
		// ========================================
		$consecutive_analysis = $this->analyze_consecutive_sentences($sentences);

		if ($consecutive_analysis['max_consecutive'] <= 2) {
			$good[] = array(
				'title' => __('Consecutive sentences', 'ai-seo-pro'),
				'message' => __('There is enough variety in your sentences. Good job!', 'ai-seo-pro'),
			);
		} else {
			$problems[] = array(
				'title' => __('Consecutive sentences', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %d: number of consecutive sentences starting with the same word */
					__('%d consecutive sentences start with the same word. Mix it up!', 'ai-seo-pro'),
					$consecutive_analysis['max_consecutive']
				),
			);
		}

		// ========================================
		// 7. Word Complexity
		// ========================================
		$complex_word_percentage = $this->calculate_complex_word_percentage($text);

		if ($complex_word_percentage <= 10) {
			$good[] = array(
				'title' => __('Word complexity', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %s: percentage of complex words in the text */
					__('%s%% of the words in your text are considered complex. Good job!', 'ai-seo-pro'),
					number_format($complex_word_percentage, 1)
				),
			);
		} else {
			$problems[] = array(
				'title' => __('Word complexity', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %s: percentage of complex words in the text */
					__('%s%% of the words in your text are considered complex. Try to use simpler alternatives.', 'ai-seo-pro'),
					number_format($complex_word_percentage, 1)
				),
			);
		}

		// ========================================
		// 8. Flesch Reading Ease
		// ========================================
		if ($flesch_score >= 60) {
			$good[] = array(
				'title' => __('Flesch Reading Ease', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %s: Flesch Reading Ease score */
					__('The copy scores %s in the Flesch Reading Ease test, which is considered good. Good job!', 'ai-seo-pro'),
					number_format($flesch_score, 1)
				),
			);
		} elseif ($flesch_score >= 30) {
			$problems[] = array(
				'title' => __('Flesch Reading Ease', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %s: Flesch Reading Ease score */
					__('The copy scores %s in the Flesch Reading Ease test, which is considered fairly difficult to read. Try to make shorter sentences and use simpler words.', 'ai-seo-pro'),
					number_format($flesch_score, 1)
				),
			);
		} else {
			$problems[] = array(
				'title' => __('Flesch Reading Ease', 'ai-seo-pro'),
				'message' => sprintf(
					/* translators: %s: Flesch Reading Ease score */
					__('The copy scores %s in the Flesch Reading Ease test, which is considered very difficult to read. Simplify your text considerably.', 'ai-seo-pro'),
					number_format($flesch_score, 1)
				),
			);
		}

		return array(
			'problems' => $problems,
			'good' => $good,
			'flesch_score' => $flesch_score,
			'grade_level' => $grade_level,
		);
	}

	/**
	 * Get sentences from text.
	 *
	 * @param string $text Text content.
	 * @return array
	 */
	private function get_sentences($text)
	{
		$sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
		return array_filter(array_map('trim', $sentences));
	}

	/**
	 * Count syllables in text.
	 *
	 * @param string $text Text content.
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
	 * @param string $word Word to count syllables.
	 * @return int
	 */
	private function count_word_syllables($word)
	{
		$word = strtolower(trim($word));
		$vowels = array('a', 'e', 'i', 'o', 'u', 'y');
		$syllables = 0;
		$previous_was_vowel = false;

		$len = strlen($word);
		for ($i = 0; $i < $len; $i++) {
			$is_vowel = in_array($word[$i], $vowels, true);

			if ($is_vowel && !$previous_was_vowel) {
				$syllables++;
			}

			$previous_was_vowel = $is_vowel;
		}

		// Adjust for silent e
		if (substr($word, -1) === 'e' && $syllables > 1) {
			$syllables--;
		}

		// Adjust for -le endings
		if (substr($word, -2) === 'le' && $len > 2 && !in_array($word[$len - 3], $vowels, true)) {
			$syllables++;
		}

		return max(1, $syllables);
	}

	/**
	 * Calculate Flesch Reading Ease score.
	 *
	 * @param int $words     Word count.
	 * @param int $sentences Sentence count.
	 * @param int $syllables Syllable count.
	 * @return float
	 */
	private function calculate_flesch_score($words, $sentences, $syllables)
	{
		if ($sentences === 0 || $words === 0) {
			return 0;
		}

		$score = 206.835 - 1.015 * ($words / $sentences) - 84.6 * ($syllables / $words);
		return max(0, min(100, round($score, 1)));
	}

	/**
	 * Get grade level from Flesch score.
	 *
	 * @param float $score Flesch score.
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
	 * Count long sentences (>20 words).
	 *
	 * @param array $sentences Array of sentences.
	 * @return int
	 */
	private function count_long_sentences($sentences)
	{
		$count = 0;
		foreach ($sentences as $sentence) {
			if (str_word_count($sentence) > 20) {
				$count++;
			}
		}
		return $count;
	}

	/**
	 * Count paragraphs.
	 *
	 * @param string $content HTML content.
	 * @return int
	 */
	private function count_paragraphs($content)
	{
		$paragraphs = preg_split('/<\/?p[^>]*>/', $content, -1, PREG_SPLIT_NO_EMPTY);
		$paragraphs = array_filter($paragraphs, function ($p) {
			return trim(wp_strip_all_tags($p)) !== '';
		});
		return count($paragraphs);
	}

	/**
	 * Count long paragraphs (>150 words).
	 *
	 * @param string $content HTML content.
	 * @return int
	 */
	private function count_long_paragraphs($content)
	{
		preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $content, $matches);

		$count = 0;
		foreach ($matches[1] as $paragraph) {
			$text = wp_strip_all_tags($paragraph);
			if (str_word_count($text) > 150) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Analyze subheading distribution.
	 *
	 * @param string $content HTML content.
	 * @return array
	 */
	private function analyze_subheading_distribution($content)
	{
		// Find all subheadings positions
		preg_match_all('/<h[2-6][^>]*>.*?<\/h[2-6]>/is', $content, $matches, PREG_OFFSET_CAPTURE);

		$subheadings = $matches[0];
		$text_length = strlen(wp_strip_all_tags($content));
		$word_count = str_word_count(wp_strip_all_tags($content));

		if ($word_count < 300) {
			return array(
				'status' => 'good',
				'message' => __('Text is short, subheadings are optional.', 'ai-seo-pro'),
			);
		}

		if (empty($subheadings)) {
			return array(
				'status' => 'problem',
				'message' => __('You are not using any subheadings, although your text is long enough. This makes the text harder to scan.', 'ai-seo-pro'),
			);
		}

		// Check for text sections longer than 300 words without subheadings
		$sections = $this->get_text_sections($content);
		$long_sections = 0;

		foreach ($sections as $section) {
			if (str_word_count($section) > 300) {
				$long_sections++;
			}
		}

		if ($long_sections > 0) {
			return array(
				'status' => 'warning',
				'message' => sprintf(
					/* translators: %d: number of text sections with more than 300 words */
					__('%d section(s) of your text have more than 300 words without a subheading. Add more subheadings to break up the text.', 'ai-seo-pro'),
					$long_sections
				),
			);
		}

		return array(
			'status' => 'good',
			'message' => __('Great subheading distribution!', 'ai-seo-pro'),
		);
	}

	/**
	 * Get text sections between subheadings.
	 *
	 * @param string $content HTML content.
	 * @return array
	 */
	private function get_text_sections($content)
	{
		$sections = preg_split('/<h[2-6][^>]*>.*?<\/h[2-6]>/is', $content);
		return array_map(function ($section) {
			return wp_strip_all_tags($section);
		}, $sections);
	}

	/**
	 * Calculate transition word percentage.
	 *
	 * @param array $sentences Array of sentences.
	 * @return float
	 */
	private function calculate_transition_word_percentage($sentences)
	{
		if (empty($sentences)) {
			return 0;
		}

		$sentences_with_transition = 0;

		foreach ($sentences as $sentence) {
			$sentence_lower = strtolower($sentence);
			foreach ($this->transition_words as $transition) {
				if (preg_match('/\b' . preg_quote($transition, '/') . '\b/i', $sentence_lower)) {
					$sentences_with_transition++;
					break;
				}
			}
		}

		return ($sentences_with_transition / count($sentences)) * 100;
	}

	/**
	 * Calculate passive voice percentage.
	 *
	 * @param array $sentences Array of sentences.
	 * @return float
	 */
	private function calculate_passive_voice_percentage($sentences)
	{
		if (empty($sentences)) {
			return 0;
		}

		$passive_sentences = 0;

		foreach ($sentences as $sentence) {
			if ($this->is_passive_sentence($sentence)) {
				$passive_sentences++;
			}
		}

		return ($passive_sentences / count($sentences)) * 100;
	}

	/**
	 * Check if sentence contains passive voice.
	 *
	 * @param string $sentence Sentence to check.
	 * @return bool
	 */
	private function is_passive_sentence($sentence)
	{
		$sentence_lower = strtolower($sentence);

		// Simple passive voice detection
		$passive_patterns = array(
			'/\b(is|are|was|were|been|being)\s+\w+ed\b/',
			'/\b(is|are|was|were|been|being)\s+\w+en\b/',
			'/\b(is|are|was|were|has been|have been|had been|will be)\s+\w+(ed|en|t)\b/',
		);

		foreach ($passive_patterns as $pattern) {
			if (preg_match($pattern, $sentence_lower)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Analyze consecutive sentences starting with same word.
	 *
	 * @param array $sentences Array of sentences.
	 * @return array
	 */
	private function analyze_consecutive_sentences($sentences)
	{
		$max_consecutive = 0;
		$current_consecutive = 1;
		$last_first_word = '';

		foreach ($sentences as $sentence) {
			$words = str_word_count(trim($sentence), 1);
			if (empty($words)) {
				continue;
			}

			$first_word = strtolower($words[0]);

			if ($first_word === $last_first_word && !empty($last_first_word)) {
				$current_consecutive++;
				$max_consecutive = max($max_consecutive, $current_consecutive);
			} else {
				$current_consecutive = 1;
			}

			$last_first_word = $first_word;
		}

		return array(
			'max_consecutive' => $max_consecutive,
		);
	}

	/**
	 * Calculate complex word percentage (words with 4+ syllables).
	 *
	 * @param string $text Text content.
	 * @return float
	 */
	private function calculate_complex_word_percentage($text)
	{
		$words = str_word_count(strtolower($text), 1);
		$total_words = count($words);

		if ($total_words === 0) {
			return 0;
		}

		$complex_words = 0;
		foreach ($words as $word) {
			if ($this->count_word_syllables($word) >= 4) {
				$complex_words++;
			}
		}

		return ($complex_words / $total_words) * 100;
	}
}