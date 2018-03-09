<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.css" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="profile" href="http://gmpg.org/xfn/11"/>
<link rel="pingback" href="http://212.219.130.85/xmlrpc.php" /> 
<title>Charts &#8211; MK Insight</title>
<meta name='robots' content='noindex,follow' />
<link rel='stylesheet' id='wpa-css-css'  href='http://212.219.130.85/wp-content/plugins/wp-attachments/styles/2/wpa.css?ver=4.5.1' type='text/css' media='all' />
<link rel='stylesheet' id='open-sans-css'  href='https://fonts.googleapis.com/css?family=Open+Sans%3A300italic%2C400italic%2C600italic%2C300%2C400%2C600&#038;subset=latin%2Clatin-ext&#038;ver=4.5.1' type='text/css' media='all' />
<link rel='stylesheet' id='dashicons-css'  href='http://212.219.130.85/wp-includes/css/dashicons.min.css?ver=4.5.1' type='text/css' media='all' />
<link rel='stylesheet' id='admin-bar-css'  href='http://212.219.130.85/wp-includes/css/admin-bar.min.css?ver=4.5.1' type='text/css' media='all' />
<link rel='stylesheet' id='fca_fbc_poll_front_end_component-css'  href='http://212.219.130.85/wp-content/plugins/surveys-by-feedback-cat/includes/FCA/FBC/Poll/FrontEnd/Component.css?ver=4.5.1' type='text/css' media='all' />
<link rel='stylesheet' id='fca_fbc_font_awesome-css'  href='http://212.219.130.85/wp-content/plugins/surveys-by-feedback-cat/lib/font-awesome-4.3.0/css/font-awesome.min.css?ver=4.5.1' type='text/css' media='all' />
<script type='text/javascript' src='http://212.219.130.85/wp-includes/js/jquery/jquery.js?ver=1.12.3'></script>
<script type='text/javascript' src='http://212.219.130.85/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.0'></script>
<script type='text/javascript' src='http://212.219.130.85/wp-content/plugins/surveys-by-feedback-cat/includes/FCA/Form.js?ver=4.5.1'></script>
<script type='text/javascript' src='http://212.219.130.85/wp-content/plugins/surveys-by-feedback-cat/lib/fca_delay/fca_delay.js?ver=4.5.1'></script>
<script type='text/javascript' src='http://212.219.130.85/wp-content/plugins/surveys-by-feedback-cat/includes/FCA/FBC/Poll/FrontEnd/Component.js?ver=4.5.1'></script>
<link rel='https://api.w.org/' href='http://212.219.130.85/wp-json/' />
<link rel="EditURI" type="application/rsd+xml" title="RSD" href="http://212.219.130.85/xmlrpc.php?rsd" />
<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="http://212.219.130.85/wp-includes/wlwmanifest.xml" /> 
<meta name="generator" content="WordPress 4.5.1" />
<style type="text/css" media="print">#wpadminbar { display:none; }</style>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="http://212.219.130.85/wp-content/themes/mkinsight/mkio2/canvas/canvasjs.min.js"></script>
<script src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"> </script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/buttons.html5.min.js"></script>
<script src="http://212.219.130.85/wp-content/themes/mkinsight/mkio2/js/ecapi.js"></script>
<script src="http://212.219.130.85/wp-content/themes/mkinsight/mkio2/js/mkio_config.js"></script>
<script src="http://212.219.130.85/wp-content/themes/mkinsight/mkio2/js/mkio.js"></script>
<link rel="stylesheet" href="http://212.219.130.85/wp-content/themes/mkinsight/mkio2/mkio2.css" />
<script src="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.js"></script>
</head>
<body>
  <div id="resultpanel" class="col-md-12" > </div>
  <script>   
   configchart.type="<?php echo $_GET['type']; ?>";
   <?php
   if (isset($_GET['title'])) {
   ?>
   configchart.title= "<?php echo $_GET['title']; ?>";
   <?php } ?>
   <?php 
   if (!isset($_GET['l2']) || strcmp($_GET['l2'],'')===0){
      ?>
       configchart.dimensions="<?php echo $_GET['l1']; ?>";
   <?php
   } else {
         if (!isset($_GET['l3']) || strcmp($_GET['l3'],'')===0){
      ?>
       configchart.dimensions="<?php echo $_GET['l1']; ?>.<?php echo $_GET['l2'];?>";
   <?php
   } else {
      if (!isset($_GET['l4']) || strcmp($_GET['l4'],'')===0){
      ?>
       configchart.dimensions="<?php echo $_GET['l1']; ?>.<?php echo $_GET['l2'];?>.<?php echo $_GET['l3'];?>";
   <?php
      } else {
      ?>
      configchart.dimensions="<?php echo $_GET['l1']; ?>.<?php echo $_GET['l2'];?>.<?php echo $_GET['l3'];?>.<?php echo $_GET['l4'];?>";
   <?php
      }
   }
}
      ?>
   console.log(configchart);
   ccharts.push(configchart);
   getChartData(configchart);
   </script>
</body>
</html>

