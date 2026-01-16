<?php
/**
 * Redirect Manager - Core redirect functionality
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/includes/redirects
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class RankFlow_SEO_Redirect_Manager
 */
class RankFlow_SEO_Redirect_Manager
{

    /**
     * Table name
     *
     * @var string
     */
    private $table_name;

    /**
     * Constructor
     */
    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'rankflow_seo_redirects';
    }

    /**
     * Create redirects table
     */
    public function create_table()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			source_url varchar(500) NOT NULL,
			target_url varchar(500) NOT NULL,
			redirect_type varchar(10) NOT NULL DEFAULT '301',
			hits bigint(20) DEFAULT 0,
			is_regex tinyint(1) DEFAULT 0,
			is_active tinyint(1) DEFAULT 1,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			last_accessed datetime DEFAULT NULL,
			PRIMARY KEY  (id),
			KEY source_url (source_url(191)),
			KEY is_active (is_active),
			KEY redirect_type (redirect_type)
		) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Add redirect
     *
     * @param array $data Redirect data.
     * @return int|WP_Error Redirect ID or error.
     */
    public function add_redirect($data)
    {
        global $wpdb;

        // Validate data.
        $validation = $this->validate_redirect($data);
        if (is_wp_error($validation)) {
            return $validation;
        }

        // Normalize URLs.
        $source_normalized = $this->normalize_url($data['source_url']);
        $target_normalized = $this->normalize_url($data['target_url']);

        // Check for duplicates (only for non-regex).
        if (empty($data['is_regex']) && $this->redirect_exists($source_normalized)) {
            return new WP_Error('duplicate_redirect', __('A redirect for this source URL already exists.', 'rankflow-seo'));
        }

        // Prepare data.
        $insert_data = array(
            'source_url' => $source_normalized,
            'target_url' => $target_normalized,
            'redirect_type' => isset($data['redirect_type']) ? $data['redirect_type'] : '301',
            'is_regex' => isset($data['is_regex']) ? (int) $data['is_regex'] : 0,
            'is_active' => isset($data['is_active']) ? (int) $data['is_active'] : 1,
        );

        // Insert into database.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Inserting new redirect into custom table.
        $result = $wpdb->insert(
            $this->table_name,
            $insert_data,
            array('%s', '%s', '%s', '%d', '%d')
        );

        // Check for errors.
        if (false === $result) {
            $error = $wpdb->last_error;
            return new WP_Error('db_error', __('Failed to add redirect: ', 'rankflow-seo') . $error);
        }

        // Clear cache.
        $this->clear_cache();

        return $wpdb->insert_id;
    }

    /**
     * Update redirect
     *
     * @param int   $id   Redirect ID.
     * @param array $data Redirect data.
     * @return bool|WP_Error
     */
    public function update_redirect($id, $data)
    {
        global $wpdb;

        // Validate data.
        $validation = $this->validate_redirect($data);
        if (is_wp_error($validation)) {
            return $validation;
        }

        // Prepare data.
        $update_data = array(
            'source_url' => $this->normalize_url($data['source_url']),
            'target_url' => $this->normalize_url($data['target_url']),
            'redirect_type' => isset($data['redirect_type']) ? $data['redirect_type'] : '301',
            'is_regex' => isset($data['is_regex']) ? (int) $data['is_regex'] : 0,
            'is_active' => isset($data['is_active']) ? (int) $data['is_active'] : 1,
        );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Updating redirect in custom table.
        $result = $wpdb->update(
            $this->table_name,
            $update_data,
            array('id' => $id),
            array('%s', '%s', '%s', '%d', '%d'),
            array('%d')
        );

        if (false === $result) {
            return new WP_Error('db_error', __('Failed to update redirect.', 'rankflow-seo'));
        }

        // Clear cache.
        $this->clear_cache();

        return true;
    }

    /**
     * Delete redirect
     *
     * @param int $id Redirect ID.
     * @return bool
     */
    public function delete_redirect($id)
    {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Deleting redirect from custom table.
        $result = $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        );

        // Clear cache.
        $this->clear_cache();

        return false !== $result;
    }

    /**
     * Get redirect by ID
     *
     * @param int $id Redirect ID.
     * @return object|null
     */
    public function get_redirect($id)
    {
        global $wpdb;

        $table = $this->table_name;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Fetching single redirect from custom table.
        return $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM `' . $table . '` WHERE id = %d', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safe, from $wpdb->prefix.
                $id
            )
        );
    }

    /**
     * Get all redirects
     *
     * @param array $args Query arguments.
     * @return array
     */
    public function get_redirects($args = array())
    {
        global $wpdb;

        $defaults = array(
            'orderby' => 'id',
            'order' => 'DESC',
            'per_page' => 20,
            'page' => 1,
            'search' => '',
            'is_active' => null,
        );

        $args = wp_parse_args($args, $defaults);

        // Sanitize orderby using explicit mapping to prevent SQL injection.
        $orderby_map = array(
            'id' => 'id',
            'source_url' => 'source_url',
            'target_url' => 'target_url',
            'redirect_type' => 'redirect_type',
            'hits' => 'hits',
            'is_active' => 'is_active',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'last_accessed' => 'last_accessed',
        );

        // Get safe orderby value from map, default to 'id'.
        $orderby_key = isset($orderby_map[$args['orderby']]) ? $args['orderby'] : 'id';
        $orderby_value = $orderby_map[$orderby_key];

        // Sanitize order.
        $order_value = 'ASC' === strtoupper($args['order']) ? 'ASC' : 'DESC';

        $where = array('1=1');

        // Active filter.
        if (null !== $args['is_active']) {
            $where[] = $wpdb->prepare('is_active = %d', (int) $args['is_active']);
        }

        // Search.
        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where[] = $wpdb->prepare('(source_url LIKE %s OR target_url LIKE %s)', $search, $search);
        }

        $where_clause = implode(' AND ', $where);
        $table = $this->table_name;

        // Build ORDER BY clause with sanitized values.
        $order_clause = 'ORDER BY `' . $orderby_value . '` ' . $order_value;

        // Count total.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Counting redirects in custom table.
        $total = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT COUNT(*) FROM `' . $table . '` WHERE ' . $where_clause . ' AND %d=%d', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name safe from $wpdb->prefix, where_clause built with prepare().
                1,
                1
            )
        );

        // Build query with pagination.
        if ($args['per_page'] > 0) {
            $offset = ($args['page'] - 1) * $args['per_page'];
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Fetching redirects; orderby from explicit safe map.
            $redirects = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT * FROM `' . $table . '` WHERE ' . $where_clause . ' ' . $order_clause . ' LIMIT %d OFFSET %d', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name safe from $wpdb->prefix, order_clause from safe map, where_clause built with prepare().
                    $args['per_page'],
                    $offset
                )
            );
        } else {
            // No pagination - fetch all.
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Fetching all redirects; orderby from explicit safe map.
            $redirects = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT * FROM `' . $table . '` WHERE ' . $where_clause . ' ' . $order_clause . ' LIMIT %d', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name safe from $wpdb->prefix, order_clause from safe map, where_clause built with prepare().
                    PHP_INT_MAX
                )
            );
        }

        // Calculate pages.
        $pages = ($args['per_page'] > 0) ? ceil($total / $args['per_page']) : 1;

        return array(
            'redirects' => $redirects,
            'total' => (int) $total,
            'pages' => $pages,
        );
    }

    /**
     * Find redirect for URL
     *
     * @param string $url URL to check.
     * @return object|null
     */
    public function find_redirect($url)
    {
        global $wpdb;

        $normalized_url = $this->normalize_url($url);

        // Try cache first.
        $cache_key = 'redirect_' . md5($normalized_url);
        $cached = wp_cache_get($cache_key, 'rankflow_seo');

        if (false !== $cached) {
            return $cached;
        }

        // Prepare variations of the URL to check.
        $url_variations = array($normalized_url);

        // Add variation with trailing slash.
        if ('/' !== $normalized_url) {
            if ('/' === substr($normalized_url, -1)) {
                $url_variations[] = rtrim($normalized_url, '/');
            } else {
                $url_variations[] = $normalized_url . '/';
            }
        }

        $table = $this->table_name;

        // Get all active non-regex redirects.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Fetching active non-regex redirects from custom table.
        $all_redirects = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM `' . $table . '` WHERE is_active = %d AND is_regex = %d', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safe, from $wpdb->prefix.
                1,
                0
            )
        );

        // Check each redirect by normalizing its source_url.
        foreach ($all_redirects as $redirect) {
            $normalized_source = $this->normalize_url($redirect->source_url);

            // Try to match against all URL variations.
            foreach ($url_variations as $url_variant) {
                if (
                    $normalized_source === $url_variant ||
                    rtrim($normalized_source, '/') === rtrim($url_variant, '/')
                ) {
                    wp_cache_set($cache_key, $redirect, 'rankflow_seo', 3600);
                    return $redirect;
                }
            }
        }

        // Try regex matches.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Fetching active regex redirects from custom table.
        $regex_redirects = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM `' . $table . '` WHERE is_active = %d AND is_regex = %d ORDER BY id ASC', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safe, from $wpdb->prefix.
                1,
                1
            )
        );

        foreach ($regex_redirects as $regex_redirect) {
            $normalized_regex_source = $this->normalize_url($regex_redirect->source_url);

            foreach ($url_variations as $url_variant) {
                if (@preg_match($normalized_regex_source, $url_variant)) {
                    wp_cache_set($cache_key, $regex_redirect, 'rankflow_seo', 3600);
                    return $regex_redirect;
                }
            }
        }

        // No match found.
        wp_cache_set($cache_key, null, 'rankflow_seo', 3600);
        return null;
    }

    /**
     * Increment redirect hits
     *
     * @param int $id Redirect ID.
     */
    public function increment_hits($id)
    {
        global $wpdb;

        $table = $this->table_name;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Updating hit counter in custom table.
        $wpdb->query(
            $wpdb->prepare(
                'UPDATE `' . $table . '` SET hits = hits + 1, last_accessed = NOW() WHERE id = %d', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safe, from $wpdb->prefix.
                $id
            )
        );
    }

    /**
     * Validate redirect data
     *
     * @param array $data Redirect data.
     * @return bool|WP_Error
     */
    private function validate_redirect($data)
    {
        // Check source URL.
        if (empty($data['source_url'])) {
            return new WP_Error('missing_source', __('Source URL is required.', 'rankflow-seo'));
        }

        // Check target URL (not required for 410 and 451).
        $redirect_type = isset($data['redirect_type']) ? $data['redirect_type'] : '301';
        if (!in_array($redirect_type, array('410', '451'), true) && empty($data['target_url'])) {
            return new WP_Error('missing_target', __('Target URL is required.', 'rankflow-seo'));
        }

        // Validate redirect type.
        $valid_types = array('301', '302', '307', '410', '451');
        if (!in_array($redirect_type, $valid_types, true)) {
            return new WP_Error('invalid_type', __('Invalid redirect type. Must be 301, 302, 307, 410, or 451.', 'rankflow-seo'));
        }

        // Validate regex if enabled.
        if (!empty($data['is_regex'])) {
            $test = @preg_match($data['source_url'], '');
            if (false === $test) {
                return new WP_Error('invalid_regex', __('Invalid regular expression pattern.', 'rankflow-seo'));
            }
        }

        return true;
    }

    /**
     * Check if redirect exists
     *
     * @param string $source_url Source URL.
     * @return bool
     */
    private function redirect_exists($source_url)
    {
        global $wpdb;

        $normalized = $this->normalize_url($source_url);
        $table = $this->table_name;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Checking for existing redirect in custom table.
        $count = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT COUNT(*) FROM `' . $table . '` WHERE source_url = %s', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safe, from $wpdb->prefix.
                $normalized
            )
        );

        return $count > 0;
    }

    /**
     * Normalize URL
     *
     * @param string $url URL.
     * @return string
     */
    private function normalize_url($url)
    {
        // Handle empty URLs (for 410/451).
        if (empty($url)) {
            return '';
        }

        // Trim whitespace.
        $url = trim($url);

        // If it's a full URL with domain, strip the domain.
        if (0 === strpos($url, 'http://') || 0 === strpos($url, 'https://')) {
            $parsed = wp_parse_url($url);
            $url = isset($parsed['path']) ? $parsed['path'] : '/';

            // Add query string back if it exists.
            if (!empty($parsed['query'])) {
                $url .= '?' . $parsed['query'];
            }
        }

        // Strip site URL if included.
        $site_url = get_site_url();
        $url = str_replace($site_url, '', $url);

        // Strip any remaining http:// or https://.
        $url = preg_replace('#^https?://#', '', $url);

        // Remove any domain that might be left.
        $url = preg_replace('#^[^/]+#', '', $url);

        // Ensure leading slash for relative URLs.
        if (0 !== strpos($url, '/') && !empty($url)) {
            $url = '/' . $url;
        }

        return $url;
    }

    /**
     * Clear redirect cache
     */
    private function clear_cache()
    {
        wp_cache_delete('rankflow_seo_redirects', 'rankflow_seo');
    }

    /**
     * Import redirects from CSV
     *
     * @param string $file_path CSV file path.
     * @return array Import results.
     */
    public function import_from_csv($file_path)
    {
        $results = array(
            'success' => 0,
            'failed' => 0,
            'errors' => array(),
        );

        if (!file_exists($file_path)) {
            return $results;
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Reading uploaded CSV file.
        $file = fopen($file_path, 'r');

        if (false === $file) {
            return $results;
        }

        // Skip header row.
        fgetcsv($file);

        // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition -- Standard CSV reading pattern.
        while (($row = fgetcsv($file)) !== false) {
            $data = array(
                'source_url' => $row[0],
                'target_url' => isset($row[1]) ? $row[1] : '',
                'redirect_type' => isset($row[2]) ? $row[2] : '301',
            );

            $result = $this->add_redirect($data);

            if (is_wp_error($result)) {
                $results['failed']++;
                $results['errors'][] = $row[0] . ': ' . $result->get_error_message();
            } else {
                $results['success']++;
            }
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing CSV file.
        fclose($file);

        return $results;
    }

    /**
     * Export redirects to CSV
     *
     * @return string CSV content.
     */
    public function export_to_csv()
    {
        $redirects = $this->get_redirects(array('per_page' => -1));

        $csv = "Source URL,Target URL,Type,Hits,Active\n";

        if (!empty($redirects['redirects'])) {
            foreach ($redirects['redirects'] as $redirect) {
                $csv .= sprintf(
                    '"%s","%s","%s",%d,%s' . "\n",
                    str_replace('"', '""', $redirect->source_url),
                    str_replace('"', '""', $redirect->target_url),
                    $redirect->redirect_type,
                    $redirect->hits,
                    $redirect->is_active ? 'Yes' : 'No'
                );
            }
        }

        return $csv;
    }
}