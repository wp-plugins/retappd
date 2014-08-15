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
});