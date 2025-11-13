jQuery(document).ready(function($) {
	'use strict';

	/**
	 * Delete batch now
	 */
	$('#adc-delete-now').on('click', function() {
		const button = $(this);
		const result = $('#adc-delete-result');

		// Confirm action
		if (!confirm('Are you sure you want to delete a batch of comments now? This action cannot be undone.')) {
			return;
		}

		// Disable button and show loading
		button.prop('disabled', true).addClass('adc-loading');
		result.removeClass('success error').text('Processing...');

		// AJAX request
		$.ajax({
			url: adcData.ajax_url,
			type: 'POST',
			data: {
				action: 'adc_delete_now',
				nonce: adcData.nonce
			},
			success: function(response) {
				if (response.success) {
					result.addClass('success').text(response.data.message);

					// Refresh statistics after deletion
					setTimeout(function() {
						refreshStats();
					}, 500);
				} else {
					result.addClass('error').text('Error: ' + (response.data.message || response.data));
				}
			},
			error: function(xhr, status, error) {
				result.addClass('error').text('Error: ' + error);
			},
			complete: function() {
				button.prop('disabled', false).removeClass('adc-loading');

				// Clear result message after 5 seconds
				setTimeout(function() {
					result.removeClass('success error').text('');
				}, 5000);
			}
		});
	});

	/**
	 * Refresh statistics
	 */
	$('#adc-refresh-stats').on('click', function() {
		refreshStats();
	});

	function refreshStats() {
		const button = $('#adc-refresh-stats');

		button.prop('disabled', true).text('Refreshing...');

		$.ajax({
			url: adcData.ajax_url,
			type: 'POST',
			data: {
				action: 'adc_get_stats',
				nonce: adcData.nonce
			},
			success: function(response) {
				if (response.success) {
					const stats = response.data;

					// Update stat values with animation
					updateStatValue('.spam-count', stats.spam);
					updateStatValue('.pending-count', stats.pending);
					updateStatValue('.approved-count', stats.approved);
					updateStatValue('.trash-count', stats.trash);
					updateStatValue('.total-count', stats.total);
				}
			},
			error: function(xhr, status, error) {
				console.error('Error refreshing stats:', error);
			},
			complete: function() {
				button.prop('disabled', false).text('Refresh Statistics');
			}
		});
	}

	/**
	 * Update stat value with animation
	 */
	function updateStatValue(selector, newValue) {
		const element = $(selector);
		const currentValue = parseInt(element.text().replace(/,/g, '')) || 0;

		if (currentValue !== newValue) {
			element.css('opacity', '0.3');

			setTimeout(function() {
				element.text(numberWithCommas(newValue));
				element.css('opacity', '1');
			}, 200);
		}
	}

	/**
	 * Format number with commas
	 */
	function numberWithCommas(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	/**
	 * Warning for approved comments
	 */
	$('input[name="adc_settings[delete_approved]"]').on('change', function() {
		if ($(this).is(':checked')) {
			if (!confirm('WARNING: Enabling this option will delete approved comments. This action cannot be undone. Are you sure?')) {
				$(this).prop('checked', false);
			}
		}
	});

	/**
	 * Enable/disable toggle feedback
	 */
	$('#adc-enabled').on('change', function() {
		if ($(this).is(':checked')) {
			$('.adc-card').addClass('adc-enabled-highlight');
			setTimeout(function() {
				$('.adc-card').removeClass('adc-enabled-highlight');
			}, 1000);
		}
	});

	/**
	 * Auto-refresh stats every 30 seconds when enabled
	 */
	let autoRefreshInterval;

	function startAutoRefresh() {
		if ($('#adc-enabled').is(':checked')) {
			autoRefreshInterval = setInterval(function() {
				refreshStats();
			}, 30000); // 30 seconds
		}
	}

	function stopAutoRefresh() {
		if (autoRefreshInterval) {
			clearInterval(autoRefreshInterval);
		}
	}

	// Start auto-refresh if enabled
	startAutoRefresh();

	// Update auto-refresh on toggle
	$('#adc-enabled').on('change', function() {
		stopAutoRefresh();
		if ($(this).is(':checked')) {
			startAutoRefresh();
		}
	});

	/**
	 * Validate batch size and interval - Updated to max 50 for batch size
	 */
	$('#adc-batch-size').on('change', function() {
		let value = parseInt($(this).val());
		if (value < 1) {
			$(this).val(1);
		} else if (value > 50) {
			$(this).val(50);
			alert('Batch size limited to 50 to prevent server errors.');
		}
	});

	$('#adc-interval').on('change', function() {
		const value = parseInt($(this).val());
		if (value < 1) $(this).val(1);
		if (value > 60) $(this).val(60);
	});

	$('#adc-older-than').on('change', function() {
		const value = parseInt($(this).val());
		if (value < 0) $(this).val(0);
	});
});
