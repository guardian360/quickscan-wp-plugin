<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Request Quickscan Account Access', 'quickscan-connector'); ?></h1>

    <div class="quickscan-account-request-container">
        <!-- Information Section -->
        <div class="card" style="margin: 20px 0; max-width: 800px;">
            <h2><?php _e('Professional Security Scanning Platform', 'quickscan-connector'); ?></h2>

            <div class="notice notice-info inline">
                <p><strong><?php _e('Quality Assurance Process', 'quickscan-connector'); ?></strong></p>
                <p><?php _e('To ensure the highest quality of service and maintain the integrity of our security scanning platform, we personally review each account request. This allows us to:', 'quickscan-connector'); ?></p>
                <ul style="margin-left: 20px;">
                    <li><?php _e('Provide personalized onboarding and support', 'quickscan-connector'); ?></li>
                    <li><?php _e('Ensure optimal configuration for your specific needs', 'quickscan-connector'); ?></li>
                    <li><?php _e('Maintain the security and reliability of our scanning infrastructure', 'quickscan-connector'); ?></li>
                    <li><?php _e('Offer tailored recommendations based on your website profile', 'quickscan-connector'); ?></li>
                </ul>
            </div>

            <div class="quickscan-benefits" style="background: #f9f9f9; padding: 15px; border-left: 4px solid #00a0d2; margin: 20px 0;">
                <h3><?php _e('Benefits of Your Quickscan Account', 'quickscan-connector'); ?></h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 10px;">
                    <div>
                        <h4><?php _e('🔍 Advanced Security Scanning', 'quickscan-connector'); ?></h4>
                        <p><?php _e('Comprehensive vulnerability detection and security analysis', 'quickscan-connector'); ?></p>
                    </div>
                    <div>
                        <h4><?php _e('📊 Detailed Reporting', 'quickscan-connector'); ?></h4>
                        <p><?php _e('In-depth reports with actionable security recommendations', 'quickscan-connector'); ?></p>
                    </div>
                    <div>
                        <h4><?php _e('📈 Historical Tracking', 'quickscan-connector'); ?></h4>
                        <p><?php _e('View and track your website\'s security improvements over time', 'quickscan-connector'); ?></p>
                    </div>
                    <div>
                        <h4><?php _e('💬 Priority Support', 'quickscan-connector'); ?></h4>
                        <p><?php _e('Direct access to our security experts for guidance and support', 'quickscan-connector'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Section -->
        <div class="card" style="margin: 20px 0; max-width: 800px;">
            <h3><?php _e('Account Approval Process', 'quickscan-connector'); ?></h3>
            <div class="quickscan-timeline" style="margin: 20px 0;">
                <div style="display: flex; align-items: center; margin: 10px 0;">
                    <div style="background: #00a0d2; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">1</div>
                    <div>
                        <strong><?php _e('Submit Request', 'quickscan-connector'); ?></strong> - <?php _e('Complete the form below with your details', 'quickscan-connector'); ?>
                    </div>
                </div>
                <div style="display: flex; align-items: center; margin: 10px 0;">
                    <div style="background: #00a0d2; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">2</div>
                    <div>
                        <strong><?php _e('Review Process', 'quickscan-connector'); ?></strong> - <?php _e('Our team reviews your request (typically within 24 hours)', 'quickscan-connector'); ?>
                    </div>
                </div>
                <div style="display: flex; align-items: center; margin: 10px 0;">
                    <div style="background: #00a0d2; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">3</div>
                    <div>
                        <strong><?php _e('Account Activation', 'quickscan-connector'); ?></strong> - <?php _e('Receive your credentials and welcome information via email', 'quickscan-connector'); ?>
                    </div>
                </div>
                <div style="display: flex; align-items: center; margin: 10px 0;">
                    <div style="background: #46b450; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">✓</div>
                    <div>
                        <strong><?php _e('Start Scanning', 'quickscan-connector'); ?></strong> - <?php _e('Begin securing your websites with professional-grade security analysis', 'quickscan-connector'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Request Form -->
        <div class="card" style="margin: 20px 0; max-width: 800px;">
            <h3><?php _e('Account Request Form', 'quickscan-connector'); ?></h3>
            <p><?php _e('Please complete the form below to request access to your Quickscan security scanning account. All information is securely transmitted and processed.', 'quickscan-connector'); ?></p>

            <!-- Embedded Zoho Form -->
            <div class="quickscan-form-container" style="margin: 20px 0;">
                <div id="zf_div_OCmRQubppJrTZMWzGX_61hFmw8d8oVVwnktMCPgpUV4">
                    <!-- Fallback iframe for users with JavaScript disabled -->
                    <noscript>
                        <iframe
                            aria-label='Request a Quickscan account'
                            frameborder="0"
                            style="height:1009px;width:99%;border:none;"
                            src='https://forms.guardian360.eu/guardian360bv/form/RequestaQuickscanaccount/formperma/OCmRQubppJrTZMWzGX_61hFmw8d8oVVwnktMCPgpUV4'>
                        </iframe>
                    </noscript>
                </div>
            </div>
        </div>

        <!-- Support Section -->
        <div class="card" style="margin: 20px 0; max-width: 800px; background: #f0f0f1;">
            <h3><?php _e('Need Help?', 'quickscan-connector'); ?></h3>
            <p><?php _e('If you have any questions about the account request process or need technical support, please don\'t hesitate to contact us:', 'quickscan-connector'); ?></p>
            <ul style="margin-left: 20px;">
                <li><strong><?php _e('Email:', 'quickscan-connector'); ?></strong> <a href="mailto:support@guardian360.nl">support@guardian360.nl</a></li>
                <li><strong><?php _e('Website:', 'quickscan-connector'); ?></strong> <a href="https://guardian360.nl" target="_blank">guardian360.nl</a></li>
            </ul>
        </div>
    </div>
</div>

<script type="text/javascript">
(function() {
 try{
  var f = document.createElement("iframe");

   var ifrmSrc = 'https://forms.guardian360.eu/guardian360bv/form/RequestaQuickscanaccount/formperma/OCmRQubppJrTZMWzGX_61hFmw8d8oVVwnktMCPgpUV4?zf_rszfm=1';


        try{
   if ( typeof ZFAdvLead != "undefined" && typeof zfutm_zfAdvLead != "undefined" ) {
    for( var prmIdx = 0 ; prmIdx < ZFAdvLead.utmPNameArr.length ; prmIdx ++ ) {
        var utmPm = ZFAdvLead.utmPNameArr[ prmIdx ];
        utmPm = ( ZFAdvLead.isSameDomian && ( ZFAdvLead.utmcustPNameArr.indexOf(utmPm) == -1 ) ) ? "zf_" + utmPm : utmPm;
        var utmVal = zfutm_zfAdvLead.zfautm_gC_enc( ZFAdvLead.utmPNameArr[ prmIdx ] );
        if ( typeof utmVal !== "undefined" ) {
          if ( utmVal != "" ) {
            if(ifrmSrc.indexOf('?') > 0){
                 ifrmSrc = ifrmSrc+'&'+utmPm+'='+utmVal;
            }else{
                ifrmSrc = ifrmSrc+'?'+utmPm+'='+utmVal;
            }
          }
        }
    }
   }
   if ( typeof ZFLead !== "undefined" && typeof zfutm_zfLead !== "undefined" ) {
    for( var prmIdx = 0 ; prmIdx < ZFLead.utmPNameArr.length ; prmIdx ++ ) {
           var utmPm = ZFLead.utmPNameArr[ prmIdx ];
           var utmVal = zfutm_zfLead.zfutm_gC_enc( ZFLead.utmPNameArr[ prmIdx ] );
           if ( typeof utmVal !== "undefined" ) {
             if ( utmVal != "" ){
               if(ifrmSrc.indexOf('?') > 0){
                 ifrmSrc = ifrmSrc+'&'+utmPm+'='+utmVal;//No I18N
               }else{
                 ifrmSrc = ifrmSrc+'?'+utmPm+'='+utmVal;//No I18N
               }
             }
           }
         }
   }
  }catch(e){}

  f.src = ifrmSrc;
  f.style.border="none";
  f.style.height="1009px";
  f.style.width="90%";
  f.style.transition="all 0.5s ease";
  f.setAttribute("aria-label", 'Request a Quickscan account');

  var d = document.getElementById("zf_div_OCmRQubppJrTZMWzGX_61hFmw8d8oVVwnktMCPgpUV4");
  if(d) {
    d.appendChild(f);
    window.addEventListener('message', function (){
     var evntData = event.data;
     if( evntData && evntData.constructor == String ){
      var zf_ifrm_data = evntData.split("|");
      if ( zf_ifrm_data.length == 2 || zf_ifrm_data.length == 3 ) {
       var zf_perma = zf_ifrm_data[0];
       var zf_ifrm_ht_nw = ( parseInt(zf_ifrm_data[1], 10) + 15 ) + "px";
       var iframe = document.getElementById("zf_div_OCmRQubppJrTZMWzGX_61hFmw8d8oVVwnktMCPgpUV4").getElementsByTagName("iframe")[0];
       if ( (iframe.src).indexOf('formperma') > 0 && (iframe.src).indexOf(zf_perma) > 0 ) {
        var prevIframeHeight = iframe.style.height;
        var zf_tout = false;
        if( zf_ifrm_data.length == 3 ) {
            iframe.scrollIntoView();
            zf_tout = true;
        }

        if ( prevIframeHeight != zf_ifrm_ht_nw ) {
         if( zf_tout ) {
             setTimeout(function(){
                 iframe.style.height = zf_ifrm_ht_nw;
             },500);
         } else {
             iframe.style.height = zf_ifrm_ht_nw;
         }
        }
       }
      }
     }
    }, false);
  }
    }catch(e){}
})();
</script>

<style>
.quickscan-account-request-container .card {
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 20px;
}

.quickscan-benefits h4 {
    margin: 0 0 8px 0;
    font-size: 14px;
}

.quickscan-benefits p {
    margin: 0;
    font-size: 13px;
    color: #646970;
}

.quickscan-timeline {
    font-size: 14px;
}

.quickscan-form-container {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    background: #fafafa;
}

@media (max-width: 768px) {
    .quickscan-benefits > div {
        grid-template-columns: 1fr !important;
    }
}
</style>