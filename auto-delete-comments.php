<?php
/**
 * Plugin Name: Auto Delete Comments
 * Plugin URI: https://github.com/nueleluwa/Auto-Delete-Comments
 * Description: Automatically delete comments in batches with configurable scheduling. Modern UI with advanced analytics and REST API.
 * Version: 2.0.1
 * Author: Emmanuel Eluwa
 * Author URI: https://github.com/nueleluwa/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: auto-delete-comments
 * Requires at least: 5.8
 * Requires PHP: 7.4
 *
 * @package Auto_Delete_Comments
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class
 */
class Auto_Delete_Comments {

	/**
	 * Option name for settings
	 *
	 * @var string
	 */
	private $option_name = 'adc_settings';

	/**
	 * Cron hook name
	 *
	 * @var string
	 */
	private $cron_hook = 'adc_delete_comments_batch';

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	private $version = '2.0.1';

	/**
	 * Singleton instance
	 *
	 * @var Auto_Delete_Comments
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Auto_Delete_Comments
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		// Register custom cron schedules early.
		add_filter( 'cron_schedules', array( $this, 'register_cron_schedules' ) );

		// Admin menu.
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		// Register settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Register cron hook.
		add_action( $this->cron_hook, array( $this, 'delete_comments_batch' ) );

		// Cron reschedule hook.
		add_action( 'adc_reschedule_cron', array( $this, 'schedule_cron' ) );

		// Activation/Deactivation hooks.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// AJAX handlers.
		add_action( 'wp_ajax_adc_delete_now', array( $this, 'ajax_delete_now' ) );
		add_action( 'wp_ajax_adc_get_stats', array( $this, 'ajax_get_stats' ) );
		add_action( 'wp_ajax_adc_get_analytics', array( $this, 'ajax_get_analytics' ) );

		// Admin scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Settings link on plugins page.
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_action_links' ) );

		// REST API endpoints.
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		// Hook to update options to trigger cron reschedule.
		add_action( 'update_option_' . $this->option_name, array( $this, 'on_settings_update' ), 10, 2 );
	}

	/**
	 * Plugin activation
	 */
	public function activate() {
		// Check for minimum requirements.
		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( esc_html__( 'This plugin requires PHP 7.4 or higher.', 'auto-delete-comments' ) );
		}

		// Set default options.
		$default_options = array(
			'enabled'          => false,
			'batch_size'       => 15,
			'interval'         => 5,
			'delete_spam'      => true,
			'delete_pending'   => false,
			'delete_approved'  => false,
			'delete_trash'     => true,
			'older_than_days'  => 0,
		);

		if ( ! get_option( $this->option_name ) ) {
			add_option( $this->option_name, $default_options, '', 'no' );
		}

