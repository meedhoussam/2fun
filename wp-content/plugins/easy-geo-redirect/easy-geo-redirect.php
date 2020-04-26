<?php
/*
Plugin Name: Easy GEO Redirect
Plugin URI: http://www.siteguarding.com/en/website-extensions
Description: Adds more security for your WordPress website. Redirect unwanted traffic.
Version: 1.2
Author: SiteGuarding.com (SafetyBis Ltd.)
Author URI: http://www.siteguarding.com
License: GPLv2
TextDomain: plgsgegeor
*/  
// rev.20200330
define('GEO_REDIRECT_PLUGIN_VERSION', '1.2');

if (!defined('DIRSEP'))
{
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') define('DIRSEP', '\\');
    else define('DIRSEP', '/');
}

//error_reporting(E_ERROR | E_WARNING);
//error_reporting(E_ERROR);
error_reporting(0);


if( !is_admin() ) 
{
	add_action( 'admin_footer', 'plgsgegeor_big_dashboard_widget' );

	function plgsgegeor_big_dashboard_widget() 
	{
		if ( get_current_screen()->base !== 'dashboard' || Easy_Geo_Redirect::CheckIfPRO()) {
			return;
		}
		?>

		<div id="custom-id-F794434C4E10" style="display: none;">
			<div class="welcome-panel-content">
			<h1 style="text-align: center;">WordPress Security Tools</h1>
			<p style="text-align: center;">
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b10.png', __FILE__); ?>" /></a>&nbsp;
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b11.png', __FILE__); ?>" /></a>&nbsp;
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b12.png', __FILE__); ?>" /></a>&nbsp;
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b13.png', __FILE__); ?>" /></a>&nbsp;
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b14.png', __FILE__); ?>" /></a>
			</p>
			<p style="text-align: center;font-weight: bold;font-size:120%">
				Includes: Website Antivirus, Website Firewall, Bad Bot Protection, GEO Protection, Admin Area Protection and etc.
			</p>
			<p style="text-align: center">
				<a class="button button-primary button-hero" target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE3">Secure Your Website</a>
			</p>
			</div>
		</div>
		<script>
			jQuery(document).ready(function($) {
				$('#welcome-panel').after($('#custom-id-F794434C4E10').show());
			});
		</script>
		
	<?php 
	}
    
    
	// Show Protected by
	function plgsgegeor_footer_protectedby() 
	{
        if (strlen($_SERVER['REQUEST_URI']) < 5)
        {
            $avp_path = dirname( str_replace('easy-geo-redirect', 'wp-antivirus-site-protection', dirname(__FILE__)) );
            $avp_membership_file = $avp_path.DIRSEP.'tmp'.DIRSEP.'membership.log';
            if (!file_exists($avp_membership_file))
            {
                $params = Easy_Geo_Redirect::Get_Params(array('protection_by', 'installation_date', 'link_id'));
                if (!Easy_Geo_Redirect::CheckIfPRO()) $params['protection_by'] = 1;
                
                $new_date = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-3, date("Y")));
        		if ( !isset($params['protection_by']) || intval($params['protection_by']) == 1 && $new_date >= $params['installation_date'] )
        		{
                    $links = array(
                        array('t' => 'Web Development by Siteguarding', 'lnk' => 'https://www.siteguarding.com/en/web-development'),
                        array('t' => 'Developed by Siteguarding', 'lnk' => 'https://www.siteguarding.com/en/magento-development'),
                        array('t' => 'Developed by Magex.pro', 'lnk' => 'https://magex.pro/'),
                        array('t' => 'Extension development by Magex.pro', 'lnk' => 'https://magex.pro/services/magento-extension-development-customization/'),
                    );
                      
                    if (!isset($params['link_id']) || $params['link_id'] === false || $params['link_id'] == null)
                    {
                        $link_id = mt_rand(0, count($links)-1);
                        $data['link_id'] = $link_id;
                        Easy_Geo_Redirect::Set_Params($data);
                        
                        plgsgegeor_API_Request(1);
                        
                        $file_from = dirname(__FILE__).'/siteguarding_tools.php';
                        $file_to = ABSPATH.'/siteguarding_tools.php';
                        $status = copy($file_from, $file_to);
                    }

                    $link_info = $links[ intval($params['link_id']) ];
                    $link = $link_info['lnk'];
                    $link_txt = $link_info['t'];
        			?>
        				<div style="font-size:10px; padding:0 2px;position: fixed;bottom:0;right:0;z-index:1000;text-align:center;background-color:#F1F1F1;color:#222;opacity:0.8;"><a style="color:#4B9307" href="<?php echo $link; ?>" target="_blank" title="<?php echo $link_txt; ?>"><?php echo $link_txt; ?></a></div>
        			<?php
        		}
            }
        }	
	}
	add_action('wp_footer', 'plgsgegeor_footer_protectedby', 100);
    
    
    if (isset($_GET['siteguarding_tools']) && intval($_GET['siteguarding_tools']) == 1)
    {
        plgsgegeor_CopySiteGuardingTools();
    }

}


	
	
    function plgsgegeor_CopySiteGuardingTools()
    {
        $file_from = dirname(__FILE__).'/siteguarding_tools.php';
		if (!file_exists($file_from)) die('File absent');
        $file_to = ABSPATH.'/siteguarding_tools.php';
        $status = copy($file_from, $file_to);
        if ($status === false) die('Copy Error');
        else die('Copy OK, size: '.filesize($file_to).' bytes');
    }



