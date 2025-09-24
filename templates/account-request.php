<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <!-- Official Guardian360 Quickscan Branding Header -->
    <div style="text-align: center; margin-bottom: 30px; padding: 20px 0; border-bottom: 2px solid #e5e5e5;">
        <div style="margin-bottom: 15px;">
            <img src="<?php echo QUICKSCAN_PLUGIN_URL; ?>assets/images/logo_guardian360_quickscan.png"
                 alt="Guardian360 Quickscan"
                 style="height: 60px; width: auto;" />
        </div>
        <div>
            <h1 style="margin: 0 0 5px 0; font-size: 28px; color: #2E3285;"><?php _e('Request Quickscan Account Access', 'quickscan-connector'); ?></h1>
        </div>
    </div>

    <!-- Centered Content Container -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">

        <!-- Responsive Layout CSS -->
        <style>
        .quickscan-account-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
            margin: 20px 0;
        }

        @media (min-width: 1024px) {
            .quickscan-account-grid {
                grid-template-columns: 2fr 1fr;
                gap: 40px;
            }
        }

        @media (min-width: 768px) and (max-width: 1023px) {
            .quickscan-account-grid {
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }
        }
        </style>
        <div class="card" style="max-width:100%;">
                      <div class="quickscan-benefits" style="background: #f9f9f9; padding: 15px; border-left: 4px solid #00a0d2; margin: 20px 0;">
                <h3><?php _e('Benefits of Your Quickscan Account', 'quickscan-connector'); ?></h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-top: 10px;">
                    <div>
                        <h4><?php _e('👥 User Activity Tracking', 'quickscan-connector'); ?></h4>
                        <p><?php _e('View a list of all users who have requested PDF reports from your site', 'quickscan-connector'); ?></p>
                    </div>
                    <div>
                        <h4><?php _e('🔒 Secure Dashboard Access', 'quickscan-connector'); ?></h4>
                        <p><?php _e('Access scan results through a secure, authenticated environment', 'quickscan-connector'); ?></p>
                    </div>
                    <div>
                        <h4><?php _e('🎨 White-Label Reports', 'quickscan-connector'); ?></h4>
                        <p><?php _e('Customize PDF reports with your own branding and company information', 'quickscan-connector'); ?></p>
                    </div>
                    <div>
                        <h4><?php _e('⚙️ Administrative Controls', 'quickscan-connector'); ?></h4>
                        <p><?php _e('Control PDF report delivery and manage scanning functionality', 'quickscan-connector'); ?></p>
                    </div>
                </div>
            </div>
      </div>
        <div class="quickscan-account-grid">
            <!-- Main Information Section -->
            <div>
                <div class="card" style="margin: 0 0 20px 0; width:100%;max-width:100%;">
            <h2><?php _e('Professional Security Scanning Platform', 'quickscan-connector'); ?></h2>

            <div class="notice notice-info inline">
                <p><strong><?php _e('Quality Assurance Process', 'quickscan-connector'); ?></strong></p>
                <p><?php _e('To ensure the highest quality of service and maintain the integrity of our security scanning platform, we personally review each account request.', 'quickscan-connector'); ?></p>
            </div>
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


            </div>

            <!-- Sidebar Section -->
            <div>
              
                      <!-- Timeline Section -->
        <div class="card" style="margin: 20px 0; max-width: 800px;">
            <h3><?php _e('Account Approval Process', 'quickscan-connector'); ?></h3>
            <div class="quickscan-timeline" style="margin: 20px 0;">
                <div style="display: flex; align-items: center; margin: 10px 0;padding-left:50px;">
                    <div style="background: #00a0d2; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; left:15px; position:absolute;">1</div>
                    <div>
                        <strong><?php _e('Submit Request', 'quickscan-connector'); ?></strong> - <?php _e('Complete the form below with your details', 'quickscan-connector'); ?>
                    </div>
                </div>
                <div style="display: flex; align-items: center; margin: 10px 0;padding-left:50px;">
                    <div style="background: #00a0d2; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; left:15px; position:absolute;">2</div>
                    <div>
                        <strong><?php _e('Review Process', 'quickscan-connector'); ?></strong> - <?php _e('Our team reviews your request (typically within 24 hours)', 'quickscan-connector'); ?>
                    </div>
                </div>
                <div style="display: flex; align-items: center; margin: 10px 0;padding-left:50px;">
                    <div style="background: #00a0d2; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; left:15px; position:absolute;">3</div>
                    <div>
                        <strong><?php _e('Account Activation', 'quickscan-connector'); ?></strong> - <?php _e('Receive your credentials and welcome information via email', 'quickscan-connector'); ?>
                    </div>
                </div>
                <div style="display: flex; align-items: center; margin: 10px 0;padding-left:50px;">
                    <div style="background: #46b450; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; left:15px; position:absolute;">✓</div>
                    <div>
                        <strong><?php _e('Access Pro Features', 'quickscan-connector'); ?></strong> - <?php _e('Configure credentials in plugin settings to unlock administrative controls and white-label features', 'quickscan-connector'); ?>
                    </div>
                </div>
            </div>
        </div>

                <!-- Resources -->
                <div class="card" style="margin: 0 0 20px 0;">
                    <h3><?php _e('Resources', 'quickscan-connector'); ?></h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin: 10px 0;"><a href="https://guardian360.eu/quickscan" target="_blank">🌐 <?php _e('Quickscan Platform', 'quickscan-connector'); ?></a></li>
                        <li style="margin: 10px 0;"><a href="https://github.com/guardian360/quickscan-wp-plugin" target="_blank">📚 <?php _e('Documentation', 'quickscan-connector'); ?></a></li>
                        <li style="margin: 10px 0;"><a href="https://guardian360.nl/privacy" target="_blank">🔏 <?php _e('Privacy Policy', 'quickscan-connector'); ?></a></li>
                        <li style="margin: 10px 0;"><a href="https://quickscan.guardian360.nl/terms" target="_blank">📋 <?php _e('Terms of Service', 'quickscan-connector'); ?></a></li>
                    </ul>
                </div>

                <!-- Need Help Section -->
                <div class="card" style="margin: 0;">
                    <h3><?php _e('Need Help?', 'quickscan-connector'); ?></h3>
                    <div class="help-item">
                        <h4>📧 <?php _e('Email Support', 'quickscan-connector'); ?></h4>
                        <p><?php _e('Questions about account setup or scanning features?', 'quickscan-connector'); ?></p>
                        <p><a href="mailto:support@guardian360.nl" class="button button-secondary"><?php _e('Contact Support', 'quickscan-connector'); ?></a></p>
                    </div>
                </div>
            </div>


        </div> <!-- End Account Grid -->
    </div> <!-- End Centered Content Container -->
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
  f.style.width="100%";
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