<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <meta property="og:title" content="MK Insight"/>
    <meta property="og:site_name" content="MK Insight"/>
    <meta property="og:url" content="http://mkinsight.org/"/>
    <meta property="og:description" content="">
    <meta property="og:type" content="article"/>
    <meta property="og:image" content=""/>
    <meta name="author" content="Damian Dadswell - KMi">
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/img/favicon.ico">
    <title><?php wp_title(' | ', true, 'right'); ?></title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <!-- Custom styles for this template -->
    <link href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/assets/css/style.css" rel="stylesheet">
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/ie10-viewport-bug-workaround.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php wp_head(); ?>
    <script src="//code.jquery.com/jquery-latest.min.js"></script>
    <!-- script src="<?php //echo get_template_directory_uri(); ?>/assets/js/bootstrap.min.js"></script -->
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery.easing.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/inline-tweet.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery.mobile.custom.min.js"></script>
    <!--   jQuery UI-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <!-- Bootstrap toggle-->
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <!-- MDA -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript"
            src="<?php echo get_template_directory_uri(); ?>/mkio2/canvas/canvasjs.min.js"></script>
    <script type="text/javascript"
            src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-tagsinput.js"></script>
    <script type="text/javascript"
            src="<?php echo get_template_directory_uri(); ?>/assets/js/typeahead.bundle.js"></script>
    <!-- //code.jquery.com/jquery-1.11.3.min.js -->
    <script src="//cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>

    <!--    <script src="//cdn.jsdelivr.net/bootstrap.tagsinput/0.4.2/bootstrap-tagsinput.min.js"></script>-->
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.css"/>
    <!--    <link rel="stylesheet" href="//cdn.jsdelivr.net/bootstrap.tagsinput/0.4.2/bootstrap-tagsinput.css"/>-->
    <link href="<?php echo get_template_directory_uri(); ?>/assets/css/bootstrap-tagsinput.css" rel="stylesheet">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
    <!--
    //cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js
    //cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js -->
    <script src="//cdn.datatables.net/buttons/1.0.3/js/buttons.html5.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/mkio2/js/ecapi.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/mkio2/js/mkio_config.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/mkio2/js/mkio.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/mkio2/js/mkinsight.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.debug.js"></script>
    <link href="<?php echo get_template_directory_uri(); ?>/mkio2/mkio2.css" rel="stylesheet">
    <script src="//cdn.leafletjs.com/leaflet-0.7.5/leaflet.js"></script>

</head>
<body id="page-top" data-spy="scroll">
<!-- Navigation -->
<nav class="navbar navbar-default" role="navigation">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="mki-icon"><a class="page-scroll navbar-brand" href="<?php print home_url(); ?>"
                                         title="MK Insight"><img
                                src="<?php echo get_template_directory_uri(); ?>/assets/img/MKInsight-logo.svg"
                                height="60" alt="MK Insight - Logo"></a></div>
                <div class="navbar-header page-scroll">
                    <button type="button" class="navbar-toggle" data-toggle="collapse"
                            data-target="#navbar-collapse-mki">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbar-collapse-mki">
                    <?php
                    $defaults = array(
                        'theme_location' => 'main-menu',
                        'menu' => '',
                        'container' => 'ul',
                        'container_class' => 'nav navbar-nav',
                        'container_id' => '',
                        'menu_class' => '',
                        'menu_id' => '',
                        'echo' => true,
                        'fallback_cb' => 'wp_page_menu',
                        'before' => '',
                        'after' => '',
                        'link_before' => '',
                        'link_after' => '',
                        'items_wrap' => '<ul id="%1$s" class="nav navbar-nav">%3$s</ul>',
                        'depth' => 0,
                        'walker' => ''
                    );
                    wp_nav_menu($defaults);
                    ?>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- header front page -->