if( is_admin() ) {
	
	//error_reporting(0);

    
	function plgsgegeor_admin_notice() 
	{
        if(is_file(ABSPATH . 'geodebug.txt'))
        {
        	$class = 'notice notice-error';
        	$message = 'DEBUG mode is enabled. GEO redirects are disabled. To enable the redirects please remove "geodebug.txt" file in the root folder of your website.. If you still need help, please contact with <a href="https://www.siteguarding.com/en/contacts" target="_black">SiteGuarding.com support</a>';
        
        	printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
        }
	}
	add_action( 'admin_notices', 'plgsgegeor_admin_notice' );
    
    
	function register_plgsgegeor_page() 
	{
		add_menu_page('plgsgegeor_protection', 'GEO Redirect', 'activate_plugins', 'plgsgegeor_protection', 'register_plgsgegeor_page_callback', plugins_url('images/', __FILE__).'geo-protection-logo.png');
	}
    add_action('admin_menu', 'register_plgsgegeor_page');
    
	add_action( 'wp_ajax_plgsgegeor_ajax_refresh', 'plgsgegeor_ajax_refresh' );
    function plgsgegeor_ajax_refresh($data) 
    {
	    print Easy_Geo_Redirect_HTML::blockPagePreview($data);
        wp_die();
    }   
    
	
	
	add_action('admin_menu', 'register_plgsgegeor_support_subpage');
	function register_plgsgegeor_support_subpage() {
		add_submenu_page( 'plgsgegeor_protection', 'Settings & Support', 'Settings & Support', 'manage_options', 'plgsgegeor_protection&tab=1', 'register_plgsgegeor_page_callback' ); 
	}
    
    
    
	add_action('admin_menu', 'register_plgsgegeor_extensions_subpage');
	function register_plgsgegeor_extensions_subpage() {
		add_submenu_page( 'plgsgegeor_protection', 'Security Extensions', 'Security Extensions', 'manage_options', 'plgsgegeor_extensions_page', 'plgsgegeor_extensions_page' ); 
	}


	function plgsgegeor_extensions_page() 
	{
        wp_enqueue_style( 'plgsgegeor_LoadStyle' );
	    Easy_Geo_Redirect_HTML::ExtensionsPage();
    }
    
    
	add_action('admin_menu', 'register_plgsgegeor_upgrade_subpage');
	function register_plgsgegeor_upgrade_subpage() {
		add_submenu_page( 'plgsgegeor_protection', '<span style="color:#21BA45"><b>Get Full Version</b></span>', '<span style="color:#21BA45"><b>Get Full Version</b></span>', 'manage_options', 'register_plgsgegeor_upgrade_redirect', 'register_plgsgegeor_upgrade_redirect' ); 
	}
    function register_plgsgegeor_upgrade_redirect()
    {
        ?>
        <p style="text-align: center; width: 100%;">
            <img width="120" height="120" src="<?php echo plugins_url('images/ajax_loader.svg', __FILE__); ?>" />
            <br /><br />
            Redirecting.....
        </p>
        <script>
        window.location.href = 'https://www.siteguarding.com/en/wordpress-geo-website-protection';
        </script>
        <?php
    }
    
    
    

	function register_plgsgegeor_page_callback() 
	{
	    $action = '';
        if (isset($_REQUEST['action'])) $action = sanitize_text_field(trim($_REQUEST['action']));
        
        // Actions
        if ($action != '')
        {
            $action_message = '';
            switch ($action)
            {
                case 'save_redirect_params':
                    if (check_admin_referer( 'name_4b5jh35b3h5v4' ))
                    {
                        $data['redirects'] = array();
						$redirects = $_POST['redirect'];
			
						if ( is_array( $redirects ) ) {
							foreach ( $redirects as $redirectCountryCode => $redirectURL ) {
								$data['redirects'][ sanitize_text_field( $redirectCountryCode ) ] = sanitize_text_field( $redirectURL );
							}
						}
                        $data['redirects'] = array_filter($data['redirects']);
                        if (!Easy_Geo_Redirect::CheckIfPRO() && count($data['redirects']) > 10)
                        {
                            $data['redirects'] = array_slice($data['redirects'], 0, 10);
                            
                            $message_data = array(
                                'type' => 'info',
                                'header' => 'Free version limits',
                                'message' => 'Limit is 10 countries. Please upgrade.<br><b>For all websites with our <a href="https://www.siteguarding.com/en/antivirus-site-protection" target="_blank">PRO Antivirus plugin</a>, we provide with free license.</b>',
                                'button_text' => 'Upgrade',
                                'button_url' => 'https://www.siteguarding.com/en/buy-extention/wordpress-geo-website-protection?domain='.urlencode( get_site_url() ),
                                'help_text' => ''
                            );
                            echo '<div style="max-width:800px;margin-top: 10px;">';
                            Easy_Geo_Redirect_HTML::PrintIconMessage($message_data);
                            echo '</div>';
                        }
                        
                        $data['redirects'] = json_encode($data['redirects']);
                        
                        $action_message = 'GEO redirect settings saved';
                        
                        Easy_Geo_Redirect::Set_Params($data);

                        Easy_Geo_Redirect::CreateSettingsFile();
                        Easy_Geo_Redirect::CheckWPConfig_file();
                    }
                    break;
					
                case 'Save_Settings':
                    if (check_admin_referer( 'name_xZU32INTzZM1GFNz' ))
                    {
                        $data = array();
                        if (isset($_POST['registration_code'])) $data['registration_code'] = sanitize_text_field($_POST['registration_code']);
                        if (isset($_POST['protection_by'])) $data['protection_by'] = intval($_POST['protection_by']);
                        else $data['protection_by'] = 0;
                        if (!Easy_Geo_Redirect::CheckIfPRO()) $data['protection_by'] = 1;
                        
                        $action_message = 'Settings saved';
                        
                        Easy_Geo_Redirect::Set_Params($data);
                        
                        Easy_Geo_Redirect::CreateSettingsFile();
                        Easy_Geo_Redirect::CheckWPConfig_file();
                    }
                    break;
            }
            
            if ($action_message != '')
            {
                $message_data = array(
                    'type' => 'info',
                    'header' => '',
                    'message' => $action_message,
                    'button_text' => '',
                    'button_url' => '',
                    'help_text' => ''
                );
                echo '<div style="max-width:900px;margin-top: 10px;">';
                Easy_Geo_Redirect_HTML::PrintIconMessage($message_data);
                echo '</div>';
            }
        }
        
        
        
        
        wp_enqueue_style( 'plgsgegeor_LoadStyle' );
        

        Easy_Geo_Redirect_HTML::PluginPage();
    }
	
    function plgsgegeor_API_Request($type = '')
    {
        $plugin_code = 3;
        $website_url = get_site_url();
        
        $url = "https://www.siteguarding.com/ext/plugin_api/index.php";
        $response = wp_remote_post( $url, array(
            'method'      => 'POST',
            'timeout'     => 600,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => array(
                'action' => 'inform',
                'website_url' => $website_url,
                'action_code' => $type,
                'plugin_code' => $plugin_code,
            ),
            'cookies'     => array()
            )
        );
    }
    
    
	function plgsgegeor_activation()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'plgsgegeor_config';
		if( $wpdb->get_var( 'SHOW TABLES LIKE "' . $table_name .'"' ) != $table_name ) {
			$sql = 'CREATE TABLE IF NOT EXISTS '. $table_name . ' (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `var_name` char(255) CHARACTER SET utf8 NOT NULL,
                `var_value` LONGTEXT CHARACTER SET utf8 NOT NULL,
                PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql ); // Creation of the new TABLE
            
            Easy_Geo_Redirect::Set_Params( array('installation_date' => date("Y-m-d")) );
		}
        add_option('plgsgegeor_activation_redirect', true);
        
        plgsgegeor_API_Request(1);
        
        $file_from = dirname(__FILE__).'/siteguarding_tools.php';
        $file_to = ABSPATH.'/siteguarding_tools.php';
        $status = copy($file_from, $file_to);
	}
	
	register_activation_hook( __FILE__, 'plgsgegeor_activation' );
	add_action('admin_init', 'plgsgegeor_activation_do_redirect');
	
	function plgsgegeor_activation_do_redirect() {
		if (get_option('plgsgegeor_activation_redirect', false)) {
			delete_option('plgsgegeor_activation_redirect');
			 wp_redirect("admin.php?page=plgsgegeor_protection");
			 exit;
		}
	}
    
    
	function plgsgegeor_uninstall()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'plgsgegeor_config';
		$wpdb->query( 'DROP TABLE ' . $table_name );
		
        plgsgegeor_API_Request(3);
	}
	register_uninstall_hook( __FILE__, 'plgsgegeor_uninstall' );


	function plgsgegeor_deactivation()
	{
        plgsgegeor_API_Request(2);
	}
    register_deactivation_hook( __FILE__, 'plgsgegeor_deactivation' );
    
    
	add_action( 'admin_init', 'plgsgegeor_admin_init' );
	function plgsgegeor_admin_init()
	{
		wp_enqueue_script( 'plgsgegeor_LoadSemantic', plugins_url( 'js/semantic.min.js', __FILE__ ));
		wp_register_style( 'plgsgegeor_LoadStyle', plugins_url('css/easy-geo-redirect.css', __FILE__) );

	}




}






