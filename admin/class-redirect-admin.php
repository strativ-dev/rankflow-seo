<?php
/**
 * Redirect Admin - Admin interface for redirects
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class RankFlow_SEO_Redirect_Admin
 */
class RankFlow_SEO_Redirect_Admin
{

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Redirect manager
	 *
	 * @var RankFlow_SEO_Redirect_Manager
	 */
	private $redirect_manager;

	/**
	 * 404 monitor
	 *
	 * @var RankFlow_SEO_404_Monitor
	 */
	private $monitor_404;

	/**
	 * Constructor
	 *
	 * @param string $plugin_name The plugin name.
	 */
	public function __construct($plugin_name)
	{
		$this->plugin_name = $plugin_name;
		$this->redirect_manager = new RankFlow_SEO_Redirect_Manager();
		$this->monitor_404 = new RankFlow_SEO_404_Monitor();

		// Handle actions early, before any output.
		add_action('admin_init', array($this, 'handle_redirect_actions'));
		add_action('admin_init', array($this, 'handle_404_actions'));
	}


	/**
	 * Display redirects page
	 */
	public function display_redirects_page()
	{
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification not required for display action parameter.
		$rankflow_seo_action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : 'list';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification not required for display ID parameter.
		$rankflow_seo_redirect_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

		?>
		<div class="wrap rankflow-seo-redirects">
			<?php require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

			<?php if ('list' === $rankflow_seo_action): ?>
				<a href="?page=<?php echo esc_attr($this->plugin_name); ?>-redirects&action=add" class="page-title-action">
					<?php esc_html_e('Add New', 'rankflow-seo'); ?>
				</a>
			<?php endif; ?>

			<hr class="wp-header-end">

			<?php
			switch ($rankflow_seo_action) {
				case 'add':
					$this->render_add_redirect_form();
					break;
				case 'edit':
					$this->render_edit_redirect_form($rankflow_seo_redirect_id);
					break;
				case 'import':
					$this->render_import_form();
					break;
				default:
					$this->render_redirects_list();
					break;
			}
			?>
		</div>
		<?php
	}

	/**
	 * Display 404 monitor page
	 */
	public function display_404_monitor_page()
	{
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/404-monitor.php';
	}

	/**
	 * Render redirects list
	 */
	private function render_redirects_list()
	{
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification not required for pagination parameter.
		$rankflow_seo_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification not required for search parameter.
		$rankflow_seo_search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

		$rankflow_seo_results = $this->redirect_manager->get_redirects(
			array(
				'page' => $rankflow_seo_page,
				'per_page' => 20,
				'search' => $rankflow_seo_search,
			)
		);

		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/redirects-list.php';
	}

	/**
	 * Render add redirect form
	 */
	private function render_add_redirect_form()
	{
		$rankflow_seo_redirect = (object) array(
			'source_url' => '',
			'target_url' => '',
			'redirect_type' => '301',
			'is_regex' => 0,
			'is_active' => 1,
		);

		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/redirect-form.php';
	}

	/**
	 * Render edit redirect form
	 *
	 * @param int $redirect_id The redirect ID.
	 */
	private function render_edit_redirect_form($redirect_id)
	{
		$rankflow_seo_redirect = $this->redirect_manager->get_redirect($redirect_id);

		if (!$rankflow_seo_redirect) {
			wp_die(esc_html__('Redirect not found.', 'rankflow-seo'));
		}

		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/redirect-form.php';
	}

	/**
	 * Render import form
	 */
	private function render_import_form()
	{
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/views/redirect-import.php';
	}

	/**
	 * Handle redirect actions
	 *
	 * @access public (required for WordPress hooks)
	 */
	public function handle_redirect_actions()
	{
		// Only run on our redirect pages.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification happens later for specific actions.
		if (
			!isset($_GET['page']) ||
			(sanitize_text_field(wp_unslash($_GET['page'])) !== $this->plugin_name . '-redirects')
		) {
			return;
		}

		// Check nonce for POST actions.
		if (!isset($_POST['rankflow_seo_redirect_nonce']) && !isset($_GET['_wpnonce'])) {
			return;
		}

		// Export CSV - must be first, before any output.
		if (isset($_GET['action']) && 'export' === $_GET['action']) {
			if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'export_redirects')) {
				wp_die(esc_html__('Security check failed.', 'rankflow-seo'));
			}

