<?php
	add_action('admin_menu', 'ccz_register_main_menu');

	function ccz_register_main_menu() {
		add_menu_page(
			'Checkout Zones',
			'Checkout Zones',
			'manage_woocommerce',
			'ccz_main',
			'ccz_render_main_page',
			'dashicons-location', // Icon
			56 // Position in sidebar
		);

		add_submenu_page(
			'ccz_main',
			'Zone Settings',
			'Settings',
			'manage_woocommerce',
			'ccz_zone_settings',
			'ccz_render_settings_page'
		);
	}

	function ccz_render_main_page() {
		?>
		<div class="wrap">
			<h1>Checkout Zones for WooCommerce</h1>
			<p style="font-size: 15px; max-width: 700px;">
				This plugin allows you to control delivery and pickup scheduling based on customer ZIP codes and WooCommerce shipping zones.
			</p>
			<ul style="list-style-type: disc; padding-left: 20px;">
				<li>Customers can choose between "Deliver to my address" or "I will pick up myself" during checkout</li>
				<li>Each shipping zone can have custom delivery days (e.g. South = Mon–Wed)</li>
				<li>Date pickers and time pickers on the checkout page are dynamically controlled by the ZIP code entered</li>
				<li>Admin can manage delivery days for each shipping zone via the <strong>Settings</strong> tab</li>
			</ul>
			<p style="margin-top: 20px;">
				Go to <a href="<?php echo admin_url('admin.php?page=ccz_zone_settings'); ?>">Settings Page</a> to configure delivery days per zone.
			</p>
		</div>
		<?php
	}


	add_action('admin_enqueue_scripts', function($hook) {
		if ($hook !== 'toplevel_page_ccz_main' && $hook !== 'checkout-zones_page_ccz_zone_settings') return;

		wp_enqueue_script('ccz-admin-script', plugin_dir_url(__FILE__) . '../js/ccz-admin.js', ['jquery'], '1.2', true);
		wp_localize_script('ccz-admin-script', 'cczAdmin', [
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ccz_zone_settings_nonce')
		]);
	});

	function ccz_render_settings_page() {
		if (!current_user_can('manage_woocommerce')) return;

		$zones = WC_Shipping_Zones::get_zones();
		$weekdays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

		?>
		<div class="wrap">
			<h1>Checkout Zone Settings</h1>
			<br>
			<p style="font-size: 15px; max-width: 700px;">
				This plugin allows you to control delivery and pickup scheduling based on customer ZIP codes and WooCommerce shipping zones.
			</p>
			<ul style="list-style-type: disc; padding-left: 20px; margin-top:15px;">
				<li>Admin can manage delivery days for each shipping zone via below <strong>Settings</strong></li>
			</ul>
			<br>
			<label for="ccz_zone_selector"><strong>Select Shipping Zone:</strong></label>
			<?php
				$first_zone_id = !empty($zones) ? $zones[array_key_first($zones)]['zone_id'] : '';
			?>
			<select id="ccz_zone_selector">
				<?php foreach ($zones as $index => $zone): ?>
					<option value="<?= esc_attr($zone['zone_id']) ?>" <?= $zone['zone_id'] == $first_zone_id ? 'selected' : '' ?>>
						<?= esc_html($zone['zone_name']) ?>
					</option>
				<?php endforeach; ?>
			</select>

			<div id="ccz_day_checkboxes" style="margin-top: 20px; display: none;">
				<h3>Available Delivery Days</h3>
				<?php foreach ($weekdays as $day): ?>
					<label style="display:inline-block; margin-right:15px;">
						<input type="checkbox" class="ccz-day" value="<?= esc_attr($day) ?>"> <?= esc_html($day) ?>
					</label>
				<?php endforeach; ?>
				<br><br>
				<button id="ccz_save_days_btn" class="button button-primary">Save Days</button>
				<div id="ccz_save_msg" style="margin-top:10px;"></div>
			</div>
		</div>
		<?php
	}

	add_action('wp_ajax_ccz_get_zone_days', function () {
		check_ajax_referer('ccz_zone_settings_nonce', 'nonce');

		$zone_id = absint($_POST['zone_id']);
		$days = get_option("ccz_zone_days_$zone_id", []);
		wp_send_json_success($days);
	});

	add_action('wp_ajax_ccz_save_zone_days', function () {
		check_ajax_referer('ccz_zone_settings_nonce', 'nonce');

		$zone_id = absint($_POST['zone_id']);
		$days = isset($_POST['days']) ? array_map('sanitize_text_field', $_POST['days']) : [];

		update_option("ccz_zone_days_$zone_id", $days);
		wp_send_json_success('Days updated.');
	});
?>