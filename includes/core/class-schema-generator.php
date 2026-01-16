<?php
/**
 * Schema markup generator.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/includes/core
 * @author     Strativ AB
 */
class RankFlow_SEO_Schema_Generator
{

	/**
	 * Generate Article schema.
	 *
	 * @param    int    $post_id    Post ID
	 * @return   array
	 */
	public function generate_article_schema($post_id)
	{
		$post = get_post($post_id);

		if (!$post) {
			return array();
		}

		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Article',
			'headline' => get_the_title($post_id),
			'datePublished' => get_the_date('c', $post_id),
			'dateModified' => get_the_modified_date('c', $post_id),
			'author' => array(
				'@type' => 'Person',
				'name' => get_the_author_meta('display_name', $post->post_author),
			),
			'publisher' => array(
				'@type' => 'Organization',
				'name' => get_bloginfo('name'),
				'logo' => array(
					'@type' => 'ImageObject',
					'url' => get_site_icon_url(),
				),
			),
		);

		// Add featured image if available
		if (has_post_thumbnail($post_id)) {
			$schema['image'] = get_the_post_thumbnail_url($post_id, 'full');
		}

		// Add description
		$description = get_post_meta($post_id, '_rankflow_seo_description', true);
		if (!empty($description)) {
			$schema['description'] = $description;
		}

		return $schema;
	}

	/**
	 * Generate WebPage schema.
	 *
	 * @param    int    $post_id    Post ID
	 * @return   array
	 */
	public function generate_webpage_schema($post_id)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'WebPage',
			'name' => get_the_title($post_id),
			'url' => get_permalink($post_id),
			'datePublished' => get_the_date('c', $post_id),
			'dateModified' => get_the_modified_date('c', $post_id),
		);

		$description = get_post_meta($post_id, '_rankflow_seo_description', true);
		if (!empty($description)) {
			$schema['description'] = $description;
		}

		return $schema;
	}

	/**
	 * Generate Organization schema.
	 *
	 * @return   array
	 */
	public function generate_organization_schema()
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Organization',
			'name' => get_bloginfo('name'),
			'url' => home_url(),
		);

		if (get_site_icon_url()) {
			$schema['logo'] = get_site_icon_url();
		}

		return $schema;
	}

	/**
	 * Generate BreadcrumbList schema.
	 *
	 * @param    int    $post_id    Post ID
	 * @return   array
	 */
	public function generate_breadcrumb_schema($post_id)
	{
		$post = get_post($post_id);

		$items = array(
			array(
				'@type' => 'ListItem',
				'position' => 1,
				'name' => __('Home', 'rankflow-seo'),
				'item' => home_url(),
			),
		);

		$position = 2;

		// Add category for posts
		if ($post->post_type === 'post') {
			$categories = get_the_category($post_id);
			if (!empty($categories)) {
				$category = $categories[0];
				$items[] = array(
					'@type' => 'ListItem',
					'position' => $position++,
					'name' => $category->name,
					'item' => get_category_link($category->term_id),
				);
			}
		}

		// Add current page
		$items[] = array(
			'@type' => 'ListItem',
			'position' => $position,
			'name' => get_the_title($post_id),
			'item' => get_permalink($post_id),
		);

		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'BreadcrumbList',
			'itemListElement' => $items,
		);

		return $schema;
	}
}