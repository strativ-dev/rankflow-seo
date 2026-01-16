<?php
/**
 * 404 Monitor - Track 404 errors
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
 * Class RankFlow_SEO_404_Monitor
 */
class RankFlow_SEO_404_Monitor
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
        $this->table_name = $wpdb->prefix . 'rankflow_seo_404_logs';
    }

    /**
     * Create 404 logs table
     */
    public function create_table()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			url varchar(500) NOT NULL,
			referrer varchar(500) DEFAULT NULL,
			user_agent text DEFAULT NULL,
			ip_address varchar(45) DEFAULT NULL,
			hits int(11) DEFAULT 1,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY url (url(191)),
			KEY hits (hits),
			KEY created_at (created_at)
		) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Log 404 error
     *
     * @param string $url URL that triggered 404.
     */
    public function log_404($url)
    {
        global $wpdb;

        // Normalize URL.
        $url = $this->normalize_url($url);

        // Get additional data.
        $referrer = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw(wp_unslash($_SERVER['HTTP_REFERER'])) : '';
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
        $ip_address = $this->get_client_ip();

        $table = $this->table_name;

        // Check if URL already logged today.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Checking for existing 404 log.
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT id, hits FROM `' . $table . '` WHERE url = %s AND DATE(created_at) = CURDATE()', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safe, from $wpdb->prefix.
                $url
            )
        );

        if ($existing) {
            // Update hits.
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Updating 404 hit count.
            $wpdb->update(
                $this->table_name,
                array(
                    'hits' => $existing->hits + 1,
                    'updated_at' => current_time('mysql'),
                ),
                array('id' => $existing->id),
                array('%d', '%s'),
                array('%d')
            );
        } else {
            // Insert new log.
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Inserting new 404 log.
            $wpdb->insert(
                $this->table_name,
                array(
                    'url' => $url,
                    'referrer' => $referrer,
                    'user_agent' => $user_agent,
                    'ip_address' => $ip_address,
                    'hits' => 1,
                ),
                array('%s', '%s', '%s', '%s', '%d')
            );
        }

        // Cleanup old logs.
        $this->cleanup_old_logs();
    }

    /**
     * Get 404 logs
     *
     * @param array $args Query arguments.
     * @return array
     */
    public function get_404_logs($args = array())
    {
        global $wpdb;

        $defaults = array(
            'orderby' => 'hits',
            'order' => 'DESC',
            'per_page' => 20,
            'page' => 1,
            'search' => '',
            'days' => 30,
        );

        $args = wp_parse_args($args, $defaults);

        // Sanitize orderby using explicit mapping to prevent SQL injection.
        $orderby_map = array(
            'id' => 'id',
            'url' => 'url',
            'hits' => 'hits',
            'referrer' => 'referrer',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        );

        // Get safe orderby value from map, default to 'hits'.
        $orderby_key = isset($orderby_map[$args['orderby']]) ? $args['orderby'] : 'hits';
        $orderby_value = $orderby_map[$orderby_key];

        // Sanitize order.
        $order_value = 'ASC' === strtoupper($args['order']) ? 'ASC' : 'DESC';

        $where = array('1=1');

        // Date filter.
        if ($args['days'] > 0) {
            $where[] = $wpdb->prepare(
                'created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)',
                $args['days']
            );
        }

        // Search.
        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where[] = $wpdb->prepare(
                '(url LIKE %s OR referrer LIKE %s)',
                $search,
                $search
            );
        }

        $where_clause = implode(' AND ', $where);
        $table = $this->table_name;

        // Build ORDER BY clause with sanitized values.
        $order_clause = 'ORDER BY `' . $orderby_value . '` ' . $order_value;

        // Count total.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Counting 404 logs in custom table.
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
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Fetching 404 logs; orderby from explicit safe map.
            $logs = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT * FROM `' . $table . '` WHERE ' . $where_clause . ' ' . $order_clause . ' LIMIT %d OFFSET %d', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name safe from $wpdb->prefix, order_clause from safe map, where_clause built with prepare().
                    $args['per_page'],
                    $offset
                )
            );
        } else {
            // No pagination - fetch all.
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Fetching all 404 logs; orderby from explicit safe map.
            $logs = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT * FROM `' . $table . '` WHERE ' . $where_clause . ' ' . $order_clause . ' LIMIT %d', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name safe from $wpdb->prefix, order_clause from safe map, where_clause built with prepare().
                    PHP_INT_MAX
                )
            );
        }

        // Calculate pages.
        $pages = ($args['per_page'] > 0) ? ceil($total / $args['per_page']) : 1;

        return array(
            'logs' => $logs,
            'total' => (int) $total,
            'pages' => $pages,
        );
    }

    /**
     * Get 404 statistics
     *
     * @param int $days Number of days.
     * @return array
     */
    public function get_statistics($days = 30)
    {
        global $wpdb;

        $table = $this->table_name;

        // Total 404s.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Getting total 404 hits from custom table.
        $total_404s = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT SUM(hits) FROM `' . $table . '` WHERE created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safe, from $wpdb->prefix.
                $days
            )
        );

        // Unique URLs.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Getting unique 404 URLs from custom table.
        $unique_urls = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT COUNT(DISTINCT url) FROM `' . $table . '` WHERE created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safe, from $wpdb->prefix.
                $days
            )
        );

        // Top 404s.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Getting top 404 URLs from custom table.
        $top_404s = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT url, SUM(hits) as total_hits FROM `' . $table . '` WHERE created_at >= DATE_SUB(NOW(), INTERVAL %d DAY) GROUP BY url ORDER BY total_hits DESC LIMIT 10', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safe, from $wpdb->prefix.
                $days
            )
        );

        // Recent 404s.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Getting recent 404s from custom table.
        $recent_404s = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM `' . $table . '` WHERE created_at >= DATE_SUB(NOW(), INTERVAL %d DAY) ORDER BY created_at DESC LIMIT 10', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safe, from $wpdb->prefix.
                $days
            )
        );

        return array(
            'total_404s' => (int) $total_404s,
            'unique_urls' => (int) $unique_urls,
            'top_404s' => $top_404s,
            'recent_404s' => $recent_404s,
        );
    }

    /**
     * Delete 404 log
     *
     * @param int $id Log ID.
     * @return bool
     */
    public function delete_log($id)
    {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Deleting single 404 log.
        return false !== $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * Clear all 404 logs
     *
     * @return bool
     */
    public function clear_all_logs()
    {
        global $wpdb;

        $table = $this->table_name;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Truncating 404 logs table.
        return false !== $wpdb->query(
            $wpdb->prepare(
                'TRUNCATE TABLE `' . $table . '` /* %s */', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safe, from $wpdb->prefix.
                ''
            )
        );
    }

    /**
     * Cleanup old logs
     */
    private function cleanup_old_logs()
    {
        global $wpdb;

        // Only run cleanup occasionally (1% chance).
        if (wp_rand(1, 100) > 1) {
            return;
        }

        $retention_days = get_option('rankflow_seo_404_retention', 30);
        $table = $this->table_name;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Cleaning up old 404 logs.
        $wpdb->query(
            $wpdb->prepare(
                'DELETE FROM `' . $table . '` WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safe, from $wpdb->prefix.
                $retention_days
            )
        );
    }

    /**
     * Get client IP address
     *
     * @return string
     */
    private function get_client_ip()
    {
        $ip = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
        }

        return $ip;
    }

    /**
     * Normalize URL
     *
     * @param string $url URL.
     * @return string
     */
    private function normalize_url($url)
    {
        // Remove domain.
        $site_url = get_site_url();
        $url = str_replace($site_url, '', $url);

        // Ensure leading slash.
        if (0 !== strpos($url, '/')) {
            $url = '/' . $url;
        }

        return $url;
    }

    /**
     * Export 404 logs to CSV
     *
     * @param int $days Number of days.
     * @return string CSV content.
     */
    public function export_to_csv($days = 30)
    {
        $logs = $this->get_404_logs(
            array(
                'per_page' => -1,  // Get all records.
                'days' => $days,
                'orderby' => 'hits',
                'order' => 'DESC',
            )
        );

        $csv = "URL,Hits,Referrer,Created At\n";

        if (!empty($logs['logs'])) {
            foreach ($logs['logs'] as $log) {
                $csv .= sprintf(
                    '"%s",%d,"%s","%s"' . "\n",
                    str_replace('"', '""', $log->url),
                    $log->hits,
                    str_replace('"', '""', $log->referrer),
                    $log->created_at
                );
            }
        }

        return $csv;
    }
}