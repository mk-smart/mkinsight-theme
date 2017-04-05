<?php

// size of posts
@ini_set( 'upload_max_size' , '20M' );
@ini_set( 'post_max_size', '20M');
@ini_set( 'max_execution_time', '300' );

add_action( 'after_setup_theme', 'mki_setup' );
function mki_setup()
{
load_theme_textdomain( 'mki', get_template_directory() . '/languages' );
add_theme_support( 'title-tag' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'post-thumbnails' );
global $content_width;
if ( ! isset( $content_width ) ) $content_width = 640;
register_nav_menus(
array( 'main-menu' => __( 'Main Menu', 'mki' ),
'footer-menu' => __( 'Footer Menu', 'mki' ) )
);
}

// Shorten next / previous titles
function shrink_previous_post_link($format, $link){
    $in_same_cat = false;
    $excluded_categories = '';
    $previous = true;
    $link='&laquo; %title';
    $format='%link';


    if ( $previous && is_attachment() )
        $post = & get_post($GLOBALS['post']->post_parent);
    else
        $post = get_adjacent_post($in_same_cat, $excluded_categories, $previous);

    if ( !$post )
        return;

    $title = $post->post_title;

    if ( empty($post->post_title) )
        $title = $previous ? __('Previous Post', 'mki') : __('Next Post', 'mki');

    $rel = $previous ? 'prev' : 'next';

    //Save the original title
    $original_title = $title;

    //create short title, if needed
    if (strlen($title)>40){
        $first_part = substr($title, 0, 23);
        $last_part = substr($title, -17);
        $title = $first_part."...".$last_part;
    }   

    $string = '<a href="'.get_permalink($post).'" rel="'.$rel.'" title="'.$original_title.'">';
    $link = str_replace('%title', $title, $link);   
    $link = $string . $link . '</a>';

    $format = str_replace('%link', $link, $format);

    echo $format;   
}

function shrink_next_post_link($format, $link){
    $in_same_cat = false;
    $excluded_categories = '';
    $previous = false;
    $link='%title &raquo;';
    $format='%link';

    if ( $previous && is_attachment() )
        $post = & get_post($GLOBALS['post']->post_parent);
    else
        $post = get_adjacent_post($in_same_cat, $excluded_categories, $previous);

    if ( !$post )
        return;

    $title = $post->post_title;

    if ( empty($post->post_title) )
        $title = $previous ? __('Previous Post', 'mki') : __('Next Post', 'mki');

    $rel = $previous ? 'prev' : 'next';

    //Save the original title
    $original_title = $title;

    //create short title, if needed
    if (strlen($title)>40){
        $first_part = substr($title, 0, 23);
        $last_part = substr($title, -17);
        $title = $first_part."...".$last_part;
    }   

    $string = '<a href="'.get_permalink($post).'" rel="'.$rel.'" title="'.$original_title.'">';
    $link = str_replace('%title', $title, $link);   
    $link = $string . $link . '</a>';

    $format = str_replace('%link', $link, $format);

    echo $format;   
}

add_filter('next_post_link', 'shrink_next_post_link',10,2);
add_filter('previous_post_link', 'shrink_previous_post_link',10,2);
// Shorten next / previous titles


add_action( 'wp_enqueue_scripts', 'mki_load_scripts' );
function mki_load_scripts()
{
wp_enqueue_script( 'jquery' );
}
add_action( 'comment_form_before', 'mki_enqueue_comment_reply_script' );
function mki_enqueue_comment_reply_script()
{
if ( get_option( 'thread_comments' ) ) { wp_enqueue_script( 'comment-reply' ); }
}
add_filter( 'the_title', 'mki_title' );
function mki_title( $title ) {
if ( $title == '' ) {
return '&rarr;';
} else {
return $title;
}
}
add_filter( 'wp_title', 'mki_filter_wp_title' );
function mki_filter_wp_title( $title )
{
return $title . esc_attr( get_bloginfo( 'name' ) );
}
add_action( 'widgets_init', 'mki_widgets_init' );
function mki_widgets_init()
{
register_sidebar( array (
'name' => __( 'Sidebar Widget Area', 'mki' ),
'id' => 'primary-widget-area',
'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
'after_widget' => "</li>",
'before_title' => '<h3 class="widget-title">',
'after_title' => '</h3>',
) );
}
function mki_custom_pings( $comment )
{
$GLOBALS['comment'] = $comment;
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo comment_author_link(); ?></li>
<?php 
}
add_filter( 'get_comments_number', 'mki_comments_number' );
function mki_comments_number( $count )
{
if ( !is_admin() ) {
global $id;
$comments_by_type = &separate_comments( get_comments( 'status=approve&post_id=' . $id ) );
return count( $comments_by_type['comment'] );
} else {
return $count;
}
}

