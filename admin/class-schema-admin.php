<?php
/**
 * Schema Admin Handler
 *
 * Manages schema markup generation and admin interface
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

class MPSEO_Schema_Admin
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
	public function __construct($plugin_name = 'metapilot-smart-seo')
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
			'mpseo_schema',
			'mpseo_schemas',
			array(
				'type' => 'array',
				'sanitize_callback' => array($this, 'sanitize_schemas'),
				'default' => array(),
			)
		);

		register_setting(
			'mpseo_schema',
			'mpseo_schema_enabled',
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
		if ('metapilot-smart-seo_page_mpseo-schema' !== $hook) {
			return;
		}

		wp_enqueue_media();

		// Enqueue Select2 for better dropdowns.
		wp_enqueue_style(
			'select2',
			MPSEO_PLUGIN_URL . 'assets/vendor/select2/select2.min.css',
			array(),
			'4.1.0'
		);

		wp_enqueue_script(
			'select2',
			MPSEO_PLUGIN_URL . 'assets/vendor/select2/select2.min.js',
			array('jquery'),
			'4.1.0',
			true
		);

		wp_enqueue_script(
			'mpseo-schema-admin',
			MPSEO_PLUGIN_URL . 'assets/js/schema-admin.js',
			array('jquery', 'jquery-ui-sortable', 'select2'),
			MPSEO_VERSION,
			true
		);

		wp_localize_script(
			'mpseo-schema-admin',
			'mpseoSchema',
			array(
				'schemaTypes' => $this->get_schema_types(),
				'nonce' => wp_create_nonce('mpseo_schema_nonce'),
				'confirmDelete' => __('Are you sure you want to delete this schema?', 'metapilot-smart-seo'),
				'selectImage' => __('Select Image', 'metapilot-smart-seo'),
				'useImage' => __('Use this image', 'metapilot-smart-seo'),
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
				'label' => __('Local Business', 'metapilot-smart-seo'),
				'fields' => array(
					'name' => array(
						'label' => __('Business Name', 'metapilot-smart-seo'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'metapilot-smart-seo'),
						'type' => 'textarea',
					),
					'logo' => array(
						'label' => __('Logo URL', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'image' => array(
						'label' => __('Image URL', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'telephone' => array(
						'label' => __('Telephone', 'metapilot-smart-seo'),
						'type' => 'tel',
					),
					'email' => array(
						'label' => __('Email', 'metapilot-smart-seo'),
						'type' => 'email',
					),
					'url' => array(
						'label' => __('Website URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
					'streetAddress' => array(
						'label' => __('Street Address', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'city' => array(
						'label' => __('City', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'state' => array(
						'label' => __('State/Region', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'postalCode' => array(
						'label' => __('Zip/Postal Code', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'country' => array(
						'label' => __('Country', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'latitude' => array(
						'label' => __('Latitude', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'longitude' => array(
						'label' => __('Longitude', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'mapUrl' => array(
						'label' => __('Google Maps URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
					'priceRange' => array(
						'label' => __('Price Range', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => '$$ - $$$',
					),
					'openingHours' => array(
						'label' => __('Opening Hours', 'metapilot-smart-seo'),
						'type' => 'hours',
					),
					'sameAs' => array(
						'label' => __('Social Profiles (Same As)', 'metapilot-smart-seo'),
						'type' => 'textarea',
						'placeholder' => __('One URL per line', 'metapilot-smart-seo'),
					),
				),
			),
			'Organization' => array(
				'label' => __('Organization', 'metapilot-smart-seo'),
				'fields' => array(
					'name' => array(
						'label' => __('Organization Name', 'metapilot-smart-seo'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'metapilot-smart-seo'),
						'type' => 'textarea',
					),
					'logo' => array(
						'label' => __('Logo URL', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'url' => array(
						'label' => __('Website URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
					'telephone' => array(
						'label' => __('Telephone', 'metapilot-smart-seo'),
						'type' => 'tel',
					),
					'email' => array(
						'label' => __('Email', 'metapilot-smart-seo'),
						'type' => 'email',
					),
					'streetAddress' => array(
						'label' => __('Street Address', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'city' => array(
						'label' => __('City', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'state' => array(
						'label' => __('State/Region', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'postalCode' => array(
						'label' => __('Zip/Postal Code', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'country' => array(
						'label' => __('Country', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'foundingDate' => array(
						'label' => __('Founding Date', 'metapilot-smart-seo'),
						'type' => 'date',
					),
					'founders' => array(
						'label' => __('Founders', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => __('Comma separated names', 'metapilot-smart-seo'),
					),
					'sameAs' => array(
						'label' => __('Social Profiles (Same As)', 'metapilot-smart-seo'),
						'type' => 'textarea',
						'placeholder' => __('One URL per line', 'metapilot-smart-seo'),
					),
				),
			),
			'Person' => array(
				'label' => __('Person', 'metapilot-smart-seo'),
				'fields' => array(
					'name' => array(
						'label' => __('Full Name', 'metapilot-smart-seo'),
						'type' => 'text',
						'required' => true,
					),
					'givenName' => array(
						'label' => __('First Name', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'familyName' => array(
						'label' => __('Last Name', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'description' => array(
						'label' => __('Bio/Description', 'metapilot-smart-seo'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Photo URL', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'jobTitle' => array(
						'label' => __('Job Title', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'worksFor' => array(
						'label' => __('Works For (Company)', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'url' => array(
						'label' => __('Website URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
					'email' => array(
						'label' => __('Email', 'metapilot-smart-seo'),
						'type' => 'email',
					),
					'telephone' => array(
						'label' => __('Telephone', 'metapilot-smart-seo'),
						'type' => 'tel',
					),
					'birthDate' => array(
						'label' => __('Birth Date', 'metapilot-smart-seo'),
						'type' => 'date',
					),
					'sameAs' => array(
						'label' => __('Social Profiles (Same As)', 'metapilot-smart-seo'),
						'type' => 'textarea',
						'placeholder' => __('One URL per line', 'metapilot-smart-seo'),
					),
				),
			),
			'Website' => array(
				'label' => __('Website', 'metapilot-smart-seo'),
				'fields' => array(
					'name' => array(
						'label' => __('Website Name', 'metapilot-smart-seo'),
						'type' => 'text',
						'required' => true,
					),
					'alternateName' => array(
						'label' => __('Alternate Name', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'description' => array(
						'label' => __('Description', 'metapilot-smart-seo'),
						'type' => 'textarea',
					),
					'url' => array(
						'label' => __('URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
					'searchUrl' => array(
						'label' => __('Search URL Template', 'metapilot-smart-seo'),
						'type' => 'url',
						'placeholder' => home_url('?s={search_term_string}'),
					),
					'inLanguage' => array(
						'label' => __('Language', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'en-US',
					),
				),
			),
			'Article' => array(
				'label' => __('Article', 'metapilot-smart-seo'),
				'fields' => array(
					'headline' => array(
						'label' => __('Headline', 'metapilot-smart-seo'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'metapilot-smart-seo'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Image URL', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'authorName' => array(
						'label' => __('Author Name', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'authorUrl' => array(
						'label' => __('Author URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
					'publisherName' => array(
						'label' => __('Publisher Name', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'publisherLogo' => array(
						'label' => __('Publisher Logo', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'datePublished' => array(
						'label' => __('Date Published', 'metapilot-smart-seo'),
						'type' => 'date',
					),
					'dateModified' => array(
						'label' => __('Date Modified', 'metapilot-smart-seo'),
						'type' => 'date',
					),
					'articleSection' => array(
						'label' => __('Article Section/Category', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'wordCount' => array(
						'label' => __('Word Count', 'metapilot-smart-seo'),
						'type' => 'number',
					),
				),
			),
			'Product' => array(
				'label' => __('Product', 'metapilot-smart-seo'),
				'fields' => array(
					'name' => array(
						'label' => __('Product Name', 'metapilot-smart-seo'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'metapilot-smart-seo'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Image URL', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'brand' => array(
						'label' => __('Brand', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'sku' => array(
						'label' => __('SKU', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'gtin' => array(
						'label' => __('GTIN/UPC/EAN', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'price' => array(
						'label' => __('Price', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'priceCurrency' => array(
						'label' => __('Currency', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'USD',
					),
					'availability' => array(
						'label' => __('Availability', 'metapilot-smart-seo'),
						'type' => 'select',
						'options' => array(
							'InStock' => __('In Stock', 'metapilot-smart-seo'),
							'OutOfStock' => __('Out of Stock', 'metapilot-smart-seo'),
							'PreOrder' => __('Pre-Order', 'metapilot-smart-seo'),
							'Discontinued' => __('Discontinued', 'metapilot-smart-seo'),
						),
					),
					'ratingValue' => array(
						'label' => __('Rating Value', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'reviewCount' => array(
						'label' => __('Review Count', 'metapilot-smart-seo'),
						'type' => 'number',
					),
					'url' => array(
						'label' => __('Product URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
				),
			),
			'Service' => array(
				'label' => __('Service', 'metapilot-smart-seo'),
				'fields' => array(
					'name' => array(
						'label' => __('Service Name', 'metapilot-smart-seo'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'metapilot-smart-seo'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Image URL', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'serviceType' => array(
						'label' => __('Service Type', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => __('e.g., Web Development, Consulting', 'metapilot-smart-seo'),
					),
					'providerName' => array(
						'label' => __('Provider Name', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'providerType' => array(
						'label' => __('Provider Type', 'metapilot-smart-seo'),
						'type' => 'select',
						'options' => array(
							'Organization' => __('Organization', 'metapilot-smart-seo'),
							'Person' => __('Person', 'metapilot-smart-seo'),
						),
					),
					'providerUrl' => array(
						'label' => __('Provider URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
					'areaServed' => array(
						'label' => __('Area Served', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => __('e.g., New York, United States, Worldwide', 'metapilot-smart-seo'),
					),
					'audience' => array(
						'label' => __('Target Audience', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => __('e.g., Small Businesses, Homeowners', 'metapilot-smart-seo'),
					),
					'category' => array(
						'label' => __('Category', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'offerCatalog' => array(
						'label' => __('Offer Catalog', 'metapilot-smart-seo'),
						'type' => 'offer_catalog_repeater',
						'description' => __('Add service categories and their offerings.', 'metapilot-smart-seo'),
					),
					'price' => array(
						'label' => __('Base Price (if no catalog)', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => __('e.g., 99.00 or From 50.00', 'metapilot-smart-seo'),
					),
					'priceCurrency' => array(
						'label' => __('Currency', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'USD',
					),
					'url' => array(
						'label' => __('Service URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
					'telephone' => array(
						'label' => __('Contact Phone', 'metapilot-smart-seo'),
						'type' => 'tel',
					),
					'email' => array(
						'label' => __('Contact Email', 'metapilot-smart-seo'),
						'type' => 'email',
					),
					'ratingValue' => array(
						'label' => __('Rating Value', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'reviewCount' => array(
						'label' => __('Review Count', 'metapilot-smart-seo'),
						'type' => 'number',
					),
					'sameAs' => array(
						'label' => __('Related URLs (Same As)', 'metapilot-smart-seo'),
						'type' => 'textarea',
						'placeholder' => __('One URL per line', 'metapilot-smart-seo'),
					),
				),
			),
			'FAQPage' => array(
				'label' => __('FAQ Page', 'metapilot-smart-seo'),
				'fields' => array(
					'faqs' => array(
						'label' => __('FAQ Items', 'metapilot-smart-seo'),
						'type' => 'faq_repeater',
					),
				),
			),
			'HowTo' => array(
				'label' => __('How To', 'metapilot-smart-seo'),
				'fields' => array(
					'name' => array(
						'label' => __('Title', 'metapilot-smart-seo'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'metapilot-smart-seo'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Image URL', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'totalTime' => array(
						'label' => __('Total Time', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'PT30M (30 minutes)',
					),
					'estimatedCost' => array(
						'label' => __('Estimated Cost', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'supply' => array(
						'label' => __('Supplies/Materials', 'metapilot-smart-seo'),
						'type' => 'textarea',
						'placeholder' => __('One item per line', 'metapilot-smart-seo'),
					),
					'tool' => array(
						'label' => __('Tools Required', 'metapilot-smart-seo'),
						'type' => 'textarea',
						'placeholder' => __('One tool per line', 'metapilot-smart-seo'),
					),
					'steps' => array(
						'label' => __('Steps', 'metapilot-smart-seo'),
						'type' => 'steps_repeater',
					),
				),
			),
			'Event' => array(
				'label' => __('Event', 'metapilot-smart-seo'),
				'fields' => array(
					'name' => array(
						'label' => __('Event Name', 'metapilot-smart-seo'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'metapilot-smart-seo'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Image URL', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'startDate' => array(
						'label' => __('Start Date & Time', 'metapilot-smart-seo'),
						'type' => 'datetime-local',
					),
					'endDate' => array(
						'label' => __('End Date & Time', 'metapilot-smart-seo'),
						'type' => 'datetime-local',
					),
					'eventStatus' => array(
						'label' => __('Event Status', 'metapilot-smart-seo'),
						'type' => 'select',
						'options' => array(
							'EventScheduled' => __('Scheduled', 'metapilot-smart-seo'),
							'EventCancelled' => __('Cancelled', 'metapilot-smart-seo'),
							'EventPostponed' => __('Postponed', 'metapilot-smart-seo'),
							'EventRescheduled' => __('Rescheduled', 'metapilot-smart-seo'),
							'EventMovedOnline' => __('Moved Online', 'metapilot-smart-seo'),
						),
					),
					'eventAttendanceMode' => array(
						'label' => __('Attendance Mode', 'metapilot-smart-seo'),
						'type' => 'select',
						'options' => array(
							'OfflineEventAttendanceMode' => __('Offline (In Person)', 'metapilot-smart-seo'),
							'OnlineEventAttendanceMode' => __('Online', 'metapilot-smart-seo'),
							'MixedEventAttendanceMode' => __('Mixed (Online & Offline)', 'metapilot-smart-seo'),
						),
					),
					'locationName' => array(
						'label' => __('Venue Name', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'streetAddress' => array(
						'label' => __('Street Address', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'city' => array(
						'label' => __('City', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'country' => array(
						'label' => __('Country', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'onlineUrl' => array(
						'label' => __('Online Event URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
					'organizerName' => array(
						'label' => __('Organizer Name', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'organizerUrl' => array(
						'label' => __('Organizer URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
					'performerName' => array(
						'label' => __('Performer Name', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'price' => array(
						'label' => __('Ticket Price', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'priceCurrency' => array(
						'label' => __('Currency', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'USD',
					),
					'ticketUrl' => array(
						'label' => __('Ticket URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
				),
			),
			'Recipe' => array(
				'label' => __('Recipe', 'metapilot-smart-seo'),
				'fields' => array(
					'name' => array(
						'label' => __('Recipe Name', 'metapilot-smart-seo'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'metapilot-smart-seo'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Image URL', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'authorName' => array(
						'label' => __('Author Name', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'prepTime' => array(
						'label' => __('Prep Time', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'PT15M (15 minutes)',
					),
					'cookTime' => array(
						'label' => __('Cook Time', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'PT30M (30 minutes)',
					),
					'totalTime' => array(
						'label' => __('Total Time', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'PT45M (45 minutes)',
					),
					'recipeYield' => array(
						'label' => __('Yield/Servings', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => '4 servings',
					),
					'recipeCategory' => array(
						'label' => __('Category', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'Dessert, Main Course, etc.',
					),
					'recipeCuisine' => array(
						'label' => __('Cuisine', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'Italian, Mexican, etc.',
					),
					'keywords' => array(
						'label' => __('Keywords', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'calories' => array(
						'label' => __('Calories', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'recipeIngredient' => array(
						'label' => __('Ingredients', 'metapilot-smart-seo'),
						'type' => 'textarea',
						'placeholder' => __('One ingredient per line', 'metapilot-smart-seo'),
					),
					'recipeInstructions' => array(
						'label' => __('Instructions', 'metapilot-smart-seo'),
						'type' => 'textarea',
						'placeholder' => __('One step per line', 'metapilot-smart-seo'),
					),
					'ratingValue' => array(
						'label' => __('Rating Value', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'reviewCount' => array(
						'label' => __('Review Count', 'metapilot-smart-seo'),
						'type' => 'number',
					),
				),
			),
			'VideoObject' => array(
				'label' => __('Video', 'metapilot-smart-seo'),
				'fields' => array(
					'name' => array(
						'label' => __('Video Title', 'metapilot-smart-seo'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'metapilot-smart-seo'),
						'type' => 'textarea',
					),
					'thumbnailUrl' => array(
						'label' => __('Thumbnail URL', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'contentUrl' => array(
						'label' => __('Video File URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
					'embedUrl' => array(
						'label' => __('Embed URL', 'metapilot-smart-seo'),
						'type' => 'url',
					),
					'uploadDate' => array(
						'label' => __('Upload Date', 'metapilot-smart-seo'),
						'type' => 'date',
					),
					'duration' => array(
						'label' => __('Duration', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'PT5M30S (5 min 30 sec)',
					),
				),
			),
			'BreadcrumbList' => array(
				'label' => __('Breadcrumb', 'metapilot-smart-seo'),
				'fields' => array(
					'autoBreadcrumb' => array(
						'label' => __('Enable Auto Breadcrumb', 'metapilot-smart-seo'),
						'type' => 'checkbox',
						'description' => __('Automatically generates breadcrumb based on page hierarchy.', 'metapilot-smart-seo'),
					),
					'breadcrumbs' => array(
						'label' => __('Custom Breadcrumb Items', 'metapilot-smart-seo'),
						'type' => 'breadcrumb_repeater',
					),
				),
			),
			'SoftwareApplication' => array(
				'label' => __('Software Application', 'metapilot-smart-seo'),
				'fields' => array(
					'name' => array(
						'label' => __('App Name', 'metapilot-smart-seo'),
						'type' => 'text',
						'required' => true,
					),
					'description' => array(
						'label' => __('Description', 'metapilot-smart-seo'),
						'type' => 'textarea',
					),
					'image' => array(
						'label' => __('Screenshot/Image URL', 'metapilot-smart-seo'),
						'type' => 'image',
					),
					'applicationCategory' => array(
						'label' => __('Category', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'GameApplication, BusinessApplication, etc.',
					),
					'operatingSystem' => array(
						'label' => __('Operating System', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'price' => array(
						'label' => __('Price', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'priceCurrency' => array(
						'label' => __('Currency', 'metapilot-smart-seo'),
						'type' => 'text',
						'placeholder' => 'USD',
					),
					'ratingValue' => array(
						'label' => __('Rating Value', 'metapilot-smart-seo'),
						'type' => 'text',
					),
					'reviewCount' => array(
						'label' => __('Review Count', 'metapilot-smart-seo'),
						'type' => 'number',
					),
					'downloadUrl' => array(
						'label' => __('Download URL', 'metapilot-smart-seo'),
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
		check_ajax_referer('mpseo_schema_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(array('message' => __('Permission denied.', 'metapilot-smart-seo')));
		}

		$schemas = get_option('mpseo_schemas', array());
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

			case 'Service':
				return $this->generate_service($data);

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
	 * Generate Service schema
	 */
	private function generate_service($data)
	{
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Service',
		);

		// Simple fields.
		$simple_fields = array('name', 'description', 'image', 'serviceType', 'category', 'url');
		foreach ($simple_fields as $field) {
			if (!empty($data[$field])) {
				$schema[$field] = $data[$field];
			}
		}

		// Provider.
		if (!empty($data['providerName'])) {
			$provider_type = !empty($data['providerType']) ? $data['providerType'] : 'Organization';
			$schema['provider'] = array(
				'@type' => $provider_type,
				'name' => $data['providerName'],
			);
			if (!empty($data['providerUrl'])) {
				$schema['provider']['url'] = $data['providerUrl'];
			}
			if (!empty($data['telephone'])) {
				$schema['provider']['telephone'] = $data['telephone'];
			}
			if (!empty($data['email'])) {
				$schema['provider']['email'] = $data['email'];
			}
		}

		// Area served.
		if (!empty($data['areaServed'])) {
			$schema['areaServed'] = $data['areaServed'];
		}

		// Audience.
		if (!empty($data['audience'])) {
			$schema['audience'] = array(
				'@type' => 'Audience',
				'audienceType' => $data['audience'],
			);
		}

		// Offer Catalog (advanced nested structure).
		if (!empty($data['offerCatalog']) && is_array($data['offerCatalog'])) {
			$catalog = $this->generate_offer_catalog($data['offerCatalog']);
			if ($catalog) {
				$schema['hasOfferCatalog'] = $catalog;
			}
		} elseif (!empty($data['price'])) {
			// Simple offer if no catalog.
			$schema['offers'] = array(
				'@type' => 'Offer',
				'price' => $data['price'],
				'priceCurrency' => !empty($data['priceCurrency']) ? $data['priceCurrency'] : 'USD',
			);
		}

		// Aggregate rating.
		if (!empty($data['ratingValue']) && !empty($data['reviewCount'])) {
			$schema['aggregateRating'] = array(
				'@type' => 'AggregateRating',
				'ratingValue' => $data['ratingValue'],
				'reviewCount' => (int) $data['reviewCount'],
			);
		}

		// Same As (related URLs).
		if (!empty($data['sameAs'])) {
			$urls = array_filter(array_map('trim', explode("\n", $data['sameAs'])));
			if (!empty($urls)) {
				$schema['sameAs'] = $urls;
			}
		}

		return $schema;
	}

	/**
	 * Generate Offer Catalog structure
	 *
	 * @param array $catalog_data Catalog data from form.
	 * @return array|null Offer catalog schema.
	 */
	private function generate_offer_catalog($catalog_data)
	{
		if (empty($catalog_data['name'])) {
			return null;
		}

		$catalog = array(
			'@type' => 'OfferCatalog',
			'name' => $catalog_data['name'],
		);

		// Process categories.
		if (!empty($catalog_data['categories']) && is_array($catalog_data['categories'])) {
			$catalog['itemListElement'] = array();

			foreach ($catalog_data['categories'] as $category) {
				if (empty($category['name'])) {
					continue;
				}

				$category_item = array(
					'@type' => 'OfferCatalog',
					'name' => $category['name'],
				);

				// Process services within category.
				if (!empty($category['services']) && is_array($category['services'])) {
					$category_item['itemListElement'] = array();

					foreach ($category['services'] as $service) {
						if (empty($service['name'])) {
							continue;
						}

						$offer = array(
							'@type' => 'Offer',
							'itemOffered' => array(
								'@type' => 'Service',
								'name' => $service['name'],
							),
						);

						// Add optional service fields.
						if (!empty($service['description'])) {
							$offer['itemOffered']['description'] = $service['description'];
						}

						if (!empty($service['price'])) {
							$offer['price'] = $service['price'];
							$offer['priceCurrency'] = !empty($service['priceCurrency']) ? $service['priceCurrency'] : 'USD';
						}

						if (!empty($service['url'])) {
							$offer['itemOffered']['url'] = $service['url'];
						}

						$category_item['itemListElement'][] = $offer;
					}
				}

				if (!empty($category_item['itemListElement'])) {
					$catalog['itemListElement'][] = $category_item;
				}
			}
		}

		if (empty($catalog['itemListElement'])) {
			return null;
		}

		return $catalog;
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