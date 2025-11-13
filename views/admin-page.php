<?php
/**
 * Admin page template
 *
 * @package Auto_Delete_Comments
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<h1><?php echo esc_html__( 'Auto Delete Comments', 'auto-delete-comments' ); ?></h1>

	<?php settings_errors(); ?>

	<div class="adc-dashboard">
		<div class="adc-main-content">

			<!-- Comment Statistics -->
			<div class="adc-card adc-stats">
				<h2><?php echo esc_html__( 'Current Comment Statistics', 'auto-delete-comments' ); ?></h2>
				<div class="adc-stats-grid">
					<div class="adc-stat-item">
						<span class="adc-stat-label"><?php echo esc_html__( 'Spam', 'auto-delete-comments' ); ?></span>
						<span class="adc-stat-value spam-count"><?php echo esc_html( number_format_i18n( $comment_counts->spam ) ); ?></span>
					</div>
					<div class="adc-stat-item">
						<span class="adc-stat-label"><?php echo esc_html__( 'Pending', 'auto-delete-comments' ); ?></span>
						<span class="adc-stat-value pending-count"><?php echo esc_html( number_format_i18n( $comment_counts->moderated ) ); ?></span>
					</div>
					<div class="adc-stat-item">
						<span class="adc-stat-label"><?php echo esc_html__( 'Approved', 'auto-delete-comments' ); ?></span>
						<span class="adc-stat-value approved-count"><?php echo esc_html( number_format_i18n( $comment_counts->approved ) ); ?></span>
					</div>
					<div class="adc-stat-item">
						<span class="adc-stat-label"><?php echo esc_html__( 'Trash', 'auto-delete-comments' ); ?></span>
						<span class="adc-stat-value trash-count"><?php echo esc_html( number_format_i18n( $comment_counts->trash ) ); ?></span>
					</div>
					<div class="adc-stat-item">
						<span class="adc-stat-label"><?php echo esc_html__( 'Total', 'auto-delete-comments' ); ?></span>
						<span class="adc-stat-value total-count"><?php echo esc_html( number_format_i18n( $comment_counts->total_comments ) ); ?></span>
					</div>
				</div>
				<button type="button" id="adc-refresh-stats" class="button button-secondary">
					<?php echo esc_html__( 'Refresh Statistics', 'auto-delete-comments' ); ?>
				</button>
			</div>

			<!-- Settings Form -->
			<div class="adc-card">
				<h2><?php echo esc_html__( 'Plugin Settings', 'auto-delete-comments' ); ?></h2>

				<form method="post" action="options.php">
					<?php settings_fields( $option_name ); ?>

					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="adc-enabled"><?php echo esc_html__( 'Enable Auto Delete', 'auto-delete-comments' ); ?></label>
							</th>
							<td>
								<label class="adc-switch">
									<input type="checkbox" id="adc-enabled" name="<?php echo esc_attr( $option_name ); ?>[enabled]" value="1" <?php checked( $options['enabled'], true ); ?>>
									<span class="adc-slider"></span>
								</label>
								<p class="description">
									<?php echo esc_html__( 'Enable or disable automatic comment deletion', 'auto-delete-comments' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="adc-batch-size"><?php echo esc_html__( 'Batch Size', 'auto-delete-comments' ); ?></label>
							</th>
							<td>
								<input type="number" id="adc-batch-size" name="<?php echo esc_attr( $option_name ); ?>[batch_size]" value="<?php echo esc_attr( $options['batch_size'] ); ?>" min="1" max="50" class="small-text">
								<p class="description">
									<?php echo esc_html__( 'Number of comments to delete per batch (1-50). Limited to 50 to prevent server errors.', 'auto-delete-comments' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="adc-interval"><?php echo esc_html__( 'Interval (minutes)', 'auto-delete-comments' ); ?></label>
							</th>
							<td>
								<input type="number" id="adc-interval" name="<?php echo esc_attr( $option_name ); ?>[interval]" value="<?php echo esc_attr( $options['interval'] ); ?>" min="1" max="60" class="small-text">
								<p class="description">
									<?php echo esc_html__( 'How often to run the deletion batch (1-60 minutes)', 'auto-delete-comments' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<?php echo esc_html__( 'Comment Types to Delete', 'auto-delete-comments' ); ?>
							</th>
							<td>
								<fieldset>
									<label>
										<input type="checkbox" name="<?php echo esc_attr( $option_name ); ?>[delete_spam]" value="1" <?php checked( $options['delete_spam'], true ); ?>>
										<?php echo esc_html__( 'Spam Comments', 'auto-delete-comments' ); ?>
									</label><br>

									<label>
										<input type="checkbox" name="<?php echo esc_attr( $option_name ); ?>[delete_pending]" value="1" <?php checked( $options['delete_pending'], true ); ?>>
										<?php echo esc_html__( 'Pending Comments', 'auto-delete-comments' ); ?>
									</label><br>

									<label>
										<input type="checkbox" name="<?php echo esc_attr( $option_name ); ?>[delete_approved]" value="1" <?php checked( $options['delete_approved'], true ); ?>>
										<span class="adc-warning"><?php echo esc_html__( 'Approved Comments', 'auto-delete-comments' ); ?></span>
									</label><br>

									<label>
										<input type="checkbox" name="<?php echo esc_attr( $option_name ); ?>[delete_trash]" value="1" <?php checked( $options['delete_trash'], true ); ?>>
										<?php echo esc_html__( 'Trash Comments', 'auto-delete-comments' ); ?>
									</label>
								</fieldset>
								<p class="description">
									<?php echo esc_html__( 'Select which types of comments to automatically delete', 'auto-delete-comments' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="adc-older-than"><?php echo esc_html__( 'Only Delete Comments Older Than', 'auto-delete-comments' ); ?></label>
							</th>
							<td>
								<input type="number" id="adc-older-than" name="<?php echo esc_attr( $option_name ); ?>[older_than_days]" value="<?php echo esc_attr( $options['older_than_days'] ); ?>" min="0" class="small-text">
								<span><?php echo esc_html__( 'days (0 = all)', 'auto-delete-comments' ); ?></span>
								<p class="description">
									<?php echo esc_html__( 'Set to 0 to delete all matching comments, or specify a minimum age in days', 'auto-delete-comments' ); ?>
								</p>
							</td>
						</tr>
					</table>

					<?php submit_button(); ?>
				</form>
			</div>

			<!-- Manual Delete -->
			<div class="adc-card">
				<h2><?php echo esc_html__( 'Manual Actions', 'auto-delete-comments' ); ?></h2>
				<p><?php echo esc_html__( 'Delete a batch of comments immediately using the current settings.', 'auto-delete-comments' ); ?></p>
				<button type="button" id="adc-delete-now" class="button button-primary">
					<?php echo esc_html__( 'Delete Batch Now', 'auto-delete-comments' ); ?>
				</button>
				<span id="adc-delete-result" class="adc-result"></span>
			</div>

			<!-- Deletion Log -->
			<div class="adc-card">
				<h2><?php echo esc_html__( 'Recent Deletion History', 'auto-delete-comments' ); ?></h2>
				<?php if ( ! empty( $log ) && is_array( $log ) ) : ?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php echo esc_html__( 'Date & Time', 'auto-delete-comments' ); ?></th>
								<th><?php echo esc_html__( 'Comments Deleted', 'auto-delete-comments' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( array_reverse( array_slice( $log, -20 ) ) as $entry ) : ?>
								<tr>
									<td><?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $entry['date'] ) ) ); ?></td>
									<td><?php echo esc_html( number_format_i18n( $entry['count'] ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<p><?php echo esc_html__( 'No deletion history yet.', 'auto-delete-comments' ); ?></p>
				<?php endif; ?>
			</div>

		</div>

		<div class="adc-sidebar">
			<!-- Status Card -->
			<div class="adc-card">
				<h3><?php echo esc_html__( 'Status', 'auto-delete-comments' ); ?></h3>
				<div class="adc-status-info">
					<p>
						<strong><?php echo esc_html__( 'Current Status:', 'auto-delete-comments' ); ?></strong><br>
						<span class="adc-status-badge <?php echo esc_attr( $options['enabled'] ? 'active' : 'inactive' ); ?>">
							<?php echo $options['enabled'] ? esc_html__( 'Active', 'auto-delete-comments' ) : esc_html__( 'Inactive', 'auto-delete-comments' ); ?>
						</span>
					</p>

					<?php if ( $options['enabled'] ) : ?>
						<p>
							<strong><?php echo esc_html__( 'Next Run:', 'auto-delete-comments' ); ?></strong><br>
							<?php
							$next_run = wp_next_scheduled( $cron_hook );
							if ( $next_run ) {
								echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $next_run ) );
							} else {
								echo esc_html__( 'Not scheduled', 'auto-delete-comments' );
							}
							?>
						</p>

						<p>
							<strong><?php echo esc_html__( 'Configuration:', 'auto-delete-comments' ); ?></strong><br>
							<?php
							echo esc_html(
								sprintf(
									/* translators: 1: batch size, 2: interval in minutes */
									__( '%1$d comments every %2$d minutes', 'auto-delete-comments' ),
									$options['batch_size'],
									$options['interval']
								)
							);
							?>
						</p>
					<?php endif; ?>
				</div>
			</div>

			<!-- Info Card -->
			<div class="adc-card">
				<h3><?php echo esc_html__( 'How It Works', 'auto-delete-comments' ); ?></h3>
				<ul class="adc-info-list">
					<li><?php echo esc_html__( 'Enable the plugin and configure your preferences', 'auto-delete-comments' ); ?></li>
					<li><?php echo esc_html__( 'The plugin will automatically delete comments in batches', 'auto-delete-comments' ); ?></li>
					<li><?php echo esc_html__( 'Comments are permanently deleted (not recoverable)', 'auto-delete-comments' ); ?></li>
					<li><?php echo esc_html__( 'All activities are logged for your reference', 'auto-delete-comments' ); ?></li>
				</ul>
			</div>

			<!-- About Author Card -->
			<div class="adc-card">
				<h3><?php echo esc_html__( 'About the Author', 'auto-delete-comments' ); ?></h3>
				<div class="adc-author-content">
					<div class="adc-author-photo">
						<img src="<?php echo esc_url( plugins_url( 'assets/author-photo.png', dirname( __FILE__ ) ) ); ?>" alt="Emmanuel Eluwa" />
					</div>
					<div class="adc-author-info">
						<h4>Emmanuel Eluwa</h4>
						<p><?php echo esc_html__( 'Accomplished Web Developer and WordPress Technical Support specialist with nearly a decade of experience in the industry.', 'auto-delete-comments' ); ?></p>
						<div class="adc-author-links">
							<a href="https://github.com/nueleluwa" target="_blank" rel="noopener noreferrer">GitHub</a>
							<span class="adc-separator">•</span>
							<a href="https://profiles.wordpress.org/luwie93/" target="_blank" rel="noopener noreferrer">WordPress</a>
							<span class="adc-separator">•</span>
							<a href="https://instagram.com/nueleluwa" target="_blank" rel="noopener noreferrer">Instagram</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Warning Card -->
			<div class="adc-card adc-warning-card">
				<h3><?php echo esc_html__( '⚠️ Important', 'auto-delete-comments' ); ?></h3>
				<p><?php echo esc_html__( 'Deleted comments cannot be recovered. Please be careful when enabling deletion of approved comments.', 'auto-delete-comments' ); ?></p>
			</div>
		</div>
	</div>
</div>
