<!-- footer -->
    <div id="footer" role="contentinfo">
        <div class="container">
	<p class="only-print">The data above has been obtained from the MK:Insight portal (mkinsight.org) and might include data produced/distributed by the Office for National Statistics (www.ons.gov.uk) under the Open Goverment Licence.</p>
        	<div class="row">
                <div class="desktop ipad">
                	<div class="col-lg-4 col-md-4 col-sm-3 col-xs-6 no-print">
                    	<a href="https://www.milton-keynes.gov.uk/" title="Milton Keynes Council" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/mk-council-logo.png" class="mkc" alt="Milton Keynes Council Logo"></a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                        <!-- <?php wp_nav_menu( array( 'theme_location' => 'footer-menu' ) ); ?> -->
			<div style="padding-top: 50px;">
			<a href="http://mkinsight.org/about-us/">About us</a> | <a href="http://mkinsight.org/terms-and-conditions/">Terms and Conditions</a> | <a href="http://mkinsight.org/privacy-policy/">Privacy Policy</a>
			</div>
                    </div>
                	<div class="col-lg-4 col-md-4 col-sm-3 col-xs-6 no-print">
                    	<a href="http://www.mksmart.org/" title="Powered by MK:Smart" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/poweredby-mksmart-logo.png" class="mks alignright" alt="Powered by MK:Smart Logo"></a>
                    </div>
                </div>
                <div class="phone">
                    <div class="col-xs-12">
                        <?php wp_nav_menu( array( 'theme_location' => 'footer-menu' ) ); ?>
                    </div>
                    <div class="clear margin-bottom-60"></div>
                	<div class="col-xs-12 no-print"><a href="https://www.milton-keynes.gov.uk/" title="Milton Keynes Council" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/mk-council-logo.png" class="mkc alignleft" alt="Milton Keynes Council Logo"></a>
                    	<a href="http://www.mksmart.org/" title="Powered by MK:Smart" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/poweredby-mksmart-logo.png" class="mks alignright" alt="Powered by MK:Smart Logo"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php wp_footer(); ?>
	<script>
if(!$){
  $=jQuery;
}
// jQuery for page scrolling feature - requires jQuery Easing plugin
$(function() {
    $('a.page-scroll').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top
        }, 1500, 'easeInOutExpo');
        event.preventDefault();
    });
});
// jQuery for close the menu when collapsed
$(function() {
    $('button.navbar-toggle').bind('click', function(event) {
        var target = $(this).data('target');
		$("body").toggleClass('navbar-expanded');
        event.preventDefault();
    });
});
// jQuery to adjust the menu on resize
$(window).on('resize', function(){

});
	</script>
  </body>
</html>