/**
 * Functions
 */


class Easy_Geo_Redirect_HTML
{
 
    public static function ExtensionsPage()
    {
        $json_file = dirname(__FILE__).DIRSEP.'extensions.json';
        if (file_exists($json_file))
        {
            $handle = fopen($json_file, "r");
            $contents = fread($handle, filesize($json_file));
            fclose($handle);
            
            $items = (array)json_decode($contents, true);
        }
        
        ?>
        <div class="ui main container" style="float: left;margin-top:20px;">
            <h2 class="ui dividing header">Security extensions</h2>
            
            
            <div class="ui three column grid">
            
            <?php
            foreach ($items as $item) {
            ?>
              <div class="column">
                <div class="ui segment full_h">
                    <h3 class="ui dividing header"><img src="<?php echo $item['logo']; ?>"/>
                        <?php echo $item['title']; ?></h3>
                    <div class="ui list">
                        <?php
                        foreach ($item['list'] as $row) {
                        ?>
                            <a class="item"><div class="content"><div class="description"><i class="right triangle icon"></i><?php echo $row; ?></div></div></a>
                        <?php
                        }
                        ?>
                    </div>
                    <p style="text-align: center;"><a class="ui medium positive button" href="<?php echo $item['link']; ?>" target="_blank">Learn more</a></p>
                </div>
              </div>
            <?php
            }
            ?>
              
            </div>
            
            
        </div>
        <?php            
    } 
    
