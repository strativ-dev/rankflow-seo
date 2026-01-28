<?php
/**
 * The settings page functionality.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin
 * @author     Strativ AB
 */
class RankFlow_SEO_Settings
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
	 * @var RankFlow_SEO_Redirect_Admin
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
			__('RankFlow SEO', 'rankflow-seo'),
			__('RankFlow SEO', 'rankflow-seo'),
			'manage_options',
			$this->plugin_name,
			array($this, 'display_plugin_admin_page'),
			RANKFLOW_SEO_PLUGIN_URL . 'assets/images/rankflow-icon-white.png',
			65
		);

		add_submenu_page(
			$this->plugin_name,
			__('Dashboard', 'rankflow-seo'),
			__('Dashboard', 'rankflow-seo'),
			'manage_options',
			$this->plugin_name,
			array($this, 'display_plugin_admin_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Redirects', 'rankflow-seo'),
			__('Redirects', 'rankflow-seo'),
			'manage_options',
			$this->plugin_name . '-redirects',
			array($this, 'display_redirects_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('404 Monitor', 'rankflow-seo'),
			__('404 Monitor', 'rankflow-seo'),
			'manage_options',
			$this->plugin_name . '-404-monitor',
			array($this, 'display_404_monitor_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Robots.txt', 'rankflow-seo'),
			__('Robots.txt', 'rankflow-seo'),
			'manage_options',
			$this->plugin_name . '-robots-txt',
			array($this, 'display_robots_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Schema', 'rankflow-seo'),
			__('Schema', 'rankflow-seo'),
			'manage_options',
			$this->plugin_name . '-schema',
			array($this, 'display_schema_page')
		);

		add_submenu_page(
			'rankflow-seo',
			__('XML Sitemap', 'rankflow-seo'),
			__('XML Sitemap', 'rankflow-seo'),
			'manage_options',
			'rankflow-seo-sitemap',
			array($this, 'display_sitemap_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Settings', 'rankflow-seo'),
			__('Settings', 'rankflow-seo'),
			'manage_options',
			$this->plugin_name . '-settings',
			array($this, 'display_plugin_settings_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Help', 'rankflow-seo'),
			__('Help', 'rankflow-seo'),
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
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/dashboard.php';
	}

	/**
	 * Display redirects page
	 */
	public function display_redirects_page()
	{
		// Get or create redirect admin instance.
		if (!isset($this->redirect_admin)) {
			require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/class-redirect-admin.php';
			$this->redirect_admin = new RankFlow_SEO_Redirect_Admin($this->plugin_name);
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
			require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/class-redirect-admin.php';
			$this->redirect_admin = new RankFlow_SEO_Redirect_Admin($this->plugin_name);
		}

		// Call the redirect admin display method.
		$this->redirect_admin->display_404_monitor_page();
	}

	/**
	 * Display robots.txt page
	 */
	public function display_robots_page()
	{
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/settings-robots-txt.php';
	}

	/**
	 * Display schema page
	 */
	public function display_schema_page()
	{
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/settings-schema.php';
	}

	/**
	 * Display XML Sitemap page
	 */
	public function display_sitemap_page()
	{
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/settings-sitemap.php';
	}

	/**
	 * Display help page.
	 */
	public function display_help_page()
	{
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/help.php';
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings()
	{

		// General settings.
		register_setting(
			'rankflow_seo_general',
			'rankflow_seo_post_types',
			array(
				'type' => 'array',
				'sanitize_callback' => array($this, 'sanitize_post_types'),
			)
		);

		register_setting(
			'rankflow_seo_general',
			'rankflow_seo_title_separator',
			array(
				'type' => 'string',
				'default' => '-',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'rankflow_seo_general',
			'rankflow_seo_homepage_title',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'rankflow_seo_general',
			'rankflow_seo_homepage_description',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);

		register_setting(
			'rankflow_seo_general',
			'rankflow_seo_site_represents',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'rankflow_seo_general',
			'rankflow_seo_organization_name',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'rankflow_seo_general',
			'rankflow_seo_person_name',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'rankflow_seo_general',
			'rankflow_seo_site_logo',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		// API settings.
		register_setting(
			'rankflow_seo_api',
			'rankflow_seo_api_provider',
			array(
				'type' => 'string',
				'default' => 'gemini',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'rankflow_seo_api',
			'rankflow_seo_api_key',
			array(
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'rankflow_seo_api',
			'rankflow_seo_auto_generate',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		// Feature settings - SEPARATE GROUP.
		register_setting(
			'rankflow_seo_features',
			'rankflow_seo_enable_content_analysis',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'rankflow_seo_features',
			'rankflow_seo_enable_seo_score',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'rankflow_seo_features',
			'rankflow_seo_enable_schema',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'rankflow_seo_features',
			'rankflow_seo_focus_keyword',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'rankflow_seo_features',
			'rankflow_seo_readability_analysis',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		// Social settings - SEPARATE GROUP.
		register_setting(
			'rankflow_seo_social',
			'rankflow_seo_og_tags',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'rankflow_seo_social',
			'rankflow_seo_twitter_cards',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'rankflow_seo_social',
			'rankflow_seo_default_og_image',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		register_setting(
			'rankflow_seo_social',
			'rankflow_seo_twitter_username',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// Site Connections settings.
		register_setting(
			'rankflow_seo_site_connections',
			'rankflow_seo_ahrefs_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_ahrefs_key'),
				'default' => '',
			)
		);

		register_setting(
			'rankflow_seo_site_connections',
			'rankflow_seo_baidu_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'rankflow_seo_site_connections',
			'rankflow_seo_bing_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'rankflow_seo_site_connections',
			'rankflow_seo_google_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'rankflow_seo_site_connections',
			'rankflow_seo_pinterest_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'rankflow_seo_site_connections',
			'rankflow_seo_yandex_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'rankflow_seo_site_connections',
			'rankflow_seo_gtm_id',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_gtm_id'),
				'default' => '',
			)
		);

		// Advanced settings.
		register_setting(
			'rankflow_seo_advanced',
			'rankflow_seo_noindex_archives',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'rankflow_seo_advanced',
			'rankflow_seo_remove_stopwords',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'rankflow_seo_advanced',
			'rankflow_seo_breadcrumbs',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'rankflow_seo_advanced',
			'rankflow_seo_api_timeout',
			array(
				'type' => 'integer',
				'default' => 30,
				'sanitize_callback' => 'absint',
			)
		);

		register_setting(
			'rankflow_seo_advanced',
			'rankflow_seo_cache_duration',
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
		<div class="wrap rankflow-seo-settings">
			<?php require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

			<?php settings_errors(); ?>

			<nav class="nav-tab-wrapper">
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=general"
					class="nav-tab <?php echo 'general' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('General', 'rankflow-seo'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=api"
					class="nav-tab <?php echo 'api' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('AI API', 'rankflow-seo'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=features"
					class="nav-tab <?php echo 'features' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('Features', 'rankflow-seo'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=social"
					class="nav-tab <?php echo 'social' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('Social Media', 'rankflow-seo'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=site-connections"
					class="nav-tab <?php echo 'site-connections' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('Site Connections', 'rankflow-seo'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=advanced"
					class="nav-tab <?php echo 'advanced' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('Advanced', 'rankflow-seo'); ?>
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
		?>
		<form method="post" action="options.php">
			<?php
			settings_fields('rankflow_seo_general');
			do_settings_sections('rankflow_seo_general');
			?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label><?php esc_html_e('Enable for Post Types', 'rankflow-seo'); ?></label>
					</th>
					<td>
						<?php
						$rankflow_seo_post_types = get_post_types(array('public' => true), 'objects');
						$rankflow_seo_enabled_post_types = get_option('rankflow_seo_post_types', array('post', 'page'));

						foreach ($rankflow_seo_post_types as $rankflow_seo_post_type) {
							if ('attachment' === $rankflow_seo_post_type->name) {
								continue;
							}
							?>
							<label class="rankflow-seo-label-block">
								<input type="checkbox" name="rankflow_seo_post_types[]"
									value="<?php echo esc_attr($rankflow_seo_post_type->name); ?>" <?php checked(in_array($rankflow_seo_post_type->name, $rankflow_seo_enabled_post_types, true)); ?>>
								<?php echo esc_html($rankflow_seo_post_type->label); ?>
							</label>
							<?php
						}
						?>
						<p class="description">
							<?php esc_html_e('Select which post types should have AI SEO meta boxes.', 'rankflow-seo'); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="title_separator"><?php esc_html_e('Title Separator', 'rankflow-seo'); ?></label>
					</th>
					<td>
						<select name="rankflow_seo_title_separator" id="title_separator">
							<?php
							$rankflow_seo_separators = array('-', '–', '—', '|', '/', '::', '<', '>');
							$rankflow_seo_current = get_option('rankflow_seo_title_separator', '-');
							foreach ($rankflow_seo_separators as $rankflow_seo_sep) {
								?>
								<option value="<?php echo esc_attr($rankflow_seo_sep); ?>" <?php selected($rankflow_seo_current, $rankflow_seo_sep); ?>>
									<?php echo esc_html($rankflow_seo_sep); ?>
								</option>
								<?php
							}
							?>
						</select>
						<p class="description">
							<?php esc_html_e('Choose separator for page titles.', 'rankflow-seo'); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="homepage_title"><?php esc_html_e('Homepage Title', 'rankflow-seo'); ?></label>
					</th>
					<td>
						<input type="text" id="homepage_title" name="rankflow_seo_homepage_title"
							value="<?php echo esc_attr(get_option('rankflow_seo_homepage_title')); ?>" class="regular-text">
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="homepage_description"><?php esc_html_e('Homepage Description', 'rankflow-seo'); ?></label>
					</th>
					<td>
						<textarea id="homepage_description" name="rankflow_seo_homepage_description" rows="3"
							class="large-text"><?php echo esc_textarea(get_option('rankflow_seo_homepage_description')); ?></textarea>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
		<?php
	}

	/**
	 * Render API settings tab.
	 */
	private function render_api_tab()
	{
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/settings-api.php';
	}

	/**
	 * Render Features settings tab.
	 */
	private function render_features_tab()
	{
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/settings-features.php';
	}

	/**
	 * Render Social Media settings tab.
	 */
	private function render_social_tab()
	{
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/settings-social.php';
	}

	/**
	 * Render Site Connections settings tab.
	 */
	private function render_site_connections_tab()
	{
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/settings-site-connections.php';
	}

	/**
	 * Render Advanced settings tab.
	 */
	private function render_advanced_tab()
	{
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/settings-advanced.php';
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

		return array_map('sanitize_text_field', $input);
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
		if (preg_match('/content=["\']([^"\']+)["\']/i', $input, $rankflow_seo_matches)) {
			return sanitize_text_field($rankflow_seo_matches[1]);
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
		if (preg_match('/data-key=["\']([^"\']+)["\']/i', $input, $rankflow_seo_matches)) {
			return sanitize_text_field($rankflow_seo_matches[1]);
		}

		// Otherwise just sanitize and return.
		return sanitize_text_field($input);
	}

	/**
	 * Sanitize GTM Container ID.
	 *
	 * @param string $input The input value.
	 * @return string Sanitized GTM ID with GTM- prefix.
	 */
	public function sanitize_gtm_id($input)
	{
		$input = trim($input);

		if (empty($input)) {
			return '';
		}

		if (preg_match('/GTM-([A-Z0-9]+)/i', $input, $matches)) {
			return 'GTM-' . strtoupper(sanitize_text_field($matches[1]));
		}

		$input = preg_replace('/^GTM-/i', '', $input);
		$clean_id = strtoupper(sanitize_text_field($input));

		if (empty($clean_id)) {
			return '';
		}

		return 'GTM-' . $clean_id;
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

		$ahrefs_key = get_option('rankflow_seo_ahrefs_verification', '');

		if (empty($ahrefs_key)) {
			return;
		}

		wp_enqueue_script(
			'rankflow-seo-ahrefs-analytics',
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
		if ('rankflow-seo-ahrefs-analytics' !== $handle) {
			return $tag;
		}

		$ahrefs_key = get_option('rankflow_seo_ahrefs_verification', '');

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

		$rankflow_seo_verifications = array(
			'baidu' => array(
				'option' => 'rankflow_seo_baidu_verification',
				'name' => 'baidu-site-verification',
			),
			'bing' => array(
				'option' => 'rankflow_seo_bing_verification',
				'name' => 'msvalidate.01',
			),
			'google' => array(
				'option' => 'rankflow_seo_google_verification',
				'name' => 'google-site-verification',
			),
			'pinterest' => array(
				'option' => 'rankflow_seo_pinterest_verification',
				'name' => 'p:domain_verify',
			),
			'yandex' => array(
				'option' => 'rankflow_seo_yandex_verification',
				'name' => 'yandex-verification',
			),
		);

		foreach ($rankflow_seo_verifications as $rankflow_seo_key => $rankflow_seo_verification) {
			$rankflow_seo_code = get_option($rankflow_seo_verification['option'], '');

			if (!empty($rankflow_seo_code)) {
				echo '<meta name="' . esc_attr($rankflow_seo_verification['name']) . '" content="' . esc_attr($rankflow_seo_code) . '" />' . "\n";
			}
		}
	}

	/**
	 * Output Google Tag Manager script in head.
	 * GTM requires the script to be in the head as early as possible.
	 * Using wp_head hook directly is the recommended approach for GTM.
	 */
	public static function output_gtm_head()
	{
		$gtm_id = get_option('rankflow_seo_gtm_id', '');

		if (empty($gtm_id) || is_admin()) {
			return;
		}

		// Output GTM script directly in head (this is the standard GTM implementation).
		?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo esc_js($gtm_id); ?>');</script>
<!-- End Google Tag Manager -->
		<?php
	}

	/**
	 * Output Google Tag Manager noscript body tag.
	 * Note: noscript tags cannot be enqueued, they must be output directly.
	 */
	public static function output_gtm_body()
	{
		$gtm_id = get_option('rankflow_seo_gtm_id', '');

		if (empty($gtm_id) || is_admin()) {
			return;
		}

		// Output noscript fallback for GTM.
		printf(
			'<!-- Google Tag Manager (noscript) --><noscript><iframe src="%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript><!-- End Google Tag Manager (noscript) -->',
			esc_url('https://www.googletagmanager.com/ns.html?id=' . $gtm_id)
		);
	}
}