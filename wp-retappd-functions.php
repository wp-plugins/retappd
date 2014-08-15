<?
// Log the request made by this domain
function retappd_log_request ($retappd_user_uri, $retappd_username) {
	$retappd_track_link = "retappd_user_uri=$retappd_user_uri&retappd_username=$retappd_username";
	$retappd_track_link_post = urlencode(base64_encode($retappd_track_link));
	$retappd_usage = urldecode(base64_decode('aHR0cDovL3d3dy5qYXlkZW5zaWJlcnQuY29tL3JldGFwcGQvdHJhY2stdXNhZ2UvP1BBWUxPQUQ9')) . $retappd_track_link_post;
	$track_retappd = file_get_contents($retappd_usage);
}

// Check the number of requests this domain has made today
function retappd_check_requests($retappd_user_uri) {
	$retappd_check_usage_link = "retappd_user_uri=$retappd_user_uri";
	$retappd_check_usage_link_post = urlencode(base64_encode($retappd_check_usage_link));
	$retappd_check_usage = base64_decode(urldecode('aHR0cDovL2pheWRlbnNpYmVydC5jb20vcmV0YXBwZC9jaGVjay11c2FnZS8%2FUEFZTE9BRD0%3D')) . $retappd_check_usage_link_post;
	$retappd_requests = urldecode(base64_decode(file_get_contents($retappd_check_usage)));
	return $retappd_requests;
}

// Get some global configuration settings for the plugin
function retappd_get_settings() {
	$retappd_user_uri = trim($_SERVER['SERVER_NAME']);
	$retappd_track_donation_link = "retappd_user_uri=$retappd_user_uri";
	$retappd_donation_post = urlencode(base64_encode($retappd_track_donation_link));
	$retappd_get_settings = base64_decode(urldecode('aHR0cDovL3d3dy5qYXlkZW5zaWJlcnQuY29tL3JldGFwcGQvc2V0dGluZ3MvP1BBWUxPQUQ9')) . $retappd_donation_post;
	$retappd_settings = base64_decode(urldecode(file_get_contents($retappd_get_settings)));
	return $retappd_settings;
}

// Get user settings
function retappd_get_user_info() {
	$json_retappd_settings   = json_decode(get_option('retappd_settings'));
	$retappd_username = trim($json_retappd_settings->retappd_username);
	$client_id = trim($json_retappd_settings->retappd_client_id);
	$client_secret = trim($json_retappd_settings->retappd_client_secret);
	$result_limit = $json_retappd_settings->retappd_display_limit;
	$retappd_display_beer_label = $json_retappd_settings->retappd_display_beer_label;
	$retappd_display_brewery = $json_retappd_settings->retappd_display_brewery;
	$retappd_display_brewery_country = $json_retappd_settings->retappd_display_brewery_country;
	$retappd_display_beer_style = $json_retappd_settings->retappd_display_beer_style;
	$retappd_display_venue = $json_retappd_settings->retappd_display_venue;
	$retappd_display_beer_abv = $json_retappd_settings->retappd_display_beer_abv;
	$retappd_display_first_had = $json_retappd_settings->retappd_display_first_had;
	$retappd_display_first_had_date_format = $json_retappd_settings->retappd_display_first_had_date_format;
	$retappd_display_my_rating = $json_retappd_settings->retappd_display_my_rating;
	$retappd_user_uri = $json_retappd_settings->retappd_user_uri;
return array ($retappd_username, $client_id, $client_secret, $result_limit, $retappd_display_beer_label, $retappd_display_brewery, $retappd_display_brewery_country, $retappd_display_venue, $retappd_display_beer_style, $retappd_display_beer_abv, $retappd_display_first_had, $retappd_display_first_had_date_format, $retappd_display_my_rating, $retappd_user_uri);
}

// Get the user settings, log the request, check the number of requests, get global configuration settings
function retappd() {
	
	// Enqueue some css
	wp_register_style( my_retappd_css, plugins_url('css/wp-retappd.css', __FILE__), false);
	wp_enqueue_style( my_retappd_css);
	
	// Get user settings
	list ($retappd_username, $client_id, $client_secret, $result_limit, $retappd_display_beer_label, $retappd_display_brewery, $retappd_display_brewery_country, $retappd_display_venue, $retappd_display_beer_style, $retappd_display_beer_abv, $retappd_display_first_had, $retappd_display_first_had_date_format, $retappd_display_my_rating, $retappd_user_uri) = retappd_get_user_info();
	
	// Log the request
	retappd_log_request($retappd_user_uri, $retappd_username);
	
	// Check the number of requests
	$retappd_requests = retappd_check_requests($retappd_user_uri);
	parse_str($retappd_requests);

	// Get some configuration settings
	$retappd_settings = retappd_get_settings();
	parse_str($retappd_settings);
	
	// Allow/Deny Request
	if($my_donation == 'Y') {
	  $output = retappd_user_info($retappd_username, $client_id, $client_secret, $result_limit, $retappd_display_beer_label, $retappd_display_brewery, $retappd_display_brewery_country, $retappd_display_venue, $retappd_display_beer_style, $retappd_display_beer_abv, $retappd_display_first_had, $retappd_display_first_had_date_format, $retappd_display_my_rating, $author_uri, $plugin_uri, $application_name);
	} else {
	  if($requests_made <= $requests_allowed) {
	    $output = retappd_user_info($retappd_username, $client_id, $client_secret, $result_limit, $retappd_display_beer_label, $retappd_display_brewery, $retappd_display_brewery_country, $retappd_display_venue, $retappd_display_beer_style, $retappd_display_beer_abv, $retappd_display_first_had, $retappd_display_first_had_date_format, $retappd_display_my_rating, $author_uri, $plugin_uri, $application_name);
	  } else {
	    $output = "$requests_exceeded_msg";
	  }
	}  
	return $output;
}

