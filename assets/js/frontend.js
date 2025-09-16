document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize all Quickscan blocks and widgets on the page
    initializeQuickscanBlocks();
    initializeQuickscanWidgets();
    
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
            html += '</div>';
        }
        
        html += '</div>';
        
        // Add Guardian360 signature if enabled
        if (typeof quickscan_frontend !== 'undefined' && quickscan_frontend.show_signature) {
            html += '<div class="quickscan-signature">';
            html += '<a href="https://guardian360.eu" target="_blank" rel="noopener noreferrer">';
            html += 'Powered by Guardian360';
            html += '</a>';
            html += '</div>';
        }
        
        container.innerHTML = html;
        
        // Add event listeners
        const button = container.querySelector('.quickscan-button');
        const input = container.querySelector('.quickscan-url-input');
        
        button.addEventListener('click', function() {
            const url = input.value.trim();
            if (!url) {
                alert(quickscan_frontend.strings.enter_valid_url);
                input.focus();
                return;
            }
            
            startScan(container, url, scanType, showResults);
        });
        
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                button.click();
            }
        });
    }
    
    function startScan(container, url, scanType, showResults) {
        const button = container.querySelector('.quickscan-button');
        const status = container.querySelector('.quickscan-status');
        const results = container.querySelector('.quickscan-results');
        const originalButtonText = button.textContent;
        
        // Update UI
        button.disabled = true;
        button.innerHTML = '<span class="quickscan-loading"></span>' + quickscan_frontend.strings.scanning;
        status.style.display = 'block';
        if (results) results.style.display = 'none';
        
        // Prepare form data
        const formData = new FormData();
        formData.append('action', 'quickscan_start_scan');
        formData.append('url', url);
        formData.append('scan_type', scanType);
        formData.append('is_frontend', 'true');
        formData.append('nonce', quickscan_frontend.nonce);
        
        // Make AJAX request
        fetch(quickscan_frontend.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(responseText => {
            console.log('Scan response:', responseText);
            
            // Handle mixed HTML/JSON response
            let jsonResponse;
            if (responseText.includes('{"success"')) {
                const jsonStart = responseText.indexOf('{"success"');
                jsonResponse = JSON.parse(responseText.substring(jsonStart));
            } else {
                throw new Error('Invalid response format');
            }
            
            if (jsonResponse.success) {
                status.querySelector('.status-message').innerHTML = '<span class="success">' + quickscan_frontend.strings.scan_completed + '</span>';
                
                if (showResults && results) {
                    displayResults(results, jsonResponse.data);
                    results.style.display = 'block';
                } else {
                    // Redirect to results page or show popup
                    status.querySelector('.status-message').innerHTML += '<br><a href="/wp-admin/admin.php?page=quickscan-results" target="_blank">View detailed results →</a>';
                }
            } else {
                throw new Error(jsonResponse.data || 'Scan failed');
            }
        })
        .catch(error => {
            console.error('Scan error:', error);
            status.querySelector('.status-message').innerHTML = '<span class="error">✗ Scan failed: ' + error.message + '</span>';
        })
        .finally(() => {
            button.disabled = false;
            button.textContent = originalButtonText;
        });
    }
    
    function displayResults(resultsContainer, data) {
        const content = resultsContainer.querySelector('.results-content');
        // V1 API response structure: data.data contains the scan results
        const scanData = data.data;
        
        let html = '';
        
        // Add GDPR compliance notice
        html += '<div class="quickscan-gdpr-notice" style="background: #f0f6fc; border: 1px solid #0969da; border-radius: 6px; padding: 12px; margin-bottom: 16px; color: #0969da;">';
        html += '<div style="display: flex; align-items: center; gap: 8px;">';
        html += '<span style="font-size: 16px;">ℹ️</span>';
        html += '<div>';
        html += '<strong>Privacy Notice:</strong> ';
        html += quickscan_frontend.strings.gdpr_notice || 'This scan is performed in real-time and results are not stored on our servers. Your data is processed according to GDPR regulations.';
        html += ' <a href="https://gdpr.eu/what-is-gdpr/" target="_blank" rel="noopener noreferrer" style="color: #0969da;">Learn more about GDPR</a>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        
        // Start table
        html += '<table class="table table-bordered table-responsive quickscan-results-table">';
        html += '<tbody>';
        
        // Info Section
        if (scanData && scanData.Info) {
            html += '<tr><th colspan="2" class="bg-guardian"><strong>Info</strong></th></tr>';
            html += '<tr><td>URL</td><td>' + escapeHtml(scanData.Info.URL || '') + '</td></tr>';
            
            // Score from API v1
            const score = scanData.Info.Score || 60;
            
            // Score with progress bar - use the same logic as Guardian360
            let scoreClass = 'progress-bar-success';
            if (score < 40) scoreClass = 'progress-bar-danger';
            else if (score < 70) scoreClass = 'progress-bar-warning';
            
            html += '<tr><td>Score</td><td>';
            html += '<div class="progress">';
            html += '<div data-score="' + score + '" class="progress-bar ' + scoreClass + ' result__score" style="width: ' + score + '%">';
            html += score + '%';
            html += '</div></div></td></tr>';
            
            // Raw headers (collapsible)
            if (scanData.Info.raw_headers) {
                const headerCount = Object.keys(scanData.Info.raw_headers).length;
                html += '<tr class="collapsible">';
                html += '<td>Raw headers</td>';
                html += '<td>' + headerCount + ' headers found <span class="pull-right"><span class="fa fa-angle-left"></span></span></td>';
                html += '</tr>';
                html += '<tr class="details" style="display: none;">';
                html += '<td colspan="2"><table class="table table-bordered table-responsive"><tbody>';
                for (const [header, value] of Object.entries(scanData.Info.raw_headers)) {
                    html += '<tr><td>' + escapeHtml(header) + '</td><td>' + escapeHtml(value) + '</td></tr>';
                }
                html += '</tbody></table></td></tr>';
            }
            
            // IP Addresses if available
            if (scanData.Info.IP && Array.isArray(scanData.Info.IP) && scanData.Info.IP.length > 0) {
                html += '<tr><td rowspan="' + scanData.Info.IP.length + '">IP addresses</td>';
                html += '<td>' + escapeHtml(scanData.Info.IP[0]) + '</td></tr>';
                for (let i = 1; i < scanData.Info.IP.length; i++) {
                    html += '<tr><td>' + escapeHtml(scanData.Info.IP[i]) + '</td></tr>';
                }
            }
        }
        
        // Security Headers Section
        if (scanData && scanData.security_headers) {
            html += '<tr><th colspan="2" class="bg-medium"><strong>Security Headers</strong></th></tr>';
            
            const importantHeaders = {
                'Strict-Transport-Security': 'HSTS',
                'X-Content-Type-Options': 'X-Content-Type-Options',
                'X-Frame-Options': 'X-Frame-Options',
                'Content-Security-Policy': 'CSP',
                'X-XSS-Protection': 'X-XSS-Protection',
                'Referrer-Policy': 'Referrer-Policy'
            };
            
            for (const [header, displayName] of Object.entries(importantHeaders)) {
                const headerData = scanData.security_headers[header];
                const isConfigured = headerData && (Array.isArray(headerData) ? headerData.length > 0 : true);
                
                if (isConfigured) {
                    html += '<tr class="safe">';
                    html += '<td>' + displayName + '</td>';
                    html += '<td class="toggle"><span class="label label-success">Veilig</span></td>';
                    html += '</tr>';
                } else {
                    html += '<tr class="collapsible">';
                    html += '<td>' + displayName + '</td>';
                    html += '<td class="toggle"><span class="label label-danger">Niet geconfigureerd</span>';
                    html += '<span class="pull-right"><span class="fa fa-angle-left"></span></span></td>';
                    html += '</tr>';
                    html += '<tr class="details" style="display: none;">';
                    html += '<td colspan="2"><table class="table table-bordered table-responsive"><tbody>';
                    html += '<tr><td>Probleem</td><td>' + header + ' header not found</td></tr>';
                    html += '<tr><td>Risico</td><td>' + getHeaderRisk(header) + '</td></tr>';
                    html += '</tbody></table></td></tr>';
                }
            }
        }
        
        // Misconfigurations Section
        if (scanData && scanData.Misconfigurations) {
            let hasVulnerabilities = false;
            let vulnerableItems = [];
            
            for (const [key, config] of Object.entries(scanData.Misconfigurations)) {
                if (config && config.Vulnerable) {
                    hasVulnerabilities = true;
                    vulnerableItems.push({
                        name: key,
                        issue: config.Issue || 'Security issue detected',
                        risk: config.Risk || 'Medium',
                        solution: config.Solution || 'Please review your configuration'
                    });
                }
            }
            
            if (vulnerableItems.length > 0) {
                html += '<tr><th colspan="2" class="bg-high"><strong>Misconfigurations</strong></th></tr>';
                
                vulnerableItems.forEach(item => {
                    html += '<tr class="collapsible">';
                    html += '<td>' + formatConfigName(item.name) + '</td>';
                    html += '<td class="toggle"><span class="label label-danger">Kwetsbaar</span>';
                    html += '<span class="pull-right"><span class="fa fa-angle-left"></span></span></td>';
                    html += '</tr>';
                    html += '<tr class="details" style="display: none;">';
                    html += '<td colspan="2"><table class="table table-bordered table-responsive"><tbody>';
                    html += '<tr><td>Probleem</td><td>' + escapeHtml(item.issue) + '</td></tr>';
                    html += '<tr><td>Risico</td><td>' + item.risk + '</td></tr>';
                    html += '<tr><td>Oplossing</td><td>' + escapeHtml(item.solution) + '</td></tr>';
                    html += '</tbody></table></td></tr>';
                });
            } else {
                html += '<tr><th colspan="2" class="bg-green"><strong>Misconfigurations</strong></th></tr>';
                html += '<tr class="safe"><td colspan="2">✓ No major security misconfigurations detected</td></tr>';
            }
        }
        
        // DNS Section
        if (scanData && scanData.DNS) {
            html += '<tr><th colspan="2" class="bg-green"><strong>DNS</strong></th></tr>';
            
            for (const [dnsType, dnsData] of Object.entries(scanData.DNS)) {
                if (dnsData && typeof dnsData === 'object' && dnsData.Value) {
                    const isVulnerable = dnsData.Vulnerable === true;
                    if (!isVulnerable) {
                        html += '<tr class="safe">';
                        html += '<td>' + dnsType + '</td>';
                        html += '<td class="toggle"><span class="label label-success">Veilig</span></td>';
                    } else {
                        html += '<tr>';
                        html += '<td>' + dnsType + '</td>';
                        html += '<td class="toggle"><span class="label label-danger">Niet geconfigureerd</span></td>';
                    }
                    html += '</tr>';
                }
            }
        }
        
        // Close table
        html += '</tbody></table>';
        
        // Add email PDF button
        html += '<div class="quickscan-email-section" style="margin: 20px 0; text-align: center;">';
        html += '<button type="button" class="quickscan-email-button" style="background: #0073aa; color: white; border: none; padding: 12px 24px; border-radius: 4px; cursor: pointer; font-size: 14px;">';
        html += quickscan_frontend.strings.email_pdf || 'Email PDF Report';
        html += '</button>';
        html += '</div>';
        
        // Add Guardian360 signature if enabled
        if (typeof quickscan_frontend !== 'undefined' && quickscan_frontend.show_signature) {
            html += '<div class="quickscan-signature" style="margin-top: 20px;">';
            html += '<a href="https://guardian360.eu" target="_blank">';
            html += quickscan_frontend.signature_text || 'Powered by Guardian360';
            html += '</a>';
            html += '</div>';
        }
        
        content.innerHTML = html;
        resultsContainer.style.display = 'block';
        
        // Add click handler for email button
        setupEmailButton(content, scanData);
        
        // Add click handlers for collapsible rows
        setupCollapsibleRows(content);
    }
    
    function setupCollapsibleRows(container) {
        const collapsibleRows = container.querySelectorAll('tr.collapsible');
        collapsibleRows.forEach(row => {
            row.addEventListener('click', function() {
                const detailsRow = this.nextElementSibling;
                if (detailsRow && detailsRow.classList.contains('details')) {
                    const isVisible = detailsRow.style.display !== 'none';
                    detailsRow.style.display = isVisible ? 'none' : 'table-row';
                    
                    // Toggle icon
                    const icon = this.querySelector('.fa');
                    if (icon) {
                        icon.className = isVisible ? 'fa fa-angle-left' : 'fa fa-angle-down';
                    }
                }
            });
        });
    }
    
    function setupEmailButton(container, scanData) {
        const emailButton = container.querySelector('.quickscan-email-button');
        if (!emailButton) return;
        
        emailButton.addEventListener('click', function() {
            showEmailModal(scanData);
        });
    }
    
    function showEmailModal(scanData) {
        // Create modal overlay
        const modalOverlay = document.createElement('div');
        modalOverlay.className = 'quickscan-modal-overlay';
        modalOverlay.style.cssText = `
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.5); z-index: 10000; display: flex; 
            align-items: center; justify-content: center;
        `;
        
        // Create modal content
        const modal = document.createElement('div');
        modal.className = 'quickscan-email-modal';
        modal.style.cssText = `
            background: white; border-radius: 8px; max-width: 500px; 
            width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        `;
        
        const url = scanData && scanData.Info ? scanData.Info.URL : window.location.href;
        
        modal.innerHTML = `
            <div style="padding: 20px; border-bottom: 1px solid #ddd;">
                <h3 style="margin: 0; color: #333; display: flex; align-items: center; justify-content: space-between;">
                    📧 ${quickscan_frontend.strings.email_pdf || 'Email PDF Report'}
                    <button type="button" class="quickscan-close-modal" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666; line-height: 1;">&times;</button>
                </h3>
            </div>
            <form class="quickscan-email-form" style="padding: 20px;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">
                        ${quickscan_frontend.strings.company || 'Company'} <span style="color: #d63638;">*</span>
                    </label>
                    <input type="text" name="company" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
                
                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">
                            ${quickscan_frontend.strings.first_name || 'First Name'} <span style="color: #d63638;">*</span>
                        </label>
                        <input type="text" name="firstname" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">
                            ${quickscan_frontend.strings.last_name || 'Last Name'} <span style="color: #d63638;">*</span>
                        </label>
                        <input type="text" name="surname" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    </div>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">
                        ${quickscan_frontend.strings.email || 'Email Address'} <span style="color: #d63638;">*</span>
                    </label>
                    <input type="email" name="email" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">
                        ${quickscan_frontend.strings.phone || 'Phone'} <span style="color: #666; font-weight: normal;">(${quickscan_frontend.strings.optional || 'optional'})</span>
                    </label>
                    <input type="tel" name="phone" style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
                
                <div style="background: #f0f6fc; border: 1px solid #0969da; border-radius: 4px; padding: 12px; margin-bottom: 20px; font-size: 13px; color: #0969da;">
                    <strong>Privacy Notice:</strong> ${quickscan_frontend.strings.privacy_notice || 'Your information will be used solely to email you the PDF report. We respect your privacy and will not share your data with third parties.'}
                </div>
                
                <div style="text-align: right; border-top: 1px solid #ddd; padding-top: 15px; margin-top: 20px;">
                    <button type="button" class="quickscan-cancel-email" style="margin-right: 10px; padding: 8px 16px; background: #f6f7f7; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                        ${quickscan_frontend.strings.cancel || 'Cancel'}
                    </button>
                    <button type="submit" class="quickscan-send-email" style="padding: 8px 20px; background: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                        ${quickscan_frontend.strings.send_pdf || 'Send PDF Report'}
                    </button>
                </div>
                
                <input type="hidden" name="url" value="${escapeHtml(url)}">
            </form>
        `;
        
        modalOverlay.appendChild(modal);
        document.body.appendChild(modalOverlay);
        
        // Setup modal event handlers
        setupModalHandlers(modalOverlay, modal, scanData);
    }
    
    function setupModalHandlers(modalOverlay, modal, scanData) {
        const closeModal = () => {
            modalOverlay.remove();
        };
        
        // Close button
        modal.querySelector('.quickscan-close-modal').addEventListener('click', closeModal);
        
        // Cancel button
        modal.querySelector('.quickscan-cancel-email').addEventListener('click', closeModal);
        
        // Click outside to close
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) closeModal();
        });
        
        // Form submission
        const form = modal.querySelector('.quickscan-email-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleEmailSubmission(form, closeModal, scanData);
        });
    }
    
    function handleEmailSubmission(form, closeModal, scanData) {
        const submitButton = form.querySelector('.quickscan-send-email');
        const originalText = submitButton.textContent;
        
        submitButton.disabled = true;
        submitButton.textContent = quickscan_frontend.strings.sending || 'Sending...';
        
        // Get form data
        const formData = new FormData(form);
        
        // Add WordPress nonce for security
        formData.append('action', 'quickscan_send_pdf');
        formData.append('nonce', quickscan_frontend.nonce);
        
        // Send to WordPress first (for lead capture), then WordPress forwards to Quickscan
        fetch(quickscan_frontend.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showEmailSuccess(closeModal);
            } else {
                throw new Error(data.data || 'Failed to send email');
            }
        })
        .catch(error => {
            console.error('Error sending email:', error);
            showEmailError(submitButton, originalText);
        });
    }
    
    function showEmailSuccess(closeModal) {
        // Create success message
        const successOverlay = document.createElement('div');
        successOverlay.style.cssText = `
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.5); z-index: 10001; display: flex; 
            align-items: center; justify-content: center;
        `;
        
        const successModal = document.createElement('div');
        successModal.style.cssText = `
            background: white; border-radius: 8px; max-width: 400px; 
            width: 90%; padding: 30px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        `;
        
        successModal.innerHTML = `
            <div style="color: #00a32a; font-size: 48px; margin-bottom: 15px;">✓</div>
            <h3 style="margin: 0 0 10px 0; color: #333;">${quickscan_frontend.strings.email_sent || 'Email Sent!'}</h3>
            <p style="color: #666; margin-bottom: 20px;">${quickscan_frontend.strings.check_email || 'Please check your email for the PDF security report.'}</p>
            <button style="padding: 10px 20px; background: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer;">
                ${quickscan_frontend.strings.close || 'Close'}
            </button>
        `;
        
        successOverlay.appendChild(successModal);
        document.body.appendChild(successOverlay);
        
        successModal.querySelector('button').addEventListener('click', () => {
            successOverlay.remove();
            closeModal();
        });
        
        // Auto close after 5 seconds
        setTimeout(() => {
            if (document.body.contains(successOverlay)) {
                successOverlay.remove();
                closeModal();
            }
        }, 5000);
    }
    
    function showEmailError(submitButton, originalText) {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
        submitButton.style.background = '#d63638';
        submitButton.textContent = quickscan_frontend.strings.try_again || 'Try Again';
        
        setTimeout(() => {
            submitButton.style.background = '#0073aa';
            submitButton.textContent = originalText;
        }, 3000);
    }
    
    function countVulnerabilities(scanData) {
        let count = 0;
        
        if (scanData.Misconfigurations) {
            for (const config of Object.values(scanData.Misconfigurations)) {
                if (config && config.Vulnerable) count++;
            }
        }
        
        if (scanData.security_headers) {
            const importantHeaders = ['Strict-Transport-Security', 'X-Content-Type-Options', 'X-Frame-Options', 'Content-Security-Policy'];
            for (const header of importantHeaders) {
                const headerData = scanData.security_headers[header];
                if (!headerData || (Array.isArray(headerData) && headerData.length === 0)) {
                    count++;
                }
            }
        }
        
        return count;
    }
    
    function getHeaderRisk(header) {
        const risks = {
            'X-Frame-Options': 'Medium',
            'X-Content-Type-Options': 'Low',
            'Strict-Transport-Security': 'High',
            'Content-Security-Policy': 'Medium',
            'X-XSS-Protection': 'Low',
            'Referrer-Policy': 'Low'
        };
        return risks[header] || 'Medium';
    }
    
    function formatConfigName(name) {
        // Convert snake_case or kebab-case to Title Case
        return name.replace(/[-_]/g, ' ')
            .replace(/\b\w/g, l => l.toUpperCase());
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }
    
});