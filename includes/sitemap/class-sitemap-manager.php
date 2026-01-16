<?php
/**
 * Sitemap Manager - Core sitemap functionality
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/includes/sitemap
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class RankFlow_SEO_Sitemap_Manager
 */
class RankFlow_SEO_Sitemap_Manager
{

	/**
	 * Sitemap providers
	 *
	 * @var array
	 */
	private $providers = array();

	/**
	 * Post types to exclude from sitemap
	 *
	 * @var array
	 */
	private $excluded_post_types = array(
		'attachment',
		'elementor_library',
		'e-landing-page',
		'elementor_font',
		'elementor_icons',
	);

	/**
	 * Taxonomies to exclude from sitemap
	 *
	 * @var array
	 */
	private $excluded_taxonomies = array(
		'post_format',
		'elementor_library_type',
		'elementor_library_category',
		'elementor_font_type',
	);

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->register_default_providers();
	}

	/**
	 * Initialize sitemap functionality
	 */
	public function init()
	{
		// Check if sitemaps are enabled.
		if (!$this->is_enabled()) {
			return;
		}

		// Add rewrite rules.
		add_action('init', array($this, 'add_rewrite_rules'), 1);

		// Handle sitemap requests.
		add_action('template_redirect', array($this, 'handle_sitemap_request'), 1);

		// Add query vars.
		add_filter('query_vars', array($this, 'add_query_vars'));

		// Ping search engines on post publish.
		add_action('publish_post', array($this, 'ping_search_engines'));
		add_action('publish_page', array($this, 'ping_search_engines'));

		// Add sitemap link to robots.txt.
		add_filter('robots_txt', array($this, 'add_sitemap_to_robots'), 10, 2);

		// Flush rewrite rules on settings save.
		add_action('update_option_rankflow_seo_sitemap_enabled', array($this, 'flush_rewrite_rules'));
	}

	/**
	 * Check if sitemaps are enabled
	 *
	 * @return bool
	 */
	public function is_enabled()
	{
		return (bool) get_option('rankflow_seo_sitemap_enabled', true);
	}

	/**
	 * Register default sitemap providers
	 */
	private function register_default_providers()
	{
		// Post types provider.
		$this->providers['post_type'] = array(
			'class' => 'RankFlow_SEO_Sitemap_Post_Type',
			'file' => 'class-sitemap-post-type.php',
		);

		// Taxonomy provider.
		$this->providers['taxonomy'] = array(
			'class' => 'RankFlow_SEO_Sitemap_Taxonomy',
			'file' => 'class-sitemap-taxonomy.php',
		);

		// Author provider.
		$this->providers['author'] = array(
			'class' => 'RankFlow_SEO_Sitemap_Author',
			'file' => 'class-sitemap-author.php',
		);
	}

	/**
	 * Add rewrite rules for sitemaps
	 */
	public function add_rewrite_rules()
	{
		// Redirect sitemap.xml to sitemap_index.xml.
		add_rewrite_rule(
			'^sitemap\.xml$',
			'index.php?rankflow_seo_sitemap=redirect',
			'top'
		);

		// Main sitemap index.
		add_rewrite_rule(
			'^sitemap_index\.xml$',
			'index.php?rankflow_seo_sitemap=index',
			'top'
		);

		// Taxonomy sitemaps.
		add_rewrite_rule(
			'^([a-zA-Z0-9_-]+)-taxonomy-sitemap([0-9]*)\.xml$',
			'index.php?rankflow_seo_sitemap=taxonomy&rankflow_seo_sitemap_type=$matches[1]&rankflow_seo_sitemap_page=$matches[2]',
			'top'
		);

		// Post type sitemaps.
		add_rewrite_rule(
			'^((?!taxonomy)[a-z0-9_-]+)-sitemap([0-9]*)\.xml$',
			'index.php?rankflow_seo_sitemap=post_type&rankflow_seo_sitemap_type=$matches[1]&rankflow_seo_sitemap_page=$matches[2]',
			'top'
		);


		// Author sitemap.
		add_rewrite_rule(
			'^author-sitemap([0-9]*)\.xml$',
			'index.php?rankflow_seo_sitemap=author&rankflow_seo_sitemap_page=$matches[1]',
			'top'
		);

		// XSL stylesheet.
		add_rewrite_rule(
			'^sitemap\.xsl$',
			'index.php?rankflow_seo_sitemap=xsl',
			'top'
		);
	}

	/**
	 * Add query vars
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function add_query_vars($vars)
	{
		$vars[] = 'rankflow_seo_sitemap';
		$vars[] = 'rankflow_seo_sitemap_type';
		$vars[] = 'rankflow_seo_sitemap_page';
		return $vars;
	}

	/**
	 * Handle sitemap request
	 */
	public function handle_sitemap_request()
	{
		$sitemap = get_query_var('rankflow_seo_sitemap');

		if (empty($sitemap)) {
			return;
		}

		// Handle redirect from sitemap.xml to sitemap_index.xml.
		if ('redirect' === $sitemap) {
			wp_safe_redirect(home_url('sitemap_index.xml'), 301);
			exit;
		}

		// Set XML headers.
		$this->set_headers();

		switch ($sitemap) {
			case 'index':
				$this->render_sitemap_index();
				break;

			case 'post_type':
				$type = get_query_var('rankflow_seo_sitemap_type');
				$page = (int) get_query_var('rankflow_seo_sitemap_page', 1);
				$this->render_post_type_sitemap($type, $page);
				break;

			case 'taxonomy':
				$type = get_query_var('rankflow_seo_sitemap_type');
				$page = (int) get_query_var('rankflow_seo_sitemap_page', 1);
				$this->render_taxonomy_sitemap($type, $page);
				break;

			case 'author':
				$page = (int) get_query_var('rankflow_seo_sitemap_page', 1);
				$this->render_author_sitemap($page);
				break;

			case 'xsl':
				$this->render_xsl_stylesheet();
				break;

			default:
				return;
		}

		exit;
	}

	/**
	 * Set XML headers
	 */
	private function set_headers()
	{
		header('Content-Type: application/xml; charset=UTF-8');
		header('X-Robots-Tag: noindex, follow');
		header('Cache-Control: max-age=3600');
	}

	/**
	 * Render sitemap index
	 */
	private function render_sitemap_index()
	{
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<?xml-stylesheet type="text/xsl" href="' . esc_url(home_url('sitemap.xsl')) . '"?>' . "\n";
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		// Get enabled post types.
		$post_types = $this->get_enabled_post_types();

		foreach ($post_types as $post_type) {
			$count = $this->get_post_type_count($post_type);

			if (0 === $count) {
				continue;
			}

			$pages = ceil($count / $this->get_entries_per_page());

			for ($page = 1; $page <= $pages; $page++) {
				$page_suffix = ($page > 1) ? $page : '';
				$lastmod = $this->get_post_type_lastmod($post_type);

				echo "\t<sitemap>\n";
				echo "\t\t<loc>" . esc_url(home_url($post_type . '-sitemap' . $page_suffix . '.xml')) . "</loc>\n";

				if ($lastmod) {
					echo "\t\t<lastmod>" . esc_html($lastmod) . "</lastmod>\n";
				}

				echo "\t</sitemap>\n";
			}
		}

		// Get enabled taxonomies - FIXED: Added lastmod inside the loop
		if ($this->is_taxonomy_sitemap_enabled()) {
			$taxonomies = $this->get_enabled_taxonomies();

			foreach ($taxonomies as $taxonomy) {
				$count = $this->get_taxonomy_count($taxonomy);

				if (0 === $count) {
					continue;
				}

				$pages = ceil($count / $this->get_entries_per_page());

				for ($page = 1; $page <= $pages; $page++) {
					$page_suffix = ($page > 1) ? $page : '';
					$lastmod = $this->get_taxonomy_lastmod($taxonomy); // Get lastmod for THIS taxonomy

					echo "\t<sitemap>\n";
					echo "\t\t<loc>" . esc_url(home_url($taxonomy . '-taxonomy-sitemap' . $page_suffix . '.xml')) . "</loc>\n";

					// Add lastmod if available
					if ($lastmod) {
						echo "\t\t<lastmod>" . esc_html($lastmod) . "</lastmod>\n";
					}

					echo "\t</sitemap>\n";
				}
			}
		}

		// Author sitemap.
		if ($this->is_author_sitemap_enabled()) {
			$count = $this->get_author_count();

			if ($count > 0) {
				$pages = ceil($count / $this->get_entries_per_page());

				for ($page = 1; $page <= $pages; $page++) {
					$page_suffix = ($page > 1) ? $page : '';

					echo "\t<sitemap>\n";
					echo "\t\t<loc>" . esc_url(home_url('author-sitemap' . $page_suffix . '.xml')) . "</loc>\n";
					echo "\t</sitemap>\n";
				}
			}
		}

		echo '</sitemapindex>';
	}

	/**
	 * Render post type sitemap
	 *
	 * @param string $post_type Post type.
	 * @param int    $page      Page number.
	 */
	private function render_post_type_sitemap($post_type, $page = 1)
	{
		// Validate post type.
		if (!post_type_exists($post_type) || !in_array($post_type, $this->get_enabled_post_types(), true)) {
			status_header(404);
			return;
		}

		$page = max(1, $page);
		$per_page = $this->get_entries_per_page();
		$offset = ($page - 1) * $per_page;

		$posts = get_posts(array(
			'post_type' => $post_type,
			'post_status' => 'publish',
			'posts_per_page' => $per_page,
			'offset' => $offset,
			'orderby' => 'modified',
			'order' => 'DESC',
			'no_found_rows' => true,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Necessary for sitemap exclusion.
			'meta_query' => array(
				'relation' => 'AND',
				// Exclude posts with noindex robots meta.
				array(
					'relation' => 'OR',
					array(
						'key' => '_rankflow_seo_robots',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key' => '_rankflow_seo_robots',
						'value' => 'noindex',
						'compare' => 'NOT LIKE',
					),
				),
				// Exclude posts marked as "Exclude from Sitemap".
				array(
					'relation' => 'OR',
					array(
						'key' => '_rankflow_seo_exclude_sitemap',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key' => '_rankflow_seo_exclude_sitemap',
						'value' => '1',
						'compare' => '!=',
					),
				),
			),
		));

		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<?xml-stylesheet type="text/xsl" href="' . esc_url(home_url('sitemap.xsl')) . '"?>' . "\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
		echo ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

		foreach ($posts as $post) {
			$url = $this->get_post_sitemap_entry($post);
			$this->render_url_entry($url);
		}

		echo '</urlset>';
	}

	/**
	 * Get post sitemap entry data
	 *
	 * @param WP_Post $post Post object.
	 * @return array
	 */
	private function get_post_sitemap_entry($post)
	{
		$entry = array(
			'loc' => get_permalink($post),
			'lastmod' => get_post_modified_time('c', true, $post),
		);

		// Add priority based on post type.
		$priority = '0.6';
		if ('page' === $post->post_type) {
			$priority = '0.8';
			if ((int) get_option('page_on_front') === $post->ID) {
				$priority = '1.0';
			}
		}
		$entry['priority'] = $priority;

		// Add change frequency.
		$entry['changefreq'] = $this->get_changefreq($post);

		// Add images if enabled.
		if ($this->is_image_sitemap_enabled()) {
			$entry['images'] = $this->get_post_images($post);
		}

		return $entry;
	}

	/**
	 * Get post images for sitemap
	 *
	 * @param WP_Post $post Post object.
	 * @return array
	 */
	private function get_post_images($post)
	{
		$images = array();

		// Featured image.
		if (has_post_thumbnail($post->ID)) {
			$image_id = get_post_thumbnail_id($post->ID);
			$image_url = wp_get_attachment_url($image_id);
			$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);

			if ($image_url) {
				$images[] = array(
					'loc' => $image_url,
					'title' => get_the_title($image_id),
					'alt' => $image_alt,
				);
			}
		}

		// Images in content.
		if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $post->post_content, $matches)) {
			foreach ($matches[1] as $image_url) {
				// Skip external images.
				if (false === strpos($image_url, home_url()) && 0 !== strpos($image_url, '/')) {
					continue;
				}

				$images[] = array(
					'loc' => $image_url,
				);
			}
		}

		// Limit to 1000 images per page (Google limit).
		return array_slice($images, 0, 1000);
	}

	/**
	 * Render taxonomy sitemap
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param int    $page     Page number.
	 */
	private function render_taxonomy_sitemap($taxonomy, $page = 1)
	{
		// Validate taxonomy.
		if (
			!taxonomy_exists($taxonomy)
			|| !in_array($taxonomy, $this->get_enabled_taxonomies(), true)
		) {
			wp_die('', '', array('response' => 404));
		}

		$page = max(1, $page);
		$per_page = $this->get_entries_per_page();
		$offset = ($page - 1) * $per_page;

		$terms = get_terms(array(
			'taxonomy' => $taxonomy,
			'hide_empty' => true,
			'number' => $per_page,
			'offset' => $offset,
		));

		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<?xml-stylesheet type="text/xsl" href="' . esc_url(home_url('sitemap.xsl')) . '"?>' . "\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		foreach ($terms as $term) {
			$url = array(
				'loc' => get_term_link($term),
				'changefreq' => 'weekly',
				'priority' => '0.4',
			);

			$this->render_url_entry($url);
		}

		echo '</urlset>';
	}

	/**
	 * Render author sitemap
	 *
	 * @param int $page Page number.
	 */
	private function render_author_sitemap($page = 1)
	{
		$page = max(1, $page);
		$per_page = $this->get_entries_per_page();
		$offset = ($page - 1) * $per_page;

		$authors = get_users(array(
			'has_published_posts' => true,
			'number' => $per_page,
			'offset' => $offset,
			'orderby' => 'post_count',
			'order' => 'DESC',
		));

		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<?xml-stylesheet type="text/xsl" href="' . esc_url(home_url('sitemap.xsl')) . '"?>' . "\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		foreach ($authors as $author) {
			$url = array(
				'loc' => get_author_posts_url($author->ID),
				'changefreq' => 'weekly',
				'priority' => '0.4',
			);

			$this->render_url_entry($url);
		}

		echo '</urlset>';
	}

	/**
	 * Render URL entry
	 *
	 * @param array $url URL data.
	 */
	private function render_url_entry($url)
	{
		if (empty($url['loc']) || is_wp_error($url['loc'])) {
			return;
		}

		echo "\t<url>\n";
		echo "\t\t<loc>" . esc_url($url['loc']) . "</loc>\n";

		if (!empty($url['lastmod'])) {
			echo "\t\t<lastmod>" . esc_html($url['lastmod']) . "</lastmod>\n";
		}

		if (!empty($url['changefreq'])) {
			echo "\t\t<changefreq>" . esc_html($url['changefreq']) . "</changefreq>\n";
		}

		if (!empty($url['priority'])) {
			echo "\t\t<priority>" . esc_html($url['priority']) . "</priority>\n";
		}

		// Images.
		if (!empty($url['images'])) {
			foreach ($url['images'] as $image) {
				echo "\t\t<image:image>\n";
				echo "\t\t\t<image:loc>" . esc_url($image['loc']) . "</image:loc>\n";

				if (!empty($image['title'])) {
					echo "\t\t\t<image:title>" . esc_html($image['title']) . "</image:title>\n";
				}

				if (!empty($image['alt'])) {
					echo "\t\t\t<image:caption>" . esc_html($image['alt']) . "</image:caption>\n";
				}

				echo "\t\t</image:image>\n";
			}
		}

		echo "\t</url>\n";
	}

	/**
	 * Render XSL stylesheet
	 */
	private function render_xsl_stylesheet()
	{
		header('Content-Type: text/xsl; charset=UTF-8');

		echo '<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>

<xsl:template match="/">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>XML Sitemap - ' . esc_html(get_bloginfo('name')) . '</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		body {
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
			font-size: 14px;
			color: #333;
			background: #f1f1f1;
			margin: 0;
			padding: 20px;
		}
		.container {
			max-width: 1200px;
			margin: 0 auto;
			background: #fff;
			padding: 30px;
			border-radius: 4px;
			box-shadow: 0 1px 3px rgba(0,0,0,0.1);
		}
		h1 {
			color: #23282d;
			font-size: 24px;
			font-weight: 600;
			margin: 0 0 10px;
		}
		.description {
			color: #666;
			margin-bottom: 20px;
		}
		.stats {
			background: #f7f7f7;
			padding: 15px;
			border-radius: 4px;
			margin-bottom: 20px;
		}
		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 20px;
		}
		th {
			background: #0073aa;
			color: #fff;
			padding: 12px;
			text-align: left;
			font-weight: 600;
		}
		td {
			padding: 10px 12px;
			border-bottom: 1px solid #eee;
		}
		tr:hover td {
			background: #f7f7f7;
		}
		a {
			color: #0073aa;
			text-decoration: none;
		}
		a:hover {
			text-decoration: underline;
		}
		.priority {
			text-align: center;
		}
		.images {
			color: #666;
			font-size: 12px;
		}
		.brand {
			margin-top: 20px;
			padding-top: 20px;
			border-top: 1px solid #eee;
			color: #666;
			font-size: 12px;
		}
	</style>
