=== Quickscan Connector ===
Contributors: guardian360
Donate link: https://guardian360.eu/quickscan
Tags: security, scanner, vulnerability, quickscan, website security
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free website security scanning with optional Pro features. Works immediately - no registration required!

== Description ==

The Quickscan Connector plugin provides professional website security scanning through Guardian360's Quickscan platform. **Start scanning immediately** with our free version - no account required!

= ✨ Free Version Features =

* **Instant Security Scanning** - Works immediately, no registration needed
* **Professional Scan Engine** - Powered by Guardian360's enterprise platform
* **Multiple Integration Options** - Gutenberg blocks, widgets, and shortcodes
* **Email Report Requests** - Request detailed reports via embedded form
* **WordPress.org Compliant** - Fully functional without external accounts

= ⭐ Pro Version Features =

* **Everything in Free** - Plus advanced professional features
* **Detailed Vulnerability Reports** - In-depth security analysis with actionable insights
* **Security Headers Analysis** - Comprehensive header security evaluation
* **CMS Detection** - Identify and analyze content management systems
* **Direct PDF Email Reports** - Instant delivery of comprehensive security reports
* **Historical Data** - Track security improvements over time
* **Priority Support** - Direct access to security experts

= How It Works =

**Free Version:**
1. Install and activate the plugin
2. Add a security scanner block/widget to any page
3. Visitors can scan websites immediately
4. Request detailed reports via the email form

**Pro Version:**
1. Request a Pro account through the plugin (24-hour approval)
2. Configure your Pro credentials in settings
3. Access detailed scan results and direct email reports
4. Enjoy priority support and advanced features

= Privacy & External Services =

This plugin connects to external services:

**Quickscan API (Required)**
* Service: https://quickscan.guardian360.nl/api/v1
* Purpose: Performs security scans and stores results
* Privacy: https://guardian360.nl/privacy

**Zoho CRM Forms (Account Registration)**
* Service: https://forms.guardian360.eu
* Purpose: Processes new account requests
* Data sent: Name, email, company, phone (with user consent)
* Privacy: https://www.zoho.com/privacy.html

= Important Notice =

* This plugin requires a Quickscan account to function
* Account approval typically takes 24 hours
* All scan data is stored on Quickscan servers, not in WordPress
* No user tracking or data collection without explicit consent

== Installation ==

1. Upload the `quickscan-connector` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to Quickscan → Settings to configure your account
4. For new users: Go to Quickscan → Request Account to apply for access
5. For existing users: Enter your Quickscan credentials in Settings

== Frequently Asked Questions ==

= Do I need a Quickscan account? =

Yes, this plugin requires a Quickscan account to perform security scans. New users can request an account through the plugin's admin interface.

= Why is there an approval process? =

The manual review process ensures optimal service quality, personalized configuration, and maintains the integrity of our security scanning infrastructure.

= How long does account approval take? =

Account requests are typically reviewed within 24 hours during business days.

= Where is my data stored? =

All scan data is stored securely on Quickscan servers. No scan results or email addresses are stored in your WordPress database.

= Can I use this plugin without sending data externally? =

No, this plugin requires connection to the Quickscan API to function. All security scans are performed through our external service.

= Is this plugin GDPR compliant? =

Yes, the plugin follows GDPR principles. User consent is required before any data is sent to external services. Please review our privacy policy at https://guardian360.nl/privacy

= Can I white-label or customize the scanner? =

The scanner appearance can be customized through CSS. The Guardian360 signature can be toggled in settings.

== Screenshots ==

1. Admin dashboard showing scan interface
2. Account request form with Zoho CRM integration
3. Settings page with credential configuration
4. Gutenberg block in the editor
5. Frontend scanner widget
6. Scan results display

== Changelog ==

= 1.0.0 =
* Initial release
* Quickscan API v1 integration
* Zoho CRM account request system
* Gutenberg block support
* Widget functionality
* Shortcode implementation
* Secure credential management
* Translation ready with .pot file

== Upgrade Notice ==

= 1.0.0 =
Initial release of the Quickscan Connector plugin.

== External Services ==

This plugin relies on the following external services:

= Quickscan API =
* **Purpose**: Security scanning and vulnerability detection
* **API Endpoint**: https://quickscan.guardian360.nl/api/v1
* **Data Sent**: URLs to scan, user credentials for authentication
* **Data Received**: Scan results and vulnerability reports
* **Privacy Policy**: https://guardian360.nl/privacy
* **Terms of Service**: https://guardian360.nl/terms

= Zoho CRM Forms =
* **Purpose**: Processing new account requests
* **Form URL**: https://forms.guardian360.eu/guardian360bv/form/RequestaQuickscanaccount
* **Data Sent**: User registration information (name, email, company, phone)
* **Privacy Policy**: https://www.zoho.com/privacy.html
* **Note**: Only used when explicitly requesting a new account

By using this plugin, you agree to the terms and privacy policies of these external services.

== Developer Information ==

* **GitHub**: https://github.com/guardian360/quickscan-wp-plugin
* **Support**: support@guardian360.nl
* **Website**: https://guardian360.nl

== Credits ==

Developed by Guardian360 - Professional Security Solutions