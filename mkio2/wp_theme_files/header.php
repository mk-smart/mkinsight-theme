<?php 
/**
 * The Header for Optimizer
 *
 * Displays all of the <head> section and everything
 *
 * @package Optimizer
 * 
 * @since Optimizer 1.0
 */
/*OPTION DEFAULTS*/ 
global $optimizer;
$optimizer = optimizer_option_defaults();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
 <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.css" />
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo( 'charset' ); ?>" />	
<?php // Google Chrome Frame for IE ?>
<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="profile" href="http://gmpg.org/xfn/11"/>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" /> 
<?php wp_head(); ?>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/canvas/canvasjs.min.js"></script>
<!-- //code.jquery.com/jquery-1.11.3.min.js -->
<script src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"> </script>
<!-- 
//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js
//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js -->
<script src="https://cdn.datatables.net/buttons/1.0.3/js/buttons.html5.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/mkio2/js/ecapi.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/mkio2/js/mkio_config.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/mkio2/js/mkio.js"></script>
<script src="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.js"></script>
</head>

<body <?php body_class();?>>
<!--HEADER-->
<div class="header_wrap layer_wrapper">
	<?php get_template_part('template_parts/head','type1'); ?>
</div><!--layer_wrapper class END-->

	<!--Slider START-->
		<?php if (is_home() && is_front_page()) { ?>
        
            <div id="slidera" class="layer_wrapper <?php if(!empty($optimizer['hide_mob_slide'])){ echo 'mobile_hide_slide';} ?>">
                <?php $slidertype = $optimizer['slider_type_id']; ?>
                <?php get_template_part('frontpage/slider',''.$slidertype.''); ?>
            </div> 
            
          <?php } ?> 
      <!--Slider END-->
