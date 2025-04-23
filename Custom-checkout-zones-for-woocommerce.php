<?php
/**
 * Plugin Name: Custom Checkout Zones for WooCommerce
 * Description: Adds dynamic delivery/pickup options with zip-based date/time selection.
 * Version: 1.2
 * Author: <a href="mailto:theprodeveloper789@gmail.com">The Pro Developer</a>
 */

if (!defined('ABSPATH')) exit;

add_action('woocommerce_before_checkout_billing_form', 'ccz_add_delivery_toggle');
add_action('woocommerce_checkout_fields', 'ccz_modify_checkout_fields');
add_action('woocommerce_after_checkout_billing_form', 'ccz_custom_checkout_fields');
add_action('woocommerce_checkout_process', 'ccz_validate_fields');
add_action('woocommerce_checkout_update_order_meta', 'ccz_save_fields');
add_action('wp_enqueue_scripts', 'ccz_enqueue_scripts');

function ccz_add_delivery_toggle() {
    echo '<h3>Delivery/Pickup Option</h3>';
    echo "<div class='ccz-delivery-options'>
            <label class='ccz-option selected' data-type='delivery'>
                <input type='radio' name='ccz_delivery_type' value='delivery' checked>
                <svg width='50' height='40' viewBox='0 0 50 40' xmlns='http://www.w3.org/2000/svg'><path d='M29 5a2 2 0 012 2v2h8l8 10.1V31h-4.1a5 5 0 01-9.8 0H18.9a5 5 0 01-9.8 0H5a2 2 0 01-2-2V7a2 2 0 012-2zM14 27a3 3 0 103 3 3 3 0 00-3-3zm24 0a3 3 0 103 3 3 3 0 00-3-3zM29 7H5v22h4.1a5 5 0 014.9-4 5.1 5.1 0 015 4h10zm16 14H31v8h2.1a5 5 0 019.8 0H45zm-7-10h-7v8h13.4z' fill='currentColor' fill-rule='evenodd'></path></svg>
                Deliver to address
            </label>

            <label class='ccz-option' data-type='pickup'>
                <input type='radio' name='ccz_delivery_type' value='pickup'>
                <svg width='50' height='40' viewBox='0 0 50 40' xmlns='http://www.w3.org/2000/svg'><path d='M41.5 18l-4-2.3a5 5 0 00-2.6-.7H32V9a1 1 0 00-1-1h-5V7a7 7 0 00-14 0v1H7a1 1 0 00-1 1v25a1 1 0 001 1h11.2a3 3 0 002.8 2h18a5 5 0 005-5v-9.7a5 5 0 00-2.5-4.3zM14 7a5 5 0 0110 0v1H14zm-6 3h4v2a1 1 0 002 0v-2h10v2a1 1 0 002 0v-2h4v9H19a3 3 0 00-2.8 4.1 3 3 0 00-.8 5.4A3 3 0 0015 30a3 3 0 003 3H8zm34 22a3 3 0 01-3 3H21a1 1 0 010-2h8a1 1 0 000-2H18a1 1 0 010-2h11a1 1 0 000-2H17a1 1 0 010-2h12a1 1 0 000-2H19a1 1 0 010-2h12a1 1 0 001-1v-3h3a3 3 0 011.4.4l4 2.3a3 3 0 011.6 2.6z' fill='currentColor'></path></svg>        I'll pick it up myself
            </label>
        </div>";
}

function ccz_modify_checkout_fields($fields) {
    // Remove default address and zip
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_postcode']);

    return $fields;
}