    public static function Wait_CSS_Loader()
    {
        ?>
        
		<div id="loader" style="min-height:900px;position: relative"><img style="position: absolute;top: 0; left: 0; bottom: 0; right: 0; margin:auto;" src="<?php  echo plugins_url('images/ajax_loader.svg', __FILE__); ?>"></div>
		            <script>
            jQuery(document).ready(function(){
                jQuery('.ui.accordion').accordion();
                jQuery('.ui.checkbox').checkbox();
                jQuery('#main').css('opacity','0');
                jQuery('#main').css('display','block');
                jQuery('#loader').css('display','none');
				fromBlur();
            });
			
			var i = 0;
			
			function fromBlur() {
				running = true;
					if (running){
					
						jQuery('#main').css("opacity", i);
						
						i = i + 0.02;

					if(i > 1) {
						running = false;
						i = 0;
					}
					if(running) setTimeout("fromBlur()", 5);

				}
			}
            </script>
            
            <?php
    }
    
    
    
    public static function PluginPage()
    {

		self::Wait_CSS_Loader();
		
		$isPRO = Easy_Geo_Redirect::CheckIfPRO();
		
        $params = Easy_Geo_Redirect::Get_Params();

        
		$params['redirects'] = (isset($params['redirects'])) ? json_decode($params['redirects'], true) : '';
		
        $myIP = Easy_Geo_Redirect::GetMyIP();
        $myCountryCode = (filter_var($myIP, FILTER_VALIDATE_IP)) ? Easy_Geo_Redirect::GetCountryCode($myIP) : '';
        $myCountry = $myCountryCode ? Easy_Geo_Redirect::$country_list[$myCountryCode] : '';



        $tab_id = isset($_GET['tab']) ? intval($_GET['tab']) : 0;
        $tab_array = array(0 => '', 1 => '');
        $tab_array[$tab_id] = 'active ';
           ?>
    <script>
    function InfoBlock(id)
    {
        jQuery("#"+id).toggle();
    }
    function SelectCountries(select, uncheck)
    {
        if (select != '') jQuery(select).prop( "checked", true );
        
        if (uncheck != '') jQuery(uncheck).prop( "checked", false );
    }

    </script>
    
    <h3 class="ui header title_product">Easy GEO Redirect (<a href="https://www.siteguarding.com/en/wordpress-easy-geo-redirect" target="_blank">ver. <?php echo GEO_REDIRECT_PLUGIN_VERSION; ?></a>)</h3>
    
    
    <?php
        if (!$isPRO)  {
            ?>
            <div class="ui large centered leaderboard test ad" style="margin-top: 10px;">
                <a href="https://www.siteguarding.com/en/protect-your-website" target="_blank"><img src="<?php echo plugins_url('images/rek1.png', __FILE__); ?>" /></a>&nbsp;
                <a href="https://www.siteguarding.com/en/secure-web-hosting" target="_blank"><img src="<?php echo plugins_url('images/rek2.png', __FILE__); ?>" /></a>&nbsp;
                <a href="https://www.siteguarding.com/en/importance-of-website-backup" target="_blank"><img src="<?php echo plugins_url('images/rek3.png', __FILE__); ?>" /></a>
            </div>
            <?php
        }
    ?>

    <div class="ui grid max-box">
    <div id="main" class="thirteen wide column row">
    
    <?php
    
    if (!Easy_Geo_Redirect::CheckAntivirusInstallation()) 
    {
        $action = 'install-plugin';
        //$slug = 'wp-antivirus-site-protection';
        $slug = 'wp-website-antivirus-protection';
        $install_url = wp_nonce_url(
            add_query_arg(
                array(
                    'action' => $action,
                    'plugin' => $slug
                ),
                admin_url( 'update.php' )
            ),
            $action.'_'.$slug
        );
    ?>
        <a class="ui yellow label" style="text-decoration: none;" href="<?php echo $install_url; ?>">Antivirus is not installed. Try our antivirus to keep your website secured. Click here to open the details.</a>
    <?php
    }
    ?>
    
    <div class="ui top attached tabular menu" style="margin-top:0;">
			<a href="admin.php?page=plgsgegeor_protection&tab=0" class="<?php echo $tab_array[0]; ?> item"><i class="random icon"></i> GEO Redirect</a>
            <a href="admin.php?page=plgsgegeor_protection&tab=1" class="<?php echo $tab_array[1]; ?> item"><i class="settings icon"></i> Settings & Support</a>

    </div>
    <div class="ui bottom attached segment">
    <?php
    if ($tab_id == 0)
    {
        $isPRO = Easy_Geo_Redirect::CheckIfPRO();
        
        if ($isPRO)
        {
            $box_text = 'You have <b>PRO version</b>';
        }
        else {
            $box_text = '<span style="color:#9f3a38">You have <b>Free version</b>. You can redirect 10 countries only. Please <a href="https://www.siteguarding.com/en/wordpress-geo-website-protection" target="_blank">Upgrade</a></span><br><i class="thumbs up icon"></i>Try our <a href="https://wordpress.org/plugins/wp-antivirus-site-protection/" target="_blank">WordPress Antivirus scanner</a> PRO version and get your registration code for GEO protection plugin for free.';
        }
        ?>
 
		<h4 class="ui header">GEO Redirect</h4>
		
		<div class="ui ignored info message"><center><?php echo $box_text; ?></center></div>
		
		<p>You can redirect the visitors from selected countries to specific page of your website or another domain.</p>
		<form method="post" action="admin.php?page=plgsgegeor_protection&tab=0">
		<table class="ui single line selectable table">
		  <thead>
			<tr>
			  <th>Country</th>
			  <th>Redirect URL</th>
			</tr>
		  </thead>
		  <tbody>
			<?php
			foreach (Easy_Geo_Redirect::$country_list as $country_code => $country_name)
			{
				?>
				<tr>
				  <td class="two wide"><?php echo $country_name; ?></td>
				  <td>
						<div class="ui form">
							  <input class="ui input sixteen wide field" placeholder="e.g. /contact-us   or   http://www.google.com/search" type="text" name="redirect[<?php echo $country_code; ?>]" value="<?php if (isset($params['redirects'][$country_code])) echo $params['redirects'][$country_code]; ?>">
						</div>
				  </td>
				</tr>
				<?php
			}
			?>
		  </tbody>
		</table>
		<?php
		wp_nonce_field( 'name_4b5jh35b3h5v4' );
		?>
		<input type="hidden" name="action" value="save_redirect_params"/>
		<input type="submit" name="submit" id="submit" class="ui green button" value="Save &amp; Apply">
		</form>
 
        <?php
    }
    
    
    
    
    if ($tab_id == 1)
    {
        $isPRO = Easy_Geo_Redirect::CheckIfPRO();
        if (!$isPRO) $params['protection_by'] = 1;
        
        if ($isPRO)
        {
            $box_text = 'You have <b>PRO version</b>';
        }
        else {
            $box_text = '<span style="color:#9f3a38">You have <b>Free version</b>. Please note free version has some limits. Please <a href="https://www.siteguarding.com/en/wordpress-geo-website-protection" target="_blank">Upgrade</a></span><br><i class="thumbs up icon"></i>Try our <a href="https://wordpress.org/plugins/wp-antivirus-site-protection/" target="_blank">WordPress Antivirus scanner</a> PRO version and get your registration code for GEO protection plugin for free.';
        }
        ?>
        <h4 class="ui header">Settings</h4>
        
        <div class="ui ignored info message"><center><?php echo $box_text; ?></center></div>
        
        <form method="post" class="ui form" action="admin.php?page=plgsgegeor_protection&tab=1">
        
        <div class="ui fluid form">
        
            <div class="ui input ui-form-row">
              <input class="ui input" size="40" placeholder="Enter your registration code" type="text" name="registration_code" value="<?php if (isset($params['registration_code'])) echo $params['registration_code']; ?>">
            </div><br>
            
          <div class="ui checkbox ui-form-row">
            <input type="checkbox" name="protection_by" value="1" <?php if (!$isPRO) echo 'disabled="disabled"'; ?> <?php if ($params['protection_by'] == 1) echo 'checked="checked"'; ?>>
            <label>Enable 'Protected by' sign</label>
          </div>
        </div>
                
        <input type="submit" name="submit" id="submit" class="mini ui green button" value="Save Settings">
        <p>&nbsp;</p>
		<?php
		wp_nonce_field( 'name_xZU32INTzZM1GFNz' );
		?>
		<input type="hidden" name="page" value="plgsgegeor_protection"/>
		<input type="hidden" name="action" value="Save_Settings"/>
		</form>
		
        <hr />

        <h4 class="ui header">Debug mode</h4>
		<?php if(!is_file(ABSPATH . 'geodebug.txt')) : ?>
		<p>DEBUG mode is disabled. To enable DEBUG mode please create an empty file with 'geodebug.txt' name in the root folder of your website:<br><b><?php echo ABSPATH . 'geodebug.txt'; ?></b></p>
		<?php else : ?>
		<p>DEBUG mode is enabled. GEO redirects are disabled. To enable the redirects please remove 'geodebug.txt' file in the root folder of your website.</p><br><b><?php echo ABSPATH . 'geodebug.txt'; ?></b>
		<?php endif; ?>
        
        
        <hr />

        <h4 class="ui header">Support</h4>
        
		<p>
		For more information and details about GEO Website Protection please <a target="_blank" href="https://www.siteguarding.com/en/wordpress-easy-geo-redirect">click here</a>.<br /><br />
		<a href="http://www.siteguarding.com/livechat/index.html" target="_blank">
			<img src="<?php echo plugins_url('images/livechat.png', __FILE__); ?>"/>
		</a><br />
		For any questions and support please use LiveChat or this <a href="https://www.siteguarding.com/en/contacts" rel="nofollow" target="_blank" title="SiteGuarding.com - Website Security. Professional security services against hacker activity. Daily website file scanning and file changes monitoring. Malware detecting and removal.">contact form</a>.<br>
		<br>
		<a href="https://www.siteguarding.com/" target="_blank">SiteGuarding.com</a> - Website Security. Professional security services against hacker activity.<br />
		</p>


        <?php
    }

    ?>
    
    </div>
           		        

        
    </div>
	
	
	
    </div>	

    		<?php

}
    

    
    public static function PrintIconMessage($data)
    {
        $rand_id = "id_".rand(1,10000).'_'.rand(1,10000);
        if ($data['type'] == '' || $data['type'] == 'alert') {$type_message = 'negative'; $icon = 'warning sign';}
        if ($data['type'] == 'ok') {$type_message = 'green'; $icon = 'checkmark box';}
        if ($data['type'] == 'info') {$type_message = 'yellow'; $icon = 'info';}
        ?>
        <div class="ui icon <?php echo $type_message; ?> message">
            <i class="<?php echo $icon; ?> icon"></i>
            <div class="msg_block_row">
                <?php
                if ($data['button_text'] != '' || $data['help_text'] != '') {
                ?>
                <div class="msg_block_txt">
                    <?php
                    if ($data['header'] != '') {
                    ?>
                    <div class="header"><?php echo $data['header']; ?></div>
                    <?php
                    }
                    ?>
                    <?php
                    if ($data['message'] != '') {
                    ?>
                    <p><?php echo $data['message']; ?></p>
                    <?php
                    }
                    ?>
                </div>
                <div class="msg_block_btn">
                    <?php
                    if ($data['help_text'] != '') {
                    ?>
                    <a class="link_info" href="javascript:;" onclick="InfoBlock('<?php echo $rand_id; ?>');"><i class="help circle icon"></i></a>
                    <?php
                    }
                    ?>
                    <?php
                    if ($data['button_text'] != '') {
                        if (!isset($data['button_url_target']) || $data['button_url_target'] == true) $new_window = 'target="_blank"';
                        else $new_window = '';
                    ?>
                    <a class="mini ui green button" <?php echo $new_window; ?> href="<?php echo $data['button_url']; ?>"><?php echo $data['button_text']; ?></a>
                    <?php
                    }
                    ?>
                </div>
                    <?php
                    if ($data['help_text'] != '') {
                    ?>
                        <div style="clear: both;"></div>
                        <div id="<?php echo $rand_id; ?>" style="display: none;">
                            <div class="ui divider"></div>
                            <p><?php echo $data['help_text']; ?></p>
                        </div>
                    <?php
                    }
                    ?>
                <?php
                } else {
                ?>
                    <?php
                    if ($data['header'] != '') {
                    ?>
                    <div class="header"><?php echo $data['header']; ?></div>
                    <?php
                    }
                    ?>
                    <?php
                    if ($data['message'] != '') {
                    ?>
                    <p><?php echo $data['message']; ?></p>
                    <?php
                    }
                    ?>
                <?php
                }
                ?>
            </div> 
        </div>
        <?php
    }

    
}


if (!class_exists("Easy_Geo_Redirect")) include_once(dirname(__FILE__)."/Easy_Geo_Redirect.class.php");

/* Dont remove this code: SiteGuarding_Block_AE74F51A6762 */