</head>
<body>
<div class="container">
	<h1>XML Sitemap</h1>
	<p class="description">This is the XML sitemap for <strong>' . esc_html(get_bloginfo('name')) . '</strong>, generated by RankFlow SEO.</p>

	<xsl:choose>
		<xsl:when test="sitemap:sitemapindex">
			<div class="stats">
				<strong>Sitemap Index:</strong> This index contains <xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"/> sitemaps.
			</div>
			<table>
				<tr>
					<th>Sitemap URL</th>
					<th style="width:200px">Last Modified</th>
				</tr>
				<xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
					<tr>
						<td>
							<a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
						</td>
						<td>
							<xsl:value-of select="sitemap:lastmod"/>
						</td>
					</tr>
				</xsl:for-each>
			</table>
		</xsl:when>
		<xsl:otherwise>
			<div class="stats">
				<strong>URL List:</strong> This sitemap contains <xsl:value-of select="count(sitemap:urlset/sitemap:url)"/> URLs.
			</div>
			<table>
				<tr>
					<th>URL</th>
					<th style="width:120px">Priority</th>
					<th style="width:120px">Change Freq</th>
					<th style="width:200px">Last Modified</th>
				</tr>
				<xsl:for-each select="sitemap:urlset/sitemap:url">
					<tr>
						<td>
							<a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
							<xsl:if test="count(image:image) &gt; 0">
								<div class="images">
									<xsl:value-of select="count(image:image)"/> image(s)
								</div>
							</xsl:if>
						</td>
						<td class="priority">
							<xsl:value-of select="sitemap:priority"/>
						</td>
						<td>
							<xsl:value-of select="sitemap:changefreq"/>
						</td>
						<td>
							<xsl:value-of select="sitemap:lastmod"/>
						</td>
					</tr>
				</xsl:for-each>
			</table>
		</xsl:otherwise>
	</xsl:choose>

	<p class="brand">Generated by RankFlow SEO for WordPress</p>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>';
	}

	/**
	 * Get enabled post types
	 *
	 * @return array
	 */
	public function get_enabled_post_types()
	{
		$saved = get_option('rankflow_seo_sitemap_post_types', array());

		if (!empty($saved)) {
			// Filter out excluded post types from saved settings.
			return array_values(array_diff($saved, $this->excluded_post_types));
		}

		// Default to public post types.
		$post_types = get_post_types(array('public' => true), 'names');

		// Remove excluded post types.
		foreach ($this->excluded_post_types as $excluded) {
			unset($post_types[$excluded]);
		}

		return array_values($post_types);
	}

	/**
	 * Get enabled taxonomies
	 *
	 * @return array
	 */
	public function get_enabled_taxonomies()
	{
		$saved = get_option('rankflow_seo_sitemap_taxonomies', null);

		// Always detect current public taxonomies
		$public = get_taxonomies(
			array('public' => true),
			'names'
		);

		// Remove excluded taxonomies
		$public = array_diff($public, $this->excluded_taxonomies);

		/**
		 * CASE 1:
		 * Option does NOT exist yet
		 * → Seed defaults (IMPORTANT)
		 */
		if ($saved === null) {
			update_option(
				'rankflow_seo_sitemap_taxonomies',
				array_values($public)
			);
			return array_values($public);
		}

		/**
		 * CASE 2:
		 * Option exists but empty
		 * → User disabled ALL taxonomies
		 */
		if (empty($saved) || !is_array($saved)) {
			return array();
		}

		/**
		 * CASE 3:
		 * Option exists and user-selected
		 * → STRICT allow-list (disable works!)
		 */
		return array_values(array_intersect($public, $saved));
	}


	/**
	 * Get entries per page
	 *
	 * @return int
	 */
	public function get_entries_per_page()
	{
		return (int) get_option('rankflow_seo_sitemap_entries_per_page', 1000);
	}

	/**
	 * Check if taxonomy sitemap is enabled
	 *
	 * @return bool
	 */
	public function is_taxonomy_sitemap_enabled()
	{
		return (bool) get_option('rankflow_seo_sitemap_include_taxonomies', true);
	}

	/**
	 * Check if author sitemap is enabled
	 *
	 * @return bool
	 */
	public function is_author_sitemap_enabled()
	{
		return (bool) get_option('rankflow_seo_sitemap_include_authors', false);
	}

	/**
	 * Check if image sitemap is enabled
	 *
	 * @return bool
	 */
	public function is_image_sitemap_enabled()
	{
		return (bool) get_option('rankflow_seo_sitemap_include_images', true);
	}

	/**
	 * Get post type count (excluding noindex and sitemap-excluded posts)
	 *
	 * @param string $post_type Post type.
	 * @return int
	 */
	private function get_post_type_count($post_type)
	{
		global $wpdb;

		// Prepare the LIKE pattern with wildcards.
		$noindex_pattern = '%' . $wpdb->esc_like('noindex') . '%';

		// Count posts excluding those with noindex or exclude_sitemap.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom count with meta exclusions.
		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT p.ID) 
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pm_robots ON (p.ID = pm_robots.post_id AND pm_robots.meta_key = '_rankflow_seo_robots')
			LEFT JOIN {$wpdb->postmeta} pm_exclude ON (p.ID = pm_exclude.post_id AND pm_exclude.meta_key = '_rankflow_seo_exclude_sitemap')
			WHERE p.post_type = %s 
			AND p.post_status = 'publish'
			AND (pm_robots.meta_value IS NULL OR pm_robots.meta_value NOT LIKE %s)
			AND (pm_exclude.meta_value IS NULL OR pm_exclude.meta_value != '1')",
				$post_type,
				$noindex_pattern
			)
		);

		return (int) $count;
	}

	/**
	 * Get post type last modified date
	 *
	 * @param string $post_type Post type.
	 * @return string|false
	 */
	private function get_post_type_lastmod($post_type)
	{
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Simple query for lastmod.
		$date = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_modified_gmt 
				FROM {$wpdb->posts} 
				WHERE post_type = %s 
				AND post_status = 'publish' 
				ORDER BY post_modified_gmt DESC 
				LIMIT 1",
				$post_type
			)
		);

		if ($date) {
			return gmdate('c', strtotime($date));
		}

		return false;
	}

	/**
	 * Get taxonomy last modified date
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @return string|false
	 */
	private function get_taxonomy_lastmod($taxonomy)
	{
		// Check cache first.
		$cache_key = 'lastmod_' . $taxonomy;
		$cache_group = 'rankflow_seo_sitemap';
		$date = wp_cache_get($cache_key, $cache_group);

		if (false === $date) {
			global $wpdb;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Optimized custom query for performance, cached below.
			$date = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT p.post_modified_gmt 
                    FROM {$wpdb->posts} p
                    INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_ID
                    INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    WHERE tt.taxonomy = %s 
                    AND p.post_status = 'publish'
                    ORDER BY p.post_modified_gmt DESC 
                    LIMIT 1",
					$taxonomy
				)
			);

			// Set cache for 1 hour.
			wp_cache_set($cache_key, $date, $cache_group, HOUR_IN_SECONDS);
		}

		if ($date) {
			return gmdate('c', strtotime($date));
		}

		return false;
	}

	/**
	 * Get taxonomy term count
	 *
	 * @param string $taxonomy Taxonomy.
	 * @return int
	 */
	private function get_taxonomy_count($taxonomy)
	{
		return (int) wp_count_terms(array(
			'taxonomy' => $taxonomy,
			'hide_empty' => true,
		));
	}

	/**
	 * Get author count
	 *
	 * @return int
	 */
	private function get_author_count()
	{
		return count(get_users(array(
			'has_published_posts' => true,
			'fields' => 'ID',
		)));
	}

	/**
	 * Get change frequency
	 *
	 * @param WP_Post $post Post object.
	 * @return string
	 */
	private function get_changefreq($post)
	{
		$modified = strtotime($post->post_modified_gmt);
		$now = time();
		$diff = $now - $modified;

		if ($diff < DAY_IN_SECONDS) {
			return 'hourly';
		} elseif ($diff < WEEK_IN_SECONDS) {
			return 'daily';
		} elseif ($diff < MONTH_IN_SECONDS) {
			return 'weekly';
		} elseif ($diff < YEAR_IN_SECONDS) {
			return 'monthly';
		}

		return 'yearly';
	}

	/**
	 * Add sitemap to robots.txt
	 *
	 * Only adds sitemap if the Robots.txt Editor is disabled.
	 * When enabled, the Robots.txt Editor handles sitemap inclusion.
	 *
	 * @param string $output Robots.txt content.
	 * @param bool   $public Site visibility.
	 * @return string
	 */
	public function add_sitemap_to_robots($output, $public)
	{
		// Don't add if robots.txt editor is enabled - it will handle the sitemap.
		if (get_option('rankflow_seo_robots_enabled', false)) {
			return $output;
		}

		// Don't add if sitemap is disabled.
		if (!get_option('rankflow_seo_sitemap_enabled', false)) {
			return $output;
		}

		if ($public) {
			// Remove WordPress default sitemap and add ours instead.
			$output = preg_replace('/Sitemap:\s*' . preg_quote(home_url('wp-sitemap.xml'), '/') . '\s*\n?/i', '', $output);

			$output .= "\n# RankFlow SEO Sitemap\n";
			$output .= 'Sitemap: ' . home_url('sitemap_index.xml') . "\n";
		}

		return $output;
	}

	/**
	 * Ping search engines
	 *
	 * @param int $post_id Post ID.
	 */
	public function ping_search_engines($post_id = 0)
	{
		if (!get_option('rankflow_seo_sitemap_ping_search_engines', true)) {
			return;
		}

		// Only ping once per hour.
		$last_ping = get_transient('rankflow_seo_sitemap_last_ping');
		if ($last_ping) {
			return;
		}

		$sitemap_url = home_url('sitemap_index.xml');

		// Ping Google.
		wp_remote_get('https://www.google.com/ping?sitemap=' . rawurlencode($sitemap_url), array(
			'timeout' => 3,
			'blocking' => false,
			'sslverify' => false,
		));

		// Ping Bing.
		wp_remote_get('https://www.bing.com/ping?sitemap=' . rawurlencode($sitemap_url), array(
			'timeout' => 3,
			'blocking' => false,
			'sslverify' => false,
		));

		// Set transient to prevent excessive pinging.
		set_transient('rankflow_seo_sitemap_last_ping', time(), HOUR_IN_SECONDS);
	}

	/**
	 * Flush rewrite rules
	 */
	public function flush_rewrite_rules()
	{
		$this->add_rewrite_rules();
		flush_rewrite_rules();
	}

	/**
	 * Get sitemap index URL
	 *
	 * @return string
	 */
	public function get_sitemap_index_url()
	{
		return home_url('sitemap_index.xml');
	}
}