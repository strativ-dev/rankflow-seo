<?php
/**
 * The settings page functionality.
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin
 * @author     Strativ AB
 */
class MPSEO_Settings
{

	/**
	 * The ID of this plugin.
	 *
	 * @var      string    $plugin_name
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var      string    $version
	 */
	private $version;

	/**
	 * Active tab.
	 *
	 * @var      string    $active_tab
	 */
	private $active_tab;

	/**
	 * Redirect admin instance
	 *
	 * @var MPSEO_Redirect_Admin
	 */
	private $redirect_admin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification not required for tab navigation (read-only display).
		$this->active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';
	}

	/**
	 * Add options page to admin menu.
	 */
	public function add_plugin_admin_menu()
	{
		add_menu_page(
			__('Metapilot Smart SEO', 'metapilot-smart-seo'),
			__('Metapilot Smart SEO', 'metapilot-smart-seo'),
			'manage_options',
			$this->plugin_name,
			array($this, 'display_plugin_admin_page'),
			MPSEO_PLUGIN_URL . 'assets/images/mpseo-icon-white.png',
			65
		);

		add_submenu_page(
			$this->plugin_name,
			__('Dashboard', 'metapilot-smart-seo'),
			__('Dashboard', 'metapilot-smart-seo'),
			'manage_options',
			$this->plugin_name,
			array($this, 'display_plugin_admin_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Redirects', 'metapilot-smart-seo'),
			__('Redirects', 'metapilot-smart-seo'),
			'manage_options',
			$this->plugin_name . '-redirects',
			array($this, 'display_redirects_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('404 Monitor', 'metapilot-smart-seo'),
			__('404 Monitor', 'metapilot-smart-seo'),
			'manage_options',
			$this->plugin_name . '-404-monitor',
			array($this, 'display_404_monitor_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Robots.txt', 'metapilot-smart-seo'),
			__('Robots.txt', 'metapilot-smart-seo'),
			'manage_options',
			$this->plugin_name . '-robots-txt',
			array($this, 'display_robots_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Schema', 'metapilot-smart-seo'),
			__('Schema', 'metapilot-smart-seo'),
			'manage_options',
			'mpseo-schema',
			array($this, 'display_schema_page')
		);

		add_submenu_page(
			'metapilot-smart-seo',
			__('XML Sitemap', 'metapilot-smart-seo'),
			__('XML Sitemap', 'metapilot-smart-seo'),
			'manage_options',
			'mpseo-sitemap',
			array($this, 'display_sitemap_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Settings', 'metapilot-smart-seo'),
			__('Settings', 'metapilot-smart-seo'),
			'manage_options',
			$this->plugin_name . '-settings',
			array($this, 'display_plugin_settings_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Help', 'metapilot-smart-seo'),
			__('Help', 'metapilot-smart-seo'),
			'manage_options',
			$this->plugin_name . '-help',
			array($this, 'display_help_page')
		);
	}

	/**
	 * Display the plugin admin page.
	 */
	public function display_plugin_admin_page()
	{
		require_once MPSEO_PLUGIN_DIR . 'admin/views/dashboard.php';
	}

	/**
	 * Display redirects page
	 */
	public function display_redirects_page()
	{
		// Get or create redirect admin instance.
		if (!isset($this->redirect_admin)) {
			require_once MPSEO_PLUGIN_DIR . 'admin/class-redirect-admin.php';
			$this->redirect_admin = new MPSEO_Redirect_Admin($this->plugin_name);
		}

		// Call the redirect admin display method.
		$this->redirect_admin->display_redirects_page();
	}

	/**
	 * Display 404 monitor page
	 */
	public function display_404_monitor_page()
	{
		// Get or create redirect admin instance.
		if (!isset($this->redirect_admin)) {
			require_once MPSEO_PLUGIN_DIR . 'admin/class-redirect-admin.php';
			$this->redirect_admin = new MPSEO_Redirect_Admin($this->plugin_name);
		}

		// Call the redirect admin display method.
		$this->redirect_admin->display_404_monitor_page();
	}

	/**
	 * Display robots.txt page
	 */
	public function display_robots_page()
	{
		require_once MPSEO_PLUGIN_DIR . 'admin/views/settings-robots-txt.php';
	}

	/**
	 * Display schema page
	 */
	public function display_schema_page()
	{
		require_once MPSEO_PLUGIN_DIR . 'admin/views/settings-schema.php';
	}

	/**
	 * Display XML Sitemap page
	 */
	public function display_sitemap_page()
	{
		require_once MPSEO_PLUGIN_DIR . 'admin/views/settings-sitemap.php';
	}

	/**
	 * Display help page.
	 */
	public function display_help_page()
	{
		require_once MPSEO_PLUGIN_DIR . 'admin/views/help.php';
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings()
	{

		// General settings.
		register_setting(
			'mpseo_general',
			'mpseo_post_types',
			array(
				'type' => 'array',
				'sanitize_callback' => array($this, 'sanitize_post_types'),
			)
		);

		register_setting(
			'mpseo_general',
			'mpseo_title_separator',
			array(
				'type' => 'string',
				'default' => '-',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'mpseo_general',
			'mpseo_homepage_title',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'mpseo_general',
			'mpseo_homepage_description',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);

		register_setting(
			'mpseo_general',
			'mpseo_site_represents',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'mpseo_general',
			'mpseo_organization_name',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'mpseo_general',
			'mpseo_person_name',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'mpseo_general',
			'mpseo_site_logo',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		// API settings.
		register_setting(
			'mpseo_api',
			'mpseo_api_provider',
			array(
				'type' => 'string',
				'default' => 'gemini',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'mpseo_api',
			'mpseo_api_key',
			array(
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'mpseo_api',
			'mpseo_auto_generate',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		// Feature settings - SEPARATE GROUP.
		register_setting(
			'mpseo_features',
			'mpseo_enable_content_analysis',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'mpseo_features',
			'mpseo_enable_seo_score',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'mpseo_features',
			'mpseo_enable_schema',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'mpseo_features',
			'mpseo_focus_keyword',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'mpseo_features',
			'mpseo_readability_analysis',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		// Social settings - SEPARATE GROUP.
		register_setting(
			'mpseo_social',
			'mpseo_og_tags',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'mpseo_social',
			'mpseo_twitter_cards',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'mpseo_social',
			'mpseo_default_og_image',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		register_setting(
			'mpseo_social',
			'mpseo_twitter_username',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// Site Connections settings.
		register_setting(
			'mpseo_site_connections',
			'mpseo_ahrefs_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_ahrefs_key'),
				'default' => '',
			)
		);

		register_setting(
			'mpseo_site_connections',
			'mpseo_baidu_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'mpseo_site_connections',
			'mpseo_bing_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'mpseo_site_connections',
			'mpseo_google_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'mpseo_site_connections',
			'mpseo_pinterest_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'mpseo_site_connections',
			'mpseo_yandex_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		// Advanced settings.
		register_setting(
			'mpseo_advanced',
			'mpseo_noindex_archives',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'mpseo_advanced',
			'mpseo_remove_stopwords',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'mpseo_advanced',
			'mpseo_breadcrumbs',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'mpseo_advanced',
			'mpseo_api_timeout',
			array(
				'type' => 'integer',
				'default' => 30,
				'sanitize_callback' => 'absint',
			)
		);

		register_setting(
			'mpseo_advanced',
			'mpseo_cache_duration',
			array(
				'type' => 'integer',
				'default' => 3600,
				'sanitize_callback' => 'absint',
			)
		);
	}

	/**
	 * Display the plugin settings page.
	 */
	public function display_plugin_settings_page()
	{
		?>
		<div class="wrap mpseo-settings">
			<?php require_once MPSEO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

			<nav class="nav-tab-wrapper">
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=general"
					class="nav-tab <?php echo esc_attr('general' === $this->active_tab ? 'nav-tab-active' : ''); ?>">
					<span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e('General', 'metapilot-smart-seo'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=api"
					class="nav-tab <?php echo esc_attr('api' === $this->active_tab ? 'nav-tab-active' : ''); ?>">
					<span class="dashicons dashicons-cloud"></span> <?php esc_html_e('AI API', 'metapilot-smart-seo'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=features"
					class="nav-tab <?php echo esc_attr('features' === $this->active_tab ? 'nav-tab-active' : ''); ?>">
					<span class="dashicons dashicons-screenoptions"></span> <?php esc_html_e('Features', 'metapilot-smart-seo'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=social"
					class="nav-tab <?php echo esc_attr('social' === $this->active_tab ? 'nav-tab-active' : ''); ?>">
					<span class="dashicons dashicons-share"></span> <?php esc_html_e('Social Media', 'metapilot-smart-seo'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=site-connections"
					class="nav-tab <?php echo esc_attr('site-connections' === $this->active_tab ? 'nav-tab-active' : ''); ?>">
					<span class="dashicons dashicons-admin-links"></span> <?php esc_html_e('Site Connections', 'metapilot-smart-seo'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=advanced"
					class="nav-tab <?php echo esc_attr('advanced' === $this->active_tab ? 'nav-tab-active' : ''); ?>">
					<span class="dashicons dashicons-admin-tools"></span> <?php esc_html_e('Advanced', 'metapilot-smart-seo'); ?>
				</a>
			</nav>

			<div class="tab-content">
				<?php
				switch ($this->active_tab) {
					case 'general':
						$this->render_general_tab();
						break;
					case 'api':
						$this->render_api_tab();
						break;
					case 'features':
						$this->render_features_tab();
						break;
					case 'social':
						$this->render_social_tab();
						break;
					case 'site-connections':
						$this->render_site_connections_tab();
						break;
					case 'advanced':
						$this->render_advanced_tab();
						break;
					default:
						$this->render_general_tab();
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render General settings tab.
	 */
	private function render_general_tab()
	{
		$mpseo_post_types         = get_post_types(array('public' => true), 'objects');
		$mpseo_enabled_post_types = get_option('mpseo_post_types', array('post', 'page'));
		$mpseo_separators         = array('-', '–', '—', '|', '/', '::', '>', '·');
		$mpseo_current_sep        = get_option('mpseo_title_separator', '-');
		?>
		<form method="post" action="options.php">
			<?php
			settings_fields('mpseo_general');
			do_settings_sections('mpseo_general');
			?>

			<!-- Post Types -->
			<div class="mpseo-section-card">
				<h2 class="mpseo-section-title"><?php esc_html_e('Post Types', 'metapilot-smart-seo'); ?></h2>
				<p class="mpseo-section-desc"><?php esc_html_e('Select which post types should have SEO meta boxes.', 'metapilot-smart-seo'); ?></p>
				<div class="post-type-list">
					<?php foreach ($mpseo_post_types as $mpseo_post_type) : ?>
						<?php if ('attachment' === $mpseo_post_type->name) continue; ?>
						<label>
							<input type="checkbox" name="mpseo_post_types[]"
								value="<?php echo esc_attr($mpseo_post_type->name); ?>"
								<?php checked(in_array($mpseo_post_type->name, $mpseo_enabled_post_types, true)); ?>>
							<span><?php echo esc_html($mpseo_post_type->label); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Title Settings -->
			<div class="mpseo-section-card">
				<h2 class="mpseo-section-title"><?php esc_html_e('Title Settings', 'metapilot-smart-seo'); ?></h2>
				<p class="mpseo-section-desc"><?php esc_html_e('Configure how your page titles appear in search results.', 'metapilot-smart-seo'); ?></p>

				<h3><?php esc_html_e('Title Separator', 'metapilot-smart-seo'); ?></h3>
				<div class="mpseo-sep-buttons">
					<?php foreach ($mpseo_separators as $mpseo_sep) : ?>
					<button type="button" class="mpseo-sep-btn <?php echo $mpseo_current_sep === $mpseo_sep ? 'selected' : ''; ?>"
						data-value="<?php echo esc_attr($mpseo_sep); ?>"><?php echo esc_html($mpseo_sep); ?></button>
					<?php endforeach; ?>
				</div>
				<input type="hidden" name="mpseo_title_separator" id="mpseo_title_separator" value="<?php echo esc_attr($mpseo_current_sep); ?>">
				<p class="description"><?php esc_html_e('This separator appears between page title and site name.', 'metapilot-smart-seo'); ?></p>

				<table class="form-table" style="margin-top:20px">
					<tr>
						<th scope="row">
							<label for="homepage_title"><?php esc_html_e('Homepage Title', 'metapilot-smart-seo'); ?></label>
						</th>
						<td>
							<input type="text" id="homepage_title" name="mpseo_homepage_title"
								value="<?php echo esc_attr(get_option('mpseo_homepage_title')); ?>" class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="homepage_description"><?php esc_html_e('Homepage Description', 'metapilot-smart-seo'); ?></label>
						</th>
						<td>
							<textarea id="homepage_description" name="mpseo_homepage_description" rows="3"
								class="large-text"><?php echo esc_textarea(get_option('mpseo_homepage_description')); ?></textarea>
						</td>
					</tr>
				</table>
			</div>

			<?php submit_button(); ?>
		</form>
		<?php
	}

	/**
	 * Render API settings tab.
	 */
	private function render_api_tab()
	{
		require_once MPSEO_PLUGIN_DIR . 'admin/views/settings-api.php';
	}

	/**
	 * Render Features settings tab.
	 */
	private function render_features_tab()
	{
		require_once MPSEO_PLUGIN_DIR . 'admin/views/settings-features.php';
	}

	/**
	 * Render Social Media settings tab.
	 */
	private function render_social_tab()
	{
		require_once MPSEO_PLUGIN_DIR . 'admin/views/settings-social.php';
	}

	/**
	 * Render Site Connections settings tab.
	 */
	private function render_site_connections_tab()
	{
		require_once MPSEO_PLUGIN_DIR . 'admin/views/settings-site-connections.php';
	}

	/**
	 * Render Advanced settings tab.
	 */
	private function render_advanced_tab()
	{
		require_once MPSEO_PLUGIN_DIR . 'admin/views/settings-advanced.php';
	}

	/**
	 * Sanitize post types array.
	 *
	 * @param array $input User input.
	 * @return array
	 */
	public function sanitize_post_types($input)
	{
		if (!is_array($input)) {
			return array();
		}

		// Get valid public post types to whitelist against.
		$valid_post_types = get_post_types(array('public' => true), 'names');

		$sanitized = array();
		foreach ($input as $post_type) {
			$post_type = sanitize_key($post_type);
			// Only allow valid, registered public post types.
			if (isset($valid_post_types[$post_type])) {
				$sanitized[] = $post_type;
			}
		}

		return $sanitized;
	}

	/**
	 * Sanitize verification code.
	 *
	 * Extracts the content value if full meta tag is pasted.
	 *
	 * @param string $input The input value.
	 * @return string Sanitized verification code.
	 */
	public function sanitize_verification_code($input)
	{
		$input = trim($input);

		if (empty($input)) {
			return '';
		}

		// If user pasted full meta tag, extract the content value.
		if (preg_match('/content=["\']([^"\']+)["\']/i', $input, $mpseo_matches)) {
			return sanitize_text_field($mpseo_matches[1]);
		}

		// Otherwise just sanitize and return.
		return sanitize_text_field($input);
	}

	/**
	 * Sanitize Ahrefs analytics key.
	 *
	 * Extracts the data-key value if full script tag is pasted.
	 *
	 * @param string $input The input value.
	 * @return string Sanitized Ahrefs key.
	 */
	public function sanitize_ahrefs_key($input)
	{
		$input = trim($input);

		if (empty($input)) {
			return '';
		}

		// If user pasted full script tag, extract the data-key value.
		if (preg_match('/data-key=["\']([^"\']+)["\']/i', $input, $mpseo_matches)) {
			return sanitize_text_field($mpseo_matches[1]);
		}

		// Otherwise just sanitize and return.
		return sanitize_text_field($input);
	}

	/**
	 * Enqueue Ahrefs analytics script on frontend.
	 * Call this from wp_enqueue_scripts hook.
	 */
	public static function enqueue_ahrefs_analytics()
	{
		// Only on frontend.
		if (is_admin()) {
			return;
		}

		// Only on homepage/front page.
		if (!is_front_page() && !is_home()) {
			return;
		}

		$ahrefs_key = get_option('mpseo_ahrefs_verification', '');

		if (empty($ahrefs_key)) {
			return;
		}

		wp_enqueue_script(
			'mpseo-ahrefs-analytics',
			'https://analytics.ahrefs.com/analytics.js',
			array(),
			'1.0.0',
			false // Load in head.
		);
	}

	/**
	 * Add data-key and async attributes to Ahrefs script.
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 * @param string $src    The script source.
	 * @return string Modified script tag.
	 */
	public static function add_ahrefs_script_attributes($tag, $handle, $src)
	{
		if ('mpseo-ahrefs-analytics' !== $handle) {
			return $tag;
		}

		$ahrefs_key = get_option('mpseo_ahrefs_verification', '');

		if (empty($ahrefs_key)) {
			return $tag;
		}

		// Add data-key and async attributes.
		$tag = str_replace(
			' src=',
			' data-key="' . esc_attr($ahrefs_key) . '" async src=',
			$tag
		);

		return $tag;
	}

	/**
	 * Output verification meta tags in frontend head.
	 * Call this from wp_head hook.
	 */
	public static function output_verification_meta()
	{
		// Only output on homepage/front page.
		if (!is_front_page() && !is_home()) {
			return;
		}

		// Note: Ahrefs is now handled via enqueue_ahrefs_analytics() method.

		$mpseo_verifications = array(
			'baidu' => array(
				'option' => 'mpseo_baidu_verification',
				'name' => 'baidu-site-verification',
			),
			'bing' => array(
				'option' => 'mpseo_bing_verification',
				'name' => 'msvalidate.01',
			),
			'google' => array(
				'option' => 'mpseo_google_verification',
				'name' => 'google-site-verification',
			),
			'pinterest' => array(
				'option' => 'mpseo_pinterest_verification',
				'name' => 'p:domain_verify',
			),
			'yandex' => array(
				'option' => 'mpseo_yandex_verification',
				'name' => 'yandex-verification',
			),
		);

		foreach ($mpseo_verifications as $mpseo_key => $mpseo_verification) {
			$mpseo_code = get_option($mpseo_verification['option'], '');

			if (!empty($mpseo_code)) {
				echo '<meta name="' . esc_attr($mpseo_verification['name']) . '" content="' . esc_attr($mpseo_code) . '" />' . "\n";
			}
		}
	}

}