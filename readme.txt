=== Quickscan Connector ===
Contributors: guardian360
Donate link: https://guardian360.nl
Tags: security, scanner, vulnerability, quickscan, website security
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional website security scanning with vetted access through Guardian360's Quickscan platform.

== Description ==

The Quickscan Connector plugin integrates your WordPress site with Guardian360's professional Quickscan security platform, providing enterprise-grade vulnerability scanning and security analysis.

= Key Features =

* **Professional Security Scanning** - Comprehensive vulnerability detection via Quickscan API
* **Vetted Access System** - Quality-assured user onboarding through manual review process
* **Multiple Integration Methods** - Gutenberg blocks, widgets, and shortcodes
* **Secure Credential Storage** - Encrypted storage using WordPress security keys
* **No Data Collection** - No email or scan data stored in WordPress

= How It Works =

1. Request a Quickscan account through the embedded form (manual review within 24 hours)
2. Receive your credentials via email after approval
3. Configure the plugin with your Quickscan credentials
4. Start scanning websites for security vulnerabilities

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