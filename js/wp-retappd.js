jQuery(document).ready(function($) {
  // check if the user is displaying the brewery
  if($('#retappd-display-brewery-yes').is(':checked')) { 
	$("#brewery_country").show();
  } else {
	$("#brewery_country").hide();
  }
  
  // show the brewery country
  $('#retappd-display-brewery-yes').click(function () {
    $("#brewery_country").show();
  });
  
  // hide the brewery country
  $('#retappd-display-brewery-no').click(function () {
	$("#brewery_country").hide();
  });
  
  // check if the user has enabled pagination
  if($('#retappd-display-pagination-yes').is(':checked')) { 
	$("#pagination_settings").show();
  } else {
	$("#pagination_settings").hide();
  }
  
  // show the pagination settings
  $('#retappd-display-pagination-yes').click(function () {
	$("#pagination_settings").show();
  });
  
  // hide the pagination settings
  $('#retappd-display-pagination-no').click(function () {
	$("#pagination_settings").hide();
  });
  
 
});