# Custom Checkout Zones for WooCommerce

This plugin adds delivery and pickup options to WooCommerce checkout, enabling ZIP-code based scheduling and zone-day mapping.

Tags: woocommerce, checkout, delivery, pickup, zip code, date picker, shipping zones

Requires at least: 5.0

Tested up to: 6.5

Requires PHP: 7.2

Stable tag: 1.2

== Description ==

**Custom Checkout Zones for WooCommerce** lets you enhance the WooCommerce checkout experience by allowing customers to choose between **delivery** or **self-pickup** and select dates and times based on their ZIP code and zone.

This plugin integrates deeply with **WooCommerce Shipping Zones**, enabling merchants to control **available delivery/pickup days per zone** (e.g., South Denver = Mon–Wed, North = Thu–Sat, Central = Mon–Sat).

**Perfect for local businesses** offering flexible pickup or delivery based on ZIP-specific logistics.

## Features
- ZIP-based date pickers
- Delivery vs Pickup toggle
- Admin shipping zone weekday control
- Pickup/dropoff time support

= Highlights =
- ✅ Toggle between “Deliver to my address” or “I will pick up myself”
- 📅 Show only valid delivery/pickup dates based on ZIP
- 🔧 Set available weekdays per WooCommerce shipping zone
- 🧾 Save and view all selected fields in WooCommerce order admin
- ⏰ Supports time slot selection (e.g., 8 AM – 12 PM, 1 PM – 5 PM)
- ➕ NEW: Separate Pickup-Only and Dropoff fields for additional flows

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **WooCommerce > Settings > Shipping > Zones** to configure available delivery days for each zone.
4. Customize the checkout experience using the plugin’s admin settings if needed.

== Frequently Asked Questions ==

= How does the plugin detect the ZIP code zone? =
It uses the ZIP-to-zone mapping defined in **WooCommerce Shipping Zones** and checks which zone the input ZIP belongs to.

= Can I customize time slots? =
Yes, time slots (8–12 / 1–5) are hardcoded but can easily be adjusted via the plugin’s code or extended filters.

= Can customers choose both pickup and delivery? =
For delivery orders, a **secondary pickup location** is supported. For self-pickup, the customer only enters name, email, and pickup time/date.

= Is the plugin compatible with all WooCommerce themes? =
It is tested with major WooCommerce-compatible themes. For highly customized themes, slight CSS adjustments may be needed.

## Author
Developed by [The Pro Developer](mailto:theprodeveloper789@gmail.com)