		// Initialize analytics data.
		if ( ! get_option( 'adc_analytics' ) ) {
			add_option( 'adc_analytics', array(), '', 'no' );
		}
	}

	/**
	 * Plugin deactivation
	 */
	public function deactivate() {
		$this->unschedule_cron();
	}

	/**
	 * Register REST API routes
	 */
	public function register_rest_routes() {
		register_rest_route(
			'auto-delete-comments/v1',
			'/stats',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_get_stats' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		register_rest_route(
			'auto-delete-comments/v1',
			'/analytics',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_get_analytics' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * REST API: Get stats
	 *
	 * @return WP_REST_Response
	 */
	public function rest_get_stats() {
		$comment_counts = wp_count_comments();

		return new WP_REST_Response(
			array(
				'spam'     => (int) $comment_counts->spam,
				'pending'  => (int) $comment_counts->moderated,
				'approved' => (int) $comment_counts->approved,
				'trash'    => (int) $comment_counts->trash,
				'total'    => (int) $comment_counts->total_comments,
			)
		);
	}

	/**
	 * REST API: Get analytics
	 *
	 * @return WP_REST_Response
	 */
	public function rest_get_analytics() {
		$analytics = $this->get_analytics_data();
		return new WP_REST_Response( $analytics );
	}

	/**
	 * Schedule cron job
	 */
	private function schedule_cron() {
		$options = get_option( $this->option_name );

		// Always clear existing schedule first.
		$this->unschedule_cron();

		// Validate options exist and are array.
		if ( ! is_array( $options ) || empty( $options['enabled'] ) ) {
			return;
		}

		// Get interval with validation.
		$interval = isset( $options['interval'] ) ? absint( $options['interval'] ) : 5;
		$interval = max( 1, min( 60, $interval ) );

		// Get the interval key and make sure it's registered.
		$interval_key = $this->get_cron_interval_key( $interval );

		// Schedule the event.
		if ( ! wp_next_scheduled( $this->cron_hook ) ) {
			wp_schedule_event( time(), $interval_key, $this->cron_hook );
		}
	}

	/**
	 * Unschedule cron job
	 */
	private function unschedule_cron() {
		$timestamp = wp_next_scheduled( $this->cron_hook );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, $this->cron_hook );
		}
		wp_clear_scheduled_hook( $this->cron_hook );
	}

	/**
	 * Register custom cron schedules
	 *
	 * @param array $schedules Existing schedules.
	 * @return array Modified schedules.
	 */
	public function register_cron_schedules( $schedules ) {
		// Register common intervals (1-60 minutes).
		for ( $i = 1; $i <= 60; $i++ ) {
			$key = 'adc_every_' . $i . '_minutes';
			if ( ! isset( $schedules[ $key ] ) ) {
				$schedules[ $key ] = array(
					'interval' => $i * MINUTE_IN_SECONDS,
					'display'  => sprintf(
						/* translators: %d: number of minutes */
						__( 'Every %d Minutes', 'auto-delete-comments' ),
						$i
					),
				);
			}
		}
		return $schedules;
	}

	/**
	 * Get or create custom cron interval
	 *
	 * @param int $minutes Interval in minutes.
	 * @return string Interval key.
	 */
	private function get_cron_interval_key( $minutes ) {
		$minutes = absint( $minutes );
		$minutes = max( 1, min( 60, $minutes ) );
		return 'adc_every_' . $minutes . '_minutes';
	}

	/**
	 * Delete comments in batch
	 *
	 * @return int Number of deleted comments.
	 */
	public function delete_comments_batch() {
		$options = get_option( $this->option_name );

		if ( ! is_array( $options ) || empty( $options['enabled'] ) ) {
			return 0;
		}

		$batch_size = isset( $options['batch_size'] ) ? absint( $options['batch_size'] ) : 15;
		$batch_size = max( 1, min( 50, $batch_size ) );

		$statuses = $this->get_comment_statuses( $options );
		
		// If no statuses selected, return early.
		if ( empty( $statuses ) ) {
			return 0;
		}

		$args = array(
			'number' => $batch_size,
			'status' => $statuses,
			'fields' => 'ids',
		);

		if ( ! empty( $options['older_than_days'] ) ) {
			$days               = absint( $options['older_than_days'] );
			$args['date_query'] = array(
				array(
					'before' => gmdate( 'Y-m-d H:i:s', strtotime( '-' . $days . ' days' ) ),
				),
			);
		}

		$comment_ids   = get_comments( $args );
		$deleted_count = 0;

		if ( empty( $comment_ids ) ) {
			return 0;
		}

		foreach ( $comment_ids as $comment_id ) {
			if ( wp_delete_comment( $comment_id, true ) ) {
				++$deleted_count;
			}
		}

		if ( $deleted_count > 0 ) {
			$this->log_deletion( $deleted_count );
			$this->update_analytics( $deleted_count );
		}

		return $deleted_count;
	}

	/**
	 * Get comment statuses to delete
	 *
	 * @param array $options Plugin options.
	 * @return array Array of statuses.
	 */
	private function get_comment_statuses( $options ) {
		$statuses = array();

		if ( ! empty( $options['delete_spam'] ) ) {
			$statuses[] = 'spam';
		}
		if ( ! empty( $options['delete_pending'] ) ) {
			$statuses[] = 'hold';
		}
		if ( ! empty( $options['delete_approved'] ) ) {
			$statuses[] = 'approve';
		}
		if ( ! empty( $options['delete_trash'] ) ) {
			$statuses[] = 'trash';
		}

		return ! empty( $statuses ) ? $statuses : array();
	}

	/**
	 * Log deletion activity
	 *
	 * @param int $count Number of deleted comments.
	 */
	private function log_deletion( $count ) {
		$log = get_option( 'adc_deletion_log', array() );

		if ( ! is_array( $log ) ) {
			$log = array();
		}

		$log[] = array(
			'date'  => current_time( 'mysql' ),
			'count' => absint( $count ),
		);

		if ( count( $log ) > 100 ) {
			$log = array_slice( $log, -100 );
		}

		update_option( 'adc_deletion_log', $log, 'no' );
	}

	/**
	 * Update analytics data
	 *
	 * @param int $count Number of deleted comments.
	 */
	private function update_analytics( $count ) {
		$analytics = get_option( 'adc_analytics', array() );

		if ( ! is_array( $analytics ) ) {
			$analytics = array();
		}

		$date = gmdate( 'Y-m-d' );

		if ( ! isset( $analytics[ $date ] ) ) {
			$analytics[ $date ] = 0;
		}

		$analytics[ $date ] += absint( $count );

		// Keep only last 30 days.
		if ( count( $analytics ) > 30 ) {
			$analytics = array_slice( $analytics, -30, null, true );
		}

		update_option( 'adc_analytics', $analytics, 'no' );
	}

	/**
	 * Get analytics data
	 *
	 * @return array Analytics data.
	 */
	private function get_analytics_data() {
		$analytics = get_option( 'adc_analytics', array() );
		$log       = get_option( 'adc_deletion_log', array() );

		$total_deleted = array_sum( array_column( $log, 'count' ) );
		$avg_per_run   = count( $log ) > 0 ? round( $total_deleted / count( $log ), 1 ) : 0;

		// Last 7 days data.
		$last_7_days = array();
		for ( $i = 6; $i >= 0; $i-- ) {
			$date                 = gmdate( 'Y-m-d', strtotime( "-{$i} days" ) );
			$last_7_days[ $date ] = isset( $analytics[ $date ] ) ? $analytics[ $date ] : 0;
		}

		return array(
			'total_deleted' => $total_deleted,
			'avg_per_run'   => $avg_per_run,
			'total_runs'    => count( $log ),
			'last_7_days'   => $last_7_days,
		);
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'Auto Delete Comments', 'auto-delete-comments' ),
			__( 'Auto Delete Comments', 'auto-delete-comments' ),
			'manage_options',
			'auto-delete-comments',
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Register settings
	 */
	public function register_settings() {
		register_setting(
			$this->option_name,
			$this->option_name,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $input User input.
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		$sanitized['enabled']         = ! empty( $input['enabled'] );
		$sanitized['batch_size']      = isset( $input['batch_size'] ) ? max( 1, min( 50, absint( $input['batch_size'] ) ) ) : 15;
		$sanitized['interval']        = isset( $input['interval'] ) ? max( 1, min( 60, absint( $input['interval'] ) ) ) : 5;
		$sanitized['delete_spam']     = ! empty( $input['delete_spam'] );
		$sanitized['delete_pending']  = ! empty( $input['delete_pending'] );
		$sanitized['delete_approved'] = ! empty( $input['delete_approved'] );
		$sanitized['delete_trash']    = ! empty( $input['delete_trash'] );
		$sanitized['older_than_days'] = isset( $input['older_than_days'] ) ? absint( $input['older_than_days'] ) : 0;

		// Validate that at least one comment type is selected if enabled.
		if ( $sanitized['enabled'] ) {
			$has_type = $sanitized['delete_spam'] || $sanitized['delete_pending'] || 
						$sanitized['delete_approved'] || $sanitized['delete_trash'];
			
			if ( ! $has_type ) {
				$sanitized['enabled'] = false;
				add_settings_error(
					$this->option_name,
					'no_types_selected',
					__( 'Please select at least one comment type to delete. Auto-delete has been disabled.', 'auto-delete-comments' ),
					'error'
				);
				return $sanitized;
			}
		}

		add_settings_error(
			$this->option_name,
			'settings_updated',
			__( 'Settings saved successfully.', 'auto-delete-comments' ),
			'success'
		);

		return $sanitized;
	}

	/**
	 * Handle settings update
	 *
	 * @param array $old_value Old option value.
	 * @param array $new_value New option value.
	 */
	public function on_settings_update( $old_value, $new_value ) {
		// Reschedule cron when settings change.
		$this->schedule_cron();
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( 'settings_page_auto-delete-comments' !== $hook ) {
			return;
		}

		// Chart.js for analytics.
		wp_enqueue_script(
			'chartjs',
			'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
			array(),
			'4.4.0',
			true
		);

		wp_enqueue_style(
			'adc-admin-css',
			plugins_url( 'assets/admin.css', __FILE__ ),
			array(),
			$this->version
		);

		wp_enqueue_script(
			'adc-admin-js',
			plugins_url( 'assets/admin.js', __FILE__ ),
			array( 'jquery', 'chartjs' ),
			$this->version,
			true
		);

		wp_localize_script(
			'adc-admin-js',
			'adcData',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'rest_url' => rest_url( 'auto-delete-comments/v1' ),
				'nonce'    => wp_create_nonce( 'adc_nonce' ),
			)
		);
	}

	/**
	 * AJAX: Delete now
	 */
	public function ajax_delete_now() {
		check_ajax_referer( 'adc_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'auto-delete-comments' ) ) );
		}

		$deleted = $this->delete_comments_batch();

		wp_send_json_success(
			array(
				'deleted' => $deleted,
				'message' => sprintf(
					/* translators: %d: number of deleted comments */
					__( 'Deleted %d comments', 'auto-delete-comments' ),
					$deleted
				),
			)
		);
	}

	/**
	 * AJAX: Get stats
	 */
	public function ajax_get_stats() {
		check_ajax_referer( 'adc_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'auto-delete-comments' ) ) );
		}

		$comment_counts = wp_count_comments();

		$stats = array(
			'spam'     => (int) $comment_counts->spam,
			'pending'  => (int) $comment_counts->moderated,
			'approved' => (int) $comment_counts->approved,
			'trash'    => (int) $comment_counts->trash,
			'total'    => (int) $comment_counts->total_comments,
		);

		wp_send_json_success( $stats );
	}

	/**
	 * AJAX: Get analytics
	 */
	public function ajax_get_analytics() {
		check_ajax_referer( 'adc_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'auto-delete-comments' ) ) );
		}

		$analytics = $this->get_analytics_data();
		wp_send_json_success( $analytics );
	}

	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'auto-delete-comments' ) );
		}

		$options = wp_parse_args(
			get_option( $this->option_name, array() ),
			array(
				'enabled'          => false,
				'batch_size'       => 15,
				'interval'         => 5,
				'delete_spam'      => true,
				'delete_pending'   => false,
				'delete_approved'  => false,
				'delete_trash'     => true,
				'older_than_days'  => 0,
			)
		);

		$log            = get_option( 'adc_deletion_log', array() );
		$comment_counts = wp_count_comments();
		$analytics      = $this->get_analytics_data();
		$option_name    = $this->option_name;
		$cron_hook      = $this->cron_hook;

		include plugin_dir_path( __FILE__ ) . 'views/admin-page.php';
	}

	/**
	 * Add action links to plugin list
	 *
	 * @param array $links Existing links.
	 * @return array Modified links.
	 */
	public function add_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'options-general.php?page=auto-delete-comments' ) ),
			__( 'Settings', 'auto-delete-comments' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}
}

/**
 * Initialize plugin
 */
function adc_init() {
	return Auto_Delete_Comments::get_instance();
}
add_action( 'plugins_loaded', 'adc_init' );
