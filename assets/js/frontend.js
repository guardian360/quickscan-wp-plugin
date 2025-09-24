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
        console.log('initializeEmailModal called');
        // Create modal HTML if it doesn't exist
        if (!document.getElementById('quickscan-email-modal')) {
            console.log('Creating new modal HTML');
            const modalHtml = `
                <div id="quickscan-email-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 999999; background-color: rgba(0, 0, 0, 0.7);">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                        <button class="quickscan-modal-close" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 28px; cursor: pointer; color: #999; line-height: 1; padding: 0;">&times;</button>

                        <h3 style="margin-top: 0; color: #333;">📧 Get Your Security Report</h3>
                        <p>Enter your details to receive a comprehensive PDF report of your security scan results.</p>

                        <form id="quickscan-email-form" style="margin-top: 20px;">
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Company <span style="color: red;">*</span></label>
                                <input type="text" name="company" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>

                            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                                <div style="flex: 1;">
                                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">First Name <span style="color: red;">*</span></label>
                                    <input type="text" name="firstname" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <div style="flex: 1;">
                                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Last Name <span style="color: red;">*</span></label>
                                    <input type="text" name="surname" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                            </div>

                            <div style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Email Address <span style="color: red;">*</span></label>
                                <input type="email" name="email" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>

                            <div style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Phone <span style="color: #666;">(optional)</span></label>
                                <input type="tel" name="phone" style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>

                            <input type="hidden" name="url" id="scan-url-input">

                            <!-- Reminder Checkbox -->
                            <div style="margin-bottom: 15px;">
                                <label style="display: flex; align-items: center; gap: 8px; font-weight: 400; cursor: pointer;">
                                    <input type="checkbox" name="reminder" value="true" style="margin: 0;">
                                    <span>I want to receive another report with new results in 3 months.</span>
                                </label>
                            </div>

                            <!-- Captcha Field -->
                            <div style="margin-bottom: 15px;" id="captcha-container">
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Verify you're human <span style="color: red;">*</span></label>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span id="captcha-question" style="font-weight: 500;">Loading...</span>
                                    <input type="number" name="captcha_answer" id="captcha-answer" required style="width: 80px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
                                    <button type="button" id="refresh-captcha" style="padding: 6px 10px; background: #f6f7f7; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; font-size: 12px; color: #333;">🔄 New</button>
                                </div>
                                <input type="hidden" name="captcha_key" id="captcha-key">
                            </div>

                            <!-- Privacy and Legal Disclaimers -->
                            <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 12px; margin: 20px 0; font-size: 12px; color: #6c757d;">
                                <strong>Important Legal Notice:</strong>
                                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                                    <li>Your information will be used solely to email you the security scan report</li>
                                    <li>We comply with GDPR and privacy regulations</li>
                                    <li>Your data will not be shared with third parties without consent</li>
                                    <li>By submitting, you agree to Guardian360's <a href="https://guardian360.nl/privacy" target="_blank">Privacy Policy</a> and <a href="https://quickscan.guardian360.nl/terms" target="_blank">Terms of Service</a></li>
                                </ul>
                            </div>

                            <div style="background: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px; padding: 12px; margin-bottom: 20px; font-size: 12px; color: #856404;">
                                <strong>⚠️ API Usage Notice:</strong> This service uses the Guardian360 Quickscan API. Unauthorized or abusive use may result in IP blocking and legal action.
                            </div>

                            <div id="email-form-message" style="display: none; margin-bottom: 15px; padding: 15px; border-radius: 4px;"></div>

                            <div style="text-align: right; border-top: 1px solid #ddd; padding-top: 15px;">
                                <button type="button" class="quickscan-cancel-email" style="margin-right: 10px; padding: 8px 16px; background: #f6f7f7; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; color: #333;">Cancel</button>
                                <button type="submit" style="padding: 8px 20px; background: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Send PDF Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Add event handlers
            setupEmailModalHandlers();
        }
    }

    function setupEmailModalHandlers() {
        const modal = document.getElementById('quickscan-email-modal');
        const closeBtn = modal.querySelector('.quickscan-modal-close');
        const cancelBtn = modal.querySelector('.quickscan-cancel-email');
        const form = document.getElementById('quickscan-email-form');
        const refreshCaptchaBtn = document.getElementById('refresh-captcha');

        closeBtn.addEventListener('click', closeEmailModal);
        cancelBtn.addEventListener('click', closeEmailModal);
        refreshCaptchaBtn.addEventListener('click', generateCaptcha);

        // Close modal when clicking background (the modal itself)
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeEmailModal();
            }
        });

        form.addEventListener('submit', handleEmailFormSubmit);
    }

    function handleEmailFormSubmit(e) {
        console.log('handleEmailFormSubmit called');
        e.preventDefault();
        e.stopImmediatePropagation();

        console.log('Form submission prevented and stopped');

        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const messageDiv = document.getElementById('email-form-message');

        // Disable form
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        // Get form data
        const formData = new FormData(form);
        formData.append('action', 'quickscan_send_email_report');
        formData.append('nonce', quickscan_ajax.nonce);

        // Send to WordPress backend which will forward to Quickscan API
        fetch(quickscan_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('API Response:', data);
            if (data.success) {
                console.log('Showing success message');

                // Replace entire modal content with success message
                const modal = document.getElementById('quickscan-email-modal');
                const modalContent = modal.querySelector('div[style*="position: absolute"]');

                modalContent.innerHTML = `
                    <button class="quickscan-modal-close" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 28px; cursor: pointer; color: #999; line-height: 1; padding: 0;">&times;</button>

                    <div style="text-align: center; padding: 40px 20px;">
                        <div style="font-size: 64px; margin-bottom: 20px;">✅</div>
                        <h3 style="color: #155724; margin: 0 0 15px 0;">Report Sent Successfully!</h3>
                        <p style="color: #155724; margin-bottom: 20px; font-size: 16px;">${data.data}</p>

                        <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: left;">
                            <h4 style="color: #155724; margin: 0 0 10px 0; font-size: 14px;">What happens next?</h4>
                            <ul style="color: #155724; margin: 0; padding-left: 20px; font-size: 14px;">
                                <li>Check your email inbox (including spam folder)</li>
                                <li>The PDF report contains detailed security findings</li>
                                <li>Use the report to improve your website security</li>
                            </ul>
                        </div>

                        <button onclick="window.quickscan.closeEmailModal()" style="background: #28a745; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 16px;">Close</button>
                    </div>
                `;

                // Re-attach close button event
                const newCloseBtn = modalContent.querySelector('.quickscan-modal-close');
                if (newCloseBtn) {
                    newCloseBtn.addEventListener('click', closeEmailModal);
                }

                console.log('Success message displayed, modal content replaced');
            } else {
                throw new Error(data.data || 'Failed to send email');
            }
        })
        .catch(error => {
            messageDiv.style.display = 'block';
            messageDiv.style.background = '#f8d7da';
            messageDiv.style.color = '#721c24';
            messageDiv.innerHTML = '❌ <strong>Error:</strong> ' + error.message;
        })
        .finally(() => {
            // Always reset button state regardless of success or error
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send PDF Report';
        });
    }

    function openEmailModal(scanUrl) {
        console.log('openEmailModal called with URL:', scanUrl);
        try {
            // Ensure modal exists, if not create it
            let modal = document.getElementById('quickscan-email-modal');
            if (!modal) {
                console.log('Modal not found, initializing...');
                initializeEmailModal();
                modal = document.getElementById('quickscan-email-modal');
            } else {
                // Reset modal to original form state (fix caching issue)
                const modalContent = modal.querySelector('div[style*="position: absolute"]');
                if (modalContent && !modalContent.querySelector('#quickscan-email-form')) {
                    console.log('Resetting modal to form state');
                    // Modal was in success state, reinitialize it
                    modal.remove();
                    initializeEmailModal();
                    modal = document.getElementById('quickscan-email-modal');
                }
            }

            if (!modal) {
                console.error('Failed to create email modal');
                // Restore scroll if modal creation failed
                document.body.style.overflow = '';
                return;
            }

            console.log('Modal found, setting up modal content');

            // Set the scan URL
            const urlInput = document.getElementById('scan-url-input');
            if (urlInput && scanUrl) {
                urlInput.value = scanUrl;
                console.log('Set scan URL in modal input');
            }

            // Reset form
            const form = document.getElementById('quickscan-email-form');
            if (form) {
                console.log('Resetting modal form');
                form.reset();
                form.style.display = 'block';
                const messageDiv = document.getElementById('email-form-message');
                if (messageDiv) {
                    messageDiv.style.display = 'none';
                    console.log('Hidden message div during reset');
                }
                console.log('Reset modal form completed');
            }

            console.log('Displaying modal');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';

            // Generate captcha after modal is displayed
            setTimeout(function() {
                generateCaptcha();
            }, 100);
        } catch (error) {
            console.error('Error opening email modal:', error);
            // Restore scroll if there was an error
            document.body.style.overflow = '';
        }
    }

    function closeEmailModal() {
        const modal = document.getElementById('quickscan-email-modal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function generateCaptcha() {
        const questionSpan = document.getElementById('captcha-question');
        const keyInput = document.getElementById('captcha-key');
        const answerInput = document.getElementById('captcha-answer');

        if (!questionSpan || !keyInput || !answerInput) {
            console.error('Captcha elements not found');
            return;
        }

        // Check if quickscan_ajax is available
        if (typeof quickscan_ajax === 'undefined') {
            console.error('quickscan_ajax not available');
            questionSpan.textContent = 'Captcha unavailable';
            return;
        }

        questionSpan.textContent = 'Loading...';
        answerInput.value = '';

        fetch(quickscan_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'quickscan_generate_captcha',
                nonce: quickscan_ajax.nonce
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                questionSpan.textContent = data.data.question;
                keyInput.value = data.data.key;
            } else {
                questionSpan.textContent = 'Error loading captcha';
            }
        })
        .catch(error => {
            questionSpan.textContent = 'Error loading captcha';
            console.error('Captcha generation failed:', error);
        });
    }

    function renderQuickscanForm(container) {
        // Get settings from data attributes
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

            // Only show email button for Pro users
            if (quickscan_ajax && quickscan_ajax.is_pro === 'true') {
                html += '<button type="button" class="quickscan-email-button" style="display: none;">📧 Email Report</button>';
            }

            html += '</div>';
            html += '</div>';
        }

        html += '</div>';

        // Add attribution (always shown)
        if (quickscan_ajax && quickscan_ajax.show_signature) {
            html += '<div class="quickscan-signature" style="text-align: center; margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee;">';

            if (quickscan_ajax.signature_style === 'logo') {
                html += '<div style="font-size: 11px; color: #999; margin-bottom: 8px;">' + quickscan_ajax.signature_text + '</div>';
                html += '<a href="' + quickscan_ajax.signature_url + '" target="_blank" rel="noopener noreferrer">';
                html += '<img src="' + quickscan_ajax.logo_url + '" alt="Guardian360 Quickscan Security Scanner" title="Visit Guardian360 Quickscan on GitHub" style="width: 200px; height: auto; opacity: 0.7; transition: opacity 0.3s ease;" onmouseover="this.style.opacity=\'1\'" onmouseout="this.style.opacity=\'0.7\'">';
                html += '</a>';
            } else {
                html += '<a href="' + quickscan_ajax.signature_url + '" target="_blank" rel="noopener noreferrer" style="font-size: 11px; color: #999; text-decoration: none;">';
                html += quickscan_ajax.signature_text;
                html += '</a>';
            }

            html += '</div>';
        }

        container.innerHTML = html;

        // Attach event listeners
        const button = container.querySelector('.quickscan-button');
        const input = container.querySelector('.quickscan-url-input');
        const emailButton = container.querySelector('.quickscan-email-button');

        button.addEventListener('click', function() {
            startScan(container, showResults);
        });

        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                startScan(container, showResults);
            }
        });

        if (emailButton) {
            emailButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Email Report button clicked');
                const scanUrl = input.value.trim();
                console.log('Opening email modal for URL:', scanUrl);
                openEmailModal(scanUrl);
            });
        }
    }

    function startScan(container, showResults) {
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

                    // Show email button for Pro users only
                    if (quickscan_ajax && quickscan_ajax.is_pro === 'true') {
                        const emailButton = container.querySelector('.quickscan-email-button');
                        if (emailButton) {
                            emailButton.style.display = 'inline-block';
                            emailButton.dataset.scanUrl = url;
                        }
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

        // Use server-side formatting for consistency
        const formData = new FormData();
        formData.append('action', 'quickscan_format_results');
        formData.append('results', JSON.stringify(data));
        formData.append('nonce', quickscan_ajax.nonce);

        fetch(quickscan_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                content.innerHTML = response.data;
                // Initialize scan results functionality
                if (window.quickscanFunctions) {
                    quickscanFunctions.initializeScanResults();
                }
            } else {
                console.warn('Frontend formatting failed:', response);
                // Fallback to simple display if formatting fails
                displayResultsFallback(content, data);
            }
        })
        .catch(error => {
            console.warn('Frontend formatting error:', error);
            // Fallback to simple display on error
            displayResultsFallback(content, data);
        });
    }

    function displayResultsFallback(content, data) {
        // Simple fallback display for when server formatting fails
        const scanData = data.data || data;
        let html = '<div class="quickscan-results-simple">';

        if (scanData && scanData.Info) {
            html += '<h5>Scan Results</h5>';
            html += '<p><strong>URL:</strong> ' + escapeHtml(scanData.Info.URL || '') + '</p>';
            if (scanData.Info.Score !== undefined) {
                html += '<p><strong>Security Score:</strong> ' + scanData.Info.Score + '/100</p>';
            }
            html += '<p class="success">✅ Scan completed successfully</p>';
            html += '<p>Click "Email Report" to receive detailed results.</p>';
        } else {
            html += '<p class="error">⚠️ Unable to display detailed results</p>';
        }

        html += '</div>';

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