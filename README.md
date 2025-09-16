# Quickscan Connector WordPress Plugin

A professional WordPress plugin that integrates with the Quickscan API to provide enterprise-grade website security scanning functionality with a vetted user access system.

## Description

The Quickscan Connector plugin enables WordPress administrators to perform comprehensive security scans using the Quickscan API. The plugin features a professional vetting system where new users request access through an integrated Zoho CRM form, ensuring quality control and personalized onboarding for each user.

## Key Features

### 🛡️ Security Scanning
- Professional website security analysis via Quickscan API
- Real-time scan initiation and monitoring
- Comprehensive vulnerability detection
- Direct integration with Quickscan's enterprise platform

### 🔐 Vetted Access System
- **Account Request Process**: New users submit requests through embedded Zoho CRM form
- **24-Hour Review**: Each request is personally reviewed for quality assurance
- **Professional Onboarding**: Approved users receive personalized setup assistance
- **No WordPress Credential Storage**: All authentication handled through Quickscan platform

### 🎛️ Admin Dashboard
- **Dashboard**: Overview and quick access to scanning features
- **Start Scan**: Interface to initiate security scans
- **Request Account**: Embedded form for new user registration
- **Settings**: Configure plugin preferences and Quickscan credentials

### 🧩 Frontend Integration
- **Gutenberg Block**: Modern "Security Scanner" block for the WordPress editor
- **Widget**: Configurable widget for sidebars and footers
- **Shortcodes**: Flexible shortcode system for custom implementations

## Installation

### Requirements
- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Active internet connection for API communication

### Installation Steps
1. Download the plugin files
2. Upload the `quickscan-connector` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to Quickscan → Settings to configure your account

### Development Installation
```bash
git clone git@github.com:guardian360/quickscan-wp-plugin.git quickscan-connector
cd quickscan-connector
# Activate the plugin in WordPress admin
```

## Configuration

### For New Users
1. Navigate to **Quickscan → Request Account** in WordPress admin
2. Complete the embedded account request form
3. Wait for approval (typically within 24 hours)
4. Receive credentials via email once approved
5. Return to **Quickscan → Settings** to login

### For Existing Quickscan Users
1. Go to **Quickscan → Settings** in WordPress admin
2. Select "I have a Quickscan account"
3. Enter your Quickscan credentials:
   - Email address
   - Password
4. Click **Test Credentials** to verify connection
5. Click **Save Credentials** to store securely

### Plugin Settings
- **Enable Logging**: Toggle API error and debug logging
- **Show Guardian360 Signature**: Display attribution on frontend scanners

## Usage

### Admin Interface

#### Starting a Scan
1. Navigate to **Quickscan → Start Scan**
2. Enter the URL to scan
3. Click **Start Scan**
4. View real-time scan progress

#### Dashboard
Access **Quickscan → Dashboard** for:
- Quick scan initiation
- System status overview
- Recent activity summary

### Frontend Implementation

#### Gutenberg Block
1. In the WordPress editor, add a new block
2. Search for "Security Scanner"
3. Configure the block settings in the sidebar
4. Publish or update your page

#### Widget
1. Go to **Appearance → Widgets**
2. Find "Security Scanner" widget
3. Drag to desired widget area
4. Configure settings and save

#### Shortcodes

Basic implementation:
```php
[quickscan]
```

With parameters:
```php
[quickscan type="quick" title="Security Check" placeholder="Enter URL..." button_text="Scan Now"]
```

Available parameters:
- `type`: Scan type (default: "quick")
- `title`: Scanner title text
- `placeholder`: Input field placeholder
- `button_text`: Submit button text
- `show_results`: Display results inline (true/false)

## Account Request Process

### Quality Assurance Through Vetting

The Quickscan platform maintains high standards through a professional vetting process:

1. **Submit Request**: Complete the embedded form with your professional details
2. **Review Process**: Our team reviews your request to ensure optimal service delivery
3. **Approval**: Receive credentials and personalized onboarding within 24 hours
4. **Support**: Access priority support from security experts

### Benefits of Vetted Access
- Personalized configuration for your specific needs
- Maintained platform integrity and performance
- Tailored security recommendations
- Direct support channel with experts
- Access to historical scan data and trends

## API Integration

The plugin connects to the Guardian360 Quickscan API:
- **Endpoint**: `https://quickscan.guardian360.nl/api/v1`
- **Authentication**: Quickscan credentials (no WordPress credentials used)
- **Data Storage**: All scan data stored on Quickscan servers
- **Privacy**: No email or scan data stored in WordPress

## Plugin Structure

```
quickscan-connector/
├── quickscan-connector.php    # Main plugin file
├── assets/
│   ├── css/
│   │   ├── admin.css         # Admin styles
│   │   └── frontend.css      # Frontend styles
│   └── js/
│       ├── admin.js          # Admin scripts
│       └── frontend.js       # Frontend scripts
├── blocks/                   # Gutenberg block files
│   └── security-scanner/
├── templates/                # Page templates
│   ├── dashboard.php
│   ├── start-scan.php
│   ├── account-request.php
│   ├── settings.php
│   └── shortcode.php
└── languages/               # Translation files
```

## Security & Privacy

### Security Measures
- Credentials encrypted using WordPress security keys
- All API calls use secure HTTPS connections
- WordPress nonces protect against CSRF attacks
- Admin functions require `manage_options` capability
- Input sanitization and validation on all forms

### Privacy Features
- No email harvesting or storage in WordPress
- All user data managed through Zoho CRM
- Scan results stored only on Quickscan servers
- No tracking or analytics within the plugin
- GDPR compliant data handling

## Troubleshooting

### Cannot Connect to API
1. Verify credentials in Quickscan → Settings
2. Check internet connectivity
3. Ensure firewall allows outbound HTTPS
4. Enable logging to see detailed errors

### Account Request Issues
1. Ensure all form fields are completed
2. Check email for approval notification
3. Allow up to 24 hours for processing
4. Contact support if delays exceed 24 hours

### Plugin Not Appearing
1. Verify plugin activation in WordPress
2. Check user permissions (requires admin role)
3. Clear browser and WordPress cache
4. Check for PHP errors in debug log

### Debug Mode
Enable WordPress debugging in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## Support

For support and assistance:
- **Email**: support@guardian360.nl
- **Website**: [guardian360.nl](https://guardian360.nl)
- **GitHub**: [github.com/guardian360/quickscan-wp-plugin](https://github.com/guardian360/quickscan-wp-plugin)

## Changelog

### Version 1.0.0
- Initial release
- Quickscan API integration
- Vetted account request system via Zoho CRM
- Gutenberg block support
- Widget functionality
- Shortcode system
- Secure credential management
- Admin dashboard interface
- Removed email storage in favor of CRM integration
- Removed local scan data storage (now API-only)

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed by [Guardian360](https://guardian360.nl)

---

*Professional Security Scanning with Quality Assurance*