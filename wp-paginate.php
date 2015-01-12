<?
$encoded_link_post	 	= $_REQUEST['PAYLOAD'];
$max_id = $_REQUEST['max_id'];
$decoded_link_post = urldecode(base64_decode($encoded_link_post));
parse_str($decoded_link_post);
$result_limit = $result_limit + 1;
$retappd_url	= "https://api.untappd.com/v4/user/checkins/$retappd_username?client_id=$client_id&client_secret=$client_secret&limit=$limit&max_id=$max_id";
$retappd_contents = file_get_contents($retappd_url);
$json_untappd = json_decode($retappd_contents);
$max_id = $json_untappd->response->pagination->max_id;
echo $max_id;	
?>