// add featured image into the feed
add_filter( 'the_content', 'featured_image_in_feed' );
function featured_image_in_feed( $content ) {
    global $post;
    if( is_feed() ) {
        if ( has_post_thumbnail( $post->ID ) ){
            $output = get_the_post_thumbnail( $post->ID, 'large', array( 'style' => 'float:left;margin:0 10px 10px 0;' ) );
            $content = $output . $content;
        }
    }
    return $content;
}


// grap news excerpt
function get_front_excerpt(){
$excerpt = get_the_content();
$excerpt = strip_shortcodes($excerpt);
$excerpt = preg_replace(" (\[.*?\])",'',$excerpt);
$excerpt = strip_tags($excerpt);
$excerpt = substr($excerpt, 0, 400);
$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
$excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
$excerpt = $excerpt.'...';
return $excerpt;
}
function get_sub_excerpt(){
$excerpt = get_the_content();
$excerpt = strip_shortcodes($excerpt);
$excerpt = preg_replace(" (\[.*?\])",'',$excerpt);
$excerpt = strip_tags($excerpt);
$excerpt = substr($excerpt, 0, 250);
$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
$excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
$excerpt = $excerpt.'...';
return $excerpt;
}
function get_subsub_excerpt(){
$excerpt = get_the_content();
$excerpt = strip_shortcodes($excerpt);
$excerpt = preg_replace(" (\[.*?\])",'',$excerpt);
$excerpt = strip_tags($excerpt);
$excerpt = substr($excerpt, 0, 150);
$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
$excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
$excerpt = $excerpt.'...';
return $excerpt;
}

// if news has an image grab it
function catch_that_image() {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  $first_img = $matches[1][0];

  if(empty($first_img)) {
    $first_img = "wp-content/themes/mkinsight/assets/img/blank-logo.png";
  }
  return $first_img;
}
// remove width and height from inserted images - responsive
add_filter( 'post_thumbnail_html', 'remove_width_attribute', 10 );
add_filter( 'image_send_to_editor', 'remove_width_attribute', 10 );
function remove_width_attribute( $html ) {
   $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
   return $html;
}

// move admin bar to bottom
function adminBarBottom() {
    echo '<style type="text/css">
        body {
            margin-top: -28px;
        }
        #wpadminbar {
            top: auto !important;
            bottom: 0;
        }
        #wpadminbar .quicklinks .ab-sub-wrapper {
            bottom: 28px;
        }
        #wpadminbar .menupop .ab-sub-wrapper, #wpadminbar .shortlink-input {
            border-width: 1px 1px 0 1px;
            -moz-box-shadow:0 -4px 4px rgba(0,0,0,0.2);
            -webkit-box-shadow:0 -4px 4px rgba(0,0,0,0.2);
            box-shadow:0 -4px 4px rgba(0,0,0,0.2);
        }
        #wpadminbar .quicklinks .menupop ul#wp-admin-bar-wp-logo-default {
            background-color: #eee;
        }
        #wpadminbar .quicklinks .menupop ul#wp-admin-bar-wp-logo-external {
            background-color: white;
        }
        body.wp-admin div#wpwrap div#footer {
            bottom: 28px !important;
        }
    </style>';
}
// Uncomment if you want it to be done in the Admin Section too
// if ( is_admin_bar_showing() ) {
//     add_action( 'admin_head', 'adminBarBottom' );
// }
if ( is_admin_bar_showing() ) {
    add_action( 'wp_head', 'adminBarBottom' );
}

// --- MDA ---

// shortcode for icons in infographics
function mkiicon_func( $atts ){
  $a = shortcode_atts( array(
			     'icon' => 'population',
			     'text' => 'Use the "text" attribute to add text',
			     ), $atts );
  return '<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6"><img class="aligncenter mkicons size-full" src="http://mkinsight.org/wp-content/themes/mkinsight/assets/img/infographics/'.$a['icon'].'.png" alt="'.$a['icon'].'" height="138" /><p class="strapline">'.$a['text'].'</p></div>';
}
add_shortcode( 'mkiicon', 'mkiicon_func' );

require_once('mkio2/mkio2.php');

// shortcode to include the chart interface
function mkicharts_func( $atts ){
  ob_start();
  include('mkio2/datapage.php');    
  $out1 = ob_get_contents();
  ob_end_clean();
  return $out1;
}
add_shortcode( 'mkicharts', 'mkicharts_func' );