function retappd_user_info($retappd_username, $client_id, $client_secret, $result_limit, $retappd_display_beer_label, $retappd_display_brewery, $retappd_display_brewery_country, $retappd_display_venue, $retappd_display_beer_style, $retappd_display_beer_abv, $retappd_display_first_had, $retappd_display_first_had_date_format, $retappd_display_my_rating, $author_uri, $plugin_uri, $application_name) {
	
	// Make Untappd API Request
	$retappd_url	= "http://api.untappd.com/v4/user/checkins/$retappd_username?client_id=$client_id&client_secret=$client_secret&limit=$result_limit";
	$retappd_contents = file_get_contents($retappd_url);
	$json_untappd = json_decode($retappd_contents);
	 
	$uid 				= $json_untappd->response->checkins->items[0]->user->uid;
	$username			= $json_untappd->response->checkins->items[0]->user->user_name;
	$checkin_count		= $json_untappd->response->checkins->count;
	
	// Get the Untappd Data that was returned in json
	if($result_limit <= $checkin_count) {
		$checkin_count = $result_limit;
	}
	
	for ($i = 0; $i < $checkin_count; $i++) {
		
		$date = date_create($json_untappd->response->checkins->items[$i]->created_at);
		$first_had = date_format($date, $retappd_display_first_had_date_format);
		$my_rating			= $json_untappd->response->checkins->items[$i]->rating_score;
		$my_comment			= $json_untappd->response->checkins->items[$i]->checkin_comment;
	
		$bid				= $json_untappd->response->checkins->items[$i]->beer->bid;	
		$beer_name			= $json_untappd->response->checkins->items[$i]->beer->beer_name;
		$beer_label			= $json_untappd->response->checkins->items[$i]->beer->beer_label;
		$beer_abv			= $json_untappd->response->checkins->items[$i]->beer->beer_abv;
		$beer_style			= $json_untappd->response->checkins->items[$i]->beer->beer_style;
	
		$brewery_name		= $json_untappd->response->checkins->items[$i]->brewery->brewery_name;
		$brewery_country	= $json_untappd->response->checkins->items[$i]->brewery->country_name;
	
		$venue_name			= $json_untappd->response->checkins->items[$i]->venue->venue_name;
		$venue_id			= $json_untappd->response->checkins->items[$i]->venue->venue_id;
	
		$media_photo		= $json_untappd->response->checkins->items[$i]->media->items[0]->photo->photo_img_og;
	
		// If the user wants to display the beer label, display it
		if($retappd_display_beer_label == 'Y') {
			$retappd_display .= "<a href=\"http://untappd.com/beer/$bid\" target=\"_blank\"><img src=\"$beer_label\" class=\"alignleft\" width=\"100\" height=\"100\" alt=\"$beer_name - $beer_style - $brewery_name -   $brewery_country\"/></a>";
		}
		
		// If the user wants to display the venue, display it
		if($retappd_display_venue == 'Y') {
			$retappd_venue_name = "at <a href=\"http://untappd.com/venue/$venue_id\" title=\"$venue_name\" target=\"_blank\">$venue_name</a>";
		} else {
			$retappd_venue_name = "";
		}
		
		
		$retappd_display .= "<div style=\"display:block;overflow:hidden;\">";
		if($venue_id == "") {
			$retappd_display .= "<a href=\"http://untappd.com/beer/$bid\" title=\"$beer_name\" target=\"_blank\">$beer_name</a><br>";
		} else {
			$retappd_display .= "<a href=\"http://untappd.com/beer/$bid\" title=\"$beer_name\" target=\"_blank\">$beer_name</a> $retappd_venue_name<br>";
		}
		
		// If the user wants to display the brewery, display it
		if($retappd_display_brewery == 'Y') {
		
			// If the user wants to display the brewery country, only display it if they are displaying the brewery
			if($retappd_display_brewery_country == 'Y') {
				$retappd_brewery_country = ", $brewery_country";
			} else {
				$retappd_brewery_country = "";
			}
				
			$retappd_display .= "<b>Brewery</b>: " . str_replace('.', '', $brewery_name) . "$retappd_brewery_country<br>";
		}
		
		// If the user wants to display the beer style, display it
		if($retappd_display_beer_style == 'Y') {
			$retappd_display .= "<b>Style</b>: $beer_style<br>";
		}
		
		// If the user wants to display the beer abv, display it
		if($retappd_display_beer_abv == 'Y') {
			$retappd_display .= "<b>ABV</b>: $beer_abv<br>";
		}
		
		// If the user wants to display the the date they had the beer, display it
		if($retappd_display_first_had == 'Y') {
			$retappd_display .= "<b>First Had</b>: $first_had<br>";
		}
		
		// If the user wants to display the their rating of the beer, display it
		if($retappd_display_my_rating == 'Y') {
			$retappd_display .= "<b>My Rating</b>: $my_rating<br>";
		}
		
		$retappd_display .= "</div>";
		
		$retappd_display .= "<hr>";
	}	
	
	$retappd_display .= "<div class=\"retappd-credits\">Powered by <a href=\"http://www.untappd.com\" target=\"_blank\">Untappd</a> | Brought to you by <a href=\"$plugin_uri\" target=\"_blank\">$application_name for WordPress</a></div>";
	
	return $retappd_display;
}

