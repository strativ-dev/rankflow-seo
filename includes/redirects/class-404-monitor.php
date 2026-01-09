<?php
/**
 * 404 Monitor - Track 404 errors
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes/redirects
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class AI_SEO_Pro_404_Monitor
 */
class AI_SEO_Pro_404_Monitor
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
        $this->table_name = $wpdb->prefix . 'ai_seo_404_logs';
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

        // Check if URL already logged today.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Checking for existing 404 log.
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, hits FROM {$this->table_name} 
				WHERE url = %s 
				AND DATE(created_at) = CURDATE()",
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

        // Sanitize orderby to prevent SQL injection.
        $allowed_orderby = array('id', 'url', 'hits', 'referrer', 'created_at', 'updated_at');
        if (!in_array($args['orderby'], $allowed_orderby, true)) {
            $args['orderby'] = 'hits';
        }

        // Sanitize order.
        $args['order'] = 'ASC' === strtoupper($args['order']) ? 'ASC' : 'DESC';

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

        // Count total.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Dynamic WHERE clause built safely above.
        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE {$where_clause}");

        // Build query.
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Orderby and order are sanitized above.
        $query = "SELECT * FROM {$this->table_name} WHERE {$where_clause} ORDER BY {$args['orderby']} {$args['order']}";

        // Add LIMIT only if per_page is positive (not -1 for "all").
        if ($args['per_page'] > 0) {
            $offset = ($args['page'] - 1) * $args['per_page'];
            $query = $wpdb->prepare($query . ' LIMIT %d OFFSET %d', $args['per_page'], $offset);
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Query prepared conditionally above.
        $logs = $wpdb->get_results($query);

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

        $date_condition = $wpdb->prepare(
            'created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)',
            $days
        );

        // Total 404s.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Date condition prepared above.
        $total_404s = $wpdb->get_var(
            "SELECT SUM(hits) FROM {$this->table_name} WHERE {$date_condition}"
        );

        // Unique URLs.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Date condition prepared above.
        $unique_urls = $wpdb->get_var(
            "SELECT COUNT(DISTINCT url) FROM {$this->table_name} WHERE {$date_condition}"
        );

        // Top 404s.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Date condition prepared above.
        $top_404s = $wpdb->get_results(
            "SELECT url, SUM(hits) as total_hits 
			FROM {$this->table_name} 
			WHERE {$date_condition}
			GROUP BY url 
			ORDER BY total_hits DESC 
			LIMIT 10"
        );

        // Recent 404s.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Date condition prepared above.
        $recent_404s = $wpdb->get_results(
            "SELECT * FROM {$this->table_name} 
			WHERE {$date_condition}
			ORDER BY created_at DESC 
			LIMIT 10"
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

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Truncating 404 logs table.
        return false !== $wpdb->query("TRUNCATE TABLE {$this->table_name}");
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

        $retention_days = get_option('ai_seo_pro_404_retention', 30);

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Cleaning up old 404 logs.
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$this->table_name} 
				WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
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
        $logs = $this->get_404_logs(array(
            'per_page' => -1,  // Get all records.
            'days' => $days,
            'orderby' => 'hits',
            'order' => 'DESC',
        ));

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