// shortcode to include 1 chart
function mkichart_func( $atts ){
  $a = shortcode_atts( array(
			     'type' => 'place',
			     'dim' => 'demographics:population-2011',
			     'title' => ''
			     ), $atts );
  $dims = explode('.', $a['dim']);
  $dimsparam = '';
  foreach($dims as $i=>$dim){
    $dimsparam .= '&l'.($i+1).'='.$dim;
  }
  return '<iframe src="http://mkinsight.org/wp-content/themes/mkinsight/mkio2/singlegraph.php?type='.$a['type'].'&title='.urlencode($a['title']).$dimsparam.'" width="100%" height="550" frameborder="0" class="iframe-class"></iframe>';
}
add_shortcode( 'mkichart', 'mkichart_func' );


function mkixls_meta_box_markup(){
  global $post;
  $media = get_attached_media( 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $post->ID);
  if (count($media)===0){
    $media = get_attached_media( 'application/vnd.ms-excel', $post->ID);
  }
  if (count($media)===0){
    $media = get_attached_media( 'text/csv', $post->ID);
  }
  if (count($media)===0){ ?>
    <p>This function will appear if you add an attachement ("add media") 
					  which is a spreadsheet and save the post.</p>
  <?php
  } else {
    $surl = end($media)->guid;
   $types = array();
   $files = scandir('/var/www/html/wp-content/themes/mkinsight/mkio2/cache/');
   $dimensions = array();
   $acdims = array();
   foreach($files as $file){
    if (!startsWith($file, "http") && !startsWith($file, '.') && !startsWith($file, 'map') && strpos($file, '_')!==FALSE){
      $fn = str_replace("__", ":", $file);
      $afn = explode("_", $fn);
      if (!in_array ($afn[0], $types)) $types[] = $afn[0];
      if (!isset($dimensions[$afn[0]])) { $dimensions[$afn[0]] = array(); }
      $arr = &$dimensions[$afn[0]];
      $dims = substr($fn, strpos($fn,"_")+1);
      $aafn = explode(".", $dims);
      $lev = 0;
      foreach($aafn as $elem){
        if (!isset($arr[$elem])) { $arr[$elem] = array(); }	
        $arr = &$arr[$elem];	
	if (!isset($acdims[$lev])) { $acdims[$lev] = array();}
	if (!in_array($elem, $acdims[$lev])) { $acdims[$lev][] = $elem; }
	$lev++;
      }
    }
  }
    ?>
<div id="mki_secapi"></div>
<script>
<?php
echo 'var types      = '.json_encode($types).     ';'."\n";
echo 'var dimensions = '.json_encode($dimensions).';'."\n";    
echo 'var acdims     = '.json_encode($acdims).';'."\n";    
?>
   spreadsheet.url = "<?php echo $surl;?>";
   mksse_init("mki_secapi");
</script>
<?php 
  }
}

function add_mkixls_meta_box(){  
  add_meta_box("Create charts from spreadsheet", "Create charts from spreadsheet", "mkixls_meta_box_markup", "post", "normal", "low", null);
}

function startsWith($haystack, $needle) {
      return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

add_action("add_meta_boxes", "add_mkixls_meta_box");

// add scripts and css to admin
function load_mkixls_admin_style() {
  wp_register_style( 'mkixls_css', get_template_directory_uri() . '/secapi/secapi.css');
  wp_enqueue_style( 'mkixls_css' );
  //  wp_register_style( 'mkixls_jquery-ui', '//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');
  //  wp_enqueue_style( 'mkixls_jquery-ui');
  //  wp_register_style( 'mkixls_bootstrap', "http://getbootstrap.com/dist/css/bootstrap.min.css");
  //  wp_enqueue_style( 'mkixls_bootstrap');

  // wp_enqueue_script( 'mkxls_jquery', "//code.jquery.com/jquery-1.9.1.js");
  //  wp_enqueue_script( 'mkxls_jquery-ui', "//code.jquery.com/ui/1.10.4/jquery-ui.js");
  wp_enqueue_script( 'mkxls_main_js', get_template_directory_uri().'/secapi/js/secapi.js' );
  wp_enqueue_script( 'mkxls_view_js', get_template_directory_uri().'/secapi/js/vc.js' ); 
  wp_enqueue_script( 'mkxls_typeahead_js', get_template_directory_uri().'/secapi/js/typeahead.js' ); 
}
add_action( 'admin_enqueue_scripts', 'load_mkixls_admin_style' );

// enable additional mime types for uplaod
function my_myme_types($mime_types){
    $mime_types['xls'] = 'application/vnd.ms-excel'; 
    $mime_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'; 
    $mime_types['csv'] = 'text/csv';     
    return $mime_types;
}
add_filter('upload_mimes', 'my_myme_types', 1, 1);

define(‘ALLOW_UNFILTERED_UPLOADS’, true);