// Add settings link on plugin page
function retappd_plugin_settings_link($retappd_links) { 
  // Get some configuration settings
  $retappd_settings = retappd_get_settings();
  parse_str($retappd_settings);
  $retappd_settings_link = '<a href="options-general.php?page=my-retappd-identifier" title="$application_name Settings">Settings</a>'; 
  array_unshift($retappd_links, $retappd_settings_link); 
  return $retappd_links; 
}

function my_retappd_menu() {
	// Get some configuration settings
	$retappd_settings = retappd_get_settings();
	parse_str($retappd_settings);
	
    // Create a menu under settings called Retappd
	add_options_page($appliction_name, $application_name, 'manage_options', 'my-retappd-identifier', 'my_retappd_options' );
}

function add_retappd_scripts() { 
	// Enqueue some javascript
	wp_register_script( my_retappd_script, plugins_url('js/wp-retappd.js', __FILE__), array('jquery'), false);
	wp_enqueue_script( my_retappd_script);
	
	// Enqueue some css
	wp_register_style( my_retappd_css, plugins_url('css/wp-admin-retappd.css', __FILE__), false);
	wp_enqueue_style( my_retappd_css);
}

// Create the Plugin Admin Settings Form
function my_retappd_options() {

	// Get user information
	list ($retappd_username, $client_id, $client_secret, $result_limit, $retappd_display_beer_label, $retappd_display_brewery, $retappd_display_brewery_country, $retappd_display_venue, $retappd_display_beer_style, $retappd_display_beer_abv, $retappd_display_first_had, $retappd_display_first_had_date_format, $retappd_display_my_rating, $retappd_user_uri) = retappd_get_user_info();

	// Get some configuration settings
	$retappd_settings = retappd_get_settings();
	parse_str($retappd_settings);

	// add javascript and css
	add_retappd_scripts();
	
	// Check the number of requests
	$retappd_requests = retappd_check_requests($retappd_user_uri);
	parse_str($retappd_requests);
	
	echo "<h2>$application_name Integration for WordPress</h2>";
	echo "<form method=\"post\" action=\"$retappd_current_page\">";
	echo '<div class="wrap" id="sm_div">
			<div id="poststuff" class="metabox-holder has-right-sidebar">
				<div id="post-body-content" class="has-sidebar-content">
					<div class="meta-box-sortabless">';
						
	
	$retappd_mode							= $_REQUEST["mode"];
	if($retappd_mode) {
		echo "<div class=\"updated\"><p><strong>$application_name Settings updated successfully.</strong></p></div>";
	}
	
	if($my_donation <> 'Y') {
	  if($requests_made >= $requests_allowed) {
	    echo "<div class=\"error\"><p>Too many requests made today. The <strong>$application_name</strong> plugin is a free plugin. However, you are limited to <strong>$requests_allowed</strong> requests per day. Each time your Retappd plugin is loaded and displayed, this counts as a request.<br><br>To remove the <strong>$requests_allowed</strong> requests per day limitation, consider making a small donation. " . '<a href="' . urldecode($donate_link) . '" target="_blank">Donate Now</a>' . "</p></div>";
	  }
	}
	
	// If the user has changed the values, get those values and save them, else get the current values
	if($retappd_mode == "modified") {
		
		$new_retappd_client_id				= trim($_REQUEST["new-retappd-client-id"]);
		$new_retappd_client_secret			= trim($_REQUEST["new-retappd-client-secret"]);
		$new_retappd_username				= trim($_REQUEST["new-retappd-username"]);
		
		$new_retappd_user_venue_mode		= trim($_REQUEST["new-retappd-user-venue-mode"]);
		if($new_retappd_user_venue_mode == "user") { 
			$retappd_user_venue_mode_yes_checked = 'checked'; 
			$retappd_user_venue_mode_no_checked = '';
		} else {
			$retappd_user_venue_mode_yes_checked = '';
			$retappd_user_venue_mode_no_checked = 'checked';			
		}
		
		$new_retappd_display_beer_label		= trim($_REQUEST["new-retappd-display-beer-label"]);
		if($new_retappd_display_beer_label == "Y") { 
			$retappd_display_beer_label_yes_checked = 'checked'; 
			$retappd_display_beer_label_no_checked = '';
		} else {
			$retappd_display_beer_label_yes_checked = '';
			$retappd_display_beer_label_no_checked = 'checked';			
		}
		
		$new_retappd_display_brewery		= trim($_REQUEST["new-retappd-display-brewery"]);
		if($new_retappd_display_brewery == "Y") { 
			$retappd_display_brewery_yes_checked = 'checked'; 
			$retappd_display_brewery_no_checked = '';
		} else {
			$retappd_display_brewery_yes_checked = '';
			$retappd_display_brewery_no_checked = 'checked';			
		}
		
		$new_retappd_display_brewery_country		= trim($_REQUEST["new-retappd-display-brewery-country"]);
		if($new_retappd_display_brewery_country == "Y") { 
			$retappd_display_brewery_country_yes_checked = 'checked'; 
			$retappd_display_brewery_country_no_checked = '';
		} else {
			$retappd_display_brewery_country_yes_checked = '';
			$retappd_display_brewery_country_no_checked = 'checked';			
		}
		
		$new_retappd_display_beer_style		= trim($_REQUEST["new-retappd-display-beer-style"]);
		if($new_retappd_display_beer_style == "Y") { 
			$retappd_display_beer_style_yes_checked = 'checked'; 
			$retappd_display_beer_style_no_checked = '';
		} else {
			$retappd_display_beer_style_yes_checked = '';
			$retappd_display_beer_style_no_checked = 'checked';			
		}
		
		$new_retappd_display_venue		= trim($_REQUEST["new-retappd-display-venue"]);
		if($new_retappd_display_venue == "Y") { 
			$retappd_display_venue_yes_checked = 'checked'; 
			$retappd_display_venue_no_checked = '';
		} else {
			$retappd_display_venue_yes_checked = '';
			$retappd_display_venue_no_checked = 'checked';			
		}
		
		$new_retappd_display_beer_abv		= trim($_REQUEST["new-retappd-display-beer-abv"]);
		if($new_retappd_display_beer_abv == "Y") { 
			$retappd_display_beer_abv_yes_checked = 'checked'; 
			$retappd_display_beer_abv_no_checked = '';
		} else {
			$retappd_display_beer_abv_yes_checked = '';
			$retappd_display_beer_abv_no_checked = 'checked';			
		}
		
		$new_retappd_display_first_had		= trim($_REQUEST["new-retappd-display-first-had"]);
		if($new_retappd_display_first_had == "Y") { 
			$retappd_display_first_had_yes_checked = 'checked'; 
			$retappd_display_first_had_no_checked = '';
		} else {
			$retappd_display_first_had_yes_checked = '';
			$retappd_display_first_had_no_checked = 'checked';			
		}
		
		$new_retappd_display_my_rating		= trim($_REQUEST["new-retappd-display-my-rating"]);
		if($new_retappd_display_my_rating == "Y") { 
			$retappd_display_my_rating_yes_checked = 'checked'; 
			$retappd_display_my_rating_no_checked = '';
		} else {
			$retappd_display_my_rating_yes_checked = '';
			$retappd_display_my_rating_no_checked = 'checked';			
		}
		
		$new_retappd_display_limit			= trim($_REQUEST["new-retappd-display-limit"]);
		if($new_retappd_display_limit == "") {
          $retappd_display_limit_25 = 'selected';
		} else if($new_retappd_display_limit == "5") { 
			$retappd_display_limit_5 = 'selected'; 
		} else if($new_retappd_display_limit == "10") { 
			$retappd_display_limit_10 = 'selected'; 
		} else if($new_retappd_display_limit == "15") { 
			$retappd_display_limit_15 = 'selected'; 
		} else if($new_retappd_display_limit == "20") { 
			$retappd_display_limit_20 = 'selected';
		} else if($new_retappd_display_limit == "25") { 
			$retappd_display_limit_25 = 'selected';
		} else if($new_retappd_display_limit == "30") { 
			$retappd_display_limit_30 = 'selected';
		} else if($new_retappd_display_limit == "35") { 
			$retappd_display_limit_35 = 'selected';
		} else if($new_retappd_display_limit == "40") { 
			$retappd_display_limit_40 = 'selected'; 
		} if($new_retappd_display_limit == "45") { 
			$retappd_display_limit_45 = 'selected';
		} else if($new_retappd_display_limit == "50") { 
			$retappd_display_limit_50 = 'selected';
		}
		
		$new_retappd_user_uri = trim($_SERVER['SERVER_NAME']);
		
		$new_retappd_display_first_had_date_format		= trim($_REQUEST["new-retappd-display-first-had-date-format"]);
		if($new_retappd_display_first_had_date_format == "F d, Y") { 
			$retappd_display_first_had_date_format_1_checked = 'checked'; 
			$retappd_display_first_had_date_format_2_checked = '';
			$retappd_display_first_had_date_format_3_checked = '';
			$retappd_display_first_had_date_format_4_checked = '';
		} else if($new_retappd_display_first_had_date_format == "Y/m/d") {
			$retappd_display_first_had_date_format_1_checked = ''; 
			$retappd_display_first_had_date_format_2_checked = 'checked';
			$retappd_display_first_had_date_format_3_checked = '';
			$retappd_display_first_had_date_format_4_checked = '';	
		} else if($new_retappd_display_first_had_date_format == "m/d/Y") {
			$retappd_display_first_had_date_format_1_checked = ''; 
			$retappd_display_first_had_date_format_2_checked = '';
			$retappd_display_first_had_date_format_3_checked = 'checked';
			$retappd_display_first_had_date_format_4_checked = '';
		} else if($new_retappd_display_first_had_date_format == "d/m/Y") {
			$retappd_display_first_had_date_format_1_checked = ''; 
			$retappd_display_first_had_date_format_2_checked = '';
			$retappd_display_first_had_date_format_3_checked = '';
			$retappd_display_first_had_date_format_4_checked = 'checked';
		} else {
			$retappd_display_first_had_date_format_1_checked = 'checked'; 
			$retappd_display_first_had_date_format_2_checked = '';
			$retappd_display_first_had_date_format_3_checked = '';
			$retappd_display_first_had_date_format_4_checked = '';
		}
		
	$retappd_update_settings = 	array(
								"retappd_client_id" 				=> $new_retappd_client_id, 
								"retappd_client_secret" 			=> $new_retappd_client_secret, 
								"retappd_username" 					=> $new_retappd_username,
								"retappd_user_venue_mode" 			=> $new_retappd_user_venue_mode, 
								"retappd_display_beer_label" 		=> $new_retappd_display_beer_label, 
								"retappd_display_brewery" 			=> $new_retappd_display_brewery,
								"retappd_display_brewery_country"	=> $new_retappd_display_brewery_country,
								"retappd_display_beer_style" 		=> $new_retappd_display_beer_style,
								"retappd_display_venue"				=> $new_retappd_display_venue,
								"retappd_display_beer_abv" 			=> $new_retappd_display_beer_abv,
								"retappd_display_first_had" 		=> $new_retappd_display_first_had,
								"retappd_display_my_rating" 		=> $new_retappd_display_my_rating,
								"retappd_display_limit" 			=> $new_retappd_display_limit, 
								"retappd_user_uri" 					=> $new_retappd_user_uri,
								"retappd_display_first_had_date_format" => $new_retappd_display_first_had_date_format
								);
	$json_retappd_update_settings = json_encode($retappd_update_settings);
	// var_dump($json_retappd_update_settings);
	update_option('retappd_settings', $json_retappd_update_settings);

	} else {

		$json_retappd_settings   = json_decode(get_option('retappd_settings'));
		// var_dump($json_retappd_update_settings);
		$new_retappd_client_id			= $json_retappd_settings->retappd_client_id;
		$new_retappd_client_secret		= $json_retappd_settings->retappd_client_secret;
		$new_retappd_username			= $json_retappd_settings->retappd_username;
		
		$new_retappd_user_venue_mode	= $json_retappd_settings->retappd_user_venue_mode;
		if($new_retappd_user_venue_mdoe == "user") { 
			$retappd_user_venue_mode_yes_checked = 'checked'; 
			$retappd_user_venue_mode_no_checked = '';
		} else if($new_retappd_user_venue_mdoe == "venue") {
			$retappd_user_venue_mode_yes_checked = '';
			$retappd_user_venue_mode_no_checked = 'checked';			
		} else {
			$retappd_user_venue_mode_yes_checked = 'checked'; 
			$retappd_user_venue_mode_no_checked = '';
		}
		
		$new_retappd_display_beer_label	= $json_retappd_settings->retappd_display_beer_label;
		if($new_retappd_display_beer_label == "Y") { 
			$retappd_display_beer_label_yes_checked = 'checked'; 
			$retappd_display_beer_label_no_checked = '';
		} else if($new_retappd_display_beer_label == "N") {
			$retappd_display_beer_label_yes_checked = '';
			$retappd_display_beer_label_no_checked = 'checked';			
		} else {
			$retappd_display_beer_label_yes_checked = 'checked'; 
			$retappd_display_beer_label_no_checked = '';
		}
		
		$new_retappd_display_brewery	= $json_retappd_settings->retappd_display_brewery;
		if($new_retappd_display_brewery == "Y") { 
			$retappd_display_brewery_yes_checked = 'checked'; 
			$retappd_display_brewery_no_checked = '';
		} else if($new_retappd_display_brewery == "N") {
			$retappd_display_brewery_yes_checked = '';
			$retappd_display_brewery_no_checked = 'checked';			
		} else {
			$retappd_display_brewery_yes_checked = 'checked'; 
			$retappd_display_brewery_no_checked = '';
		}
		
		$new_retappd_display_brewery_country	= $json_retappd_settings->retappd_display_brewery_country;
		if($new_retappd_display_brewery_country == "Y") { 
			$retappd_display_brewery_country_yes_checked = 'checked'; 
			$retappd_display_brewery_country_no_checked = '';
		} else if($new_retappd_display_brewery_country == "N") {
			$retappd_display_brewery_country_yes_checked = '';
			$retappd_display_brewery_country_no_checked = 'checked';			
		} else {
			$retappd_display_brewery_country_yes_checked = 'checked'; 
			$retappd_display_brewery_country_no_checked = '';
		}
		
		$new_retappd_display_beer_style	= $json_retappd_settings->retappd_display_beer_style;
		if($new_retappd_display_beer_style == "Y") { 
			$retappd_display_beer_style_yes_checked = 'checked'; 
			$retappd_display_beer_style_no_checked = '';
		} else if($new_retappd_display_beer_style == "N") {
			$retappd_display_beer_style_yes_checked = '';
			$retappd_display_beer_style_no_checked = 'checked';			
		} else {
			$retappd_display_beer_style_yes_checked = 'checked'; 
			$retappd_display_beer_style_no_checked = '';
		}
		
		$new_retappd_display_venue	= $json_retappd_settings->retappd_display_venue;
		if($new_retappd_display_venue == "Y") { 
			$retappd_display_venue_yes_checked = 'checked'; 
			$retappd_display_venue_no_checked = '';
		} else if($new_retappd_display_venue == "N") {
			$retappd_display_venue_yes_checked = '';
			$retappd_display_venue_no_checked = 'checked';			
		} else {
			$retappd_display_venue_yes_checked = 'checked'; 
			$retappd_display_venue_no_checked = '';
		}
		
		
		$new_retappd_display_beer_abv	= $json_retappd_settings->retappd_display_beer_abv;
		if($new_retappd_display_beer_abv == "Y") { 
			$retappd_display_beer_abv_yes_checked = 'checked'; 
			$retappd_display_beer_abv_no_checked = '';
		} else if($new_retappd_display_beer_abv == "N") {
			$retappd_display_beer_abv_yes_checked = '';
			$retappd_display_beer_abv_no_checked = 'checked';			
		} else {
			$retappd_display_beer_abv_yes_checked = 'checked'; 
			$retappd_display_beer_abv_no_checked = '';
		}
		
		$new_retappd_display_first_had	= $json_retappd_settings->retappd_display_first_had;
		if($new_retappd_display_first_had == "Y") { 
			$retappd_display_first_had_yes_checked = 'checked'; 
			$retappd_display_first_had_no_checked = '';
		} else if($new_retappd_display_first_had == "N") {
			$retappd_display_first_had_yes_checked = '';
			$retappd_display_first_had_no_checked = 'checked';			
		} else {
			$retappd_display_first_had_yes_checked = 'checked'; 
			$retappd_display_first_had_no_checked = '';
		}
		
		$new_retappd_display_my_rating	= $json_retappd_settings->retappd_display_my_rating;
		if($new_retappd_display_my_rating == "Y") { 
			$retappd_display_my_rating_yes_checked = 'checked'; 
			$retappd_display_my_rating_no_checked = '';
		} else if($new_retappd_display_my_rating == "N") {
			$retappd_display_my_rating_yes_checked = '';
			$retappd_display_my_rating_no_checked = 'checked';			
		} else {
			$retappd_display_my_rating_yes_checked = 'checked'; 
			$retappd_display_my_rating_no_checked = '';
		}
		
		$new_retappd_display_limit	= $json_retappd_settings->retappd_display_limit;
		if($new_retappd_display_limit == "") {
          $retappd_display_limit_25 = 'selected';
		} else if($new_retappd_display_limit == "5") { 
			$retappd_display_limit_5 = 'selected'; 
		} else if($new_retappd_display_limit == "10") { 
			$retappd_display_limit_10 = 'selected'; 
		} else if($new_retappd_display_limit == "15") { 
			$retappd_display_limit_15 = 'selected'; 
		} else if($new_retappd_display_limit == "20") { 
			$retappd_display_limit_20 = 'selected';
		} else if($new_retappd_display_limit == "25") { 
			$retappd_display_limit_25 = 'selected';
		} else if($new_retappd_display_limit == "30") { 
			$retappd_display_limit_30 = 'selected';
		} else if($new_retappd_display_limit == "35") { 
			$retappd_display_limit_35 = 'selected';
		} else if($new_retappd_display_limit == "40") { 
			$retappd_display_limit_40 = 'selected'; 
		} if($new_retappd_display_limit == "45") { 
			$retappd_display_limit_45 = 'selected';
		} else if($new_retappd_display_limit == "50") { 
			$retappd_display_limit_50 = 'selected';
		}
		
		$new_retappd_display_first_had_date_format	= $json_retappd_settings->retappd_display_first_had_date_format;
		if($new_retappd_display_first_had_date_format == "F d, Y") { 
			$retappd_display_first_had_date_format_1_checked = 'checked'; 
			$retappd_display_first_had_date_format_2_checked = '';
			$retappd_display_first_had_date_format_3_checked = '';
			$retappd_display_first_had_date_format_4_checked = '';
		} else if($new_retappd_display_first_had_date_format == "Y/m/d") {
			$retappd_display_first_had_date_format_1_checked = ''; 
			$retappd_display_first_had_date_format_2_checked = 'checked';
			$retappd_display_first_had_date_format_3_checked = '';
			$retappd_display_first_had_date_format_4_checked = '';	
		} else if($new_retappd_display_first_had_date_format == "m/d/Y") {
			$retappd_display_first_had_date_format_1_checked = ''; 
			$retappd_display_first_had_date_format_2_checked = '';
			$retappd_display_first_had_date_format_3_checked = 'checked';
			$retappd_display_first_had_date_format_4_checked = '';
		} else if($new_retappd_display_first_had_date_format == "d/m/Y") {
			$retappd_display_first_had_date_format_1_checked = ''; 
			$retappd_display_first_had_date_format_2_checked = '';
			$retappd_display_first_had_date_format_3_checked = '';
			$retappd_display_first_had_date_format_4_checked = 'checked';
		} else {
			$retappd_display_first_had_date_format_1_checked = 'checked'; 
			$retappd_display_first_had_date_format_2_checked = '';
			$retappd_display_first_had_date_format_3_checked = '';
			$retappd_display_first_had_date_format_4_checked = '';
		}
		
		$new_retappd_user_uri = trim($_SERVER['SERVER_NAME']);
		$retappd_update_settings = 	array(
								"retappd_client_id" 				=> $new_retappd_client_id, 
								"retappd_client_secret" 			=> $new_retappd_client_secret, 
								"retappd_username" 					=> $new_retappd_username,
								"retappd_user_venue_mode" 			=> $new_retappd_user_venue_mode, 
								"retappd_display_beer_label" 		=> $new_retappd_display_beer_label, 
								"retappd_display_brewery" 			=> $new_retappd_display_brewery,
								"retappd_display_brewery_country"	=> $new_retappd_display_brewery_country,
								"retappd_display_beer_style" 		=> $new_retappd_display_beer_style, 
								"retappd_display_venue"				=> $new_retappd_display_venue,
								"retappd_display_beer_abv" 			=> $new_retappd_display_beer_abv,
								"retappd_display_first_had" 		=> $new_retappd_display_first_had,
								"retappd_display_my_rating" 		=> $new_retappd_display_my_rating,
								"retappd_display_limit" 			=> $new_retappd_display_limit, 
								"retappd_user_uri" 					=> $new_retappd_user_uri,
								"retappd_display_first_had_date_format" => $new_retappd_display_first_had_date_format
								);
	$json_retappd_update_settings = json_encode($retappd_update_settings);
	//var_dump($json_retappd_update_settings);
	update_option('retappd_settings', $json_retappd_update_settings);
	}
	
	// format the date that was chosen
	$date_format_1 = date('F d, Y');
	$date_format_2 = date('Y/m/d');
	$date_format_3 = date('m/d/Y');
	$date_format_4 = date('d/m/Y');
	
	// Get some configuration settings
	$retappd_settings = retappd_get_settings();
	parse_str($retappd_settings);
	
	// var_dump($retappd_settings);
	
	echo	"<div id=\"sm_pnres\" class=\"postbox\">
				<h3 class=\"hndle\"><span>Step 1 - $application_name Configurations:</span></h3>
					<div class=\"inside\">
						<div><p>To find your Untappd API Client ID and Client Secret, log in to the Untappd API Dashboard <a href=\"https://untappd.com/api/dashboard\" target=\"_blank\">https://untappd.com/api/dashboard</a>. At the top of the page, you should see your Untappd API Client ID and Client Secret. If you do not already have an API Client ID and Secret, you will need to apply for one first. You can apply for a API keys here: <a href=\"https://untappd.com/api/dashboard\" target=\"_blank\">https://untappd.com/api/dashboard</a>. This can take a few weeks to get approval.</p>
						
						<p><strong>Untappd API Client ID</strong></p><p><input type=\"text\" id=\"api-keys\" name=\"new-retappd-client-id\" value=\"$new_retappd_client_id\"/></p><p><strong>Untappd API Client Secret</strong></p><p><input type=\"text\" id=\"api-keys\" name=\"new-retappd-client-secret\" value=\"$new_retappd_client_secret\"/></p>
						
						<hr></div>
						
						<div><p>To find your Untappd Username, log in to the Untappd website <a href=\"http://untappd.com\" target=\"_blank\">http://www.untappd.com</a>. At the top of the page, hover over your profile icon (to the left of the search bar) and click Account Settings. You will see your Username displayed here. Use this value in the above field. Example: https://untappd.com/user/<strong>username</strong></p>
						
						<p><strong>Untappd Username</strong></p><p><input type=\"text\" id=\"settings-field\" name=\"new-retappd-username\" value=\"$new_retappd_username\"/></p>
						
						<hr></div>
						
						<div><p>You can easily choose which pieces of Untappd information that you would like displayed</p>
						<p><strong>Display Beer Label</strong></p>
						<p><input type=\"radio\" name=\"new-retappd-display-beer-label\" value=\"Y\" $retappd_display_beer_label_yes_checked>Yes
						<input class=\"retappd-radio\" type=\"radio\" name=\"new-retappd-display-beer-label\" value=\"N\" $retappd_display_beer_label_no_checked>No</p>
						
						<hr></div>
						
						<div><p><strong>Display Brewery</strong></p>
						<p><input type=\"radio\" id=\"retappd-display-brewery-yes\" name=\"new-retappd-display-brewery\" value=\"Y\" $retappd_display_brewery_yes_checked>Yes 
						<input class=\"retappd-radio\" type=\"radio\" id=\"retappd-display-brewery-no\" name=\"new-retappd-display-brewery\" value=\"N\" $retappd_display_brewery_no_checked>No</p>
						
						<hr></div>
						
						<div id=\"brewery_country\"><p><strong>Display The Country of the Brewery</strong></p>
						<p><input type=\"radio\" name=\"new-retappd-display-brewery-country\" value=\"Y\" $retappd_display_brewery_country_yes_checked>Yes
						<input class=\"retappd-radio\" type=\"radio\" name=\"new-retappd-display-brewery-country\" value=\"N\" $retappd_display_brewery_country_no_checked>No</p>
						
						<hr></div>
						
						<div><p><strong>Display Beer Style</strong></p>
						<p><input type=\"radio\" name=\"new-retappd-display-beer-style\" value=\"Y\" $retappd_display_beer_style_yes_checked>Yes
						<input class=\"retappd-radio\" type=\"radio\" name=\"new-retappd-display-beer-style\" value=\"N\" $retappd_display_beer_style_no_checked>No</p>
						
						<hr></div>
						
						<div><p><strong>Display The Venue Where You Had The Beer</strong></p>
						<p><input type=\"radio\" name=\"new-retappd-display-venue\" value=\"Y\" $retappd_display_venue_yes_checked>Yes
						<input  class=\"retappd-radio\" type=\"radio\" name=\"new-retappd-display-venue\" value=\"N\" $retappd_display_venue_no_checked>No</p>
						
						<hr></div>
						
						<div><p><strong>Display Beer ABV</strong></p>
						<p><input type=\"radio\" name=\"new-retappd-display-beer-abv\" value=\"Y\" $retappd_display_beer_abv_yes_checked>Yes
						<input class=\"retappd-radio\" type=\"radio\" name=\"new-retappd-display-beer-abv\" value=\"N\" $retappd_display_beer_abv_no_checked>No</p>
						
						<hr></div>
						
						<div><p><strong>Display The Date You Had This Beer</strong></p>
						<p><input type=\"radio\" name=\"new-retappd-display-first-had\" value=\"Y\" $retappd_display_first_had_yes_checked>Yes
						<input class=\"retappd-radio\" type=\"radio\" name=\"new-retappd-display-first-had\" value=\"N\" $retappd_display_first_had_no_checked>No</p>
						<p><strong>Date Format</strong></p>
						<p>
						<input type=\"radio\" name=\"new-retappd-display-first-had-date-format\" value=\"F d, Y\" $retappd_display_first_had_date_format_1_checked> $date_format_1
						<input class=\"retappd-radio\" type=\"radio\" name=\"new-retappd-display-first-had-date-format\" value=\"Y/m/d\" $retappd_display_first_had_date_format_2_checked> $date_format_2
						<input class=\"retappd-radio\" type=\"radio\" name=\"new-retappd-display-first-had-date-format\" value=\"m/d/Y\" $retappd_display_first_had_date_format_3_checked> $date_format_3
						<input class=\"retappd-radio\" type=\"radio\" name=\"new-retappd-display-first-had-date-format\" value=\"d/m/Y\" $retappd_display_first_had_date_format_4_checked> $date_format_4
						
						</p>
						
						<hr></div>
						
						<div><p><strong>Display Your Beer Rating</strong></p>
						<p><input type=\"radio\" name=\"new-retappd-display-my-rating\" value=\"Y\" $retappd_display_my_rating_yes_checked>Yes
						<input class=\"retappd-radio\" type=\"radio\" name=\"new-retappd-display-my-rating\" value=\"N\" $retappd_display_my_rating_no_checked>No</p>
						
						<hr></div>
						
						<div><p><strong>Number of Checkins to Display</strong></p>
						<p><select name=\"new-retappd-display-limit\">
						<option value=\"5\" $retappd_display_limit_5>5</option>
						<option value=\"10\" $retappd_display_limit_10>10</option>
						<option value=\"15\" $retappd_display_limit_15>15</option>
						<option value=\"20\" $retappd_display_limit_20>20</option>
						<option value=\"25\" $retappd_display_limit_25>25</option>
						<option value=\"30\" $retappd_display_limit_30>30</option>
						<option value=\"35\" $retappd_display_limit_35>35</option>
						<option value=\"40\" $retappd_display_limit_40>40</option>
						<option value=\"45\" $retappd_display_limit_45>45</option>
						<option value=\"50\" $retappd_display_limit_50>50</option>
						</select></p>
						
						<hr></div>
						
						<p><input type=\"submit\" class=\"button-primary\" value=\"Save $application_name Settings\"/></p>
						
						<input type=\"hidden\" name=\"mode\" value=\"modified\"/>
						
					</div>
				</div>";
	
	echo 	"<div id=\"sm_pnres\" class=\"postbox\">
				<h3 class=\"hndle\"><span>Step 2 - Shortcode:</span></h3>
					<div class=\"inside\">
						<p>Place this shortcode on any post or page that you would like the Untappd feed to be displayed [retappd]</p>
					</div>
				</div>";
					 
	 echo "</form>";
	 echo "</div></div></div>";
	
}
?>