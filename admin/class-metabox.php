<?php
/**
 * The meta box functionality with tabs and analysis.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin
 * @author     Strativ AB
 */

if (!defined('ABSPATH')) {
	exit;
}

class AI_SEO_Pro_Metabox
{

	/**
	 * The ID of this plugin.
	 *
	 * @var string $plugin_name
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var string $version
	 */
	private $version;

	/**
	 * Meta manager instance.
	 *
	 * @var AI_SEO_Pro_Meta_Manager $meta_manager
	 */
	private $meta_manager;

	/**
	 * SEO Analyzer instance.
	 *
	 * @var AI_SEO_Pro_SEO_Analyzer $seo_analyzer
	 */
	private $seo_analyzer;

	/**
	 * Readability Analyzer instance.
	 *
	 * @var AI_SEO_Pro_Readability_Analyzer $readability_analyzer
	 */
	private $readability_analyzer;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of the plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->meta_manager = new AI_SEO_Pro_Meta_Manager();
		$this->seo_analyzer = new AI_SEO_Pro_SEO_Analyzer();
		$this->readability_analyzer = new AI_SEO_Pro_Readability_Analyzer();
	}

	/**
	 * Add meta boxes.
	 */
	public function add_meta_boxes()
	{
		$post_types = get_option('ai_seo_pro_post_types', array('post', 'page'));

		foreach ($post_types as $post_type) {
			add_meta_box(
				'ai-seo-pro-metabox',
				sprintf(
					'<span class="dashicons dashicons-search" style="margin-right: 5px;"></span>%s',
					__('AI SEO Pro', 'ai-seo-pro')
				),
				array($this, 'render_metabox'),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Render the meta box.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_metabox($post)
	{
		wp_nonce_field('ai_seo_pro_metabox', 'ai_seo_pro_nonce');

		// Get existing meta data.
		$meta_data = $this->meta_manager->get_post_meta($post->ID);
		$seo_score = get_post_meta($post->ID, '_ai_seo_score', true);

		// Get content analysis.
		$content_analysis = get_post_meta($post->ID, '_ai_seo_content_analysis', true);

		// Perform SEO analysis.
		$seo_analysis = $this->seo_analyzer->analyze(
			$post->ID,
			$post->post_content,
			$post->post_title,
			$meta_data['focus_keyword'],
			$meta_data['title'],
			$meta_data['description'],
			$post->post_name
		);

		// Perform Readability analysis.
		$readability_analysis = $this->readability_analyzer->analyze($post->post_content);

		// Include the metabox view.
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/views/metabox.php';
	}

	/**
	 * Get readability status based on Flesch score.
	 *
	 * @param float $score Flesch Reading Ease score.
	 * @return array Status with label and color.
	 */
	public function get_readability_status($score)
	{
		if ($score >= 80) {
			return array(
				'status' => 'excellent',
				'label' => __('Very Easy to Read', 'ai-seo-pro'),
				'color' => '#46b450',
			);
		} elseif ($score >= 60) {
			return array(
				'status' => 'good',
				'label' => __('Good', 'ai-seo-pro'),
				'color' => '#46b450',
			);
		} elseif ($score >= 40) {
			return array(
				'status' => 'ok',
				'label' => __('Fairly Difficult', 'ai-seo-pro'),
				'color' => '#ffb900',
			);
		} else {
			return array(
				'status' => 'needs_improvement',
				'label' => __('Needs Improvement', 'ai-seo-pro'),
				'color' => '#dc3232',
			);
		}
	}

	/**
	 * Save meta data.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 */
	public function save_meta_data($post_id, $post)
	{
		// Security checks.
		if (
			!isset($_POST['ai_seo_pro_nonce']) ||
			!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ai_seo_pro_nonce'])), 'ai_seo_pro_metabox')
		) {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		if (!current_user_can('edit_post', $post_id)) {
			return;
		}

		// Check if auto-generate is enabled.
		$auto_generate = isset($_POST['ai_seo_auto_generate']);
		update_post_meta($post_id, '_ai_seo_auto_generate', $auto_generate);

		// Save Exclude from Sitemap setting.
		$exclude_sitemap = isset($_POST['ai_seo_exclude_sitemap']) ? '1' : '';
		update_post_meta($post_id, '_ai_seo_exclude_sitemap', $exclude_sitemap);

		// If auto-generate is enabled and fields are empty, trigger generation.
		if ($auto_generate && empty($_POST['ai_seo_title'])) {
			$this->auto_generate_meta($post_id, $post);
		} else {
			// Save manual inputs.
			$meta_fields = array(
				'title' => 'ai_seo_title',
				'description' => 'ai_seo_description',
				'keywords' => 'ai_seo_keywords',
				'focus_keyword' => 'ai_seo_focus_keyword',
			);

			foreach ($meta_fields as $key => $field_name) {
				if (isset($_POST[$field_name])) {
					if ($key === 'description') {
						$value = sanitize_textarea_field(wp_unslash($_POST[$field_name]));
					} else {
						$value = sanitize_text_field(wp_unslash($_POST[$field_name]));
					}

					update_post_meta($post_id, '_' . $field_name, $value);
				}
			}

			// Save Open Graph data.
			if (isset($_POST['ai_seo_og_title'])) {
				update_post_meta($post_id, '_ai_seo_og_title', sanitize_text_field(wp_unslash($_POST['ai_seo_og_title'])));
			}
			if (isset($_POST['ai_seo_og_description'])) {
				update_post_meta($post_id, '_ai_seo_og_description', sanitize_textarea_field(wp_unslash($_POST['ai_seo_og_description'])));
			}
			// Save Open Graph Image
			if (isset($_POST['ai_seo_og_image'])) {
				update_post_meta($post_id, '_ai_seo_og_image', esc_url_raw(wp_unslash($_POST['ai_seo_og_image'])));
			}

			// Save Twitter Card data.
			if (isset($_POST['ai_seo_twitter_title'])) {
				update_post_meta($post_id, '_ai_seo_twitter_title', sanitize_text_field(wp_unslash($_POST['ai_seo_twitter_title'])));
			}
			if (isset($_POST['ai_seo_twitter_description'])) {
				update_post_meta($post_id, '_ai_seo_twitter_description', sanitize_textarea_field(wp_unslash($_POST['ai_seo_twitter_description'])));
			}

			// Save robots meta.
			if (isset($_POST['ai_seo_robots'])) {
				update_post_meta($post_id, '_ai_seo_robots', sanitize_text_field(wp_unslash($_POST['ai_seo_robots'])));
			}

			// Save canonical URL.
			if (isset($_POST['ai_seo_canonical'])) {
				update_post_meta($post_id, '_ai_seo_canonical', esc_url_raw(wp_unslash($_POST['ai_seo_canonical'])));
			}
		}

		// IMPORTANT: Clear meta cache before recalculating score
		// This ensures the score calculation uses the NEW values
		wp_cache_delete($post_id, 'post_meta');
		clean_post_cache($post_id);

		// Recalculate SEO score with fresh data
		$this->calculate_and_save_score($post_id);
	}

	/**
	 * AJAX handler for generating meta tags.
	 */
	public function ajax_generate_meta()
	{
		check_ajax_referer('ai_seo_pro_nonce', 'nonce');

		$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
		$title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
		$content = isset($_POST['content']) ? wp_kses_post(wp_unslash($_POST['content'])) : '';
		$focus_keyword = isset($_POST['focus_keyword']) ? sanitize_text_field(wp_unslash($_POST['focus_keyword'])) : '';

		// Get generation filters.
		$generate_title = isset($_POST['generate_title']) && 'true' === $_POST['generate_title'];
		$generate_description = isset($_POST['generate_description']) && 'true' === $_POST['generate_description'];
		$generate_keywords = isset($_POST['generate_keywords']) && 'true' === $_POST['generate_keywords'];

		if (!$post_id || !current_user_can('edit_post', $post_id)) {
			wp_send_json_error(array('message' => __('Permission denied.', 'ai-seo-pro')));
		}

		// Validate at least one field is selected.
		if (!$generate_title && !$generate_description && !$generate_keywords) {
			wp_send_json_error(
				array(
					'message' => __('Please select at least one field to generate.', 'ai-seo-pro'),
				)
			);
		}

		// Get API provider.
		$api_provider = get_option('ai_seo_pro_api_provider', 'gemini');
		$api_key = get_option('ai_seo_pro_api_key');

		if (empty($api_key)) {
			wp_send_json_error(
				array(
					'message' => __('API key not configured. Please configure it in settings.', 'ai-seo-pro'),
				)
			);
		}

		// Generate meta tags using AI.
		$api = $this->get_api_instance($api_provider, $api_key);
		$result = $api->generate_meta_tags($title, $content, $focus_keyword);

		if (is_wp_error($result)) {
			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
				)
			);
		}

		// Prepare response with only requested fields.
		$response_data = array();

		if ($generate_title && !empty($result['title'])) {
			update_post_meta($post_id, '_ai_seo_title', $result['title']);
			$response_data['title'] = $result['title'];
		}

		if ($generate_description && !empty($result['description'])) {
			update_post_meta($post_id, '_ai_seo_description', $result['description']);
			$response_data['description'] = $result['description'];
		}

		if ($generate_keywords && !empty($result['keywords'])) {
			update_post_meta($post_id, '_ai_seo_keywords', $result['keywords']);
			$response_data['keywords'] = $result['keywords'];
		}

		// Optional: Update OG tags if title or description was generated.
		if ($generate_title && !empty($result['og_title'])) {
			update_post_meta($post_id, '_ai_seo_og_title', $result['og_title']);
		}
		if ($generate_description && !empty($result['og_description'])) {
			update_post_meta($post_id, '_ai_seo_og_description', $result['og_description']);
		}

		$generated_fields = array();
		if ($generate_title) {
			$generated_fields[] = 'title';
		}
		if ($generate_description) {
			$generated_fields[] = 'description';
		}
		if ($generate_keywords) {
			$generated_fields[] = 'keywords';
		}

		$message = sprintf(
			/* translators: %s: comma-separated list of generated field names */
			__('Successfully generated: %s', 'ai-seo-pro'),
			implode(', ', $generated_fields)
		);

		wp_send_json_success(
			array(
				'message' => $message,
				'data' => $response_data,
			)
		);
	}

	/**
	 * AJAX handler for content analysis.
	 */
	public function ajax_analyze_content()
	{
		check_ajax_referer('ai_seo_pro_nonce', 'nonce');

		$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
		$content = isset($_POST['content']) ? wp_kses_post(wp_unslash($_POST['content'])) : '';
		$focus_keyword = isset($_POST['focus_keyword']) ? sanitize_text_field(wp_unslash($_POST['focus_keyword'])) : '';

		if (!$post_id || !current_user_can('edit_post', $post_id)) {
			wp_send_json_error(array('message' => __('Permission denied.', 'ai-seo-pro')));
		}

		$analyzer = new AI_SEO_Pro_Content_Analyzer();
		$analysis = $analyzer->analyze($content, $focus_keyword);

		// Save analysis results.
		update_post_meta($post_id, '_ai_seo_content_analysis', $analysis);

		wp_send_json_success(
			array(
				'analysis' => $analysis,
			)
		);
	}

	/**
	 * AJAX handler for calculating SEO score.
	 */
	public function ajax_calculate_score()
	{
		check_ajax_referer('ai_seo_pro_nonce', 'nonce');

		$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

		if (!$post_id || !current_user_can('edit_post', $post_id)) {
			wp_send_json_error(array('message' => __('Permission denied.', 'ai-seo-pro')));
		}

		$score = $this->calculate_and_save_score($post_id);

		wp_send_json_success(
			array(
				'score' => $score,
			)
		);
	}

	/**
	 * AJAX handler for real-time analysis update.
	 */
	public function ajax_update_analysis()
	{
		check_ajax_referer('ai_seo_pro_nonce', 'nonce');

		$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
		$content = isset($_POST['content']) ? wp_kses_post(wp_unslash($_POST['content'])) : '';
		$title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
		$focus_keyword = isset($_POST['focus_keyword']) ? sanitize_text_field(wp_unslash($_POST['focus_keyword'])) : '';
		$meta_title = isset($_POST['meta_title']) ? sanitize_text_field(wp_unslash($_POST['meta_title'])) : '';
		$meta_description = isset($_POST['meta_description']) ? sanitize_textarea_field(wp_unslash($_POST['meta_description'])) : '';
		$slug = isset($_POST['slug']) ? sanitize_text_field(wp_unslash($_POST['slug'])) : '';

		if (!$post_id || !current_user_can('edit_post', $post_id)) {
			wp_send_json_error(array('message' => __('Permission denied.', 'ai-seo-pro')));
		}

		// Perform SEO analysis.
		$seo_analysis = $this->seo_analyzer->analyze(
			$post_id,
			$content,
			$title,
			$focus_keyword,
			$meta_title,
			$meta_description,
			$slug
		);

		// Perform Readability analysis.
		$readability_analysis = $this->readability_analyzer->analyze($content);

		// Calculate SEO score.
		$seo_score = $this->calculate_score_from_analysis($seo_analysis);

		wp_send_json_success(
			array(
				'seo_analysis' => $seo_analysis,
				'readability_analysis' => $readability_analysis,
				'seo_score' => $seo_score,
			)
		);
	}

	/**
	 * Calculate SEO score from analysis results.
	 *
	 * @param array $analysis SEO analysis results.
	 * @return int Score (0-100).
	 */
	private function calculate_score_from_analysis($analysis)
	{
		$problems = isset($analysis['problems']) ? count($analysis['problems']) : 0;
		$good = isset($analysis['good']) ? count($analysis['good']) : 0;
		$total = $problems + $good;

		if (0 === $total) {
			return 0;
		}

		$score = ($good / $total) * 100;
		return round($score);
	}

	/**
	 * Auto-generate meta tags.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 */
	private function auto_generate_meta($post_id, $post)
	{
		$api_provider = get_option('ai_seo_pro_api_provider', 'gemini');
		$api_key = get_option('ai_seo_pro_api_key');

		if (empty($api_key)) {
			return;
		}

		$focus_keyword = get_post_meta($post_id, '_ai_seo_focus_keyword', true);

		$api = $this->get_api_instance($api_provider, $api_key);
		$result = $api->generate_meta_tags($post->post_title, $post->post_content, $focus_keyword);

		if (!is_wp_error($result)) {
			update_post_meta($post_id, '_ai_seo_title', $result['title']);
			update_post_meta($post_id, '_ai_seo_description', $result['description']);
			update_post_meta($post_id, '_ai_seo_keywords', $result['keywords']);
		}
	}

	/**
	 * Calculate and save SEO score.
	 *
	 * @param int $post_id The post ID.
	 * @return int The calculated score.
	 */
	private function calculate_and_save_score($post_id)
	{
		$scorer = new AI_SEO_Pro_SEO_Score();
		$score = $scorer->calculate_score($post_id);

		update_post_meta($post_id, '_ai_seo_score', $score);

		return $score;
	}

	/**
	 * Get API instance based on provider.
	 *
	 * @param string $provider API provider name.
	 * @param string $api_key  API key.
	 * @return AI_SEO_Pro_API_Base
	 */
	private function get_api_instance($provider, $api_key)
	{
		switch ($provider) {
			case 'anthropic':
				return new AI_SEO_Pro_Anthropic_API($api_key);
			case 'gemini':
				return new AI_SEO_Pro_Gemini_API($api_key);
			default:
				return new AI_SEO_Pro_Gemini_API($api_key);
		}
	}

	/**
	 * Sanitize meta field based on type.
	 *
	 * @param mixed  $value The field value.
	 * @param string $type  The field type.
	 * @return mixed
	 */
	private function sanitize_meta_field($value, $type)
	{
		switch ($type) {
			case 'title':
			case 'focus_keyword':
			case 'keywords':
				return sanitize_text_field($value);
			case 'description':
				return sanitize_textarea_field($value);
			default:
				return sanitize_text_field($value);
		}
	}

	/**
	 * Register AJAX hooks.
	 */
	public function register_ajax_hooks()
	{
		add_action('wp_ajax_ai_seo_generate_meta', array($this, 'ajax_generate_meta'));
		add_action('wp_ajax_ai_seo_analyze_content', array($this, 'ajax_analyze_content'));
		add_action('wp_ajax_ai_seo_calculate_score', array($this, 'ajax_calculate_score'));
		add_action('wp_ajax_ai_seo_update_analysis', array($this, 'ajax_update_analysis'));
	}
}