<?php // Blackhole for Bad Bots - Blackhole Core

if (!defined('ABSPATH')) exit;

function blackhole_trigger() {
	
	$nonce = wp_create_nonce('blackhole_trigger');
	$href  = site_url('/?blackhole='. $nonce);
	$title = esc_attr__('Blackhole for Bad Bots', 'blackhole-bad-bots');
	$text  = esc_html__('Do NOT follow this link or you will be banned from the site!', 'blackhole-bad-bots');
	$link  = '<a rel="nofollow" style="display:none;" href="'. $href .'" title="'. $title .'">'. $text .'</a>' . "\n";
	
	echo apply_filters('blackhole_trigger', $link, $text, $title, $href, $nonce);
	
}

function blackhole_scanner() {
	
	$vars = blackhole_get_vars();
	
	if (blackhole_abort($vars)) return false;
	
	$verify = isset($_GET['blackhole']) ? wp_verify_nonce($_GET['blackhole'], 'blackhole_trigger') : false;
	
	$verify = apply_filters('blackhole_verify_nonce', $verify);
	
	if ($verify && isset($_GET['blackhole'])) {
		
		blackhole_disable_cache();
		
		if (blackhole_check_log($vars)) {
			
			blackhole_display_message();
			
		} else {
			
			blackhole_display_warning($vars);
			
		}
		
	} else {
		
		if (blackhole_check_log($vars)) {
			
			blackhole_disable_cache();
			
			blackhole_display_message();
			
		}
		
	}
	
	return false;
	
}

function blackhole_check_log($vars) {
	
	global $bbb_badbots;
	
	$bbb_badbots = (array) $bbb_badbots;
	
	list ($ip_address, $request_uri, $remote_host, $query_string, $user_agent, $referrer, $protocol, $method, $date) = $vars;
	
	$needle = apply_filters('blackhole_needle', 'ip_address', $vars, $bbb_badbots);
	
	if (!isset($bbb_badbots) || empty($bbb_badbots)) return false;
	
	if (!isset($needle) || empty($needle)) return false;
	
	foreach ($bbb_badbots as $bot) {
		
		$bot = (array) $bot;
		
		$haystack = isset($bot[$needle]) ? $bot[$needle] : '';
		
		$find = stripos($haystack, ${$needle});
		
		if ($find !== false) return true;
		
	}
	
	return false;
	
}

function blackhole_display_warning($vars) {
	
	blackhole_get_deps();
	
	list ($ip_address, $request_uri, $remote_host, $query_string, $user_agent, $referrer, $protocol, $method, $date) = $vars;
	
	$whois = blackhole_whois($ip_address);
	
	blackhole_template($ip_address, $remote_host, $date, $whois, $vars);
	
	if (blackhole_log_bot($vars)) blackhole_send_email($whois, $vars);
	
	exit;
	
}

function blackhole_log_bot($vars) {
	
	global $bbb_badbots;
	
	$bbb_badbots = (array) $bbb_badbots;
	
	list ($ip_address, $request_uri, $remote_host, $query_string, $user_agent, $referrer, $protocol, $method, $date) = $vars;
	
	$log = array(
		array(
			'ip_address'   => $ip_address,
			'request_uri'  => $request_uri,
			'remote_host'  => $remote_host,
			'query_string' => $query_string,
			'user_agent'   => $user_agent,
			'referrer'     => $referrer,
			'protocol'     => $protocol,
			'method'       => $method,
			'date'         => $date,
		)
	);
	
	$log = apply_filters('blackhole_log', $log, $vars);
	
	$bbb_badbots = array_merge($bbb_badbots, $log);
	
	$update = update_option('bbb_badbots', $bbb_badbots, true);
	
	return $update;
	
}