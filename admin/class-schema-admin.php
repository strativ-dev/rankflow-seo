<?php
/**
 * Schema Admin Handler
 *
 * Manages schema markup generation and admin interface
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

class AI_SEO_Pro_Schema_Admin
{

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Schema types configuration.
	 *
	 * @var array
	 */
	private $schema_types;

	/**
	 * Constructor
	 *
	 * @param string $plugin_name The plugin name.
	 */
	public function __construct($plugin_name = 'ai-seo-pro')
	{
		$this->plugin_name = $plugin_name;
		$this->schema_types = array(); // Initialize empty, load on demand.
	}

	/**
	 * Register settings
	 */
	public function register_settings()
	{
		register_setting(
			'ai_seo_pro_schema',
			'ai_seo_pro_schemas',
			array(
				'type' => 'array',
				'sanitize_callback' => array($this, 'sanitize_schemas'),
				'default' => array(),
			)
		);

		register_setting(
			'ai_seo_pro_schema',
			'ai_seo_pro_schema_enabled',
			array(
				'type' => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default' => true,
			)
		);
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_scripts($hook)
	{
		if ('ai-seo-pro_page_ai-seo-pro-schema' !== $hook) {
			return;
		}

		wp_enqueue_media();

		// Enqueue Select2 for better dropdowns.
		wp_enqueue_style(
			'select2',
			AI_SEO_PRO_PLUGIN_URL . 'assets/vendor/select2/select2.min.css',
			array(),
			'4.1.0'
		);

		wp_enqueue_script(
			'select2',
			AI_SEO_PRO_PLUGIN_URL . 'assets/vendor/select2/select2.min.js',
			array('jquery'),
			'4.1.0',
			true
		);

		wp_enqueue_script(
			'ai-seo-pro-schema-admin',
			AI_SEO_PRO_PLUGIN_URL . 'assets/js/schema-admin.js',
			array('jquery', 'jquery-ui-sortable', 'select2'),
			AI_SEO_PRO_VERSION,
			true
		);

		wp_localize_script(
			'ai-seo-pro-schema-admin',
			'aiSeoProSchema',
			array(
				'schemaTypes' => $this->get_schema_types(),
				'nonce' => wp_create_nonce('ai_seo_pro_schema_nonce'),
				'confirmDelete' => __('Are you sure you want to delete this schema?', 'ai-seo-pro'),
				'selectImage' => __('Select Image', 'ai-seo-pro'),
				'useImage' => __('Use this image', 'ai-seo-pro'),
				'pages' => $this->get_all_pages(),
				'posts' => $this->get_all_posts(),
				'postTypes' => $this->get_post_types(),
			)
		);
	}

	/**
	 * Get all pages for selection
	 *
	 * @return array Pages list.
	 */
	private function get_all_pages()
	{
		$pages = get_posts(array(
			'post_type' => 'page',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => 'title',
			'order' => 'ASC',
		));

		$options = array();
		foreach ($pages as $page) {
			$options[] = array(
				'id' => $page->ID,
				'title' => $page->post_title,
			);
		}

		return $options;
	}

	/**
	 * Get all posts for selection
	 *
	 * @return array Posts list.
	 */
	private function get_all_posts()
	{
		$posts = get_posts(array(
			'post_type' => 'post',
			'posts_per_page' => 100,
			'post_status' => 'publish',
			'orderby' => 'title',
			'order' => 'ASC',
		));

		$options = array();
		foreach ($posts as $post) {
			$options[] = array(
				'id' => $post->ID,
				'title' => $post->post_title,
			);
		}

		return $options;
	}

	/**
	 * Get public post types
	 *
	 * @return array Post types list.
	 */
	private function get_post_types()
	{
		$post_types = get_post_types(array('public' => true), 'objects');
		$options = array();

		foreach ($post_types as $post_type) {
			if ('attachment' === $post_type->name) {
				continue;
			}
			$options[] = array(
				'name' => $post_type->name,
				'label' => $post_type->label,
			);
		}

		return $options;
	}

	/**
	 * Get all schema types with their fields (public getter)
	 *
	 * @return array Schema types configuration.
	 */
	public function get_schema_types()
	{
		if (empty($this->schema_types)) {
			$this->schema_types = $this->build_schema_types();
		}
		return $this->schema_types;
	}

	/**
	 * Build schema types configuration
	 *
	 * @return array Schema types configuration.
	 */
	private function build_schema_types()
	{
		return array(
			'LocalBusiness' => array(
				'label' => __('Local Business', 'ai-seo-pro'),
				'fields' => array(
					'name' => array(
						'label' => __('Business Name', 'ai-seo-pro'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'ai-seo-pro'),
						'type' => 'textarea',
					),
					'logo' => array(
						'label' => __('Logo URL', 'ai-seo-pro'),
						'type' => 'image',
					),
					'image' => array(
						'label' => __('Image URL', 'ai-seo-pro'),
						'type' => 'image',
					),
					'telephone' => array(
						'label' => __('Telephone', 'ai-seo-pro'),
						'type' => 'tel',
					),
					'email' => array(
						'label' => __('Email', 'ai-seo-pro'),
						'type' => 'email',
					),
					'url' => array(
						'label' => __('Website URL', 'ai-seo-pro'),
						'type' => 'url',
					),
					'streetAddress' => array(
						'label' => __('Street Address', 'ai-seo-pro'),
						'type' => 'text',
					),
					'city' => array(
						'label' => __('City', 'ai-seo-pro'),
						'type' => 'text',
					),
					'state' => array(
						'label' => __('State/Region', 'ai-seo-pro'),
						'type' => 'text',
					),
					'postalCode' => array(
						'label' => __('Zip/Postal Code', 'ai-seo-pro'),
						'type' => 'text',
					),
					'country' => array(
						'label' => __('Country', 'ai-seo-pro'),
						'type' => 'text',
					),
					'latitude' => array(
						'label' => __('Latitude', 'ai-seo-pro'),
						'type' => 'text',
					),
					'longitude' => array(
						'label' => __('Longitude', 'ai-seo-pro'),
						'type' => 'text',
					),
					'mapUrl' => array(
						'label' => __('Google Maps URL', 'ai-seo-pro'),
						'type' => 'url',
					),
					'priceRange' => array(
						'label' => __('Price Range', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => '$$ - $$$',
					),
					'openingHours' => array(
						'label' => __('Opening Hours', 'ai-seo-pro'),
						'type' => 'hours',
					),
					'sameAs' => array(
						'label' => __('Social Profiles (Same As)', 'ai-seo-pro'),
						'type' => 'textarea',
						'placeholder' => __('One URL per line', 'ai-seo-pro'),
					),
				),
			),
			'Organization' => array(
				'label' => __('Organization', 'ai-seo-pro'),
				'fields' => array(
					'name' => array(
						'label' => __('Organization Name', 'ai-seo-pro'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'ai-seo-pro'),
						'type' => 'textarea',
					),
					'logo' => array(
						'label' => __('Logo URL', 'ai-seo-pro'),
						'type' => 'image',
					),
					'url' => array(
						'label' => __('Website URL', 'ai-seo-pro'),
						'type' => 'url',
					),
					'telephone' => array(
						'label' => __('Telephone', 'ai-seo-pro'),
						'type' => 'tel',
					),
					'email' => array(
						'label' => __('Email', 'ai-seo-pro'),
						'type' => 'email',
					),
					'streetAddress' => array(
						'label' => __('Street Address', 'ai-seo-pro'),
						'type' => 'text',
					),
					'city' => array(
						'label' => __('City', 'ai-seo-pro'),
						'type' => 'text',
					),
					'state' => array(
						'label' => __('State/Region', 'ai-seo-pro'),
						'type' => 'text',
					),
					'postalCode' => array(
						'label' => __('Zip/Postal Code', 'ai-seo-pro'),
						'type' => 'text',
					),
					'country' => array(
						'label' => __('Country', 'ai-seo-pro'),
						'type' => 'text',
					),
					'foundingDate' => array(
						'label' => __('Founding Date', 'ai-seo-pro'),
						'type' => 'date',
					),
					'founders' => array(
						'label' => __('Founders', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => __('Comma separated names', 'ai-seo-pro'),
					),
					'sameAs' => array(
						'label' => __('Social Profiles (Same As)', 'ai-seo-pro'),
						'type' => 'textarea',
						'placeholder' => __('One URL per line', 'ai-seo-pro'),
					),
				),
			),
			'Person' => array(
				'label' => __('Person', 'ai-seo-pro'),
				'fields' => array(
					'name' => array(
						'label' => __('Full Name', 'ai-seo-pro'),
						'type' => 'text',
						'required' => true,
					),
					'givenName' => array(
						'label' => __('First Name', 'ai-seo-pro'),
						'type' => 'text',
					),
					'familyName' => array(
						'label' => __('Last Name', 'ai-seo-pro'),
						'type' => 'text',
					),
					'description' => array(
						'label' => __('Bio/Description', 'ai-seo-pro'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Photo URL', 'ai-seo-pro'),
						'type' => 'image',
					),
					'jobTitle' => array(
						'label' => __('Job Title', 'ai-seo-pro'),
						'type' => 'text',
					),
					'worksFor' => array(
						'label' => __('Works For (Company)', 'ai-seo-pro'),
						'type' => 'text',
					),
					'url' => array(
						'label' => __('Website URL', 'ai-seo-pro'),
						'type' => 'url',
					),
					'email' => array(
						'label' => __('Email', 'ai-seo-pro'),
						'type' => 'email',
					),
					'telephone' => array(
						'label' => __('Telephone', 'ai-seo-pro'),
						'type' => 'tel',
					),
					'birthDate' => array(
						'label' => __('Birth Date', 'ai-seo-pro'),
						'type' => 'date',
					),
					'sameAs' => array(
						'label' => __('Social Profiles (Same As)', 'ai-seo-pro'),
						'type' => 'textarea',
						'placeholder' => __('One URL per line', 'ai-seo-pro'),
					),
				),
			),
			'Website' => array(
				'label' => __('Website', 'ai-seo-pro'),
				'fields' => array(
					'name' => array(
						'label' => __('Website Name', 'ai-seo-pro'),
						'type' => 'text',
						'required' => true,
					),
					'alternateName' => array(
						'label' => __('Alternate Name', 'ai-seo-pro'),
						'type' => 'text',
					),
					'description' => array(
						'label' => __('Description', 'ai-seo-pro'),
						'type' => 'textarea',
					),
					'url' => array(
						'label' => __('URL', 'ai-seo-pro'),
						'type' => 'url',
					),
					'searchUrl' => array(
						'label' => __('Search URL Template', 'ai-seo-pro'),
						'type' => 'url',
						'placeholder' => home_url('?s={search_term_string}'),
					),
					'inLanguage' => array(
						'label' => __('Language', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => 'en-US',
					),
				),
			),
			'Article' => array(
				'label' => __('Article', 'ai-seo-pro'),
				'fields' => array(
					'headline' => array(
						'label' => __('Headline', 'ai-seo-pro'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'ai-seo-pro'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Image URL', 'ai-seo-pro'),
						'type' => 'image',
					),
					'authorName' => array(
						'label' => __('Author Name', 'ai-seo-pro'),
						'type' => 'text',
					),
					'authorUrl' => array(
						'label' => __('Author URL', 'ai-seo-pro'),
						'type' => 'url',
					),
					'publisherName' => array(
						'label' => __('Publisher Name', 'ai-seo-pro'),
						'type' => 'text',
					),
					'publisherLogo' => array(
						'label' => __('Publisher Logo', 'ai-seo-pro'),
						'type' => 'image',
					),
					'datePublished' => array(
						'label' => __('Date Published', 'ai-seo-pro'),
						'type' => 'date',
					),
					'dateModified' => array(
						'label' => __('Date Modified', 'ai-seo-pro'),
						'type' => 'date',
					),
					'articleSection' => array(
						'label' => __('Article Section/Category', 'ai-seo-pro'),
						'type' => 'text',
					),
					'wordCount' => array(
						'label' => __('Word Count', 'ai-seo-pro'),
						'type' => 'number',
					),
				),
			),
			'Product' => array(
				'label' => __('Product', 'ai-seo-pro'),
				'fields' => array(
					'name' => array(
						'label' => __('Product Name', 'ai-seo-pro'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'ai-seo-pro'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Image URL', 'ai-seo-pro'),
						'type' => 'image',
					),
					'brand' => array(
						'label' => __('Brand', 'ai-seo-pro'),
						'type' => 'text',
					),
					'sku' => array(
						'label' => __('SKU', 'ai-seo-pro'),
						'type' => 'text',
					),
					'gtin' => array(
						'label' => __('GTIN/UPC/EAN', 'ai-seo-pro'),
						'type' => 'text',
					),
					'price' => array(
						'label' => __('Price', 'ai-seo-pro'),
						'type' => 'text',
					),
					'priceCurrency' => array(
						'label' => __('Currency', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => 'USD',
					),
					'availability' => array(
						'label' => __('Availability', 'ai-seo-pro'),
						'type' => 'select',
						'options' => array(
							'InStock' => __('In Stock', 'ai-seo-pro'),
							'OutOfStock' => __('Out of Stock', 'ai-seo-pro'),
							'PreOrder' => __('Pre-Order', 'ai-seo-pro'),
							'Discontinued' => __('Discontinued', 'ai-seo-pro'),
						),
					),
					'ratingValue' => array(
						'label' => __('Rating Value', 'ai-seo-pro'),
						'type' => 'text',
					),
					'reviewCount' => array(
						'label' => __('Review Count', 'ai-seo-pro'),
						'type' => 'number',
					),
					'url' => array(
						'label' => __('Product URL', 'ai-seo-pro'),
						'type' => 'url',
					),
				),
			),
			'FAQPage' => array(
				'label' => __('FAQ Page', 'ai-seo-pro'),
				'fields' => array(
					'faqs' => array(
						'label' => __('FAQ Items', 'ai-seo-pro'),
						'type' => 'faq_repeater',
					),
				),
			),
			'HowTo' => array(
				'label' => __('How To', 'ai-seo-pro'),
				'fields' => array(
					'name' => array(
						'label' => __('Title', 'ai-seo-pro'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'ai-seo-pro'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Image URL', 'ai-seo-pro'),
						'type' => 'image',
					),
					'totalTime' => array(
						'label' => __('Total Time', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => 'PT30M (30 minutes)',
					),
					'estimatedCost' => array(
						'label' => __('Estimated Cost', 'ai-seo-pro'),
						'type' => 'text',
					),
					'supply' => array(
						'label' => __('Supplies/Materials', 'ai-seo-pro'),
						'type' => 'textarea',
						'placeholder' => __('One item per line', 'ai-seo-pro'),
					),
					'tool' => array(
						'label' => __('Tools Required', 'ai-seo-pro'),
						'type' => 'textarea',
						'placeholder' => __('One tool per line', 'ai-seo-pro'),
					),
					'steps' => array(
						'label' => __('Steps', 'ai-seo-pro'),
						'type' => 'steps_repeater',
					),
				),
			),
			'Event' => array(
				'label' => __('Event', 'ai-seo-pro'),
				'fields' => array(
					'name' => array(
						'label' => __('Event Name', 'ai-seo-pro'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'ai-seo-pro'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Image URL', 'ai-seo-pro'),
						'type' => 'image',
					),
					'startDate' => array(
						'label' => __('Start Date & Time', 'ai-seo-pro'),
						'type' => 'datetime-local',
					),
					'endDate' => array(
						'label' => __('End Date & Time', 'ai-seo-pro'),
						'type' => 'datetime-local',
					),
					'eventStatus' => array(
						'label' => __('Event Status', 'ai-seo-pro'),
						'type' => 'select',
						'options' => array(
							'EventScheduled' => __('Scheduled', 'ai-seo-pro'),
							'EventCancelled' => __('Cancelled', 'ai-seo-pro'),
							'EventPostponed' => __('Postponed', 'ai-seo-pro'),
							'EventRescheduled' => __('Rescheduled', 'ai-seo-pro'),
							'EventMovedOnline' => __('Moved Online', 'ai-seo-pro'),
						),
					),
					'eventAttendanceMode' => array(
						'label' => __('Attendance Mode', 'ai-seo-pro'),
						'type' => 'select',
						'options' => array(
							'OfflineEventAttendanceMode' => __('Offline (In Person)', 'ai-seo-pro'),
							'OnlineEventAttendanceMode' => __('Online', 'ai-seo-pro'),
							'MixedEventAttendanceMode' => __('Mixed (Online & Offline)', 'ai-seo-pro'),
						),
					),
					'locationName' => array(
						'label' => __('Venue Name', 'ai-seo-pro'),
						'type' => 'text',
					),
					'streetAddress' => array(
						'label' => __('Street Address', 'ai-seo-pro'),
						'type' => 'text',
					),
					'city' => array(
						'label' => __('City', 'ai-seo-pro'),
						'type' => 'text',
					),
					'country' => array(
						'label' => __('Country', 'ai-seo-pro'),
						'type' => 'text',
					),
					'onlineUrl' => array(
						'label' => __('Online Event URL', 'ai-seo-pro'),
						'type' => 'url',
					),
					'organizerName' => array(
						'label' => __('Organizer Name', 'ai-seo-pro'),
						'type' => 'text',
					),
					'organizerUrl' => array(
						'label' => __('Organizer URL', 'ai-seo-pro'),
						'type' => 'url',
					),
					'performerName' => array(
						'label' => __('Performer Name', 'ai-seo-pro'),
						'type' => 'text',
					),
					'price' => array(
						'label' => __('Ticket Price', 'ai-seo-pro'),
						'type' => 'text',
					),
					'priceCurrency' => array(
						'label' => __('Currency', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => 'USD',
					),
					'ticketUrl' => array(
						'label' => __('Ticket URL', 'ai-seo-pro'),
						'type' => 'url',
					),
				),
			),
			'Recipe' => array(
				'label' => __('Recipe', 'ai-seo-pro'),
				'fields' => array(
					'name' => array(
						'label' => __('Recipe Name', 'ai-seo-pro'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'ai-seo-pro'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Image URL', 'ai-seo-pro'),
						'type' => 'image',
					),
					'authorName' => array(
						'label' => __('Author Name', 'ai-seo-pro'),
						'type' => 'text',
					),
					'prepTime' => array(
						'label' => __('Prep Time', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => 'PT15M (15 minutes)',
					),
					'cookTime' => array(
						'label' => __('Cook Time', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => 'PT30M (30 minutes)',
					),
					'totalTime' => array(
						'label' => __('Total Time', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => 'PT45M (45 minutes)',
					),
					'recipeYield' => array(
						'label' => __('Yield/Servings', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => '4 servings',
					),
					'recipeCategory' => array(
						'label' => __('Category', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => 'Dessert, Main Course, etc.',
					),
					'recipeCuisine' => array(
						'label' => __('Cuisine', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => 'Italian, Mexican, etc.',
					),
					'keywords' => array(
						'label' => __('Keywords', 'ai-seo-pro'),
						'type' => 'text',
					),
					'calories' => array(
						'label' => __('Calories', 'ai-seo-pro'),
						'type' => 'text',
					),
					'recipeIngredient' => array(
						'label' => __('Ingredients', 'ai-seo-pro'),
						'type' => 'textarea',
						'placeholder' => __('One ingredient per line', 'ai-seo-pro'),
					),
					'recipeInstructions' => array(
						'label' => __('Instructions', 'ai-seo-pro'),
						'type' => 'textarea',
						'placeholder' => __('One step per line', 'ai-seo-pro'),
					),
					'ratingValue' => array(
						'label' => __('Rating Value', 'ai-seo-pro'),
						'type' => 'text',
					),
					'reviewCount' => array(
						'label' => __('Review Count', 'ai-seo-pro'),
						'type' => 'number',
					),
				),
			),
			'VideoObject' => array(
				'label' => __('Video', 'ai-seo-pro'),
				'fields' => array(
					'name' => array(
						'label' => __('Video Title', 'ai-seo-pro'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'ai-seo-pro'),
						'type' => 'textarea',
					),
					'thumbnailUrl' => array(
						'label' => __('Thumbnail URL', 'ai-seo-pro'),
						'type' => 'image',
					),
					'contentUrl' => array(
						'label' => __('Video File URL', 'ai-seo-pro'),
						'type' => 'url',
					),
					'embedUrl' => array(
						'label' => __('Embed URL', 'ai-seo-pro'),
						'type' => 'url',
					),
					'uploadDate' => array(
						'label' => __('Upload Date', 'ai-seo-pro'),
						'type' => 'date',
					),
					'duration' => array(
						'label' => __('Duration', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => 'PT5M30S (5 min 30 sec)',
					),
				),
			),
			'BreadcrumbList' => array(
				'label' => __('Breadcrumb', 'ai-seo-pro'),
				'fields' => array(
					'autoBreadcrumb' => array(
						'label' => __('Enable Auto Breadcrumb', 'ai-seo-pro'),
						'type' => 'checkbox',
						'description' => __('Automatically generates breadcrumb based on page hierarchy.', 'ai-seo-pro'),
					),
					'breadcrumbs' => array(
						'label' => __('Custom Breadcrumb Items', 'ai-seo-pro'),
						'type' => 'breadcrumb_repeater',
					),
				),
			),
			'SoftwareApplication' => array(
				'label' => __('Software Application', 'ai-seo-pro'),
				'fields' => array(
					'name' => array(
						'label' => __('App Name', 'ai-seo-pro'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'ai-seo-pro'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Screenshot/Image URL', 'ai-seo-pro'),
						'type' => 'image',
					),
					'applicationCategory' => array(
						'label' => __('Category', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => 'GameApplication, BusinessApplication, etc.',
					),
					'operatingSystem' => array(
						'label' => __('Operating System', 'ai-seo-pro'),
						'type' => 'text',
					),
					'price' => array(
						'label' => __('Price', 'ai-seo-pro'),
						'type' => 'text',
					),
					'priceCurrency' => array(
						'label' => __('Currency', 'ai-seo-pro'),
						'type' => 'text',
						'placeholder' => 'USD',
					),
					'ratingValue' => array(
						'label' => __('Rating Value', 'ai-seo-pro'),
						'type' => 'text',
					),
					'reviewCount' => array(
						'label' => __('Review Count', 'ai-seo-pro'),
						'type' => 'number',
					),
					'downloadUrl' => array(
						'label' => __('Download URL', 'ai-seo-pro'),
						'type' => 'url',
					),
				),
			),
		);
	}

	/**
	 * Sanitize schemas array
	 *
	 * @param array $input User input.
	 * @return array Sanitized schemas.
	 */
	public function sanitize_schemas($input)
	{
		if (!is_array($input)) {
			return array();
		}

		$sanitized = array();

		foreach ($input as $index => $schema) {
			if (empty($schema['type'])) {
				continue;
			}

			$sanitized_schema = array(
				'type' => sanitize_text_field($schema['type']),
				'enabled' => isset($schema['enabled']) ? (bool) $schema['enabled'] : true,
				'data' => array(),
				'display_mode' => isset($schema['display_mode']) ? sanitize_text_field($schema['display_mode']) : 'all',
				'include_ids' => array(),
				'exclude_ids' => array(),
				'post_types' => array(),
			);

			// Sanitize include IDs.
			if (!empty($schema['include_ids']) && is_array($schema['include_ids'])) {
				$sanitized_schema['include_ids'] = array_map('absint', $schema['include_ids']);
			}

			// Sanitize exclude IDs.
			if (!empty($schema['exclude_ids']) && is_array($schema['exclude_ids'])) {
				$sanitized_schema['exclude_ids'] = array_map('absint', $schema['exclude_ids']);
			}

			// Sanitize post types.
			if (!empty($schema['post_types']) && is_array($schema['post_types'])) {
				$sanitized_schema['post_types'] = array_map('sanitize_key', $schema['post_types']);
			}

			if (isset($schema['data']) && is_array($schema['data'])) {
				foreach ($schema['data'] as $key => $value) {
					// Preserve the original key (don't use sanitize_key as it lowercases).
					$clean_key = preg_replace('/[^a-zA-Z0-9_]/', '', $key);

					if (is_array($value)) {
						// Handle nested arrays (repeaters).
						$sanitized_schema['data'][$clean_key] = $this->sanitize_nested_array($value);
					} else {
						$sanitized_schema['data'][$clean_key] = wp_kses_post($value);
					}
				}
			}

			$sanitized[] = $sanitized_schema;
		}

		return $sanitized;
	}

	/**
	 * Sanitize nested array
	 *
	 * @param array $array The array to sanitize.
	 * @return array Sanitized array.
	 */
	private function sanitize_nested_array($array)
	{
		$sanitized = array();

		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$sanitized[$key] = $this->sanitize_nested_array($value);
			} else {
				$sanitized[$key] = wp_kses_post($value);
			}
		}

		return $sanitized;
	}

	/**
	 * AJAX handler for getting schema preview
	 */
	public function ajax_preview_schema()
	{
		check_ajax_referer('ai_seo_pro_schema_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(array('message' => __('Permission denied.', 'ai-seo-pro')));
		}

		$schemas = get_option('ai_seo_pro_schemas', array());
		$output = array();

		foreach ($schemas as $schema) {
			if (!empty($schema['enabled'])) {
				$generated = $this->generate_schema_markup($schema);
				if ($generated) {
					$output[] = $generated;
				}
			}
		}

		wp_send_json_success(array('schemas' => $output));
	}

	/**
	 * Generate schema markup for a single schema
	 *
	 * @param array $schema Schema data.
	 * @return array|null Generated schema or null.
	 */
	public function generate_schema_markup($schema)
	{
		if (empty($schema['type']) || empty($schema['data'])) {
			return null;
		}

		$type = $schema['type'];
		$data = $schema['data'];

		switch ($type) {
			case 'LocalBusiness':
				return $this->generate_local_business($data);

			case 'Organization':
				return $this->generate_organization($data);

			case 'Person':
				return $this->generate_person($data);

			case 'Website':
				return $this->generate_website($data);

			case 'Article':
				return $this->generate_article($data);

			case 'Product':
				return $this->generate_product($data);

			case 'FAQPage':
				return $this->generate_faq($data);

			case 'HowTo':
				return $this->generate_howto($data);

			case 'Event':
				return $this->generate_event($data);

			case 'Recipe':
				return $this->generate_recipe($data);

			case 'VideoObject':
				return $this->generate_video($data);

			case 'BreadcrumbList':
				return $this->generate_breadcrumb($data);

			case 'SoftwareApplication':
				return $this->generate_software($data);

			default:
				return null;
		}
	}

	/**
	 * Generate LocalBusiness schema
	 */
	private function generate_local_business($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'LocalBusiness',
		);

		if (!empty($data['name'])) {
			$schema['name'] = $data['name'];
		}

		if (!empty($data['description'])) {
			$schema['description'] = $data['description'];
		}

		if (!empty($data['logo'])) {
			$schema['logo'] = $data['logo'];
		}

		if (!empty($data['image'])) {
			$schema['image'] = $data['image'];
		}

		if (!empty($data['telephone'])) {
			$schema['telephone'] = $data['telephone'];
		}

		if (!empty($data['email'])) {
			$schema['email'] = $data['email'];
		}

		if (!empty($data['url'])) {
			$schema['url'] = $data['url'];
		}

		if (!empty($data['priceRange'])) {
			$schema['priceRange'] = $data['priceRange'];
		}

		// Address.
		if (!empty($data['streetAddress']) || !empty($data['city'])) {
			$schema['address'] = array(
				'@type' => 'PostalAddress',
			);

			if (!empty($data['streetAddress'])) {
				$schema['address']['streetAddress'] = $data['streetAddress'];
			}
			if (!empty($data['city'])) {
				$schema['address']['addressLocality'] = $data['city'];
			}
			if (!empty($data['state'])) {
				$schema['address']['addressRegion'] = $data['state'];
			}
			if (!empty($data['postalCode'])) {
				$schema['address']['postalCode'] = $data['postalCode'];
			}
			if (!empty($data['country'])) {
				$schema['address']['addressCountry'] = $data['country'];
			}
		}

		// Geo coordinates.
		if (!empty($data['latitude']) && !empty($data['longitude'])) {
			$schema['geo'] = array(
				'@type' => 'GeoCoordinates',
				'latitude' => $data['latitude'],
				'longitude' => $data['longitude'],
			);
		}

		// Map URL.
		if (!empty($data['mapUrl'])) {
			$schema['hasMap'] = $data['mapUrl'];
		}

		// Opening hours.
		if (!empty($data['openingHours']) && is_array($data['openingHours'])) {
			$hours = array();
			foreach ($data['openingHours'] as $hour) {
				if (!empty($hour['days']) && !empty($hour['open']) && !empty($hour['close'])) {
					$days = implode(',', $hour['days']);
					$hours[] = $days . ' ' . $hour['open'] . '-' . $hour['close'];
				}
			}
			if (!empty($hours)) {
				$schema['openingHours'] = $hours;
			}
		}

		// Same As (social profiles).
		if (!empty($data['sameAs'])) {
			$urls = array_filter(array_map('trim', explode("\n", $data['sameAs'])));
			if (!empty($urls)) {
				$schema['sameAs'] = $urls;
			}
		}

		return $schema;
	}

	/**
	 * Generate Organization schema
	 */
	private function generate_organization($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Organization',
		);

		$simple_fields = array('name', 'description', 'logo', 'url', 'telephone', 'email');
		foreach ($simple_fields as $field) {
			if (!empty($data[$field])) {
				$schema[$field] = $data[$field];
			}
		}

		// Address.
		if (!empty($data['streetAddress']) || !empty($data['city'])) {
			$schema['address'] = array(
				'@type' => 'PostalAddress',
			);

			$address_fields = array(
				'streetAddress' => 'streetAddress',
				'city' => 'addressLocality',
				'state' => 'addressRegion',
				'postalCode' => 'postalCode',
				'country' => 'addressCountry',
			);

			foreach ($address_fields as $data_key => $schema_key) {
				if (!empty($data[$data_key])) {
					$schema['address'][$schema_key] = $data[$data_key];
				}
			}
		}

		if (!empty($data['foundingDate'])) {
			$schema['foundingDate'] = $data['foundingDate'];
		}

		if (!empty($data['founders'])) {
			$founders = array_map('trim', explode(',', $data['founders']));
			$schema['founders'] = array_map(
				function ($name) {
					return array(
						'@type' => 'Person',
						'name' => $name,
					);
				},
				$founders
			);
		}

		if (!empty($data['sameAs'])) {
			$urls = array_filter(array_map('trim', explode("\n", $data['sameAs'])));
			if (!empty($urls)) {
				$schema['sameAs'] = $urls;
			}
		}

		return $schema;
	}

	/**
	 * Generate Person schema
	 */
	private function generate_person($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Person',
		);

		$simple_fields = array('name', 'givenName', 'familyName', 'description', 'image', 'jobTitle', 'url', 'email', 'telephone', 'birthDate');
		foreach ($simple_fields as $field) {
			if (!empty($data[$field])) {
				$schema[$field] = $data[$field];
			}
		}

		if (!empty($data['worksFor'])) {
			$schema['worksFor'] = array(
				'@type' => 'Organization',
				'name' => $data['worksFor'],
			);
		}

		if (!empty($data['sameAs'])) {
			$urls = array_filter(array_map('trim', explode("\n", $data['sameAs'])));
			if (!empty($urls)) {
				$schema['sameAs'] = $urls;
			}
		}

		return $schema;
	}

	/**
	 * Generate Website schema
	 */
	private function generate_website($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'WebSite',
		);

		if (!empty($data['name'])) {
			$schema['name'] = $data['name'];
		}

		if (!empty($data['alternateName'])) {
			$schema['alternateName'] = $data['alternateName'];
		}

		if (!empty($data['description'])) {
			$schema['description'] = $data['description'];
		}

		if (!empty($data['url'])) {
			$schema['url'] = $data['url'];
		}

		if (!empty($data['inLanguage'])) {
			$schema['inLanguage'] = $data['inLanguage'];
		}

		if (!empty($data['searchUrl'])) {
			$schema['potentialAction'] = array(
				'@type' => 'SearchAction',
				'target' => array(
					'@type' => 'EntryPoint',
					'urlTemplate' => $data['searchUrl'],
				),
				'query-input' => 'required name=search_term_string',
			);
		}

		return $schema;
	}

	/**
	 * Generate Article schema
	 */
	private function generate_article($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Article',
		);

		if (!empty($data['headline'])) {
			$schema['headline'] = $data['headline'];
		}

		if (!empty($data['description'])) {
			$schema['description'] = $data['description'];
		}

		if (!empty($data['image'])) {
			$schema['image'] = $data['image'];
		}

		if (!empty($data['authorName'])) {
			$schema['author'] = array(
				'@type' => 'Person',
				'name' => $data['authorName'],
			);
			if (!empty($data['authorUrl'])) {
				$schema['author']['url'] = $data['authorUrl'];
			}
		}

		if (!empty($data['publisherName'])) {
			$schema['publisher'] = array(
				'@type' => 'Organization',
				'name' => $data['publisherName'],
			);
			if (!empty($data['publisherLogo'])) {
				$schema['publisher']['logo'] = array(
					'@type' => 'ImageObject',
					'url' => $data['publisherLogo'],
				);
			}
		}

		if (!empty($data['datePublished'])) {
			$schema['datePublished'] = $data['datePublished'];
		}

		if (!empty($data['dateModified'])) {
			$schema['dateModified'] = $data['dateModified'];
		}

		if (!empty($data['articleSection'])) {
			$schema['articleSection'] = $data['articleSection'];
		}

		if (!empty($data['wordCount'])) {
			$schema['wordCount'] = (int) $data['wordCount'];
		}

		return $schema;
	}

	/**
	 * Generate Product schema
	 */
	private function generate_product($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Product',
		);

		$simple_fields = array('name', 'description', 'image', 'sku', 'gtin', 'url');
		foreach ($simple_fields as $field) {
			if (!empty($data[$field])) {
				$schema[$field] = $data[$field];
			}
		}

		if (!empty($data['brand'])) {
			$schema['brand'] = array(
				'@type' => 'Brand',
				'name' => $data['brand'],
			);
		}

		if (!empty($data['price'])) {
			$schema['offers'] = array(
				'@type' => 'Offer',
				'price' => $data['price'],
				'priceCurrency' => !empty($data['priceCurrency']) ? $data['priceCurrency'] : 'USD',
			);

			if (!empty($data['availability'])) {
				$schema['offers']['availability'] = 'https://schema.org/' . $data['availability'];
			}
		}

		if (!empty($data['ratingValue']) && !empty($data['reviewCount'])) {
			$schema['aggregateRating'] = array(
				'@type' => 'AggregateRating',
				'ratingValue' => $data['ratingValue'],
				'reviewCount' => (int) $data['reviewCount'],
			);
		}

		return $schema;
	}

	/**
	 * Generate FAQ schema
	 */
	private function generate_faq($data)
	{
		if (empty($data['faqs']) || !is_array($data['faqs'])) {
			return null;
		}

		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'FAQPage',
			'mainEntity' => array(),
		);

		foreach ($data['faqs'] as $faq) {
			if (!empty($faq['question']) && !empty($faq['answer'])) {
				$schema['mainEntity'][] = array(
					'@type' => 'Question',
					'name' => $faq['question'],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text' => $faq['answer'],
					),
				);
			}
		}

		if (empty($schema['mainEntity'])) {
			return null;
		}

		return $schema;
	}

	/**
	 * Generate HowTo schema
	 */
	private function generate_howto($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'HowTo',
		);

		if (!empty($data['name'])) {
			$schema['name'] = $data['name'];
		}

		if (!empty($data['description'])) {
			$schema['description'] = $data['description'];
		}

		if (!empty($data['image'])) {
			$schema['image'] = $data['image'];
		}

		if (!empty($data['totalTime'])) {
			$schema['totalTime'] = $data['totalTime'];
		}

		if (!empty($data['estimatedCost'])) {
			$schema['estimatedCost'] = array(
				'@type' => 'MonetaryAmount',
				'value' => $data['estimatedCost'],
			);
		}

		if (!empty($data['supply'])) {
			$supplies = array_filter(array_map('trim', explode("\n", $data['supply'])));
			$schema['supply'] = array_map(
				function ($item) {
					return array(
						'@type' => 'HowToSupply',
						'name' => $item,
					);
				},
				$supplies
			);
		}

		if (!empty($data['tool'])) {
			$tools = array_filter(array_map('trim', explode("\n", $data['tool'])));
			$schema['tool'] = array_map(
				function ($item) {
					return array(
						'@type' => 'HowToTool',
						'name' => $item,
					);
				},
				$tools
			);
		}

		if (!empty($data['steps']) && is_array($data['steps'])) {
			$schema['step'] = array();
			$position = 1;
			foreach ($data['steps'] as $step) {
				if (!empty($step['text'])) {
					$step_data = array(
						'@type' => 'HowToStep',
						'position' => $position,
						'text' => $step['text'],
					);
					if (!empty($step['name'])) {
						$step_data['name'] = $step['name'];
					}
					if (!empty($step['image'])) {
						$step_data['image'] = $step['image'];
					}
					$schema['step'][] = $step_data;
					$position++;
				}
			}
		}

		return $schema;
	}

	/**
	 * Generate Event schema
	 */
	private function generate_event($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Event',
		);

		$simple_fields = array('name', 'description', 'image', 'startDate', 'endDate');
		foreach ($simple_fields as $field) {
			if (!empty($data[$field])) {
				$schema[$field] = $data[$field];
			}
		}

		if (!empty($data['eventStatus'])) {
			$schema['eventStatus'] = 'https://schema.org/' . $data['eventStatus'];
		}

		if (!empty($data['eventAttendanceMode'])) {
			$schema['eventAttendanceMode'] = 'https://schema.org/' . $data['eventAttendanceMode'];
		}

		if (!empty($data['locationName']) || !empty($data['streetAddress'])) {
			$schema['location'] = array(
				'@type' => 'Place',
			);
			if (!empty($data['locationName'])) {
				$schema['location']['name'] = $data['locationName'];
			}
			if (!empty($data['streetAddress'])) {
				$schema['location']['address'] = array(
					'@type' => 'PostalAddress',
					'streetAddress' => $data['streetAddress'],
				);
				if (!empty($data['city'])) {
					$schema['location']['address']['addressLocality'] = $data['city'];
				}
				if (!empty($data['country'])) {
					$schema['location']['address']['addressCountry'] = $data['country'];
				}
			}
		}

		if (!empty($data['onlineUrl'])) {
			$schema['location'] = array(
				'@type' => 'VirtualLocation',
				'url' => $data['onlineUrl'],
			);
		}

		if (!empty($data['organizerName'])) {
			$schema['organizer'] = array(
				'@type' => 'Organization',
				'name' => $data['organizerName'],
			);
			if (!empty($data['organizerUrl'])) {
				$schema['organizer']['url'] = $data['organizerUrl'];
			}
		}

		if (!empty($data['performerName'])) {
			$schema['performer'] = array(
				'@type' => 'Person',
				'name' => $data['performerName'],
			);
		}

		if (!empty($data['price'])) {
			$schema['offers'] = array(
				'@type' => 'Offer',
				'price' => $data['price'],
				'priceCurrency' => !empty($data['priceCurrency']) ? $data['priceCurrency'] : 'USD',
			);
			if (!empty($data['ticketUrl'])) {
				$schema['offers']['url'] = $data['ticketUrl'];
			}
		}

		return $schema;
	}

	/**
	 * Generate Recipe schema
	 */
	private function generate_recipe($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Recipe',
		);

		$simple_fields = array('name', 'description', 'image', 'prepTime', 'cookTime', 'totalTime', 'recipeYield', 'recipeCategory', 'recipeCuisine', 'keywords');
		foreach ($simple_fields as $field) {
			if (!empty($data[$field])) {
				$schema[$field] = $data[$field];
			}
		}

		if (!empty($data['authorName'])) {
			$schema['author'] = array(
				'@type' => 'Person',
				'name' => $data['authorName'],
			);
		}

		if (!empty($data['calories'])) {
			$schema['nutrition'] = array(
				'@type' => 'NutritionInformation',
				'calories' => $data['calories'],
			);
		}

		if (!empty($data['recipeIngredient'])) {
			$schema['recipeIngredient'] = array_filter(array_map('trim', explode("\n", $data['recipeIngredient'])));
		}

		if (!empty($data['recipeInstructions'])) {
			$steps = array_filter(array_map('trim', explode("\n", $data['recipeInstructions'])));
			$schema['recipeInstructions'] = array_map(
				function ($step) {
					return array(
						'@type' => 'HowToStep',
						'text' => $step,
					);
				},
				$steps
			);
		}

		if (!empty($data['ratingValue']) && !empty($data['reviewCount'])) {
			$schema['aggregateRating'] = array(
				'@type' => 'AggregateRating',
				'ratingValue' => $data['ratingValue'],
				'reviewCount' => (int) $data['reviewCount'],
			);
		}

		return $schema;
	}

	/**
	 * Generate Video schema
	 */
	private function generate_video($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'VideoObject',
		);

		$simple_fields = array('name', 'description', 'thumbnailUrl', 'contentUrl', 'embedUrl', 'uploadDate', 'duration');
		foreach ($simple_fields as $field) {
			if (!empty($data[$field])) {
				$schema[$field] = $data[$field];
			}
		}

		return $schema;
	}

	/**
	 * Generate Breadcrumb schema
	 */
	private function generate_breadcrumb($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'BreadcrumbList',
			'itemListElement' => array(),
		);

		if (!empty($data['breadcrumbs']) && is_array($data['breadcrumbs'])) {
			$position = 1;
			foreach ($data['breadcrumbs'] as $item) {
				if (!empty($item['name'])) {
					$element = array(
						'@type' => 'ListItem',
						'position' => $position,
						'name' => $item['name'],
					);
					if (!empty($item['url'])) {
						$element['item'] = $item['url'];
					}
					$schema['itemListElement'][] = $element;
					$position++;
				}
			}
		}

		if (empty($schema['itemListElement'])) {
			return null;
		}

		return $schema;
	}

	/**
	 * Generate Software Application schema
	 */
	private function generate_software($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'SoftwareApplication',
		);

		$simple_fields = array('name', 'description', 'image', 'applicationCategory', 'operatingSystem');
		foreach ($simple_fields as $field) {
			if (!empty($data[$field])) {
				$schema[$field] = $data[$field];
			}
		}

		if (!empty($data['price'])) {
			$schema['offers'] = array(
				'@type' => 'Offer',
				'price' => $data['price'],
				'priceCurrency' => !empty($data['priceCurrency']) ? $data['priceCurrency'] : 'USD',
			);
		}

		if (!empty($data['ratingValue']) && !empty($data['reviewCount'])) {
			$schema['aggregateRating'] = array(
				'@type' => 'AggregateRating',
				'ratingValue' => $data['ratingValue'],
				'reviewCount' => (int) $data['reviewCount'],
			);
		}

		if (!empty($data['downloadUrl'])) {
			$schema['downloadUrl'] = $data['downloadUrl'];
		}

		return $schema;
	}
}