<?php
// header of the search page
if (is_search()):
    ?>
    <form name="search" id="advanced-search-form" class="content" role="search" method="get"
          action="<?php print home_url(); ?>">
        <div id="searchpage-header" class="page-header">
            <div class="desktop ipad">
                <h1 style="text-align: center;">
                    <?php
                    _e("Download, Filter, Sort or datasources by Text, Tags or Year", "mki");
                    ?>
                </h1>
            </div>
            <div id="searchbox">

                <input type="<?php echo @$_GET['s'] || !@$_GET['tags'] ? 'text' : 'hidden'; ?>" name="s" value="<?php echo @$_GET['s']; ?>"/>
                <input type="<?php echo !@$_GET['s'] && @$_GET['tags'] ? 'text' : 'hidden'; ?>" name="tags" value="<?php echo str_replace("-", " ", @$_GET['tags']); ?>"/>
                <div id="text-switch" class="switch">
                    <button type="reset" class="btn <?php echo @$_GET['s'] || !@$_GET['tags'] ? 'active' : ''; ?>"><?php _e("Text", "mki"); ?></button>
                </div>
                <div id="tag-switch" class="switch">
                    <button type="reset" class="btn <?php echo !@$_GET['s'] && @$_GET['tags'] ? 'active' : ''; ?>"><?php _e("Tags", "mki"); ?></button>
                </div>
                <button type="submit" class="btn"><i class="icon ion-search"></i></button>

            </div>
        </div>
        <div class="container advanced-search">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div id="timeline">
                        <div id="slider-range">
                            <div class="ui-slider-handle" id="year-from"></div>
                            <div class="ui-slider-handle" id="year-to"></div>
                        </div>
                    </div>
                    <input name="ymin" value="<?php echo $_GET['ymin']; ?>" type="hidden">
                    <input name="ymax" value="<?php echo $_GET['ymax']; ?>" type="hidden">
                    <div id="timestamp">
                        <div class="toggler">
                            <span class="stamp-label"><?php _e("Time Stamped only") ?></span>
                            <!--                <input type="checkbox" name="timeless" -->
                            <?php //echo $_GET['timeless'] ? 'checked' : '';
                            ?><!-- />-->
                            <input type="checkbox" name="stamped" <?php echo $_GET['stamped'] ? 'checked' : ''; ?>
                                   data-toggle="toggle" data-off="No" data-on="Yes" data-size="mini" data-style="ios"
                                   data-offstyle="default" data-onstyle="primary">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row desktop hidden-xs" id="sorting">
                <input type="hidden" name="orderby" value="<?php echo @$_GET['orderby']? @$_GET['orderby'] : 'title,year,keywords,files'; ?>" >
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">
                    <button class="btn btn-group-xs btn-link btn-sort" sorting="title" type="reset">
                        <?php _e("Title", "mki"); ?>
                        <i class="icon <?php echo @$_GET['title'] == 'DESC' ? 'ion-ios-arrow-thin-up' : 'ion-ios-arrow-thin-down'; ?>"></i>
                        <input type="hidden" name="title"
                               value="<?php echo @$_GET['title'] ? @$_GET['title'] : 'ASC'; ?>"/>
                    </button>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-0">
<!--                    <button class="btn btn-group-xs btn-link btn-sort" sorting="year" type="reset">-->
                        <label><?php _e("Years", "mki"); ?></label>
<!--                        <i class="icon --><?php //echo @$_GET['year'] == 'ASC' ? 'ion-ios-arrow-thin-down' : 'ion-ios-arrow-thin-up'; ?><!--"></i>-->
                        <input type="hidden" name="year"
                               value="<?php echo @$_GET['year'] ? @$_GET['year']: 'DESC'; ?>"/>
<!--                    </button>-->
                </div>
                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
<!--                    <button class="btn btn-group-xs btn-link btn-sort" sorting="keywords" type="reset">-->
                        <label><?php _e("Tags", "mki"); ?></label>
<!--                        <i class="icon --><?php //echo @$_GET['keywords'] == 'ASC' ? 'ion-ios-arrow-thin-up' : 'ion-ios-arrow-thin-down'; ?><!--"></i>-->
                        <input type="hidden" name="keywords"
                               value="<?php echo @$_GET['keywords'] ? @$_GET['keywords'] : 'ASC'; ?>"/>
<!--                    </button>-->
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-0">
<!--                    <button class="btn btn-group-xs btn-link btn-sort" sorting="files" type="reset">-->
                        <label><?php _e("Files", "mki"); ?></label>
<!--                        <i class="icon --><?php //echo @$_GET['files'] == 'ASC' ? 'ion-ios-arrow-thin-up' : 'ion-ios-arrow-thin-down'; ?><!--"></i>-->
                        <input type="hidden" name="files"
                               value="<?php echo @$_GET['files'] ? @$_GET['keywords'] : 'ASC'; ?>"/>
<!--                    </button>-->
                </div>
            </div>
        </div>
    </form>
<?php
endif;
?>
<?php if (is_front_page()): ?>
    <div id="frontpage-header" class="page-header">
        <h1 class="home centered desktop ipad">
            <?php _e("Quick access to information about Milton Keynes", "mki"); ?>
        </h1>
        <div class="desktop ipad">
            <p style="text-align: center;">
                <?php
                _e("Built on the MK Data Hub, it aims to be a one-stop-shop for sharing documents, information and data, and provides tools for exploring these data both in their original form, and in convenient charts and maps.", "mki");
                ?>
            </p>
        </div>
        <div id="searchbox">
            <form name="search">
                <input type="text" name="s"/>
                <button type="submit" class="btn"><i class="icon ion-search"></i></button>
            </form>
        </div>
    </div>
<?php endif; ?>
<!-- end header front page-->
<!-- content -->
<div id="content" class="<?php echo is_search() ? 'no-margin':''; ?>">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php echo is_search() ? 'table-box':''; ?>">