function ccz_custom_checkout_fields($checkout) {
    echo '<div id="ccz_custom_fields">';

    // Delivery fields
    echo '<div class="ccz-delivery-fields">';
    woocommerce_form_field('ccz_delivery_address', array(
        'type' => 'text',
        'label' => 'Delivery Address',
        'required' => true,
    ), $checkout->get_value('ccz_delivery_address'));

    woocommerce_form_field('ccz_delivery_zip', array(
        'type' => 'text',
        'label' => 'Delivery Zip Code',
        'required' => true,
        'class' => ['ccz-zip', 'ccz-delivery-zip']
    ), $checkout->get_value('ccz_delivery_zip'));
    woocommerce_form_field('ccz_delivery_date', [
        'type' => 'text',
        'label' => 'Delivery Date',
        'required' => true,
        'class' => ['ccz-datepicker', 'ccz-delivery-date']
    ]);
    woocommerce_form_field('ccz_delivery_time', [
        'type' => 'select',
        'label' => 'Delivery Time',
        'required' => true,
        'options' => [
            '' => 'Select a time',
            '8-12' => '8 AM - 12 PM',
            '1-5' => '1 PM - 5 PM'
        ]
    ]);
    echo '</div>';

    // Pickup fields
    echo '<div class="ccz-pickup-fields">';
    woocommerce_form_field('ccz_pickup_address', array(
        'type' => 'text',
        'label' => 'Pickup Address',
        'required' => true,
    ), $checkout->get_value('ccz_pickup_address'));

    woocommerce_form_field('ccz_pickup_zip', array(
        'type' => 'text',
        'label' => 'Pickup Zip Code',
        'required' => true,
        'class' => ['ccz-zip', 'ccz-pickup-zip']
    ), $checkout->get_value('ccz_pickup_zip'));
    woocommerce_form_field('ccz_pickup_date', [
        'type' => 'text',
        'label' => 'Pickup Date',
        'required' => true,
        'class' => ['ccz-datepicker', 'ccz-pickup-date']
    ]);
    woocommerce_form_field('ccz_pickup_time', [
        'type' => 'select',
        'label' => 'Pickup Time',
        'required' => true,
        'options' => [
            '' => 'Select a time',
            '8-12' => '8 AM - 12 PM',
            '1-5' => '1 PM - 5 PM'
        ]
    ]);

    echo '</div>'; // end custom
	echo '<div class="ccz-pickup-only-fields">';
		woocommerce_form_field('ccz_pickup_only_date', [
			'type' => 'text',
			'label' => 'Pickup Only - Date',
			'required' => false,
			'class' => ['ccz-datepicker', 'ccz-pickup-only-date']
		]);

		woocommerce_form_field('ccz_pickup_only_time', [
			'type' => 'select',
			'label' => 'Pickup Only - Time',
			'required' => false,
			'options' => [
				'' => 'Select a time',
				'8-12' => '8 AM - 12 PM',
				'1-5' => '1 PM - 5 PM'
			]
		]);
	
		woocommerce_form_field('ccz_dropoff_only_date', [
			'type' => 'text',
			'label' => 'Dropoff - Date',
			'required' => false,
			'class' => ['ccz-datepicker', 'ccz-dropoff-only-date']
		]);

		woocommerce_form_field('ccz_dropoff_only_time', [
			'type' => 'select',
			'label' => 'Dropoff - Time',
			'required' => false,
			'options' => [
				'' => 'Select a time',
				'8-12' => '8 AM - 12 PM',
				'1-5' => '1 PM - 5 PM'
			]
		]);
	echo '</div>';

	echo '</div>'; // end main div
}

function ccz_save_fields($order_id) {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'ccz_') === 0) {
            update_post_meta($order_id, '_' . $key, sanitize_text_field($value));
        }
    }
}

function ccz_enqueue_scripts() {
    if (!is_checkout()) {
        return;
    }

    // jQuery + jQuery UI
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-datepicker');

    // Datepicker CSS
    wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css');

    // Register script if not already registered
    if (!wp_script_is('ccz-script', 'registered')) {
        wp_register_script(
            'ccz-script',
            plugin_dir_url(__FILE__) . 'js/ccz-script.js',
            array('jquery', 'jquery-ui-datepicker'),
            1.6,
            true
        );
    }

    // Build zip-to-days map
    $zones = WC_Shipping_Zones::get_zones();
    $zip_map = [];

    foreach ($zones as $zone_data) {
        $zone_id = $zone_data['zone_id'] ?? $zone_data['id'];
        $days = get_option("ccz_zone_days_{$zone_id}", []);
        if (!is_array($days)) $days = [];

        foreach ($zone_data['zone_locations'] as $loc) {
            if ($loc->type === 'postcode') {
                $zip_map[$loc->code] = array_values($days);
            }
        }
    }

    // Pass data to JS
    wp_localize_script('ccz-script', 'cczData', array(
        'zipDays' => $zip_map,
        'timeSlots' => ['8-12' => '8 AM - 12 PM', '1-5' => '1 PM - 5 PM'],
        'ajaxUrl' => admin_url('admin-ajax.php') // ADD THIS if not already present
    ));

    // Enqueue it
    wp_enqueue_script('ccz-script');
}



