document.addEventListener('DOMContentLoaded', function() {

    // Initialize all Quickscan blocks and widgets on the page
    initializeQuickscanBlocks();
    initializeQuickscanWidgets();
    initializeEmailModal();

    function initializeQuickscanBlocks() {
        const blocks = document.querySelectorAll('.quickscan-frontend-block');
        blocks.forEach(function(block) {
            renderQuickscanForm(block);
        });
    }

    function initializeQuickscanWidgets() {
        const widgets = document.querySelectorAll('.quickscan-widget');
        widgets.forEach(function(widget) {
            renderQuickscanForm(widget);
        });
    }

    function initializeEmailModal() {
        // Create modal HTML if it doesn't exist
        if (!document.getElementById('quickscan-email-modal')) {
            const modalHtml = `
                <div id="quickscan-email-modal" class="quickscan-modal" style="display: none;">
                    <div class="quickscan-modal-overlay"></div>
                    <div class="quickscan-modal-content">
                        <button class="quickscan-modal-close">&times;</button>
                        <h3>Get Your Security Report</h3>
                        <p>Enter your details to receive a comprehensive PDF report of your security scan results.</p>
                        <div id="quickscan-zoho-form-container">
                            <iframe
                                aria-label='Request Security Report'
                                frameborder="0"
                                style="height:500px;width:100%;border:none;"
                                src='https://forms.guardian360.eu/guardian360bv/form/RequestaQuickscanaccount/formperma/OCmRQubppJrTZMWzGX_61hFmw8d8oVVwnktMCPgpUV4'>
                            </iframe>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Add close functionality
            const modal = document.getElementById('quickscan-email-modal');
            const closeBtn = modal.querySelector('.quickscan-modal-close');
            const overlay = modal.querySelector('.quickscan-modal-overlay');

            closeBtn.addEventListener('click', closeEmailModal);
            overlay.addEventListener('click', closeEmailModal);
        }
    }

    function openEmailModal() {
        const modal = document.getElementById('quickscan-email-modal');
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeEmailModal() {
        const modal = document.getElementById('quickscan-email-modal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function renderQuickscanForm(container) {
        // Get settings from data attributes
        const scanType = container.dataset.scanType || 'quick';
        const showResults = container.dataset.showResults !== 'false';
        const placeholder = container.dataset.placeholder || 'Enter website URL to scan...';
        const buttonText = container.dataset.buttonText || 'Start Security Scan';
        const title = container.dataset.title || '';
        const showTitle = container.dataset.showTitle !== 'false';

        // Build the form HTML
        let html = '<div class="quickscan-form-container">';

        if (showTitle && title) {
            html += '<h3>' + escapeHtml(title) + '</h3>';
        }

        html += '<div class="quickscan-input-group">';
        html += '<input type="url" class="quickscan-url-input" placeholder="' + escapeHtml(placeholder) + '" required>';
        html += '<button type="button" class="quickscan-button">' + escapeHtml(buttonText) + '</button>';
        html += '</div>';

        html += '<div class="quickscan-status" style="display: none;">';
        html += '<p class="status-message">Scanning...</p>';
        html += '</div>';

        if (showResults) {
            html += '<div class="quickscan-results" style="display: none;">';
            html += '<h4>Security Scan Results</h4>';
            html += '<div class="results-content"></div>';
            html += '<div class="results-actions" style="margin-top: 20px;">';
            html += '<button type="button" class="quickscan-email-button" style="display: none;">📧 Email Report</button>';
            html += '</div>';
            html += '</div>';
        }

        html += '</div>';

        // Check if user has Pro access
        const isPro = quickscan_ajax && quickscan_ajax.is_pro === 'true';

        if (!isPro) {
            html += '<div class="quickscan-free-notice" style="margin-top: 10px; font-size: 12px; color: #666;">';
            html += '🆓 Free Version - <a href="' + quickscan_ajax.admin_url + 'admin.php?page=quickscan-account-request">Upgrade to Pro</a> for email reports and advanced features';
            html += '</div>';
        }

        container.innerHTML = html;

        // Attach event listeners
        const button = container.querySelector('.quickscan-button');
        const input = container.querySelector('.quickscan-url-input');
        const emailButton = container.querySelector('.quickscan-email-button');

        button.addEventListener('click', function() {
            startScan(container, scanType, showResults);
        });

        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                startScan(container, scanType, showResults);
            }
        });

        if (emailButton) {
            emailButton.addEventListener('click', function() {
                openEmailModal();
            });
        }
    }

    function startScan(container, scanType, showResults) {
        const input = container.querySelector('.quickscan-url-input');
        const button = container.querySelector('.quickscan-button');
        const statusDiv = container.querySelector('.quickscan-status');
        const statusMessage = container.querySelector('.status-message');
        const resultsDiv = container.querySelector('.quickscan-results');

        const url = input.value.trim();

        if (!url) {
            alert('Please enter a URL to scan');
            return;
        }

        // Show loading state
        button.disabled = true;
        button.textContent = 'Scanning...';
        statusDiv.style.display = 'block';
        statusMessage.textContent = 'Initializing security scan...';

        if (resultsDiv) {
            resultsDiv.style.display = 'none';
        }

        // Make AJAX request
        const data = new FormData();
        data.append('action', 'quickscan_start_scan');
        data.append('url', url);
        data.append('scan_type', scanType);
        data.append('nonce', quickscan_ajax.nonce);
        data.append('is_frontend', 'true');

        fetch(quickscan_ajax.ajax_url, {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(response => {
            button.disabled = false;
            button.textContent = container.dataset.buttonText || 'Start Security Scan';
            statusDiv.style.display = 'none';

            if (response.success) {
                statusMessage.textContent = 'Scan completed successfully!';

                if (showResults && resultsDiv) {
                    displayResults(resultsDiv, response.data);
                    resultsDiv.style.display = 'block';

                    // Show email button for completed scans
                    const emailButton = container.querySelector('.quickscan-email-button');
                    if (emailButton) {
                        emailButton.style.display = 'inline-block';
                        emailButton.dataset.scanUrl = url;
                    }
                } else {
                    // Show success message
                    statusDiv.style.display = 'block';
                    setTimeout(() => {
                        statusDiv.style.display = 'none';
                    }, 3000);
                }
            } else {
                statusMessage.textContent = 'Error: ' + response.data;
                statusDiv.style.display = 'block';
            }
        })
        .catch(error => {
            button.disabled = false;
            button.textContent = container.dataset.buttonText || 'Start Security Scan';
            statusMessage.textContent = 'Network error. Please try again.';
            statusDiv.style.display = 'block';
        });
    }

    function displayResults(resultsContainer, data) {
        const content = resultsContainer.querySelector('.results-content');

        // Check if using v2 API (free version) or v1 API (pro version)
        const isV2 = !data.data; // v2 API returns results directly, v1 wraps in data
        const scanData = isV2 ? data : data.data;

        let html = '';

        // Display summary based on API version
        if (isV2) {
            // V2 API simple response
            html += '<div class="quickscan-summary">';
            html += '<h5>Scan Complete</h5>';
            if (scanData.statusCode === 200) {
                html += '<p class="success">✅ Scan completed successfully</p>';
                html += '<p>Security analysis has been performed on the requested URL.</p>';
                html += '<p><strong>Want detailed results?</strong> Click the "Email Report" button below to receive a comprehensive PDF report.</p>';
            } else {
                html += '<p class="error">⚠️ Scan encountered issues</p>';
                if (scanData.error) {
                    html += '<p>' + escapeHtml(scanData.error) + '</p>';
                }
            }
            html += '</div>';
        } else {
            // V1 API detailed response (Pro users)
            html += '<div class="quickscan-detailed-results">';
            html += '<table class="table table-bordered">';
            html += '<thead><tr><th>Security Check</th><th>Status</th></tr></thead>';
            html += '<tbody>';

            // Basic Information Section
            if (scanData && scanData.Info) {
                html += '<tr><th colspan="2" class="bg-light"><strong>Basic Information</strong></th></tr>';

                // Domain
                if (scanData.Info.domain) {
                    html += '<tr><td>Domain</td><td>' + escapeHtml(scanData.Info.domain) + '</td></tr>';
                }

                // Status Code
                if (scanData.Info.status) {
                    const statusClass = scanData.Info.status >= 200 && scanData.Info.status < 300 ? 'success' : 'warning';
                    html += '<tr><td>HTTP Status</td><td><span class="label label-' + statusClass + '">' + scanData.Info.status + '</span></td></tr>';
                }

                // SSL/TLS
                if (scanData.Info.protocol) {
                    html += '<tr><td>Protocol</td><td>' + escapeHtml(scanData.Info.protocol) + '</td></tr>';
                }

                // CMS Detection
                if (scanData.Info.cms && scanData.Info.cms.name) {
                    html += '<tr><td>CMS Detected</td><td>' + escapeHtml(scanData.Info.cms.name);
                    if (scanData.Info.cms.version) {
                        html += ' v' + escapeHtml(scanData.Info.cms.version);
                    }
                    html += '</td></tr>';
                }
            }

            // Security Headers Section
            if (scanData && scanData.security_headers) {
                html += '<tr><th colspan="2" class="bg-light"><strong>Security Headers</strong></th></tr>';

                const headers = {
                    'Strict-Transport-Security': 'HSTS',
                    'X-Content-Type-Options': 'X-Content-Type-Options',
                    'X-Frame-Options': 'X-Frame-Options',
                    'Content-Security-Policy': 'CSP',
                    'X-XSS-Protection': 'X-XSS-Protection',
                    'Referrer-Policy': 'Referrer-Policy'
                };

                for (const [header, displayName] of Object.entries(headers)) {
                    const headerData = scanData.security_headers[header];
                    const isConfigured = headerData && (Array.isArray(headerData) ? headerData.length > 0 : true);

                    if (isConfigured) {
                        html += '<tr><td>' + displayName + '</td><td><span class="label label-success">✓ Configured</span></td></tr>';
                    } else {
                        html += '<tr><td>' + displayName + '</td><td><span class="label label-danger">✗ Missing</span></td></tr>';
                    }
                }
            }

            html += '</tbody></table>';
            html += '</div>';
        }

        content.innerHTML = html;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Export functions for external use
    window.quickscan = {
        openEmailModal: openEmailModal,
        closeEmailModal: closeEmailModal
    };
});