			$rankflow_seo_csv = $this->redirect_manager->export_to_csv();

			// Clear any output buffers.
			if (ob_get_length()) {
				ob_end_clean();
			}

			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename="rankflow-seo-redirects-' . gmdate('Y-m-d') . '.csv"');
			header('Pragma: no-cache');
			header('Expires: 0');

			echo "\xEF\xBB\xBF"; // UTF-8 BOM.
			echo $rankflow_seo_csv; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSV output.
			exit;
		}

		// Import CSV.
		if (isset($_POST['action']) && 'import_csv' === $_POST['action']) {
			check_admin_referer('import_redirects', 'rankflow_seo_redirect_nonce');

			// Validate file upload.
			if (
				isset($_FILES['csv_file']) &&
				isset($_FILES['csv_file']['error']) &&
				isset($_FILES['csv_file']['tmp_name']) &&
				UPLOAD_ERR_OK === intval($_FILES['csv_file']['error'])
			) {
				// Sanitize the temporary file path.
				$rankflow_seo_tmp_file = sanitize_text_field(wp_unslash($_FILES['csv_file']['tmp_name']));

				// Additional security: verify it's actually a valid uploaded file.
				if (is_uploaded_file($rankflow_seo_tmp_file)) {
					$rankflow_seo_import_results = $this->redirect_manager->import_from_csv($rankflow_seo_tmp_file);

					$rankflow_seo_message = sprintf(
						/* translators: 1: number of successful imports, 2: number of failed imports */
						__('Import complete: %1$d succeeded, %2$d failed.', 'rankflow-seo'),
						$rankflow_seo_import_results['success'],
						$rankflow_seo_import_results['failed']
					);

					add_settings_error(
						'rankflow_seo_redirects',
						'import_complete',
						$rankflow_seo_message,
						$rankflow_seo_import_results['failed'] > 0 ? 'warning' : 'success'
					);
				} else {
					add_settings_error(
						'rankflow_seo_redirects',
						'import_error',
						__('Invalid file upload.', 'rankflow-seo'),
						'error'
					);
				}
			} else {
				add_settings_error(
					'rankflow_seo_redirects',
					'import_error',
					__('No file was uploaded or there was an upload error.', 'rankflow-seo'),
					'error'
				);
			}

			set_transient('rankflow_seo_redirect_notices', get_settings_errors('rankflow_seo_redirects'), 30);
			wp_safe_redirect(admin_url('admin.php?page=' . $this->plugin_name . '-redirects'));
			exit;
		}

		// Save redirect.
		if (isset($_POST['action']) && 'save_redirect' === $_POST['action']) {
			check_admin_referer('rankflow_seo_redirect_save', 'rankflow_seo_redirect_nonce');

			$rankflow_seo_redirect_id = isset($_POST['redirect_id']) ? intval($_POST['redirect_id']) : 0;

			$rankflow_seo_data = array(
				'source_url' => isset($_POST['source_url']) ? sanitize_text_field(wp_unslash($_POST['source_url'])) : '',
				'target_url' => isset($_POST['target_url']) ? sanitize_text_field(wp_unslash($_POST['target_url'])) : '',
				'redirect_type' => isset($_POST['redirect_type']) ? sanitize_text_field(wp_unslash($_POST['redirect_type'])) : '301',
				'is_regex' => isset($_POST['is_regex']) ? 1 : 0,
				'is_active' => isset($_POST['is_active']) ? 1 : 0,
			);

			if ($rankflow_seo_redirect_id > 0) {
				// Update.
				$rankflow_seo_result = $this->redirect_manager->update_redirect($rankflow_seo_redirect_id, $rankflow_seo_data);
				$rankflow_seo_message = __('Redirect updated successfully.', 'rankflow-seo');
			} else {
				// Add.
				$rankflow_seo_result = $this->redirect_manager->add_redirect($rankflow_seo_data);
				$rankflow_seo_message = __('Redirect added successfully.', 'rankflow-seo');
			}

			if (is_wp_error($rankflow_seo_result)) {
				add_settings_error(
					'rankflow_seo_redirects',
					'redirect_error',
					$rankflow_seo_result->get_error_message(),
					'error'
				);
			} else {
				add_settings_error(
					'rankflow_seo_redirects',
					'redirect_success',
					$rankflow_seo_message,
					'success'
				);
			}

			set_transient('rankflow_seo_redirect_notices', get_settings_errors('rankflow_seo_redirects'), 30);
			wp_safe_redirect(admin_url('admin.php?page=' . $this->plugin_name . '-redirects'));
			exit;
		}

		// Delete redirect.
		if (isset($_GET['action']) && 'delete' === $_GET['action'] && isset($_GET['id'])) {
			$rankflow_seo_delete_id = intval($_GET['id']);
			if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'delete_redirect_' . $rankflow_seo_delete_id)) {
				wp_die(esc_html__('Security check failed.', 'rankflow-seo'));
			}

			$this->redirect_manager->delete_redirect($rankflow_seo_delete_id);

			add_settings_error(
				'rankflow_seo_redirects',
				'redirect_deleted',
				__('Redirect deleted successfully.', 'rankflow-seo'),
				'success'
			);

			set_transient('rankflow_seo_redirect_notices', get_settings_errors('rankflow_seo_redirects'), 30);
			wp_safe_redirect(admin_url('admin.php?page=' . $this->plugin_name . '-redirects'));
			exit;
		}

		// Bulk delete.
		if (isset($_POST['action']) && 'bulk_delete' === $_POST['action'] && isset($_POST['redirect_ids'])) {
			check_admin_referer('bulk_delete_redirects', 'rankflow_seo_redirect_nonce');

			$rankflow_seo_ids = array_map('intval', wp_unslash($_POST['redirect_ids']));
			$rankflow_seo_count = 0;

			foreach ($rankflow_seo_ids as $rankflow_seo_id) {
				if ($this->redirect_manager->delete_redirect($rankflow_seo_id)) {
					$rankflow_seo_count++;
				}
			}

			add_settings_error(
				'rankflow_seo_redirects',
				'redirects_deleted',
				sprintf(
					/* translators: %d: number of deleted redirects */
					__('%d redirect(s) deleted successfully.', 'rankflow-seo'),
					$rankflow_seo_count
				),
				'success'
			);

			set_transient('rankflow_seo_redirect_notices', get_settings_errors('rankflow_seo_redirects'), 30);
			wp_safe_redirect(admin_url('admin.php?page=' . $this->plugin_name . '-redirects'));
			exit;
		}
	}

	/**
	 * Handle 404 actions
	 *
	 * @access public (required for WordPress hooks)
	 */
	public function handle_404_actions()
	{
		// Only run on our 404 monitor page.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification happens later for specific actions.
		if (
			!isset($_GET['page']) ||
			(sanitize_text_field(wp_unslash($_GET['page'])) !== $this->plugin_name . '-404-monitor')
		) {
			return;
		}

		// Check nonce.
		if (!isset($_GET['_wpnonce']) && !isset($_POST['rankflow_seo_404_nonce'])) {
			return;
		}

		// Export 404 logs - must be first.
		if (isset($_GET['action']) && 'export_404' === $_GET['action']) {
			if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'export_404_logs')) {
				wp_die(esc_html__('Security check failed.', 'rankflow-seo'));
			}

			$rankflow_seo_days = isset($_GET['days']) ? intval($_GET['days']) : 30;
			$rankflow_seo_csv = $this->monitor_404->export_to_csv($rankflow_seo_days);

			// Clear any output buffers.
			if (ob_get_length()) {
				ob_end_clean();
			}

			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename="rankflow-seo-404-logs-' . gmdate('Y-m-d') . '.csv"');
			header('Pragma: no-cache');
			header('Expires: 0');

			echo "\xEF\xBB\xBF"; // UTF-8 BOM.
			echo $rankflow_seo_csv; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSV output.
			exit;
		}

		// Delete 404 log.
		if (isset($_GET['action']) && 'delete' === $_GET['action'] && isset($_GET['id'])) {
			$rankflow_seo_delete_id = intval($_GET['id']);
			if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'delete_404_' . $rankflow_seo_delete_id)) {
				wp_die(esc_html__('Security check failed.', 'rankflow-seo'));
			}

			$this->monitor_404->delete_log($rankflow_seo_delete_id);

			add_settings_error(
				'rankflow_seo_404',
				'log_deleted',
				__('404 log deleted successfully.', 'rankflow-seo'),
				'success'
			);

			set_transient('rankflow_seo_404_notices', get_settings_errors('rankflow_seo_404'), 30);
			wp_safe_redirect(admin_url('admin.php?page=' . $this->plugin_name . '-404-monitor'));
			exit;
		}

		// Clear all logs.
		if (isset($_GET['action']) && 'clear_all' === $_GET['action']) {
			if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'clear_all_404')) {
				wp_die(esc_html__('Security check failed.', 'rankflow-seo'));
			}

			$this->monitor_404->clear_all_logs();

			add_settings_error(
				'rankflow_seo_404',
				'logs_cleared',
				__('All 404 logs cleared successfully.', 'rankflow-seo'),
				'success'
			);

			set_transient('rankflow_seo_404_notices', get_settings_errors('rankflow_seo_404'), 30);
			wp_safe_redirect(admin_url('admin.php?page=' . $this->plugin_name . '-404-monitor'));
			exit;
		}

		// Create redirect from 404.
		if (isset($_POST['action']) && 'create_redirect_from_404' === $_POST['action']) {
			check_admin_referer('create_redirect_404', 'rankflow_seo_404_nonce');

			$rankflow_seo_source_url = isset($_POST['source_url']) ? sanitize_text_field(wp_unslash($_POST['source_url'])) : '';
			$rankflow_seo_target_url = isset($_POST['target_url']) ? sanitize_text_field(wp_unslash($_POST['target_url'])) : '';

			$rankflow_seo_result = $this->redirect_manager->add_redirect(
				array(
					'source_url' => $rankflow_seo_source_url,
					'target_url' => $rankflow_seo_target_url,
					'redirect_type' => '301',
				)
			);

			if (is_wp_error($rankflow_seo_result)) {
				add_settings_error(
					'rankflow_seo_404',
					'redirect_error',
					$rankflow_seo_result->get_error_message(),
					'error'
				);
			} else {
				// Delete the 404 log.
				if (isset($_POST['log_id']) && !empty($_POST['log_id'])) {
					$rankflow_seo_log_id = intval(wp_unslash($_POST['log_id']));
					$this->monitor_404->delete_log($rankflow_seo_log_id);
				}

				add_settings_error(
					'rankflow_seo_404',
					'redirect_created',
					__('Redirect created successfully.', 'rankflow-seo'),
					'success'
				);
			}

			set_transient('rankflow_seo_404_notices', get_settings_errors('rankflow_seo_404'), 30);
			wp_safe_redirect(admin_url('admin.php?page=' . $this->plugin_name . '-404-monitor'));
			exit;
		}
	}

	/**
	 * Display admin notices
	 */
	public function display_notices()
	{
		$rankflow_seo_notices = get_transient('rankflow_seo_redirect_notices');

		if ($rankflow_seo_notices) {
			foreach ($rankflow_seo_notices as $rankflow_seo_notice) {
				printf(
					'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
					esc_attr($rankflow_seo_notice['type']),
					esc_html($rankflow_seo_notice['message'])
				);
			}
			delete_transient('rankflow_seo_redirect_notices');
		}

		$rankflow_seo_notices_404 = get_transient('rankflow_seo_404_notices');

		if ($rankflow_seo_notices_404) {
			foreach ($rankflow_seo_notices_404 as $rankflow_seo_notice) {
				printf(
					'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
					esc_attr($rankflow_seo_notice['type']),
					esc_html($rankflow_seo_notice['message'])
				);
			}
			delete_transient('rankflow_seo_404_notices');
		}
	}
}