# Quickscan Connector WordPress Plugin

[![Download Plugin](https://img.shields.io/badge/Download-WordPress%20Plugin-blue?style=for-the-badge&logo=wordpress)](./quickscan-connector.zip)

**[📦 Download quickscan-connector.zip](./quickscan-connector.zip)** | **[📖 View Documentation](#installation)** | **[🌟 Request Pro Account](https://quickscan.guardian360.nl/request-account)**

---

A professional WordPress plugin that integrates with the Guardian360 Quickscan API to provide comprehensive website security scanning functionality. Available in Basic (free, no account required) and Pro (free with account) versions.

## Description

The Quickscan Connector plugin enables WordPress users to perform comprehensive security scans using the Guardian360 Quickscan API. The Basic version works immediately with full scan results, while the Pro version offers additional administrative features through a vetted account system with personalized onboarding.

## Key Features

### 🛡️ Security Scanning
- Complete security analysis via Guardian360 Quickscan API
- Full vulnerability reports for all users (Basic and Pro)
- PDF email reports (Pro users only)
- Real-time scan results with comprehensive findings

### 💼 Two Versions Available

#### 🔧 Basic Version (Free - No Account Required)
- Complete security scanning with full results
- Gutenberg blocks, widgets, and shortcodes
- Ready to use immediately

#### ⭐ Pro Version (Free - Requires Account)
- Everything in Basic version
- **PDF email report functionality** - Receive comprehensive PDF reports via email
- **User activity tracking** - View list of users who requested PDF reports
- **Secure admin dashboard** - Access results in authenticated environment
- **White-label PDF reports** - Customize reports with your branding
- **Administrative controls** - Manage PDF delivery and scanning features
- **Priority support** - Direct access to security experts

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

#### Option 1: Direct Download (Recommended)
1. **[Download the plugin zip file](./quickscan-connector.zip)** from this repository
2. In WordPress admin, go to **Plugins → Add New → Upload Plugin**
3. Choose the downloaded `quickscan-connector.zip` file
4. Click **Install Now** and then **Activate**
5. Navigate to **Quickscan → Settings** to configure your account

#### Option 2: Manual Installation
1. Download and extract the plugin files
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
- **Attribution Display**: Choose between clickable logo or text link (attribution required)

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
[quickscan title="Security Check" placeholder="Enter URL..." button_text="Scan Now"]
```

Available parameters:
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

### Pro Version Benefits
- **User Activity Tracking**: View all users who requested PDF reports from your site
- **Secure Dashboard Access**: Access scan results through authenticated environment
- **White-Label Reports**: Customize PDF reports with your own branding and company information
- **Administrative Controls**: Control PDF report delivery and manage scanning functionality
- **Priority Support**: Direct support channel with security experts

## API Integration

The plugin connects to the Guardian360 Quickscan API:
- **Basic Version**: Uses public API (`https://quickscan.guardian360.nl/api/v2`) - no authentication required
- **Pro Version**: Uses authenticated API (`https://quickscan.guardian360.nl/api/v1`) with user credentials
- **Data Storage**: All scan data stored on Quickscan servers
- **Privacy**: No email or scan data stored locally in WordPress

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
4. Contact support if connection issues persist

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
- **Website**: [guardian360.eu](https://guardian360.eu)
- **Quickscan Platform**: [guardian360.eu/quickscan](https://guardian360.eu/quickscan)
- **GitHub**: [github.com/guardian360/quickscan-wp-plugin](https://github.com/guardian360/quickscan-wp-plugin)

## Changelog

### Version 1.0.1
- Updated Basic vs Pro messaging with accurate feature descriptions
- Removed password requirement for clearing credentials
- Improved attribution display with enforced branding
- Added logo/text choice for attribution with clickable links
- Fixed frontend upgrade messages (removed inappropriate admin CTAs)
- Enhanced accessibility with proper alt and title attributes
- Updated all links to point to guardian360.eu/quickscan platform

### Version 1.0.0
- Initial release with Basic and Pro versions (both free)
- Complete security scanning available to all users
- Guardian360 Quickscan API integration (v1/v2 endpoints)
- Pro account request system via Zoho CRM for administrative features
- Gutenberg block, widget, and shortcode support
- Secure credential management for Pro users
- Admin dashboard interface
- PDF email report functionality for Pro users only
- User activity tracking for Pro accounts
- White-label report options for Pro accounts

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed by [Guardian360](https://guardian360.eu)

---

*Professional Security Scanning with Quality Assurance*