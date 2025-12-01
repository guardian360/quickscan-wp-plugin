# QuickScan Connector WordPress Plugin - Technical Specification

**Version:** 1.0.1
**Author:** Guardian360
**Last Updated:** 2025-10-02

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [System Architecture](#system-architecture)
3. [Plugin Pages & User Interface](#plugin-pages--user-interface)
4. [User Flows & Journeys](#user-flows--journeys)
5. [Technical Components](#technical-components)
6. [API Integration](#api-integration)
7. [Security Implementation](#security-implementation)
8. [Data Flow Diagrams](#data-flow-diagrams)
9. [Frontend Integration Methods](#frontend-integration-methods)
10. [Database & Storage](#database--storage)
11. [Authentication & Authorization](#authentication--authorization)

---

## Executive Summary

### What It Is
QuickScan Connector is a WordPress plugin that integrates with the Guardian360 Quickscan API to provide **real-time website security scanning** functionality. It is NOT a questionnaire or assessment tool—it performs active security analysis of URLs and IP addresses through API calls to Guardian360's scanning infrastructure.

### Core Functionality
- **Real-time security scanning** of URLs via Guardian360 Quickscan API
- **Dual-tier system**: Basic (free, no auth) and Pro (free, requires account)
- **Comprehensive vulnerability detection**: SSL/TLS, security headers, DNS, CSP, cookies, misconfigurations
- **PDF email reports** (Pro only) generated and delivered by Guardian360
- **Frontend embedding** via Gutenberg blocks, widgets, and shortcodes
- **Per-user credential management** with AES-256-GCM encryption
- **Zero local data storage** - all scan results stored on Guardian360 servers

### Technical Stack
- **Backend**: PHP 7.4+ (WordPress plugin architecture)
- **Frontend**: JavaScript (jQuery + Vanilla JS)
- **API**: RESTful HTTP requests to Guardian360 Quickscan API
- **Encryption**: AES-256-GCM with PBKDF2 key derivation
- **Authentication**: Bearer token (v1 API) / None (v2 API)

---

## System Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     WordPress Site                           │
│                                                               │
│  ┌──────────────┐          ┌──────────────┐                 │
│  │  Admin Panel │          │   Frontend   │                 │
│  │  (Dashboard, │          │  (Gutenberg, │                 │
│  │   Settings,  │          │   Widgets,   │                 │
│  │  Start Scan) │          │  Shortcodes) │                 │
│  └──────┬───────┘          └──────┬───────┘                 │
│         │                          │                         │
│         └──────────┬───────────────┘                         │
│                    │                                         │
│         ┌──────────▼──────────┐                             │
│         │ QuickScan Connector │                             │
│         │  (Main Plugin Class) │                             │
│         │  - AJAX Handlers     │                             │
│         │  - API Integration   │                             │
│         │  - Credential Mgmt   │                             │
│         └──────────┬──────────┘                             │
└────────────────────┼────────────────────────────────────────┘
                     │
                     │ HTTPS API Calls
                     │
          ┌──────────▼──────────┐
          │  Guardian360 Cloud  │
          │                     │
          │  ┌──────────────┐  │
          │  │ v2 API (Free)│  │
          │  │ - Scan URL   │  │
          │  │ - No Auth    │  │
          │  └──────────────┘  │
          │                     │
          │  ┌──────────────┐  │
          │  │ v1 API (Pro) │  │
          │  │ - Scan URL   │  │
          │  │ - Auth Token │  │
          │  │ - PDF Report │  │
          │  │ - User Track │  │
          │  └──────────────┘  │
          │                     │
          │  ┌──────────────┐  │
          │  │ Zoho CRM     │  │
          │  │ - Contacts   │  │
          │  │ - Leads      │  │
          │  └──────────────┘  │
          └─────────────────────┘
```

### Plugin File Structure

```
quickscan-connector/
├── quickscan-connector.php         # Main plugin file (1,697 lines)
│   └── QuickscanConnector class    # Singleton, handles all core logic
│
├── includes/
│   ├── class-github-updater.php    # Auto-updates from GitHub releases
│   └── class-results-formatter.php # Formats API results to HTML (722 lines)
│
├── templates/                      # PHP template files for admin pages
│   ├── dashboard.php              # Main dashboard (300 lines)
│   ├── start-scan.php             # Scan initiation page (217 lines)
│   ├── account-request.php        # Pro account request (embedded Zoho form)
│   ├── settings.php               # Credentials & plugin settings (309 lines)
│   └── shortcode.php              # Shortcode render template (21 lines)
│
├── assets/
│   ├── js/
│   │   ├── admin.js               # Admin panel interactions (130 lines)
│   │   └── frontend.js            # Scan interface & modal logic (566 lines)
│   ├── css/
│   │   ├── admin.css              # Admin styling
│   │   └── frontend.css           # Frontend scanner styling
│   └── images/
│       └── logo_guardian360_quickscan.png
│
├── blocks/
│   └── security-scanner/          # Gutenberg block
│       ├── block.json             # Block metadata
│       ├── index.js               # Block registration
│       ├── index.compiled.js      # Compiled block JS
│       ├── editor.css             # Block editor styles
│       └── style.css              # Block frontend styles
│
└── languages/                     # Translation files (.pot)
```

---

## Plugin Pages & User Interface

### 1. Dashboard Page (`admin.php?page=quickscan`)

**Template:** `templates/dashboard.php` (300 lines)

**Purpose:** Central hub for plugin access and navigation

**Components:**

1. **Header Section**
   - Guardian360 Quickscan logo (60px height)
   - Centered branding banner

2. **Connection Status Banner**
   - **If connected**: Green success notice with authenticated email
   - **If not connected**: Warning notice + Pro upgrade prompt

3. **Quick Action Cards** (3-column grid on desktop)
   - **Start New Scan**
     - Description: Run comprehensive security scan
     - CTA: "Start Scan" → Links to Start Scan page

   - **View Scan Results**
     - Description: Access scan history in Quickscan Portal
     - CTA: "Login to Quickscan Portal" → External link

   - **Frontend Integration**
     - Description: Embed scanners on website
     - Expandable details showing:
       - Gutenberg block usage
       - Widget location
       - Shortcode syntax: `[quickscan]`

4. **Pro Upgrade Section** (Only shown if no credentials)
   - **Left Panel (2/3 width)**: Feature comparison table
     - Basic version features (7 items, 4 with ✗)
     - Pro version features (7 items, all ✓)
     - CTA: "Request Pro Account" button

   - **Right Panel (1/3 width)**: Existing users card
     - Text: "Have a Quickscan Account?"
     - CTA: "Connect Account" → Links to Settings

5. **Footer Attribution**
   - Guardian360 logo (200px width, clickable)
   - OR text link: "Powered by Guardian360"
   - Links to: https://guardian360.eu/quickscan

**User Actions:**
- Navigate to scanning interface
- Request Pro account
- Connect existing account
- Learn integration methods

---

### 2. Start Scan Page (`admin.php?page=quickscan-start-scan`)

**Template:** `templates/start-scan.php` (217 lines)

**Purpose:** Initiate security scans from WordPress admin

**Components:**

1. **Header**
   - Logo and "Start New Scan" title

2. **Scan Form**
   - Single URL input field
     - Type: `url` (HTML5 validation)
     - Placeholder: "https://example.com"
     - Label: "Website URL"
   - Submit button: "Start Scan"

3. **Scan Progress Section** (Hidden by default)
   - Shows during scan execution
   - Status message: "Starting scan..."
   - Button disabled during scan

4. **Results Display** (Hidden until scan completes)
   - Header: "Scan Results"
   - Results formatted via AJAX call to `quickscan_format_results`
   - Full HTML rendering with collapsible sections
   - Fallback: Raw JSON in `<details>` tag if formatting fails

**JavaScript Logic (`start-scan.php:90-194`):**
```javascript
// Form submit handler
1. Validate URL input
2. Disable button, show progress
3. AJAX POST to admin-ajax.php
   - action: quickscan_start_scan
   - url: user_input
   - nonce: security token
4. On success:
   - AJAX POST to format results
   - Display formatted HTML
   - Initialize interactive elements
5. On error:
   - Show error message
   - Re-enable button
```

**Data Flow:**
1. User enters URL
2. JavaScript validates input
3. AJAX request to WordPress
4. Plugin calls Guardian360 API
5. Results formatted server-side
6. HTML injected into results container
7. Interactive features initialized (collapsible sections, info modals)

---

### 3. Account Request Page (`admin.php?page=quickscan-account-request`)

**Template:** `templates/account-request.php` (281 lines)

**Purpose:** Request vetted Pro account access

**Components:**

1. **Header**
   - Logo and "Request Quickscan Account Access" title

2. **Benefits Section** (4-column grid on desktop)
   - **User Activity Tracking**: View PDF report requesters
   - **Secure Dashboard Access**: Authenticated environment
   - **White-Label Reports**: Custom branding on PDFs
   - **Administrative Controls**: Manage scanning features

3. **Main Content Area (2/3 width)**
   - **Quality Assurance Notice** (blue info box)
     - Explains vetting process

   - **Embedded Zoho Form** (iframe + JS embedding)
     - Form ID: `OCmRQubppJrTZMWzGX_61hFmw8d8oVVwnktMCPgpUV4`
     - Dynamically adjusts height based on content
     - Fields handled by Zoho CRM integration:
       - Company name
       - Contact name
       - Email
       - Phone
       - Website URL
       - Message/reason for request

4. **Sidebar (1/3 width)**
   - **Timeline Visual** (4-step process)
     1. Submit Request
     2. Review Process (24 hours)
     3. Account Activation (email credentials)
     4. Access Pro Features (configure in Settings)

   - **Resources Card**
     - Links to Quickscan platform
     - Documentation (GitHub)
     - Privacy Policy
     - Terms of Service

   - **Help Card**
     - Email: support@guardian360.nl
     - "Contact Support" button

**Backend Integration:**
- Form submission handled by Zoho CRM
- No WordPress database storage
- Approval notifications sent via Zoho workflows
- Credentials created in Guardian360 user management system

---

### 4. Settings Page (`admin.php?page=quickscan-settings`)

**Template:** `templates/settings.php` (309 lines)

**Purpose:** Configure Pro credentials and plugin options

**Components:**

1. **Header**
   - Logo and "Settings" title

2. **Credential Management Card**

   **Connection Status Banner:**
   - **If connected**: Green success → "Connected: user@example.com"
   - **If not connected**: Yellow warning → "No credentials configured"

   **Account Type Radio Buttons:**
   - ○ I have a Quickscan account (default)
   - ○ Request new Quickscan account

   **Existing Account Form** (visible by default):
   - **Email Address** (text input)
     - Pre-filled if credentials exist
     - Description: "The email address you use to login to Quickscan Portal"

   - **Password** (password input)
     - Never pre-filled for security
     - Description: "Your Quickscan Portal password. This will be encrypted and stored securely."

   - **Action Buttons:**
     - "Test Credentials" (secondary) → AJAX test before saving
     - "Save Credentials" (primary) → Encrypt & store
     - "Clear Credentials" (secondary) → Delete stored data (only shown if credentials exist)

   **New Account Form** (hidden by default):
   - Info notice explaining vetting process
   - "Why We Vet Accounts" box with 4 reasons
   - CTA: "Request Account Access" → Links to Account Request page
   - Note: "Already submitted? Check email for credentials"

3. **Plugin Settings Card** (WordPress options form)

   **Attribution Display:**
   - Radio buttons:
     - ○ Show clickable Quickscan logo
     - ○ Show text link only: "Powered by Guardian360"
   - Description: "Attribution is required and helps support this free plugin"

   - Submit button: "Save Changes"

4. **Credential Result Messages** (AJAX response area)
   - Success messages (green)
   - Error messages (red)
   - Info messages (blue)

**JavaScript Logic (`settings.php:197-309`):**

```javascript
// Account type switching
- Show/hide forms based on radio selection

// Test Credentials button
1. Validate email/password not empty
2. Disable button, show "Testing..." message
3. AJAX POST to quickscan_test_credentials
4. Attempt authentication against v1 API
5. Display success/error message

// Save Credentials button
1. Validate inputs
2. Show "Saving..." message
3. AJAX POST to quickscan_save_credentials
4. Server encrypts password with AES-256-GCM
5. Store in user meta (per-user storage)
6. Reload page on success

// Clear Credentials button
1. Confirm with user (dialog)
2. AJAX POST with empty email/password
3. Delete user meta entries
4. Reload page
```

**Security Features:**
- Passwords never stored in plaintext
- Encryption uses WordPress AUTH_KEY + SECURE_AUTH_KEY
- Per-user credential storage (user meta)
- Nonce verification on all AJAX requests
- Password fields never pre-populated

---

## User Flows & Journeys

### Flow 1: Basic User (No Account) - Frontend Scan

**Actor:** Website visitor (unauthenticated)

**Steps:**
1. **Visit page** with Quickscan shortcode/block/widget
2. **See scanner interface:**
   - URL input field
   - "Start Security Scan" button
   - Guardian360 attribution (logo or text)
3. **Enter URL** (e.g., "https://example.com")
4. **Click "Start Security Scan"**
5. **JavaScript validates** URL format
6. **AJAX request** to WordPress backend
   - Action: `quickscan_start_scan`
   - Data: URL, nonce, `is_frontend=true`
7. **Plugin determines API version:**
   - No admin credentials configured
   - Uses v2 API (no authentication)
8. **API call** to `https://quickscan.guardian360.nl/api/v2/scan?url=example.com`
9. **Guardian360 performs real-time scan:**
   - SSL/TLS certificate analysis
   - Security header inspection
   - DNS record validation
   - Cookie security check
   - Misconfiguration detection
10. **Results returned** to plugin (JSON)
11. **Server-side formatting** via `quickscan_format_results`
12. **Results displayed** in Guardian360 style:
    - Security score (0-100)
    - Collapsible sections (SSL, Headers, DNS, etc.)
    - Color-coded vulnerabilities (red/yellow/green)
    - Risk levels (High/Medium/Low)
13. **User views results** on page
14. **No email button shown** (Basic version)

**Exit Points:**
- Invalid URL → Error message
- API timeout → Network error message
- Scan fails → API error displayed

---

### Flow 2: Pro User (Admin) - Request PDF Report

**Actor:** Site administrator with Pro account

**Prerequisites:**
- Admin has configured Pro credentials in Settings
- v1 API authentication working

**Steps:**
1. **Admin navigates** to Quickscan → Start Scan
2. **Enters URL** and clicks "Start Scan"
3. **Scan completes** (same as Basic flow, but uses v1 API with auth)
4. **Results displayed** with full detail
5. **Admin embeds scanner** on frontend page via:
   - Gutenberg block, OR
   - Widget, OR
   - Shortcode: `[quickscan]`
6. **Visitor performs scan** on frontend
7. **After scan completes**, visitor sees:
   - Full results
   - **"📧 Email Report" button** (Pro only)
8. **Visitor clicks "Email Report"**
9. **Modal opens** with form fields:
   - Company (required)
   - First Name (required)
   - Last Name (required)
   - Email (required)
   - Phone (optional)
   - Reminder checkbox (3-month follow-up)
   - **Math captcha** (spam prevention)
     - Example: "What is 7 + 3?"
     - Answer validated server-side
10. **Visitor completes form**
11. **JavaScript validates** all required fields
12. **AJAX POST** to `quickscan_send_email_report`
13. **Plugin checks:**
    - Captcha answer correct
    - URL not scanned today (rate limiting)
    - IP not exceeding rate limit (5 per hour)
14. **Request forwarded** to Guardian360 v1 API:
    - Endpoint: `/scan/report`
    - Data: URL, contact info, source=wordpress_plugin
15. **Guardian360 processes:**
    - Generates PDF report with scan results
    - Applies white-label branding (if configured)
    - Sends email with PDF attachment
    - Stores contact in Zoho CRM
16. **Success response** returned to plugin
17. **Modal content replaced** with success message:
    - ✅ icon
    - "Report Sent Successfully!"
    - Instructions to check email
    - "Close" button
18. **Visitor receives email** with PDF report

**Technical Details:**
- **Rate Limiting:**
  - Same URL: 1 scan per day (WordPress transient)
  - Same IP: 5 requests per hour (WordPress transient)
- **Captcha:**
  - Generated server-side with `quickscan_generate_captcha`
  - Stored in transient with 10-minute expiry
  - Deleted after single use
- **Data Storage:**
  - Contact info NOT stored in WordPress
  - Sent directly to Guardian360/Zoho CRM
  - Logged for compliance (if logging enabled)

---

### Flow 3: New User - Request Pro Account

**Actor:** WordPress administrator (no Quickscan account)

**Steps:**
1. **Admin installs plugin** from WordPress.org or ZIP
2. **Activates plugin**
3. **Sees admin menu item:** "Quickscan" with Guardian360 shield icon
4. **Clicks "Quickscan"** → Dashboard loads
5. **Sees Pro upgrade section** (if no credentials configured)
6. **Reads feature comparison:** Basic vs Pro
7. **Clicks "Request Pro Account"** button
8. **Account Request page loads** with embedded Zoho form
9. **Admin completes form fields:**
   - Company name
   - Full name
   - Email address
   - Phone number
   - Website URL
   - Reason for request (optional)
10. **Submits form** to Zoho CRM
11. **Confirmation message** displayed by Zoho
12. **Guardian360 team receives notification**
13. **Review process begins:**
    - Verify company/website legitimacy
    - Check for abuse indicators
    - Personalize onboarding materials
14. **Within 24 hours:**
    - Account created in Guardian360 system
    - Credentials generated
    - Welcome email sent with:
      - Login credentials
      - Setup instructions
      - Pro feature documentation
      - Priority support contact
15. **Admin receives email**
16. **Admin returns to WordPress:**
    - Quickscan → Settings
    - Selects "I have a Quickscan account"
    - Enters email and password
    - Clicks "Test Credentials"
17. **Plugin authenticates** against v1 API
18. **Success message:** "Credentials are valid!"
19. **Admin clicks "Save Credentials"**
20. **Plugin encrypts password** with AES-256-GCM
21. **Stores in user meta** (user ID specific)
22. **Page reloads** showing "Connected" status
23. **Pro features now active:**
    - Email reports on frontend
    - v1 API access
    - User activity tracking
    - White-label options

**Timeline:**
- Form submission: 2 minutes
- Guardian360 review: 4-24 hours
- Credential setup: 2 minutes
- **Total to Pro access:** 4-24 hours

---

### Flow 4: Embed Scanner on Frontend

**Actor:** Site administrator

**Method 1: Gutenberg Block**

1. **Admin edits page/post** in Block Editor
2. **Clicks "+" (Add Block)**
3. **Searches:** "Security Scanner"
4. **Selects:** "Security Scanner" block (Guardian360 icon)
5. **Block inserted** with default settings
6. **Sidebar settings panel** shows options:
   - **Show Title:** Toggle (default: ON)
   - **Title Text:** "Website Security Scanner"
   - **Placeholder:** "Enter website URL to scan..."
   - **Button Text:** "Start Security Scan"
   - **Show Results:** Toggle (default: ON)
7. **Admin customizes** as needed
8. **Publishes page**
9. **Frontend displays:** Scanner interface with configured options

**Method 2: Widget**

1. **Admin navigates** to Appearance → Widgets
2. **Finds:** "Security Scanner" widget
3. **Drags to widget area** (e.g., sidebar, footer)
4. **Widget settings panel:**
   - **Title:** "Security Scanner"
   - **Placeholder Text:** "Enter website URL..."
   - **Button Text:** "Scan"
   - **Show results on page:** Checkbox
5. **Clicks "Save"**
6. **Widget appears** on frontend in selected area

**Method 3: Shortcode**

1. **Admin edits page** in any editor
2. **Adds shortcode:**
   ```php
   [quickscan]
   ```

   **With parameters:**
   ```php
   [quickscan
     title="Check Your Security"
     placeholder="Enter URL..."
     button_text="Scan Now"
     show_results="true"]
   ```

3. **Publishes page**
4. **Shortcode renders** scanner interface

**All Methods Result In:**
- URL input field
- Scan button
- Results area (if show_results=true)
- Attribution footer (logo or text)
- Email button (if Pro credentials configured)

---

## Technical Components

### 1. Main Plugin Class (`QuickscanConnector`)

**File:** `quickscan-connector.php` (1,697 lines)

**Design Pattern:** Singleton

**Key Properties:**
```php
private static $instance = null;        // Singleton instance
private $api_base_url = '';            // v1 or v2 API endpoint
private $user_email = '';              // Current user's Quickscan email
private $user_password = '';           // Decrypted password (memory only)
private $api_token = '';               // Bearer token (v1 API, 1hr expiry)
```

**Initialization Flow:**
```php
1. get_instance() → Creates singleton
2. __construct() → Private constructor
3. init_hooks() → Register WP hooks (117 lines)
4. load_settings() → Determine v1 vs v2 API
5. init_updater() → GitHub auto-update
6. load_formatter() → Include results formatter
```

**Hook Registration (`init_hooks()`):**
- **Lifecycle:** `register_activation_hook`, `register_deactivation_hook`
- **Translations:** `add_action('init', 'load_textdomain')`
- **Admin:**
  - `add_action('admin_menu')` → 4 menu pages
  - `add_action('admin_init')` → Settings API
  - `add_action('admin_enqueue_scripts')` → CSS/JS assets
- **REST API:** `add_action('rest_api_init')` → 2 endpoints
- **Shortcodes:** `add_shortcode('quickscan')`
- **AJAX:** 10 handlers (logged-in + non-logged-in)
- **Blocks:** `add_action('init', 'register_blocks')`
- **Frontend:** `add_action('wp_enqueue_scripts')`, `add_action('wp_footer')`
- **Widgets:** `add_action('widgets_init')`

**Major Methods:**

| Method | Lines | Purpose |
|--------|-------|---------|
| `load_settings()` | 150-189 | Determine API version, load credentials |
| `authenticate()` | 862-899 | Login to v1 API, get Bearer token |
| `call_api()` | 920-997 | HTTP requests to Guardian360 |
| `ajax_start_scan()` | 455-504 | Handle scan requests |
| `ajax_send_email_report()` | 608-755 | Handle PDF report requests |
| `encrypt_password()` | 764-781 | AES-256-GCM encryption |
| `decrypt_password()` | 788-811 | AES-256-GCM decryption |
| `render_quickscan_shortcode()` | 439-450 | Shortcode rendering |
| `register_blocks()` | 1173-1223 | Gutenberg block registration |
| `format_results` (via formatter) | External | Convert JSON to HTML |

---

### 2. Results Formatter (`Quickscan_Results_Formatter`)

**File:** `includes/class-results-formatter.php` (722 lines)

**Purpose:** Convert Guardian360 API JSON to styled HTML

**Main Method:**
```php
public static function format_results($data, $is_frontend = false)
```

**Input Structure:**
```json
{
  "data": {
    "Info": {
      "URL": "https://example.com",
      "Score": 85,
      "IP": ["192.0.2.1"],
      "Raw-Headers": {...}
    },
    "SSL": {...},
    "Content-Security-Policy": {...},
    "DNS": {...},
    "Security-Headers": {...},
    "Misconfigurations": {...},
    "Cookies": {...}
  }
}
```

**Output:** HTML with Guardian360 styling (inline CSS + structured markup)

**Section Formatters:**

| Method | Lines | Renders |
|--------|-------|---------|
| `format_info_section()` | 74-106 | URL, Score, IP, Raw Headers |
| `format_ssl_section()` | 111-146 | SSL certificates, cipher suites |
| `format_csp_section()` | 151-183 | Content Security Policy directives |
| `format_dns_section()` | 188-206 | DNS records (SPF, DMARC) |
| `format_security_headers_section()` | 211-239 | HTTP security headers |
| `format_misconfigurations_section()` | 244-270 | Security misconfigurations |
| `format_cookies_section()` | 275-306 | Cookie security flags |

**Styling Features:**
- Collapsible sections (click header to collapse)
- Color-coded status:
  - ✓ Secure (green background)
  - ✗ Vulnerable (red background)
- Risk badges:
  - **High** (red bold)
  - **Medium** (orange bold)
  - **Low** (blue bold)
- Info modals for section explanations
- Responsive design (mobile-friendly tables)
- 720 lines of inline CSS (`get_css_styles()`)

---

### 3. Frontend JavaScript (`frontend.js`)

**File:** `assets/js/frontend.js` (566 lines)

**Responsibilities:**
1. Initialize scanner interfaces (blocks, widgets, shortcodes)
2. Handle scan button clicks
3. Display scan results
4. Manage email modal (Pro users)
5. Generate/validate captchas

**Key Functions:**

| Function | Lines | Purpose |
|----------|-------|---------|
| `initializeQuickscanBlocks()` | 8-13 | Find and render all blocks |
| `initializeQuickscanWidgets()` | 15-20 | Find and render all widgets |
| `initializeEmailModal()` | 22-113 | Create modal HTML (only once) |
| `renderQuickscanForm()` | 335-421 | Build scanner HTML, attach events |
| `startScan()` | 423-497 | AJAX scan request, handle response |
| `displayResults()` | 499-531 | Format and inject results HTML |
| `openEmailModal()` | 216-280 | Show modal, set URL, generate captcha |
| `handleEmailFormSubmit()` | 136-214 | Validate, send AJAX, show success |
| `generateCaptcha()` | 290-333 | Get math problem from server |

**Scanner HTML Structure:**
```html
<div class="quickscan-form-container">
  <h3>Title (optional)</h3>

  <div class="quickscan-input-group">
    <input type="url" class="quickscan-url-input" placeholder="...">
    <button type="button" class="quickscan-button">Start Security Scan</button>
  </div>

  <div class="quickscan-status" style="display:none">
    <p class="status-message">Scanning...</p>
  </div>

  <div class="quickscan-results" style="display:none">
    <h4>Security Scan Results</h4>
    <div class="results-content"><!-- Formatted HTML --></div>

    <div class="results-actions">
      <button class="quickscan-email-button">📧 Email Report</button>
      <!-- Only shown if Pro credentials configured -->
    </div>
  </div>

  <div class="quickscan-signature">
    <!-- Attribution (logo or text) -->
  </div>
</div>
```

**Email Modal HTML:**
- Created once on page load
- Fixed positioning, full-screen overlay
- Form fields:
  - Company (text, required)
  - First Name (text, required)
  - Last Name (text, required)
  - Email (email, required)
  - Phone (tel, optional)
  - URL (hidden, auto-filled)
  - Reminder (checkbox)
  - Captcha question (text)
  - Captcha answer (number, required)
- Legal disclaimers (GDPR, Terms, Privacy)
- Submit/Cancel buttons

---

### 4. Admin JavaScript (`admin.js`)

**File:** `assets/js/admin.js` (130 lines)

**Responsibilities:**
1. Connection testing
2. Scan status auto-refresh
3. Form validation
4. Results table enhancements
5. Settings page interactions

**Key Features:**
- **Test Connection:** AJAX to `quickscan_test_connection`
- **Auto-refresh:** Every 30 seconds for active scans
- **URL validation:** Ensure http/https prefix
- **Copy results:** Clipboard copy functionality
- **Expandable sections:** Toggle visibility

---

### 5. Gutenberg Block

**Files:**
- `blocks/security-scanner/block.json` - Metadata
- `blocks/security-scanner/index.js` - React component
- `blocks/security-scanner/index.compiled.js` - Bundled JS

**Block Metadata (`block.json`):**
```json
{
  "apiVersion": 2,
  "name": "quickscan/security-scanner",
  "title": "Security Scanner",
  "category": "common",
  "icon": "shield",
  "attributes": {
    "showResults": {"type": "boolean", "default": true},
    "placeholder": {"type": "string", "default": "Enter website URL to scan..."},
    "buttonText": {"type": "string", "default": "Start Security Scan"},
    "title": {"type": "string", "default": "Website Security Scanner"},
    "showTitle": {"type": "boolean", "default": true}
  }
}
```

**Render Callback:**
```php
render_callback: QuickscanConnector::render_security_scanner_block()
```

**Output:**
```html
<div class="wp-block-quickscan-security-scanner">
  <div class="quickscan-frontend-block"
       data-show-results="true"
       data-placeholder="..."
       data-button-text="..."
       data-title="..."
       data-show-title="true">
    <!-- JavaScript renders scanner here -->
  </div>
</div>
```

---

### 6. Widget (`Quickscan_Security_Widget`)

**Class:** Extends `WP_Widget`

**Constructor:**
```php
'quickscan_security_widget'
'Security Scanner'
'Add a security scanner form to scan websites for vulnerabilities'
```

**Widget Settings:**
- **Title:** Text input
- **Placeholder Text:** Text input
- **Button Text:** Text input
- **Show results on page:** Checkbox

**Output:**
```html
<div class="quickscan-widget"
     data-show-results="true"
     data-placeholder="..."
     data-button-text="..."
     data-show-title="false">
</div>
```

---

## API Integration

### Guardian360 Quickscan API

**Base URLs:**
- **v2 (Basic):** `https://quickscan.guardian360.nl/api/v2`
- **v1 (Pro):** `https://quickscan.guardian360.nl/api/v1`

### v2 API (Basic - No Authentication)

**Endpoint:** `POST /scan?url={encoded_url}`

**Request:**
```http
POST https://quickscan.guardian360.nl/api/v2/scan?url=https%3A%2F%2Fexample.com
Content-Type: application/x-www-form-urlencoded
```

**Response:**
```json
{
  "data": {
    "Info": {
      "URL": "https://example.com",
      "Score": 85,
      "IP": ["192.0.2.1"],
      "Raw-Headers": {
        "Server": "nginx",
        "Content-Type": "text/html"
      }
    },
    "SSL": {
      "Algemeen": {
        "Items": [
          {
            "Issue": "Certificate valid",
            "Vulnerable": false,
            "Risk": "None"
          }
        ]
      }
    },
    "Security-Headers": {
      "X-Frame-Options": {
        "Vulnerable": false,
        "Value": "SAMEORIGIN"
      },
      "X-Content-Type-Options": {
        "Vulnerable": true,
        "Issue": "Header not set",
        "Risk": "Medium"
      }
    },
    "DNS": {
      "SPF": {
        "Value": "v=spf1 include:_spf.google.com ~all",
        "Vulnerable": false
      }
    },
    "Misconfigurations": {
      "HTTPS-Redirect": {
        "Vulnerable": false
      }
    },
    "Cookies": {
      "session": {
        "Secure": {
          "Vulnerable": false
        },
        "HttpOnly": {
          "Vulnerable": true,
          "Issue": "Cookie accessible via JavaScript",
          "Risk": "Medium"
        }
      }
    }
  }
}
```

**Limitations:**
- No PDF reports
- No user tracking
- No white-label options

---

### v1 API (Pro - Authentication Required)

**Step 1: Authentication**

**Endpoint:** `POST /login`

**Request:**
```http
POST https://quickscan.guardian360.nl/api/v1/login
Content-Type: application/x-www-form-urlencoded

email=user@example.com
password=base64_encoded_password
```

**Response:**
```json
{
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
      "id": 123,
      "email": "user@example.com",
      "name": "John Doe"
    }
  }
}
```

**Token Storage:**
- Stored in WordPress transient: `quickscan_api_token_{user_id}`
- Expiry: 1 hour (3600 seconds)
- Auto-refresh on 401 responses

---

**Step 2: Scan with Authentication**

**Endpoint:** `POST /scan?url={encoded_url}`

**Request:**
```http
POST https://quickscan.guardian360.nl/api/v1/scan?url=https%3A%2F%2Fexample.com
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
Content-Type: application/x-www-form-urlencoded

language=en
```

**Response:** Same structure as v2 API, but may include additional Pro fields

---

**Step 3: Request PDF Report**

**Endpoint:** `POST /scan/report`

**Request:**
```http
POST https://quickscan.guardian360.nl/api/v1/scan/report
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
Content-Type: application/x-www-form-urlencoded

url=https://example.com
company=Acme Corp
firstname=John
surname=Doe
email=john@acme.com
phone=+31612345678
reminder=false
source=wordpress_plugin
plugin_version=1.0.1
site_url=https://mywordpress.com
```

**Response:**
```json
{
  "success": true,
  "message": "PDF report will be sent to your email shortly"
}
```

**Backend Actions:**
1. Guardian360 generates PDF with scan results
2. Applies white-label branding (if configured)
3. Sends email with PDF attachment
4. Stores contact in Zoho CRM
5. Triggers 3-month reminder workflow (if opted in)

---

### API Error Handling

**HTTP Status Codes:**
- **200:** Success
- **401:** Unauthorized (token expired, re-authenticate)
- **422:** Validation error (missing fields, invalid data)
- **429:** Rate limit exceeded
- **500:** Server error

**Error Response Format:**
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

**Plugin Error Handling:**
```php
// In call_api() method (line 920-997)
1. Check if response is WP_Error
2. Check HTTP status code
3. If 401: Delete token, re-authenticate, retry
4. Parse JSON response
5. Return data or WP_Error
```

---

## Security Implementation

### 1. Password Encryption

**Algorithm:** AES-256-GCM (Authenticated Encryption)

**Implementation:** `encrypt_password()` (lines 764-781)

```php
// Key Derivation
$salt = SECURE_AUTH_KEY;
$key = hash_pbkdf2('sha256', AUTH_KEY, $salt, 10000, 32, true);

// Encryption
$iv = openssl_random_pseudo_bytes(16);  // Random IV
$encrypted = openssl_encrypt($password, 'AES-256-GCM', $key, OPENSSL_RAW_DATA, $iv, $tag);

// Storage Format: base64(IV + tag + encrypted)
return base64_encode($iv . $tag . $encrypted);
```

**Security Features:**
- **PBKDF2:** 10,000 iterations prevents brute force
- **Random IV:** Each encryption unique, prevents pattern analysis
- **Authentication tag:** Ensures data integrity, detects tampering
- **WordPress keys:** Ties encryption to site-specific secrets
- **No password storage:** Only encrypted blob stored in database

**Decryption:** `decrypt_password()` (lines 788-811)
```php
$data = base64_decode($encrypted_data);
$iv = substr($data, 0, 16);
$tag = substr($data, 16, 16);
$encrypted = substr($data, 32);

$key = hash_pbkdf2('sha256', AUTH_KEY, $salt, 10000, 32, true);
$decrypted = openssl_decrypt($encrypted, 'AES-256-GCM', $key, OPENSSL_RAW_DATA, $iv, $tag);
```

---

### 2. Nonce Verification

**All AJAX handlers verify nonces:**
```php
if (!wp_verify_nonce($_POST['nonce'] ?? '', 'quickscan_nonce')) {
    wp_send_json_error('Security check failed');
    return;
}
```

**Nonce creation:**
```php
// Admin JS
wp_localize_script('quickscan-admin', 'quickscan_ajax', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('quickscan_nonce')
]);

// Frontend JS
wp_localize_script('quickscan-frontend', 'quickscan_ajax', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('quickscan_nonce')
]);
```

**Expiry:** 12-24 hours (WordPress default)

---

### 3. Input Sanitization

**All user inputs sanitized before use:**

```php
// URL sanitization
$url = sanitize_url($_POST['url'] ?? '');

// Email sanitization
$email = sanitize_email($_POST['email'] ?? '');

// Text sanitization
$company = sanitize_text_field($_POST['company'] ?? '');

// Validation
if (!is_email($email)) {
    wp_send_json_error('Invalid email address');
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    wp_send_json_error('Invalid URL');
}
```

---

### 4. Captcha System

**Purpose:** Prevent spam on PDF email requests

**Implementation:**

**Generation:** `ajax_generate_captcha()` (lines 1550-1574)
```php
$num1 = rand(1, 10);
$num2 = rand(1, 10);
$operation = rand(0, 1) ? '+' : '-';

$question = "$num1 $operation $num2";
$answer = ($operation === '+') ? $num1 + $num2 : $num1 - $num2;

$captcha_key = 'quickscan_captcha_' . wp_generate_uuid4();
set_transient($captcha_key, $answer, 600); // 10 min expiry

wp_send_json_success([
    'question' => "What is $question?",
    'key' => $captcha_key
]);
```

**Validation:** `validate_captcha()` (lines 1579-1602)
```php
$correct_answer = get_transient($captcha_key);
delete_transient($captcha_key); // Single use

return intval($user_answer) === intval($correct_answer);
```

**Features:**
- Random math problems (addition or subtraction)
- Stored server-side in transients
- 10-minute expiry
- Single-use (deleted after validation)
- UUID-based key prevents guessing

---

### 5. Rate Limiting

**URL Rate Limit:** 1 scan per URL per day
```php
$today_scan_key = 'quickscan_scanned_today_' . md5($url);
$scanned_today = get_transient($today_scan_key);

if ($scanned_today) {
    wp_send_json_error('This URL has already been scanned today.');
    return;
}

// After successful scan
set_transient($today_scan_key, true, DAY_IN_SECONDS);
```

**IP Rate Limit:** 5 email requests per hour per IP
```php
$client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
$rate_limit_key = 'quickscan_email_rate_' . md5($client_ip);
$rate_limit_count = get_transient($rate_limit_key);

if ($rate_limit_count && $rate_limit_count >= 5) {
    wp_send_json_error('Rate limit exceeded.');
    return;
}

// Increment counter
set_transient($rate_limit_key, ($rate_limit_count ? $rate_limit_count + 1 : 1), HOUR_IN_SECONDS);
```

---

### 6. Permission Checks

**Admin pages require capability:**
```php
add_menu_page(
    'Quickscan',
    'Quickscan',
    'manage_options', // Required capability
    'quickscan',
    [$this, 'render_dashboard_page']
);
```

**Settings save requires permission:**
```php
if (!current_user_can('manage_options') && !current_user_can('edit_posts')) {
    wp_send_json_error('Insufficient permissions');
    return;
}
```

---

### 7. Data Privacy

**No Local Storage:**
- Scan results NOT stored in WordPress database
- Contact info NOT stored locally
- All data sent to Guardian360 API
- Guardian360 stores in their secure infrastructure

**Logged Data (if enabled):**
```php
private function log_message($message, $data = []) {
    if (get_option('quickscan_enable_logging')) {
        $log_entry = '[Quickscan Connector] ' . $message;
        if (!empty($data)) {
            $log_entry .= ' | Data: ' . json_encode($data);
        }
        error_log($log_entry);
    }
}
```

**Compliance:**
- GDPR-compliant (no unauthorized data collection)
- Privacy policy links included in forms
- Terms of service acceptance required
- User consent for data processing

---

## Data Flow Diagrams

### Diagram 1: Basic Scan Flow (v2 API)

```
┌─────────────┐
│   Visitor   │
└──────┬──────┘
       │ 1. Enter URL, click "Start Scan"
       ▼
┌──────────────────┐
│  Frontend JS     │
│  (frontend.js)   │
└──────┬───────────┘
       │ 2. AJAX POST to WordPress
       │    - action: quickscan_start_scan
       │    - url: https://example.com
       │    - nonce: abc123
       ▼
┌────────────────────────┐
│  WordPress Backend     │
│  ajax_start_scan()     │
│  Line 455-504          │
└──────┬─────────────────┘
       │ 3. Verify nonce
       │ 4. Sanitize URL
       │ 5. Determine API version
       │    → No credentials → v2 API
       ▼
┌────────────────────────┐
│  call_api()            │
│  Line 920-997          │
└──────┬─────────────────┘
       │ 6. Build request:
       │    POST https://quickscan.guardian360.nl/api/v2/scan?url=example.com
       │    No Authorization header
       ▼
┌─────────────────────────┐
│  Guardian360 API (v2)   │
│  - Parse URL            │
│  - Fetch website        │
│  - Analyze SSL          │
│  - Check headers        │
│  - Validate DNS         │
│  - Test cookies         │
│  - Detect misconfigs    │
│  - Calculate score      │
└──────┬──────────────────┘
       │ 7. Return JSON results
       ▼
┌────────────────────────┐
│  WordPress Backend     │
│  ajax_start_scan()     │
└──────┬─────────────────┘
       │ 8. wp_send_json_success($result)
       ▼
┌──────────────────────┐
│  Frontend JS         │
│  displayResults()    │
│  Line 499-531        │
└──────┬───────────────┘
       │ 9. AJAX POST to format results
       │    - action: quickscan_format_results
       │    - results: JSON
       ▼
┌────────────────────────────┐
│  ajax_format_results()     │
│  Line 1152-1168            │
└──────┬─────────────────────┘
       │ 10. Call formatter
       ▼
┌────────────────────────────────┐
│  Quickscan_Results_Formatter   │
│  format_results()              │
│  Line 21-69                    │
└──────┬─────────────────────────┘
       │ 11. Generate HTML:
       │     - Info section
       │     - SSL section
       │     - Headers section
       │     - DNS section
       │     - Misconfigurations
       │     - Cookies
       │     - Inline CSS
       ▼
┌────────────────────────┐
│  WordPress Backend     │
│  wp_send_json_success  │
└──────┬─────────────────┘
       │ 12. Return formatted HTML
       ▼
┌──────────────────────┐
│  Frontend JS         │
│  displayResults()    │
└──────┬───────────────┘
       │ 13. Inject HTML into page
       │ 14. Initialize interactive features
       │     - Collapsible sections
       │     - Info modals
       ▼
┌─────────────┐
│   Visitor   │
│  Views      │
│  Results    │
└─────────────┘
```

---

### Diagram 2: Pro Email Report Flow (v1 API)

```
┌─────────────┐
│   Visitor   │
└──────┬──────┘
       │ 1. Complete scan (see Diagram 1)
       │ 2. Click "📧 Email Report" button
       ▼
┌──────────────────────┐
│  openEmailModal()    │
│  Line 216-280        │
└──────┬───────────────┘
       │ 3. Display modal with form
       │ 4. AJAX request for captcha
       │    - action: quickscan_generate_captcha
       ▼
┌────────────────────────┐
│  ajax_generate_captcha │
│  Line 1550-1574        │
└──────┬─────────────────┘
       │ 5. Generate math problem
       │    - "What is 7 + 3?"
       │ 6. Store answer in transient
       │    - Key: quickscan_captcha_uuid123
       │    - Value: 10
       │    - Expiry: 10 minutes
       │ 7. Return question + key
       ▼
┌──────────────────────┐
│  Frontend Modal      │
│  Display captcha     │
└──────┬───────────────┘
       │ 8. Visitor fills form:
       │    - Company
       │    - Name
       │    - Email
       │    - Phone (optional)
       │    - Captcha answer
       │ 9. Click "Send PDF Report"
       ▼
┌──────────────────────────┐
│  handleEmailFormSubmit() │
│  Line 136-214            │
└──────┬───────────────────┘
       │ 10. Validate all fields
       │ 11. AJAX POST to WordPress
       │     - action: quickscan_send_email_report
       │     - url, company, firstname, surname, email, phone
       │     - captcha_key, captcha_answer
       ▼
┌──────────────────────────────┐
│  ajax_send_email_report()    │
│  Line 608-755                │
└──────┬───────────────────────┘
       │ 12. Verify nonce
       │ 13. Validate captcha
       │ 14. Check rate limits:
       │     - URL not scanned today
       │     - IP < 5 requests/hour
       │ 15. Sanitize inputs
       │ 16. Log request for compliance
       ▼
┌────────────────────────┐
│  load_admin_credentials│
│  Line 194-211          │
└──────┬─────────────────┘
       │ 17. Find admin with Pro credentials
       │ 18. Decrypt password
       │ 19. Set $this->user_email, $this->user_password
       │ 20. API base URL → v1
       ▼
┌────────────────────────┐
│  ensure_authenticated()│
│  Line 904-915          │
└──────┬─────────────────┘
       │ 21. Check if token exists
       │ 22. If not, call authenticate()
       ▼
┌────────────────────────┐
│  authenticate()        │
│  Line 862-899          │
└──────┬─────────────────┘
       │ 23. POST to /api/v1/login
       │     - email: admin@site.com
       │     - password: base64_encoded
       │ 24. Receive token
       │ 25. Store in transient (1 hour)
       ▼
┌────────────────────────┐
│  call_api()            │
│  Line 920-997          │
└──────┬─────────────────┘
       │ 26. POST to /api/v1/scan/report
       │     Authorization: Bearer {token}
       │     Body:
       │       - url
       │       - company, firstname, surname, email, phone
       │       - reminder (true/false)
       │       - source: wordpress_plugin
       │       - plugin_version, site_url
       ▼
┌─────────────────────────────────┐
│  Guardian360 API (v1)           │
│  - Validate token               │
│  - Verify request data          │
│  - Generate PDF report          │
│  - Apply white-label branding   │
│  - Send email with attachment   │
│  - Store contact in Zoho CRM    │
│  - Set 3-month reminder         │
└──────┬──────────────────────────┘
       │ 27. Return success response
       ▼
┌──────────────────────────────┐
│  ajax_send_email_report()    │
└──────┬───────────────────────┘
       │ 28. Set rate limit transients
       │ 29. Log success
       │ 30. wp_send_json_success("Email sent!")
       ▼
┌──────────────────────────┐
│  handleEmailFormSubmit() │
└──────┬───────────────────┘
       │ 31. Replace modal content with success message
       │     - ✅ icon
       │     - "Report Sent Successfully!"
       │     - Instructions
       ▼
┌─────────────┐
│   Visitor   │
│  Receives   │
│  Email      │
│  with PDF   │
└─────────────┘
```

---

### Diagram 3: Pro Account Setup Flow

```
┌─────────────────┐
│  Admin          │
└──────┬──────────┘
       │ 1. Install plugin
       │ 2. Navigate to Quickscan → Dashboard
       ▼
┌────────────────────────────┐
│  Dashboard (Basic mode)    │
│  - Pro upgrade section     │
│  - Feature comparison      │
└──────┬─────────────────────┘
       │ 3. Click "Request Pro Account"
       ▼
┌────────────────────────────┐
│  Account Request Page      │
│  templates/account-request │
└──────┬─────────────────────┘
       │ 4. View embedded Zoho form
       │ 5. Fill form fields:
       │    - Company, Name, Email, Phone
       │    - Website URL
       │    - Message/reason
       │ 6. Submit form
       ▼
┌─────────────────────────────┐
│  Zoho Forms API             │
│  (Guardian360 instance)     │
└──────┬──────────────────────┘
       │ 7. Store form submission
       │ 8. Create lead in Zoho CRM
       │ 9. Send notification to Guardian360 team
       ▼
┌─────────────────────────────┐
│  Guardian360 Team           │
│  (Human review)             │
└──────┬──────────────────────┘
       │ 10. Review request (4-24 hours):
       │     - Verify company legitimacy
       │     - Check website authenticity
       │     - Assess use case
       │ 11. If approved:
       │     - Create account in Guardian360 system
       │     - Generate login credentials
       │     - Prepare welcome email
       │ 12. If rejected:
       │     - Send explanation email
       ▼
┌─────────────────────────────┐
│  Zoho CRM Workflow          │
└──────┬──────────────────────┘
       │ 13. Send approval email:
       │     Subject: "Your Quickscan Pro Account"
       │     Body:
       │       - Welcome message
       │       - Email: user@company.com
       │       - Password: SecurePass123
       │       - Setup instructions
       │       - Pro features overview
       │       - Support contact
       ▼
┌─────────────────┐
│  Admin Email    │
│  Inbox          │
└──────┬──────────┘
       │ 14. Admin receives email
       │ 15. Navigate to Quickscan → Settings
       ▼
┌────────────────────────────┐
│  Settings Page             │
│  templates/settings.php    │
└──────┬─────────────────────┘
       │ 16. Select "I have a Quickscan account"
       │ 17. Enter email and password
       │ 18. Click "Test Credentials"
       ▼
┌────────────────────────────┐
│  ajax_test_credentials()   │
│  Line 1099-1147            │
└──────┬─────────────────────┘
       │ 19. POST to /api/v1/login
       │     - email: user@company.com
       │     - password: base64(SecurePass123)
       ▼
┌─────────────────────────────┐
│  Guardian360 API (v1)       │
│  /login endpoint            │
└──────┬──────────────────────┘
       │ 20. Validate credentials
       │ 21. Return token + user data
       ▼
┌────────────────────────────┐
│  ajax_test_credentials()   │
└──────┬─────────────────────┘
       │ 22. wp_send_json_success("Credentials are valid!")
       ▼
┌────────────────────────────┐
│  Settings Page (Frontend)  │
│  Display success message   │
└──────┬─────────────────────┘
       │ 23. Admin clicks "Save Credentials"
       ▼
┌────────────────────────────┐
│  ajax_save_credentials()   │
│  Line 1055-1094            │
└──────┬─────────────────────┘
       │ 24. Verify nonce
       │ 25. Check permissions
       │ 26. Encrypt password:
       │     encrypt_password(SecurePass123)
       │     → AES-256-GCM with PBKDF2
       │ 27. Store in user meta:
       │     - quickscan_email: user@company.com
       │     - quickscan_password: {encrypted_blob}
       │ 28. Clear any existing token
       │ 29. wp_send_json_success("Credentials saved!")
       ▼
┌────────────────────────────┐
│  Settings Page (Frontend)  │
└──────┬─────────────────────┘
       │ 30. Reload page
       ▼
┌────────────────────────────┐
│  Settings Page (Reloaded)  │
│  - Green banner: "Connected"│
│  - Shows email address     │
│  - "Clear Credentials" btn │
└────────────────────────────┘
       │
       ▼
┌─────────────────────────────┐
│  Pro Features Now Active:   │
│  - v1 API access            │
│  - Email report buttons     │
│  - User tracking            │
│  - White-label options      │
└─────────────────────────────┘
```

---

## Frontend Integration Methods

### Method 1: Gutenberg Block

**Block Name:** `quickscan/security-scanner`

**Registration:** `quickscan-connector.php:1173-1223`

**Attributes:**
| Attribute | Type | Default |
|-----------|------|---------|
| `showResults` | boolean | `true` |
| `placeholder` | string | `"Enter website URL to scan..."` |
| `buttonText` | string | `"Start Security Scan"` |
| `title` | string | `"Website Security Scanner"` |
| `showTitle` | boolean | `true` |

**Editor Interface:**
- Block icon: Shield (🛡️)
- Category: Common
- Sidebar controls for all attributes
- Live preview in editor

**Render Method:**
```php
public function render_security_scanner_block($attributes, $content)
```

**Output Structure:**
```html
<div class="wp-block-quickscan-security-scanner">
  <div class="quickscan-frontend-block"
       data-show-results="true"
       data-placeholder="Enter website URL to scan..."
       data-button-text="Start Security Scan"
       data-title="Website Security Scanner"
       data-show-title="true">
    <!-- JavaScript renders scanner interface -->
  </div>
</div>
```

**JavaScript Initialization:**
```javascript
// frontend.js:8-13
function initializeQuickscanBlocks() {
    const blocks = document.querySelectorAll('.quickscan-frontend-block');
    blocks.forEach(function(block) {
        renderQuickscanForm(block);
    });
}
```

**Usage in Content:**
1. Open Block Editor
2. Click + (Add Block)
3. Search "Security Scanner"
4. Configure in sidebar panel
5. Publish

---

### Method 2: Widget

**Widget ID:** `quickscan_security_widget`

**Class:** `Quickscan_Security_Widget` extends `WP_Widget`

**Registration:** `quickscan-connector.php:1371-1373`

**Widget Settings Form:**
```php
// templates visible in Widgets panel
- Title: text input
- Placeholder Text: text input
- Button Text: text input
- Show results on page: checkbox
```

**Output Method:**
```php
public function widget($args, $instance)
```

**Rendered HTML:**
```html
{widget_before}
  {title_before}Title{title_after}
  <div class="quickscan-widget"
       data-show-results="false"
       data-placeholder="Enter website URL..."
       data-button-text="Scan"
       data-show-title="false">
  </div>
{widget_after}
```

**JavaScript Initialization:**
```javascript
// frontend.js:15-20
function initializeQuickscanWidgets() {
    const widgets = document.querySelectorAll('.quickscan-widget');
    widgets.forEach(function(widget) {
        renderQuickscanForm(widget);
    });
}
```

**Usage:**
1. Navigate to Appearance → Widgets
2. Find "Security Scanner" widget
3. Drag to widget area (sidebar, footer, etc.)
4. Configure settings
5. Save

**Common Use Cases:**
- Sidebar scanners
- Footer security checks
- Header utility areas

---

### Method 3: Shortcode

**Shortcode Tag:** `[quickscan]`

**Registration:** `quickscan-connector.php:116`

**Handler Method:** `render_quickscan_shortcode()` (lines 439-450)

**Parameters:**
| Parameter | Default | Description |
|-----------|---------|-------------|
| `show_results` | `"true"` | Display results inline |
| `title` | `""` | Scanner title (empty = no title) |
| `placeholder` | `"Enter website URL to scan..."` | Input placeholder |
| `button_text` | `"Start Security Scan"` | Button label |

**Basic Usage:**
```php
[quickscan]
```

**With Parameters:**
```php
[quickscan
  title="Check Your Website Security"
  placeholder="Enter your domain..."
  button_text="Scan Now"
  show_results="true"]
```

**Template File:** `templates/shortcode.php` (21 lines)

**Rendered HTML:**
```html
<div class="quickscan-frontend-block"
     data-show-results="true"
     data-placeholder="Enter your domain..."
     data-button-text="Scan Now"
     data-title="Check Your Website Security"
     data-show-title="true">
  <!-- JavaScript renders scanner -->
</div>
```

**JavaScript Initialization:**
Same as Gutenberg block - uses `initializeQuickscanBlocks()`

**Common Use Cases:**
- Embedded in classic editor posts
- Service pages
- Landing pages
- Custom page templates

---

## Database & Storage

### WordPress Options Table

**Plugin Settings:**
```sql
option_name: quickscan_enable_logging
option_value: 1 (boolean)

option_name: quickscan_show_signature
option_value: 1 (boolean, deprecated - always shown)

option_name: quickscan_signature_style
option_value: "logo" or "text"

option_name: quickscan_signature_text
option_value: "Powered by Guardian360"

option_name: quickscan_api_version
option_value: "v2" (default, unused)
```

**Set During Activation:** `quickscan-connector.php:233-245`

---

### User Meta Table

**Per-User Credentials:**
```sql
-- User ID: 1 (admin)
meta_key: quickscan_email
meta_value: "admin@example.com"

meta_key: quickscan_password
meta_value: "YmFzZTY0X2VuY29kZWRfZW5jcnlwdGVkX2RhdGFfd2l0aF9pdl9hbmRfdGFn..."
```

**Storage Method:** `save_user_credentials()` (lines 816-838)

**Retrieval Method:** `load_settings()` (lines 150-189)

**Deletion Method:** `clear_user_credentials()` (lines 843-857)

---

### Transients (wp_options table)

**API Tokens (Per User, 1 hour expiry):**
```sql
option_name: _transient_quickscan_api_token_1
option_value: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
option_name: _transient_timeout_quickscan_api_token_1
option_value: 1704123456 (timestamp)
```

**Captchas (10 minute expiry):**
```sql
option_name: _transient_quickscan_captcha_uuid123
option_value: 10 (correct answer)
option_name: _transient_timeout_quickscan_captcha_uuid123
option_value: 1704122856 (timestamp)
```

**Rate Limits - URL (24 hour expiry):**
```sql
option_name: _transient_quickscan_scanned_today_md5_hash
option_value: true
option_name: _transient_timeout_quickscan_scanned_today_md5_hash
option_value: 1704123456 (timestamp)
```

**Rate Limits - IP (1 hour expiry):**
```sql
option_name: _transient_quickscan_email_rate_md5_hash
option_value: 3 (request count)
option_name: _transient_timeout_quickscan_email_rate_md5_hash
option_value: 1704122856 (timestamp)
```

---

### External Storage (Guardian360)

**Scan Results:**
- NOT stored in WordPress
- Stored on Guardian360 servers
- Accessible via Quickscan Portal: https://quickscan.guardian360.nl

**Contact Data:**
- NOT stored in WordPress
- Sent to Zoho CRM via Guardian360 API
- Managed by Guardian360 GDPR compliance team

**Account Data:**
- User accounts managed by Guardian360
- Credentials created during vetting process
- Stored in Guardian360 user database

---

## Authentication & Authorization

### Per-User Authentication Model

**Key Concept:** Each WordPress user can have their own Quickscan Pro account

**Storage:**
- User meta: `quickscan_email` (plaintext)
- User meta: `quickscan_password` (AES-256-GCM encrypted)
- Transient: `quickscan_api_token_{user_id}` (Bearer token, 1hr)

**Context-Specific Loading:**

**Admin Context (Current User):**
```php
// quickscan-connector.php:165-178
$current_user_id = get_current_user_id();
$this->user_email = get_user_meta($current_user_id, 'quickscan_email', true);
$this->user_password = get_user_meta($current_user_id, 'quickscan_password', true);
$this->user_password = $this->decrypt_password($this->user_password);
$this->api_token = get_transient('quickscan_api_token_' . $current_user_id);
```

**Frontend Context (Site Admin):**
```php
// quickscan-connector.php:151-162
if ($is_frontend_email_request) {
    $this->load_admin_credentials();
}

// load_admin_credentials() - lines 194-211
$admin_users = get_users(array('role' => 'administrator'));
foreach ($admin_users as $admin_user) {
    $admin_email = get_user_meta($admin_user->ID, 'quickscan_email', true);
    if (!empty($admin_email)) {
        $this->user_email = $admin_email;
        $this->user_password = $this->decrypt_password(...);
        $this->api_token = get_transient('quickscan_api_token_' . $admin_user->ID);
        break; // Use first admin with credentials
    }
}
```

**Rationale:**
- Frontend email reports use site admin's Pro account
- Allows visitors to receive reports without individual accounts
- Admin pays for/controls the feature

---

### API Version Determination

**Logic:** `load_settings()` (lines 180-189)

```php
if (!empty($this->user_email) && !empty($this->user_password)) {
    // Pro user
    $this->api_base_url = 'https://quickscan.guardian360.nl/api/v1';
    error_log("Using v1 API for authenticated user: " . substr($this->user_email, 0, 5) . "***");
} else {
    // Basic user
    $this->api_base_url = 'https://quickscan.guardian360.nl/api/v2';
    error_log("Using v2 API for non-authenticated access");
}
```

**Decision Tree:**
```
Has credentials (email + password)?
├─ YES → v1 API (Pro)
│   ├─ Requires authentication
│   ├─ Get Bearer token via /login
│   ├─ Include Authorization header
│   └─ Access to /scan/report endpoint
│
└─ NO → v2 API (Basic)
    ├─ No authentication required
    ├─ No Authorization header
    └─ Only /scan endpoint available
```

---

### Token Lifecycle

**1. Token Generation:**
```php
// authenticate() - lines 862-899
$response = wp_remote_request('https://quickscan.guardian360.nl/api/v1/login', [
    'method' => 'POST',
    'body' => [
        'email' => $this->user_email,
        'password' => base64_encode($this->user_password)
    ]
]);

$auth_data = json_decode(wp_remote_retrieve_body($response), true);
$token = $auth_data['data']['token'];

// Store per-user token
set_transient('quickscan_api_token_' . $current_user_id, $token, 3600);
$this->api_token = $token;
```

**2. Token Usage:**
```php
// call_api() - lines 920-997
$args = [
    'method' => 'POST',
    'headers' => [
        'Authorization' => 'Bearer ' . $this->api_token,
        'Accept' => 'application/json'
    ]
];

$response = wp_remote_request($url, $args);
```

**3. Token Refresh (On 401):**
```php
// call_api() - lines 975-994
if ($http_code === 401) {
    // Token expired or invalid
    delete_transient('quickscan_api_token_' . $current_user_id);
    $this->api_token = null;

    // Re-authenticate
    $auth_result = $this->authenticate();

    if ($auth_result === true) {
        // Retry request with new token
        $args['headers']['Authorization'] = 'Bearer ' . $this->api_token;
        $response = wp_remote_request($url, $args);
    }
}
```

**4. Token Expiry:**
- Server-side: Guardian360 API invalidates after inactivity
- Client-side: WordPress transient expires after 1 hour
- Auto-refresh: Plugin re-authenticates transparently

---

### Permission Levels

**Admin Pages (manage_options):**
- Dashboard
- Start Scan
- Account Request
- Settings

**Frontend Scanning (public):**
- No authentication required
- Uses v2 API for Basic users
- Uses site admin's v1 API for Pro features

**Credential Management (manage_options OR edit_posts):**
```php
// ajax_save_credentials() - line 1063-1066
if (!current_user_can('manage_options') && !current_user_can('edit_posts')) {
    wp_send_json_error('Insufficient permissions');
    return;
}
```

**Rationale:**
- Editors can manage their own Pro credentials
- Only affects their personal scans in admin
- Frontend still uses site admin's credentials

---

## Appendix: Code Reference Map

### Core Files Line Count
- `quickscan-connector.php`: 1,697 lines
- `includes/class-results-formatter.php`: 722 lines
- `assets/js/frontend.js`: 566 lines
- `templates/dashboard.php`: 300 lines
- `templates/settings.php`: 309 lines
- `templates/start-scan.php`: 217 lines
- `templates/account-request.php`: 281 lines
- `assets/js/admin.js`: 130 lines
- `templates/shortcode.php`: 21 lines

**Total Plugin Lines:** ~4,243 lines of custom code

### Key Method References

| Functionality | Method | File:Line |
|---------------|--------|-----------|
| **Scanning** |
| Handle scan AJAX | `ajax_start_scan()` | `quickscan-connector.php:455-504` |
| Call Guardian360 API | `call_api()` | `quickscan-connector.php:920-997` |
| Format results | `format_results()` | `class-results-formatter.php:21-69` |
| **Email Reports** |
| Handle email AJAX | `ajax_send_email_report()` | `quickscan-connector.php:608-755` |
| Generate captcha | `ajax_generate_captcha()` | `quickscan-connector.php:1550-1574` |
| Validate captcha | `validate_captcha()` | `quickscan-connector.php:1579-1602` |
| **Authentication** |
| Login to API | `authenticate()` | `quickscan-connector.php:862-899` |
| Ensure authenticated | `ensure_authenticated()` | `quickscan-connector.php:904-915` |
| Load credentials | `load_settings()` | `quickscan-connector.php:150-189` |
| Load admin creds | `load_admin_credentials()` | `quickscan-connector.php:194-211` |
| **Security** |
| Encrypt password | `encrypt_password()` | `quickscan-connector.php:764-781` |
| Decrypt password | `decrypt_password()` | `quickscan-connector.php:788-811` |
| Save credentials | `ajax_save_credentials()` | `quickscan-connector.php:1055-1094` |
| Test credentials | `ajax_test_credentials()` | `quickscan-connector.php:1099-1147` |
| **Frontend** |
| Initialize blocks | `initializeQuickscanBlocks()` | `frontend.js:8-13` |
| Render scanner | `renderQuickscanForm()` | `frontend.js:335-421` |
| Start scan | `startScan()` | `frontend.js:423-497` |
| Display results | `displayResults()` | `frontend.js:499-531` |
| Open email modal | `openEmailModal()` | `frontend.js:216-280` |
| Handle form submit | `handleEmailFormSubmit()` | `frontend.js:136-214` |

---

## Summary

This QuickScan Connector WordPress plugin is a **real-time website security scanner** that integrates with Guardian360's Quickscan API. It provides:

1. **Dual-tier system**: Basic (v2 API, no auth) and Pro (v1 API, auth required)
2. **Comprehensive scanning**: SSL, headers, DNS, CSP, cookies, misconfigurations
3. **Flexible embedding**: Gutenberg blocks, widgets, shortcodes
4. **Professional reporting**: PDF emails generated by Guardian360 (Pro only)
5. **Enterprise security**: AES-256-GCM encryption, per-user credentials, rate limiting
6. **Zero local storage**: All scan data remains on Guardian360 servers
7. **Vetted account system**: Manual review process for Pro accounts
8. **White-label options**: Customizable reports for Pro users

**Technical Architecture:**
- Singleton plugin class managing all functionality
- AJAX-driven frontend for seamless UX
- Server-side result formatting for consistency
- Token-based authentication with auto-refresh
- Context-aware API selection (v1 for Pro, v2 for Basic)

**User Journeys:**
- **Visitors**: Scan websites, request PDF reports
- **Admins**: Configure credentials, embed scanners, manage settings
- **New Users**: Request Pro accounts, receive credentials, activate features

This plugin enables WordPress site owners to offer professional security scanning services to their visitors without managing the scanning infrastructure themselves.

---

**End of Technical Specification**