add_action('wp_head', function () {
    if (is_checkout()) {
        echo '<style>
            .ui-datepicker {
                z-index: 10000 !important;
                background: #fff;
                border: 1px solid #ccc;
                padding: 10px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            }
            .ccz-delivery-options {
                display: flex;
                gap: 10px;
            }
            .ccz-option {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 15px;
                border: 2px solid #ccc;
                border-radius: 8px;
                cursor: pointer;
                text-align: center;
                transition: all 0.3s ease;
                background-color: #fff;
            }
            .ccz-option.selected {
                border-color: #0071dc;
                background-color: #f8fbff;
            }
            .ccz-option img {
                width: 24px;
                height: 24px;
                margin-right: 8px;
            }
            .ccz-option input {
                display: none;
            }
            .ccz-option:hover {
                border-color: #005bb5;
            }
        </style>';
    }
});

add_filter('woocommerce_checkout_fields', function ($fields) {
    $is_pickup = isset($_POST['ccz_delivery_type']) && $_POST['ccz_delivery_type'] === 'pickup';

    if ($is_pickup) {
        // Remove extra fields from billing 
        unset($fields['billing']['billing_country']);
        unset($fields['billing']['billing_email']);
        unset($fields['billing']['billing_address_1']);
        unset($fields['billing']['billing_address_2']);
        unset($fields['billing']['billing_city']);
        unset($fields['billing']['billing_state']);
        unset($fields['billing']['billing_postcode']);

        // Hide plugin's custom fields
        foreach ($fields['billing'] as $key => $value) {
            if (strpos($key, 'ccz_') === 0) {
                unset($fields['billing'][$key]);
            }
        }
    }

    return $fields;
});


add_action('woocommerce_shipping_zone_after_zone_table', 'ccz_add_delivery_days_section', 10, 1);
function ccz_add_delivery_days_section($zone) {
    $zone_id = $zone->get_id();
    $saved_days = get_option("ccz_zone_days_$zone_id", []);
    $weekdays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

    echo '<table class="form-table wc-shipping-zone-settings"><tr><th>Available Delivery Days</th><td>';
    echo '<div id="ccz-day-checkboxes" style="display:flex; flex-wrap: wrap; gap: 10px;">';
    foreach ($weekdays as $day) {
        $checked = in_array($day, $saved_days) ? 'checked' : '';
        echo "<label style='min-width: 100px;'><input type='checkbox' class='ccz-day' value='{$day}' {$checked}> {$day}</label>";
    }
    echo '</div>';
    // Label before button
    echo '<p style="margin-top:10px; font-weight: 600; color: #333;">Note: This button only saves the Delivery Days for this zone.</p>';
    echo "<button type='button' class='button button-secondary' id='ccz-save-days' data-zone-id='{$zone_id}' style='margin-top:10px;'>Save Days</button>";
    echo "<div id='ccz-save-status' style='margin-top:5px;font-size:13px;'></div>";
    echo '</td></tr></table>';
}


add_action('admin_footer', function () {
    $screen = get_current_screen();

    if ($screen && $screen->id === 'woocommerce_page_wc-settings' && isset($_GET['zone_id'])) {
        $zone_id = intval($_GET['zone_id']);
    $saved_days = get_option("ccz_zone_days_$zone_id", []);
    $weekdays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

    echo '<table class="form-table wc-shipping-zone-settings"><tr><th>Available Delivery Days</th><td>';
    echo '<div id="ccz-day-checkboxes" style="display:flex; flex-wrap: wrap; gap: 10px;">';
    foreach ($weekdays as $day) {
        $checked = in_array($day, $saved_days) ? 'checked' : '';
        echo "<label style='min-width: 100px;'><input type='checkbox' class='ccz-day' value='{$day}' {$checked}> {$day}</label>";
    }
    echo '</div>';
    // Label before button
    echo '<p style="margin-top:10px; font-weight: 600; color: #333;">Note: This button only saves the Delivery Days for this zone.</p>';
    echo "<button type='button' class='button button-secondary' id='ccz-save-days' data-zone-id='{$zone_id}' style='margin-top:10px;'>Save Days</button>";
    echo "<div id='ccz-save-status' style='margin-top:5px;font-size:13px;'></div>";
    echo '</td></tr></table>';
}
});

add_action('wp_ajax_ccz_save_zone_days_manual', function () {
    if (!current_user_can('manage_woocommerce')) {
        wp_send_json_error('Unauthorized');
    }

    $zone_id = isset($_POST['zone_id']) ? intval($_POST['zone_id']) : 0;
    $days = isset($_POST['days']) && is_array($_POST['days']) ? array_map('sanitize_text_field', $_POST['days']) : [];

    if ($zone_id > 0) {
        update_option("ccz_zone_days_{$zone_id}", $days);
        wp_send_json_success();
    } else {
        wp_send_json_error('Invalid zone ID');
    }
});


