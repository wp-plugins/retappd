<?
$encoded_link_post	 	= $_REQUEST['PAYLOAD'];
$max_id = $_REQUEST['max_id'];
$decoded_link_post = urldecode(base64_decode($encoded_link_post));
parse_str($decoded_link_post);

$retappd_url	= "https://api.untappd.com/v4/user/checkins/$retappd_username?client_id=$client_id&client_secret=$client_secret&limit=$result_limit&max_id=$max_id";

	$retappd_contents = file_get_contents($retappd_url);

	$json_untappd = json_decode($retappd_contents);
	 

	$checkin_count		= $json_untappd->response->checkins->count;
	

	// Make Untappd API Request
	$retappd_url	= "https://api.untappd.com/v4/user/checkins/$retappd_username?client_id=$client_id&client_secret=$client_secret&max_id=$max_id&limit=$result_limit";
	


	$retappd_contents = file_get_contents($retappd_url);

	$json_untappd = json_decode($retappd_contents);
	 

	$uid 				= $json_untappd->response->checkins->items[0]->user->uid;

	$username			= $json_untappd->response->checkins->items[0]->user->user_name;

	$checkin_count		= $json_untappd->response->checkins->count;	

	// Get the Untappd Data that was returned in json

	if($limit <= $checkin_count) {

		$checkin_count = $limit;

	}

	

	for ($i = 0; $i < $checkin_count; $i++) {

		

		$date = date_create($json_untappd->response->checkins->items[$i]->created_at);

		$checkin_id = $json_untappd->response->checkins->items[$i]->checkin_id;
		
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

		
		// If the user wants to display their comment of the beer, display it

		if($retappd_display_my_comment == 'Y') {

			// Only display my comment if a comment was made
			if($my_comment) {
				$retappd_display .= "<b>My Comment</b>: $my_comment<br>";
			}

		}
		

		$retappd_display .= "</div>";

		

		$retappd_display .= "<hr>";

	}	
	
	echo $retappd_display;

?> 