 var $jquey = jQuery.noConflict();

$jquey(function () {
     // start the ticker 
	$jquey('#js-news').ticker();
	
	// hide the release history when the page loads
	$jquey('#release-wrapper').css('margin-top', '-' + ($jquey('#release-wrapper').height() + 20) + 'px');

	// show/hide the release history on click
	$jquey('a[href="#release-history"]').toggle(function () {	
		$jquey('#release-wrapper').animate({
			marginTop: '0px'
		}, 600, 'linear');
	}, function () {
		$jquey('#release-wrapper').animate({
			marginTop: '-' + ($jquey('#release-wrapper').height() + 20) + 'px'
		}, 600, 'linear');
	});	
	
	$jquey('#download a').mousedown(function () {
		_gaq.push(['_trackEvent', 'download-button', 'clicked'])		
	});
});

// google analytics code
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-6132309-2']);
_gaq.push(['_setDomainName', 'www.jquerynewsticker.com']);
_gaq.push(['_trackPageview']);

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();