add_action('admin_footer', function () {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'woocommerce_page_wc-settings') {
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('#ccz-save-days').on('click', function() {
                let zoneId = $(this).data('zone-id');
                let selectedDays = [];

                $('#ccz-day-checkboxes input:checked').each(function() {
                    selectedDays.push($(this).val());
                });

                $('#ccz-save-status').text('Saving...');

                $.post(ajaxurl, {
                    action: 'ccz_save_zone_days_manual',
                    zone_id: zoneId,
                    days: selectedDays
                }, function(response) {
                    if (response.success) {
                        $('#ccz-save-status').css('color', 'green').text('Days saved successfully.');
                    } else {
                        $('#ccz-save-status').css('color', 'red').text('Error saving days.');
                    }
                });
            });
        });
        </script>
        <?php
    }
});

// View details in admin penal when order placed

add_action('woocommerce_admin_order_data_after_billing_address', 'ccz_display_order_custom_fields_in_admin', 10, 1);

function ccz_display_order_custom_fields_in_admin($order) {
    echo '<div class="ccz-admin-fields"><h4>Delivery/Pickup Info</h4><ul>';

    $fields = [
        'ccz_delivery_type'   => 'Order Type (Delivery or Pickup)',
        'ccz_delivery_address'=> 'Delivery Address',
        'ccz_delivery_zip'    => 'Delivery ZIP Code',
        'ccz_delivery_date'   => 'Delivery Date',
        'ccz_delivery_time'   => 'Delivery Time',
        'ccz_pickup_address'  => 'Pickup Address',
        'ccz_pickup_zip'      => 'Pickup ZIP Code',
        'ccz_pickup_date'     => 'Pickup Date',
        'ccz_pickup_time'     => 'Pickup Time',
		'ccz_pickup_only_date' => 'Pickup Only - Date',
		'ccz_pickup_only_time' => 'Pickup Only - Time',
		'ccz_dropoff_only_date' => 'Dropoff - Date',
		'ccz_dropoff_only_time' => 'Dropoff - Time',
    ];

    foreach ($fields as $meta_key => $label) {
        $value = get_post_meta($order->get_id(), '_' . $meta_key, true);
        if (!empty($value)) {
            echo '<li><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</li>';
        }
    }

    echo '</ul></div>';
}

// Zip code validation

function ccz_validate_fields() {
    $type = $_POST['ccz_delivery_type'] ?? 'delivery';

    if ($type === 'delivery') {
        $required = ['ccz_delivery_address', 'ccz_delivery_zip', 'ccz_delivery_date', 'ccz_delivery_time', 'ccz_pickup_address', 'ccz_pickup_zip', 'ccz_pickup_date', 'ccz_pickup_time'];

        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                wc_add_notice("Please fill in all required fields for delivery and pickup.", 'error');
                return;
            }
        }
    

        // Validate zip
        $zip = sanitize_text_field($_POST['ccz_' . $type . '_zip']);
        $zones = WC_Shipping_Zones::get_zones();
        $valid_zips = [];

        foreach ($zones as $zone_data) {
            foreach ($zone_data['zone_locations'] as $loc) {
                if ($loc->type === 'postcode') {
                    $valid_zips[] = $loc->code;
                }
            }
        }

        if (!in_array($zip, $valid_zips)) {
            wc_add_notice('Incorrect ZIP code. Please enter a valid ZIP.', 'error');
        }
    }
}


add_action('wp_ajax_ccz_validate_zip', 'ccz_validate_zip');
add_action('wp_ajax_nopriv_ccz_validate_zip', 'ccz_validate_zip');

function ccz_validate_zip() {
    if (!isset($_POST['zip_code'])) {
        wp_send_json_error(['message' => 'ZIP code missing']);
    }

    $zip_code = sanitize_text_field($_POST['zip_code']);
    $zones = WC_Shipping_Zones::get_zones();
    $valid_zips = [];

    foreach ($zones as $zone_data) {
        foreach ($zone_data['zone_locations'] as $loc) {
            if ($loc->type === 'postcode') {
                $valid_zips[] = $loc->code;
            }
        }
    }

    if (in_array($zip_code, $valid_zips)) {
        wp_send_json_success(['message' => 'Valid ZIP']);
    } else {
        wp_send_json_error(['message' => 'Incorrect ZIP']);
    }
}

/////////////////////////////////////////////////////////////
include ('includes/admin-menu-setting.php');