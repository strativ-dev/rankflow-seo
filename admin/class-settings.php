<?php
/**
 * The settings page functionality.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin
 * @author     Strativ AB
 */
class AI_SEO_Pro_Settings
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
	 * @var AI_SEO_Pro_Redirect_Admin
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
			__('AI SEO Pro', 'ai-seo-pro'),
			__('AI SEO Pro', 'ai-seo-pro'),
			'manage_options',
			$this->plugin_name,
			array($this, 'display_plugin_admin_page'),
			'dashicons-search',
			65
		);

		add_submenu_page(
			$this->plugin_name,
			__('Dashboard', 'ai-seo-pro'),
			__('Dashboard', 'ai-seo-pro'),
			'manage_options',
			$this->plugin_name,
			array($this, 'display_plugin_admin_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Redirects', 'ai-seo-pro'),
			__('Redirects', 'ai-seo-pro'),
			'manage_options',
			$this->plugin_name . '-redirects',
			array($this, 'display_redirects_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('404 Monitor', 'ai-seo-pro'),
			__('404 Monitor', 'ai-seo-pro'),
			'manage_options',
			$this->plugin_name . '-404-monitor',
			array($this, 'display_404_monitor_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Robots.txt', 'ai-seo-pro'),
			__('Robots.txt', 'ai-seo-pro'),
			'manage_options',
			$this->plugin_name . '-robots-txt',
			array($this, 'display_robots_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Schema', 'ai-seo-pro'),
			__('Schema', 'ai-seo-pro'),
			'manage_options',
			$this->plugin_name . '-schema',
			array($this, 'display_schema_page')
		);

		add_submenu_page(
			'ai-seo-pro',
			__('XML Sitemap', 'ai-seo-pro'),
			__('XML Sitemap', 'ai-seo-pro'),
			'manage_options',
			'ai-seo-pro-sitemap',
			array($this, 'display_sitemap_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Settings', 'ai-seo-pro'),
			__('Settings', 'ai-seo-pro'),
			'manage_options',
			$this->plugin_name . '-settings',
			array($this, 'display_plugin_settings_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Help', 'ai-seo-pro'),
			__('Help', 'ai-seo-pro'),
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
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/views/dashboard.php';
	}

	/**
	 * Display redirects page
	 */
	public function display_redirects_page()
	{
		// Get or create redirect admin instance
		if (!isset($this->redirect_admin)) {
			require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/class-redirect-admin.php';
			$this->redirect_admin = new AI_SEO_Pro_Redirect_Admin($this->plugin_name);
		}

		// Call the redirect admin display method
		$this->redirect_admin->display_redirects_page();
	}

	/**
	 * Display 404 monitor page
	 */
	public function display_404_monitor_page()
	{
		// Get or create redirect admin instance
		if (!isset($this->redirect_admin)) {
			require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/class-redirect-admin.php';
			$this->redirect_admin = new AI_SEO_Pro_Redirect_Admin($this->plugin_name);
		}

		// Call the redirect admin display method
		$this->redirect_admin->display_404_monitor_page();
	}

	/**
	 * Display robots.txt page
	 */
	public function display_robots_page()
	{
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/views/settings-robots-txt.php';
	}

	/**
	 * Display schema page
	 */
	public function display_schema_page()
	{
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/views/settings-schema.php';
	}

	/**
	 * Display XML Sitemap page
	 */
	public function display_sitemap_page()
	{
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/views/settings-sitemap.php';
	}

	/**
	 * Display help page.
	 */
	public function display_help_page()
	{
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/views/help.php';
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings()
	{

		// General settings.
		register_setting(
			'ai_seo_pro_general',
			'ai_seo_pro_post_types',
			array(
				'type' => 'array',
				'sanitize_callback' => array($this, 'sanitize_post_types'),
			)
		);

		register_setting(
			'ai_seo_pro_general',
			'ai_seo_pro_title_separator',
			array(
				'type' => 'string',
				'default' => '-',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'ai_seo_pro_general',
			'ai_seo_pro_homepage_title',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'ai_seo_pro_general',
			'ai_seo_pro_homepage_description',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);

		register_setting(
			'ai_seo_pro_general',
			'ai_seo_pro_site_represents',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'ai_seo_pro_general',
			'ai_seo_pro_organization_name',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'ai_seo_pro_general',
			'ai_seo_pro_person_name',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'ai_seo_pro_general',
			'ai_seo_pro_site_logo',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		// API settings.
		register_setting(
			'ai_seo_pro_api',
			'ai_seo_pro_api_provider',
			array(
				'type' => 'string',
				'default' => 'gemini',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'ai_seo_pro_api',
			'ai_seo_pro_api_key',
			array(
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'ai_seo_pro_api',
			'ai_seo_pro_auto_generate',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		// Feature settings - SEPARATE GROUP.
		register_setting(
			'ai_seo_pro_features',
			'ai_seo_pro_enable_content_analysis',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'ai_seo_pro_features',
			'ai_seo_pro_enable_seo_score',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'ai_seo_pro_features',
			'ai_seo_pro_enable_schema',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'ai_seo_pro_features',
			'ai_seo_pro_focus_keyword',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'ai_seo_pro_features',
			'ai_seo_pro_readability_analysis',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		// Social settings - SEPARATE GROUP.
		register_setting(
			'ai_seo_pro_social',
			'ai_seo_pro_og_tags',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'ai_seo_pro_social',
			'ai_seo_pro_twitter_cards',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'ai_seo_pro_social',
			'ai_seo_pro_default_og_image',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		register_setting(
			'ai_seo_pro_social',
			'ai_seo_pro_twitter_username',
			array(
				'type' => 'string',
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// Site Connections settings.
		register_setting(
			'ai_seo_pro_site_connections',
			'ai_seo_pro_ahrefs_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'ai_seo_pro_site_connections',
			'ai_seo_pro_baidu_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'ai_seo_pro_site_connections',
			'ai_seo_pro_bing_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'ai_seo_pro_site_connections',
			'ai_seo_pro_google_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'ai_seo_pro_site_connections',
			'ai_seo_pro_pinterest_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		register_setting(
			'ai_seo_pro_site_connections',
			'ai_seo_pro_yandex_verification',
			array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_verification_code'),
				'default' => '',
			)
		);

		// Advanced settings.
		register_setting(
			'ai_seo_pro_advanced',
			'ai_seo_pro_noindex_archives',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'ai_seo_pro_advanced',
			'ai_seo_pro_remove_stopwords',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'ai_seo_pro_advanced',
			'ai_seo_pro_breadcrumbs',
			array(
				'type' => 'boolean',
				'default' => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_setting(
			'ai_seo_pro_advanced',
			'ai_seo_pro_api_timeout',
			array(
				'type' => 'integer',
				'default' => 30,
				'sanitize_callback' => 'absint',
			)
		);

		register_setting(
			'ai_seo_pro_advanced',
			'ai_seo_pro_cache_duration',
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
		<div class="wrap ai-seo-pro-settings">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>

			<?php settings_errors(); ?>

			<nav class="nav-tab-wrapper">
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=general"
					class="nav-tab <?php echo 'general' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('General', 'ai-seo-pro'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=api"
					class="nav-tab <?php echo 'api' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('AI API', 'ai-seo-pro'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=features"
					class="nav-tab <?php echo 'features' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('Features', 'ai-seo-pro'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=social"
					class="nav-tab <?php echo 'social' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('Social Media', 'ai-seo-pro'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=site-connections"
					class="nav-tab <?php echo 'site-connections' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('Site Connections', 'ai-seo-pro'); ?>
				</a>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-settings&tab=advanced"
					class="nav-tab <?php echo 'advanced' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('Advanced', 'ai-seo-pro'); ?>
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
			settings_fields('ai_seo_pro_general');
			do_settings_sections('ai_seo_pro_general');
			?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label><?php esc_html_e('Enable for Post Types', 'ai-seo-pro'); ?></label>
					</th>
					<td>
						<?php
						$ai_seo_pro_post_types = get_post_types(array('public' => true), 'objects');
						$ai_seo_pro_enabled_post_types = get_option('ai_seo_pro_post_types', array('post', 'page'));

						foreach ($ai_seo_pro_post_types as $ai_seo_pro_post_type) {
							if ('attachment' === $ai_seo_pro_post_type->name) {
								continue;
							}
							?>
							<label style="display: block; margin-bottom: 5px;">
								<input type="checkbox" name="ai_seo_pro_post_types[]"
									value="<?php echo esc_attr($ai_seo_pro_post_type->name); ?>" <?php checked(in_array($ai_seo_pro_post_type->name, $ai_seo_pro_enabled_post_types, true)); ?>>
								<?php echo esc_html($ai_seo_pro_post_type->label); ?>
							</label>
							<?php
						}
						?>
						<p class="description">
							<?php esc_html_e('Select which post types should have AI SEO meta boxes.', 'ai-seo-pro'); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="title_separator"><?php esc_html_e('Title Separator', 'ai-seo-pro'); ?></label>
					</th>
					<td>
						<select name="ai_seo_pro_title_separator" id="title_separator">
							<?php
							$ai_seo_pro_separators = array('-', '–', '—', '|', '/', '::', '<', '>');
							$ai_seo_pro_current = get_option('ai_seo_pro_title_separator', '-');
							foreach ($ai_seo_pro_separators as $ai_seo_pro_sep) {
								?>
								<option value="<?php echo esc_attr($ai_seo_pro_sep); ?>" <?php selected($ai_seo_pro_current, $ai_seo_pro_sep); ?>>
									<?php echo esc_html($ai_seo_pro_sep); ?>
								</option>
								<?php
							}
							?>
						</select>
						<p class="description">
							<?php esc_html_e('Choose separator for page titles.', 'ai-seo-pro'); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="homepage_title"><?php esc_html_e('Homepage Title', 'ai-seo-pro'); ?></label>
					</th>
					<td>
						<input type="text" id="homepage_title" name="ai_seo_pro_homepage_title"
							value="<?php echo esc_attr(get_option('ai_seo_pro_homepage_title')); ?>" class="regular-text">
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="homepage_description"><?php esc_html_e('Homepage Description', 'ai-seo-pro'); ?></label>
					</th>
					<td>
						<textarea id="homepage_description" name="ai_seo_pro_homepage_description" rows="3"
							class="large-text"><?php echo esc_textarea(get_option('ai_seo_pro_homepage_description')); ?></textarea>
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
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/views/settings-api.php';
	}

	/**
	 * Render Features settings tab.
	 */
	private function render_features_tab()
	{
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/views/settings-features.php';
	}

	/**
	 * Render Social Media settings tab.
	 */
	private function render_social_tab()
	{
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/views/settings-social.php';
	}

	/**
	 * Render Site Connections settings tab.
	 */
	private function render_site_connections_tab()
	{
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/views/settings-site-connections.php';
	}

	/**
	 * Render Advanced settings tab.
	 */
	private function render_advanced_tab()
	{
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/views/settings-advanced.php';
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
		if (preg_match('/content=["\']([^"\']+)["\']/i', $input, $ai_seo_pro_matches)) {
			return sanitize_text_field($ai_seo_pro_matches[1]);
		}

		// Otherwise just sanitize and return.
		return sanitize_text_field($input);
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

		$ai_seo_pro_verifications = array(
			'ahrefs' => array(
				'option' => 'ai_seo_pro_ahrefs_verification',
				'name' => 'ahrefs-site-verification',
			),
			'baidu' => array(
				'option' => 'ai_seo_pro_baidu_verification',
				'name' => 'baidu-site-verification',
			),
			'bing' => array(
				'option' => 'ai_seo_pro_bing_verification',
				'name' => 'msvalidate.01',
			),
			'google' => array(
				'option' => 'ai_seo_pro_google_verification',
				'name' => 'google-site-verification',
			),
			'pinterest' => array(
				'option' => 'ai_seo_pro_pinterest_verification',
				'name' => 'p:domain_verify',
			),
			'yandex' => array(
				'option' => 'ai_seo_pro_yandex_verification',
				'name' => 'yandex-verification',
			),
		);

		foreach ($ai_seo_pro_verifications as $ai_seo_pro_key => $ai_seo_pro_verification) {
			$ai_seo_pro_code = get_option($ai_seo_pro_verification['option'], '');

			if (!empty($ai_seo_pro_code)) {
				echo '<meta name="' . esc_attr($ai_seo_pro_verification['name']) . '" content="' . esc_attr($ai_seo_pro_code) . '" />' . "\n";
			}
		}
	}
}