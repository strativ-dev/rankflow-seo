<?php
/**
 * Meta data manager.
 *
 * @package    MPSEO
 * @subpackage MPSEO/includes/core
 * @author     Strativ AB
 */
class MPSEO_Meta_Manager
{

	/**
	 * Get all SEO meta for a post.
	 *
	 * @param    int    $post_id    Post ID
	 * @return   array
	 */
	public function get_post_meta($post_id)
	{
		return array(
			'title' => get_post_meta($post_id, '_mpseo_title', true),
			'description' => get_post_meta($post_id, '_mpseo_description', true),
			'keywords' => get_post_meta($post_id, '_mpseo_keywords', true),
			'focus_keyword' => get_post_meta($post_id, '_mpseo_focus_keyword', true),
			'og_title' => get_post_meta($post_id, '_mpseo_og_title', true),
			'og_description' => get_post_meta($post_id, '_mpseo_og_description', true),
			'twitter_title' => get_post_meta($post_id, '_mpseo_twitter_title', true),
			'twitter_description' => get_post_meta($post_id, '_mpseo_twitter_description', true),
			'robots' => get_post_meta($post_id, '_mpseo_robots', true),
			'canonical' => get_post_meta($post_id, '_mpseo_canonical', true),
			'auto_generate' => get_post_meta($post_id, '_mpseo_auto_generate', true),
		);
	}

	/**
	 * Save SEO meta for a post.
	 *
	 * @param    int      $post_id    Post ID
	 * @param    array    $data       Meta data
	 * @return   bool
	 */
	public function save_post_meta($post_id, $data)
	{
		$meta_fields = array(
			'title' => '_mpseo_title',
			'description' => '_mpseo_description',
			'keywords' => '_mpseo_keywords',
			'focus_keyword' => '_mpseo_focus_keyword',
			'og_title' => '_mpseo_og_title',
			'og_description' => '_mpseo_og_description',
			'twitter_title' => '_mpseo_twitter_title',
			'twitter_description' => '_mpseo_twitter_description',
			'robots' => '_mpseo_robots',
			'canonical' => '_mpseo_canonical',
		);

		foreach ($meta_fields as $key => $meta_key) {
			if (isset($data[$key])) {
				update_post_meta($post_id, $meta_key, $data[$key]);
			}
		}

		return true;
	}

	/**
	 * Delete SEO meta for a post.
	 *
	 * @param    int    $post_id    Post ID
	 * @return   bool
	 */
	public function delete_post_meta($post_id)
	{
		$meta_keys = array(
			'_mpseo_title',
			'_mpseo_description',
			'_mpseo_keywords',
			'_mpseo_focus_keyword',
			'_mpseo_og_title',
			'_mpseo_og_description',
			'_mpseo_twitter_title',
			'_mpseo_twitter_description',
			'_mpseo_robots',
			'_mpseo_canonical',
			'_mpseo_score',
			'_mpseo_content_analysis',
		);

		foreach ($meta_keys as $meta_key) {
			delete_post_meta($post_id, $meta_key);
		}

		return true;
	}

	/**
	 * Get meta title or fallback.
	 *
	 * @param    int    $post_id    Post ID
	 * @return   string
	 */
	public function get_title($post_id)
	{
		$title = get_post_meta($post_id, '_mpseo_title', true);

		if (empty($title)) {
			$post = get_post($post_id);
			$separator = get_option('mpseo_title_separator', '-');
			$title = $post->post_title . ' ' . $separator . ' ' . get_bloginfo('name');
		}

		return $title;
	}

	/**
	 * Get meta description or fallback.
	 *
	 * @param    int    $post_id    Post ID
	 * @return   string
	 */
	public function get_description($post_id)
	{
		$description = get_post_meta($post_id, '_mpseo_description', true);

		if (empty($description)) {
			$post = get_post($post_id);
			$description = wp_trim_words(wp_strip_all_tags($post->post_content), 20);
		}

		return $description;
	}
}