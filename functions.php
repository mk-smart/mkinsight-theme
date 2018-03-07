<?php

// size of posts
@ini_set('upload_max_size', '20M');
@ini_set('post_max_size', '20M');
@ini_set('max_execution_time', '300');


add_action('after_setup_theme', 'mki_setup');
function mki_setup()
{
    load_theme_textdomain('mki', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails');
    global $content_width;
    if (!isset($content_width)) $content_width = 640;
    register_nav_menus(
        array('main-menu' => __('Main Menu', 'mki'),
            'footer-menu' => __('Footer Menu', 'mki'))
    );
}

// Shorten next / previous titles
function shrink_previous_post_link($format, $link)
{
    $in_same_cat = false;
    $excluded_categories = '';
    $previous = true;
    $link = '&laquo; %title';
    $format = '%link';


    if ($previous && is_attachment())
        $post = &get_post($GLOBALS['post']->post_parent);
    else
        $post = get_adjacent_post($in_same_cat, $excluded_categories, $previous);

    if (!$post)
        return;

    $title = $post->post_title;

    if (empty($post->post_title))
        $title = $previous ? __('Previous Post', 'mki') : __('Next Post', 'mki');

    $rel = $previous ? 'prev' : 'next';

    //Save the original title
    $original_title = $title;

    //create short title, if needed
    if (strlen($title) > 40) {
        $first_part = substr($title, 0, 23);
        $last_part = substr($title, -17);
        $title = $first_part . "..." . $last_part;
    }

    $string = '<a href="' . get_permalink($post) . '" rel="' . $rel . '" title="' . $original_title . '">';
    $link = str_replace('%title', $title, $link);
    $link = $string . $link . '</a>';

    $format = str_replace('%link', $link, $format);

    echo $format;
}

function shrink_next_post_link($format, $link)
{
    $in_same_cat = false;
    $excluded_categories = '';
    $previous = false;
    $link = '%title &raquo;';
    $format = '%link';

    if ($previous && is_attachment())
        $post = &get_post($GLOBALS['post']->post_parent);
    else
        $post = get_adjacent_post($in_same_cat, $excluded_categories, $previous);

    if (!$post)
        return;

    $title = $post->post_title;

    if (empty($post->post_title))
        $title = $previous ? __('Previous Post', 'mki') : __('Next Post', 'mki');

    $rel = $previous ? 'prev' : 'next';

    //Save the original title
    $original_title = $title;

    //create short title, if needed
    if (strlen($title) > 40) {
        $first_part = substr($title, 0, 23);
        $last_part = substr($title, -17);
        $title = $first_part . "..." . $last_part;
    }

    $string = '<a href="' . get_permalink($post) . '" rel="' . $rel . '" title="' . $original_title . '">';
    $link = str_replace('%title', $title, $link);
    $link = $string . $link . '</a>';

    $format = str_replace('%link', $link, $format);

    echo $format;
}

add_filter('next_post_link', 'shrink_next_post_link', 10, 2);
add_filter('previous_post_link', 'shrink_previous_post_link', 10, 2);
// Shorten next / previous titles


add_action('wp_enqueue_scripts', 'mki_load_scripts');
function mki_load_scripts()
{
    wp_enqueue_script('jquery');
}

add_action('comment_form_before', 'mki_enqueue_comment_reply_script');
function mki_enqueue_comment_reply_script()
{
    if (get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

add_filter('the_title', 'mki_title');
function mki_title($title)
{
    if ($title == '') {
        return '&rarr;';
    } else {
        return $title;
    }
}

add_filter('wp_title', 'mki_filter_wp_title');
function mki_filter_wp_title($title)
{
    return $title . esc_attr(get_bloginfo('name'));
}

add_action('widgets_init', 'mki_widgets_init');
function mki_widgets_init()
{
    register_sidebar(array(
        'name' => __('Sidebar Widget Area', 'mki'),
        'id' => 'primary-widget-area',
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => "</li>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}

function mki_custom_pings($comment)
{
    $GLOBALS['comment'] = $comment;
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo comment_author_link(); ?></li>
    <?php
}

add_filter('get_comments_number', 'mki_comments_number');
function mki_comments_number($count)
{
    if (!is_admin()) {
        global $id;
        $comments_by_type = @separate_comments(get_comments('status=approve&post_id=' . $id));
        return count($comments_by_type['comment']);
    } else {
        return $count;
    }
}

// add featured image into the feed
add_filter('the_content', 'featured_image_in_feed');
function featured_image_in_feed($content)
{
    global $post;
    if (is_feed()) {
        if (has_post_thumbnail($post->ID)) {
            $output = get_the_post_thumbnail($post->ID, 'large', array('style' => 'float:left;margin:0 10px 10px 0;'));
            $content = $output . $content;
        }
    }
    return $content;
}


// grap news excerpt
function get_front_excerpt()
{
    $excerpt = get_the_content();
    $excerpt = strip_shortcodes($excerpt);
    $excerpt = preg_replace(" (\[.*?\])", '', $excerpt);
    $excerpt = strip_tags($excerpt);
    $excerpt = substr($excerpt, 0, 400);
    $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
    $excerpt = trim(preg_replace('/\s+/', ' ', $excerpt));
    $excerpt = $excerpt . '...';
    return $excerpt;
}

function get_sub_excerpt()
{
    $excerpt = get_the_content();
    $excerpt = strip_shortcodes($excerpt);
    $excerpt = preg_replace(" (\[.*?\])", '', $excerpt);
    $excerpt = strip_tags($excerpt);
    $excerpt = substr($excerpt, 0, 250);
    $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
    $excerpt = trim(preg_replace('/\s+/', ' ', $excerpt));
    $excerpt = $excerpt . '...';
    return $excerpt;
}

function get_subsub_excerpt()
{
    $excerpt = get_the_content();
    $excerpt = strip_shortcodes($excerpt);
    $excerpt = preg_replace(" (\[.*?\])", '', $excerpt);
    $excerpt = strip_tags($excerpt);
    $excerpt = substr($excerpt, 0, 150);
    $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
    $excerpt = trim(preg_replace('/\s+/', ' ', $excerpt));
    $excerpt = $excerpt . '...';
    return $excerpt;
}

// if news has an image grab it
function catch_that_image()
{
    global $post, $posts;
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches[1][0];

    if (empty($first_img)) {
        $first_img = "wp-content/themes/mkinsight/assets/img/blank-logo.png";
    }
    return $first_img;
}

// remove width and height from inserted images - responsive
add_filter('post_thumbnail_html', 'remove_width_attribute', 10);
add_filter('image_send_to_editor', 'remove_width_attribute', 10);
function remove_width_attribute($html)
{
    $html = preg_replace('/(width|height)="\d*"\s/', "", $html);
    return $html;
}

// move admin bar to bottom
function adminBarBottom()
{
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
if (is_admin_bar_showing()) {
    add_action('wp_head', 'adminBarBottom');
}

// --- MDA ---

// shortcode for icons in infographics
function mkiicon_func($atts)
{
    $a = shortcode_atts(array(
        'icon' => 'population',
        'text' => 'Use the "text" attribute to add text',
        'link' => '',
        'img_height' => ''
    ), $atts);
    if (strpos($a['icon'], 'http://') === 0 ||
        strpos($a['icon'], 'https://') === 0 ||
        strpos($a['icon'], '//') === 0) {
        $img_url = $a['icon'];
    } else {
        $img_url = get_template_directory_uri() . '/assets/img/svg/' . $a['icon'] . '.svg';
//        $img_url = get_template_directory_uri() . '/assets/img/infographics/' . $a['icon'] . '.png';
    }

    if (strpos($a['link'], 'http://') === 0 ||
        strpos($a['link'], 'https://') === 0 ||
        strpos($a['link'], '//') === 0) {
        $href = $a['link'];
    } else if ($a['link']) {
        $href = home_url() . '/' . $a['link'];
    } else {
        $href = false;
    }
    $text = $a['text'];
    if (!$a['img_height']) {
        $height = 120;
        if (strlen($text) > 30) {
            $height = 90;
        }
        if (strlen($text) > 60) {
            $height = 70;
        }
    } else {
        $height = $a['img_height'];
    }
    // if a ref is defined it is a button
    if ($href) {
        return <<<HTML
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
        <a href="$href" class="btn btn-default btn-mkinsight vcenter">
            <div class="align-middle">
                <img class="aligncenter mkicons size-full" src="$img_url" alt="$text" style="height:${height}px;"/>
                <span class="strapline">$text</span>
            </div>
        </a>
    </div>
HTML;
    } else {
        // if it has no link therefore it is a fact
        return <<<HTML
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
        <div class="btn-mkinsight btn-nolink vcenter">
            <div class="align-middle">
                <img class="aligncenter mkicons size-full" src="$img_url" alt="$text" style="height:${height}px;"/>
                <span class="strapline">$text</span>
            </div>
        </div>
    </div>
HTML;
    }
}

add_shortcode('mkiicon', 'mkiicon_func');


// shortcode for icons in infographics
function mkifigures_func($atts)
{
//    var_dump($atts);
    $a = shortcode_atts(array(
        'icon' => 'population',
        'text' => 'Use the "text" attribute to add text',
        'link' => '',
        'img_height' => ''
    ), $atts);
    if (strpos($a['icon'], 'http://') === 0 ||
        strpos($a['icon'], 'https://') === 0 ||
        strpos($a['icon'], '//') === 0) {
        $img_url = $a['icon'];
    } else {
        $img_url = get_template_directory_uri() . '/assets/img/svg/' . $a['icon'] . '.svg';
//        $img_url = get_template_directory_uri() . '/assets/img/infographics/' . $a['icon'] . '.png';
    }

    // management of multiple links
    $hrefs = explode(",", $a['link']);
    $links = "";
    // for each link
    foreach ($hrefs as $href) {
        $href = trim($href);
        $url = parse_url($href);
        $path_parts = pathinfo($href);
//        var_dump($url["host"]);
        // complete url
        if ($url["host"]) {
            $link = $href;
            // file
            if($path_parts['extension'] && $path_parts['extension'] != "php" && $path_parts['extension'] != "html"){
                $iconClass = 'ion-android-attach';
            }else
            // full link ...
            if (($url["host"] == "mkinsight.org" || $url["host"] == "localhost") || url_to_postid($link)) {
                // if it is a file
                 $iconClass = 'ion-document';
            } else {
                // external link ...
                $iconClass = 'ion-link';
            }
        } else if ($a['link']) { // relative path
            // internal link
            // if it is a file
            if($path_parts['extension'] && $path_parts['extension'] != "php" && $path_parts['extension'] != "html"){
                $iconClass = 'ion-android-attach';
            }else{
                $iconClass = 'ion-document';
            }
            $link = home_url() . '/' .$href;
        } else {
            $link = false;
        }
        $urlName = basename($link);
        $links = $links . "<a href =\"$link\" class=\"aligncenter\" title=\"$urlName\"><i class=\"icon $iconClass\"></i></a>";
    }


    // text management
    $text = $a['text'];
    $height = 90;
    if (strlen($text) > 30) {
        $height = 90;
    }
    if (strlen($text) > 60) {
        $height = 70;
    }
    if ($a['img_height']) {
        $height = min($height, $a['img_height']);
    }


    // parse text to hightlight numbers
    preg_match_all('![£]*\d+[\,\.]?\d*[%stndrdth]*\d*[kbnml]*!', $text, $matches);
    foreach ($matches[0] as $numb) {
        $text = str_replace($numb, "<strong>$numb</strong>", $text);
    }
    global $mkifigures_counter;
    if (!isset($mkifigures_counter)) {
        $mkifigures_counter = 0;
    }
    $mkifigures_counter++;
    // optimise layout
    $optimise = '';
    if ($mkifigures_counter % 3 == 0) {
        // Print each 3 boxes
        $optimise = "<div class=\"clearfix ipadv\"></div>";
    }
    if ($mkifigures_counter % 4 == 0) {
        // Print each 3 boxes
        $optimise = "<div class=\"clearfix desktop\"></div>";
    }
    // if a ref is defined it is a button
    if ($links) {
        return <<<HTML
    <div class="col-lg-3 col-lg-offset-0 col-md-3 col-md-offset-0 col-sm-4 col-sm-offset-0 col-xs-10 col-xs-offset-1 mkifigure">
        <div class="align-middle figure">
            <img class="aligncenter size-full" src="$img_url" alt="$text" style="height:${height}px;"/>
            <span class="strapline">$text</span>
        </div>
        <div class="sources align-middle">
            $links
        </div>
    </div>$optimise
HTML;
    } else {
        // if it has no link therefore it is a fact
        return <<<HTML
    <div class="col-lg-3 col-lg-offset-0 col-md-3 col-md-offset-0 col-sm-4 col-sm-offset-0 col-xs-10 col-xs-offset-1 mkifigure">
        <div class="btn-mkinsight btn-nolink vcenter">
            <div class="align-middle">
                <img class="aligncenter mkicons size-full" src="$img_url" alt="$text" style="height:${height}px;"/>
                <span class="strapline">$text</span>
            </div>
        </div>
    </div>$optimise
HTML;
    }
}

add_shortcode('mkifigures', 'mkifigures_func');


require_once('mkio2/mkio2.php');

// shortcode to include the chart interface
function mkicharts_func($atts)
{
    ob_start();
    include('mkio2/datapage.php');
    $out1 = ob_get_contents();
    ob_end_clean();
    return $out1;
}

add_shortcode('mkicharts', 'mkicharts_func');

// shortcode to include 1 chart
function mkichart_func($atts)
{
    $a = shortcode_atts(array(
        'type' => 'place',
        'dim' => 'demographics:population-2011',
        'title' => ''
    ), $atts);
    $dims = explode('.', $a['dim']);
    $dimsparam = '';
    foreach ($dims as $i => $dim) {
        $dimsparam .= '&l' . ($i + 1) . '=' . $dim;
    }
    return '<iframe src="http://mkinsight.org/wp-content/themes/mkinsight/mkio2/singlegraph.php?type=' . $a['type'] . '&title=' . urlencode($a['title']) . $dimsparam . '" width="100%" height="550" frameborder="0" class="iframe-class"></iframe>';
}

add_shortcode('mkichart', 'mkichart_func');


function mkixls_meta_box_markup()
{
    global $post;
    $media = get_attached_media('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $post->ID);
    if (count($media) === 0) {
        $media = get_attached_media('application/vnd.ms-excel', $post->ID);
    }
    if (count($media) === 0) {
        $media = get_attached_media('text/csv', $post->ID);
    }
    if (count($media) === 0) { ?>
        <p>This function will appear if you add an attachement ("add media")
            which is a spreadsheet and save the post.</p>
        <?php
    } else {
        $surl = end($media)->guid;
        $types = array();
        $files = scandir('/var/www/html/wp-content/themes/mkinsight/mkio2/cache/');
        $dimensions = array();
        $acdims = array();
        foreach ($files as $file) {
            if (!startsWith($file, "http") && !startsWith($file, '.') && !startsWith($file, 'map') && strpos($file, '_') !== FALSE) {
                $fn = str_replace("__", ":", $file);
                $afn = explode("_", $fn);
                if (!in_array($afn[0], $types)) $types[] = $afn[0];
                if (!isset($dimensions[$afn[0]])) {
                    $dimensions[$afn[0]] = array();
                }
                $arr = &$dimensions[$afn[0]];
                $dims = substr($fn, strpos($fn, "_") + 1);
                $aafn = explode(".", $dims);
                $lev = 0;
                foreach ($aafn as $elem) {
                    if (!isset($arr[$elem])) {
                        $arr[$elem] = array();
                    }
                    $arr = &$arr[$elem];
                    if (!isset($acdims[$lev])) {
                        $acdims[$lev] = array();
                    }
                    if (!in_array($elem, $acdims[$lev])) {
                        $acdims[$lev][] = $elem;
                    }
                    $lev++;
                }
            }
        }
        ?>
        <div id="mki_secapi"></div>
        <script>
            <?php
            echo 'var types      = ' . json_encode($types) . ';' . "\n";
            echo 'var dimensions = ' . json_encode($dimensions) . ';' . "\n";
            echo 'var acdims     = ' . json_encode($acdims) . ';' . "\n";
            ?>
            spreadsheet.url = "<?php echo $surl;?>";
            mksse_init("mki_secapi");
        </script>
        <?php
    }
}

function add_mkixls_meta_box()
{
    add_meta_box("Create charts from spreadsheet", "Create charts from spreadsheet", "mkixls_meta_box_markup", "post", "normal", "low", null);
}

function startsWith($haystack, $needle)
{
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

add_action("add_meta_boxes", "add_mkixls_meta_box");

// add scripts and css to admin
function load_mkixls_admin_style()
{
    wp_register_style('mkixls_css', get_template_directory_uri() . '/secapi/secapi.css');
    wp_enqueue_style('mkixls_css');
    //  wp_register_style( 'mkixls_jquery-ui', '//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');
    //  wp_enqueue_style( 'mkixls_jquery-ui');
    //  wp_register_style( 'mkixls_bootstrap', "http://getbootstrap.com/dist/css/bootstrap.min.css");
    //  wp_enqueue_style( 'mkixls_bootstrap');

    // wp_enqueue_script( 'mkxls_jquery', "//code.jquery.com/jquery-1.9.1.js");
    //  wp_enqueue_script( 'mkxls_jquery-ui', "//code.jquery.com/ui/1.10.4/jquery-ui.js");
    wp_enqueue_script('mkxls_main_js', get_template_directory_uri() . '/secapi/js/secapi.js');
    wp_enqueue_script('mkxls_view_js', get_template_directory_uri() . '/secapi/js/vc.js');
    wp_enqueue_script('mkxls_typeahead_js', get_template_directory_uri() . '/secapi/js/typeahead.js');
}

add_action('admin_enqueue_scripts', 'load_mkixls_admin_style');

// enable additional mime types for uplaod
function my_myme_types($mime_types)
{
    $mime_types['xls'] = 'application/vnd.ms-excel';
    $mime_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    $mime_types['csv'] = 'text/csv';
    return $mime_types;
}

add_filter('upload_mimes', 'my_myme_types', 1, 1);

define('ALLOW_UNFILTERED_UPLOADS', true);


//hook into the init action and call create_book_taxonomies when it fires
add_action('init', 'mki_create_year_taxonomy', 0);

//create a custom taxonomy
function mki_create_year_taxonomy()
{

    $labels = array(
        'name' => _x('About Year', 'taxonomy general name'),
        'singular_name' => _x('Year', 'taxonomy singular name'),
        'search_items' => __('Search Year'),
        'all_items' => __('All Years'),
        'parent_item' => __('Parent Year'),
        'parent_item_colon' => __('Parent Year:'),
        'edit_item' => __('Edit Year'),
        'update_item' => __('Update Year'),
        'add_new_item' => __('Add New Year'),
        'new_item_name' => __('New Year Name'),
        'menu_name' => __('Year'),
    );

// Now register the taxonomy

    register_taxonomy('years', array('post'), array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'year'),
    ));

}

// Save the Year in the Post Meta Field
function mki_update_year_from_tag($post_id)
{
    $years = wp_get_post_terms($post_id, array('years'));
    delete_post_meta($post_id, 'years');
    foreach ($years as $year) {
        $y = $year->slug;
        add_post_meta($post_id, 'years', $y);
    }
}

add_action('save_post', 'mki_update_year_from_tag');

// Override query args to sort by years desc
function mki_orderby_args()
{
    return array(
        'meta_query' => array(
            'relation' => 'OR',
            'years_not' => array(
                'key' => 'years',
                'compare' => 'NOT EXISTS'
            ),
            'years' => array(
                'key' => 'years',
                'type' => 'NUMERIC',
                'compare' => 'EXISTS'
            )
        ),
        'orderby' => array('meta_value_num' => 'DESC', 'date' => 'DESC')
    );
}

function mki_search_filter_years($query)
{
    if (!is_admin()) {
        foreach (mki_orderby_args() as $key => $val) {
            $query->set($key, $val);
        }
    }
    return $query;
}

add_action('pre_get_posts', 'mki_search_filter_years');

// Advanced Search
function mki_advanced_search_query($query)
{

    if ($query->is_search()) {

        // tag search (not working)
        // if (isset($_GET['years']) && is_array($_GET['years'])) {
        // 	$query->set('tag_slug__or', $_GET['years']);
        // }
        // If ymin and ymax
        if (isset($_GET['ymin']) || isset($_GET['ymax'])) {
            $years = get_categories(array('taxonomy' => 'years', 'order' => 'DESC'));
            $use_years = array();
            foreach ($years as $yid => $year) {
                if (isset($_GET['ymin']) && $_GET['ymin'] > $year->slug) {
                    // Ignore
                } else
                    if (isset($_GET['ymax']) && $_GET['ymax'] < $year->slug) {
                        // Ignore
                    } else {
                        // Use!
                        array_push($use_years, $year->slug);
                    }
            }
            if (!empty($use_years)) {
//                var_dump($use_years);
                $query->set('years', $use_years);
            }
        }

        // category search
        if (isset($_GET['category']) && is_array($_GET['category'])) {
            $query->set('category_name', implode(',', $_GET['category']));
        }
        if (isset($_GET['tag'])) {
            $tags = explode(",", $_GET['tag']);
            $tagString = implode('+', $tags);
            $tagQuery = str_replace(" ", "-", $tagString);
//            $query->set('tag', "Milton Keynes");
            $query->set('tag', $tagQuery);
//            $query->set('tag',$tagQuery);
        }
        return $query;
    }

}

add_action('pre_get_posts', 'mki_advanced_search_query', 1000);

// DATA CHARTS
require_once('PHPExcel/Classes/PHPExcel.php');

function mki_data_file_get()
{
    if (!wp_verify_nonce($_REQUEST['nonce'], "mki_data_file_get_nonce")) {
        exit("Forbidden");
    }
    // However, even non logged in users can generate charts.
    $attachment_id = $_GET['post_id'];
    $post = get_post($attachment_id);
    // Only if it is an attachment
    if (!$post || !$post->post_type == 'attachment') {
        exit("Bad parameter");
    }
// TODO multiple file support
// output json supported by google chart
//    {
//        "cols": [
//        {"id":"","label":"Topping","pattern":"","type":"string"},
//        {"id":"","label":"Slices","pattern":"","type":"number"}
//      ],
//   "rows": [
//        {"c":[{"v":"Mushrooms","f":null},{"v":3,"f":null}]},
//        {"c":[{"v":"Onions","f":null},{"v":1,"f":null}]},
//        {"c":[{"v":"Olives","f":null},{"v":1,"f":null}]},
//        {"c":[{"v":"Zucchini","f":null},{"v":1,"f":null}]},
//        {"c":[{"v":"Pepperoni","f":null},{"v":2,"f":null}]}
//      ]
//    }

    // loading file
    $file = get_attached_file($attachment_id);
    $excelReader = PHPExcel_IOFactory::createReaderForFile($file);
    $excelReader->setReadDataOnly();
    $excelObj = $excelReader->load($file);

    // init result
    $tmp = $excelObj->getActiveSheet()->toArray(null, true, true, true);


    function arrayToCols($data)
    {
        $res = array();
        if (!$data[0]) {
            return $res;
        }
        $cols = $data[0];
        for ($i = 0; $i < count($cols); $i++) {
            $col = new stdClass();
            $col->id = "";
//            $label = $cols[$i] ? (string)$cols[$i] : 'vuoto';
//            print $label;
            $col->label = $cols[$i];
            $col->pattern = "";
            $col->type = "string";
            array_push($res, $col);
        }
        return $res;
    }

    function arrayToRows($data)
    {
        $res = array();
        if (!$data[0]) {
            return $res;
        }
        for ($i = 1; $i <= count($data); $i++) {
            $row = new stdClass();
            $row->c = rowToCels($data[$i]);
            array_push($res, $row);
        }
        return $res;
    }

    function rowToCels($data)
    {
        $res = array();
        if (!$data[0]) {
            return $res;
        }
        foreach ($data as $cel) {
            $c = new stdClass();
            $c->v = $cel;
            $c->f = null;
            array_push($res, $c);
        }
        return $res;
    }

    function objectToArray($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                array_push($result, objectToArray($value));
            }
            return $result;
        }
        return $data;
    }


    $dataArray = objectToArray($tmp);

    $data4charts = new stdClass();
    $data4charts->cols = arrayToCols($dataArray);
    $data4charts->rows = arrayToRows($dataArray);


    if ($_GET['format'] != 'twocols') {

        // return values
        print json_encode($data4charts);
        die;
    } else {
        /*
         * cr=4 start index of rows, default 0
         * ce=0 first col index, default 0
         * cv=2 second col index, default 1
         * vt=string data type, default string
         */
        $ce = (is_numeric(@$_GET['ce']) ? $_GET['ce'] : 0);
        $cv = (is_numeric(@$_GET['cv']) ? $_GET['cv'] : 1);
        $vt = $_GET['vt'];
        $cr = (is_numeric(@$_GET['cr']) ? $_GET['cr'] : 0);


        function extractCols($data, $start, $end, $type)
        {
            $col1 = $data[$start];
            $col2 = $data[$end];
            $col2->type = $type;
            $res = array($col1, $col2);
            return $res;
        }

        function extractRows($data, $col1, $col2, $start, $type)
        {
            $res = array();
            $batch = array_slice($data, max(0, $start - 1));
//            if(!$col1){$col1 = 0;}
            foreach ($batch as $row) {
                $newRow = new stdClass();
                $newRow->c = extractCels($row->c, $col1, $col2, $type);
                array_push($res, $newRow);
            }
            return $res;
        }

        function extractCels($data, $col1, $col2, $type)
        {
            $cel = new stdClass();

            // force casting to type of col2 values
            if ($type == 'number') {
                $cel->v = (int)$data[$col2]->v;
                $cel->f = $data[$col2]->f;
            } else {
                $cel->v = (string)$data[$col2]->v;
                $cel->f = $data[$col2]->f;
            }
            $res = array($data[$col1], $cel);
            return $res;
        }

        $twoColTable = new stdClass();
        $twoColTable->cols = extractCols($data4charts->cols, $ce, $cv, $vt);
        $twoColTable->rows = extractRows($data4charts->rows, $ce, $cv, $cr, $vt);
        print json_encode($twoColTable);
        die;
    }
}

add_action("wp_ajax_mki_data_file_get", "mki_data_file_get");
add_action("wp_ajax_nopriv_mki_data_file_get", "mki_data_file_get");

function mki_svg_to_png($stream, $output)
{
    $im = new Imagick();
    $im->readImageBlob($stream);
    $im->setImageFormat("png24");
    $im->writeImage($output);
    $im->clear();
    $im->destroy();
}

function mki_save_as_attachment()
{
    // check user is logged
    if (!is_user_logged_in()) {
        header("HTTP/1.0 403 Forbidden");
        print "You need to be logged in";
        die;
    }
    $p = get_post($_GET['post_id']);
    //
    $parent = wp_get_post_parent_id($p->ID);
    //check post is valid post
    if (!$parent) {
        header("HTTP/1.0 500 Server error");
        print "An error occurred: $parent";
        die;
    }
    // check user can edit post
    if (!current_user_can('edit_post', $parent)) {
        header("HTTP/1.0 403 Forbidden");
        $pa = get_post($parent);
        print "File not saved. You cannot edit \"" . $pa->post_title . "\"";
        die;
    }
    // Two supported formats
    $format = 'svg';
    if (@$_GET['format'] == 'png') {
        $format = 'png';
    }
    $bits = file_get_contents('php://input');
    // If PNG TODO Maybe other formats can be supported
    if ($format == 'png') {
        $temporaryPng = tempnam(sys_get_temp_dir(), 'prefix');
        mki_svg_to_png($bits, $temporaryPng);
        $bits = file_get_contents($temporaryPng);
    }
    $filename = sanitize_text_field($_GET['name'] . '.' . $format);
    //$filename = sanitize_text_field($p->post_title . '-chart.' . $format);
    $time = current_time('mysql');
    $upload = wp_upload_bits($filename, null, $bits, $time);
    // The ID of the post this attachment is for.
    $parent_post_id = $parent;
    // Get the path to the upload directory.
    $wp_upload_dir = wp_upload_dir();
    // Prepare an array of post data for the attachment.
    $attachment = array(
        //'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
        'guid' => $upload['url'],
        'post_mime_type' => 'image/svg+xml',
        'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
        'post_content' => 'Generated on ' . date('l jS \of F Y h:i:s A'),
        'post_status' => 'inherit'
    );
    // Insert the attachment.
    $attach_id = wp_insert_attachment($attachment, $upload['url'], $parent_post_id);
    // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    // Generate the metadata for the attachment, and update the database record.
    $attach_data = wp_generate_attachment_metadata($attach_id, $upload['url']);
    wp_update_attachment_metadata($attach_id, $attach_data);
    set_post_thumbnail($parent_post_id, $attach_id);
    print "The chart has been attached to the page ";
    die;
}

add_action("wp_ajax_mki_save_chart", "mki_save_as_attachment");
//
function allow_new_mime_type($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svgz+xml';
    return $mimes;
}

add_filter('mime_types', 'allow_new_mime_type');
function mki_shortcode_mkisvg($atts)
{
    if (!isset($atts['url'])) {
        return '[mkisvg error: "url" missing]';
    } else {
        return '<div>' . file_get_contents($atts['url']) . '</div>';
    }
}

add_shortcode('mkisvg', 'mki_shortcode_mkisvg');

/* INFOBOXES SHORTCODES */
function mki_shortcode_mkiinfobox($atts, $content = null)
{
    $content = do_shortcode($content);
    return <<<EOT
  <div class="panel-group" id="accordion">${content}</div>
  <p class="note"><strong>Note:</strong> Click on the linked heading text to expand or collapse the panels.</p>
  <div class="clearfix margin-bottom-40"></div>
EOT;
}

function mki_shortcode_mkiinfo($atts, $content = null)
{
    $title = @$atts['title'];
    $open = @$atts['open'] ? 'in' : '';
    $openClass = @$atts['open'] ? '' : 'collapsed';
    $expanded = @$atts['open'] ? 'aria-expanded="true"' : '';
    $id = sanitize_html_class($title);
    $content = do_shortcode($content);
    return <<<EOT
  <div class="panel panel-default">
    <div class="panel-heading">
        <a data-toggle="collapse" data-parent="#accordion" href="#${id}" class="${openClass}">
            <h4 class="panel-title">
                <i class="open-icon ion-arrow-down-b"></i>
                <i class="closed-icon ion-arrow-right-b"></i>
                ${title}
            </h4>
        </a>
    </div>
    <div id="${id}" class="panel-collapse collapse ${open}" ${expanded}>
      <div class="panel-body">${content}</div>
    </div>
  </div>
EOT;
}

add_shortcode('mkiinfobox', 'mki_shortcode_mkiinfobox');
add_shortcode('mkiinfo', 'mki_shortcode_mkiinfo');
/* FACTS SHORTCODES */
function mki_shortcode_mkifacts($atts, $content = null)
{
    $title = @$atts['title'];
    $color = @$atts['color'] ? sanitize_html_class($atts['color']) : 'blue';
    global $mkifacts_color, $mkifacts_counter;// FIXME there must be a better way
    if (!isset($mkifacts_counter)) {
        $mkifacts_counter = 0;
    }
    $mkifacts_counter++;
    // optimise layout
    $optimise = '';
    if ($mkifacts_counter % 3 == 0) {
        // Print each 3 boxes
        $optimise = <<<EOT
<div class="clearfix ipadv"></div>
EOT;
    }
    if ($mkifacts_counter % 4 == 0) {
        // Print each 3 boxes
        $optimise = <<<EOT
<div class="clearfix desktop"></div>
EOT;
    }
    $mkifacts_color = $color;
    //remove_filter( 'the_content', 'wpautop' );
    $content = do_shortcode($content);
    //add_filter( 'the_content', 'wpautop' );
    $mkifacts_color = null;
    return <<<EOT
  <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
    <div class="${color}-box-full">${title}</div>
  </div> ${optimise}
${content}
EOT;
}

function mki_shortcode_mkifact($atts, $content = null)
{
    global $mkifacts_color, $mkifacts_counter;

    if (!isset($mkifacts_counter)) {
        $mkifacts_counter = 0;
    }
    $mkifacts_counter++;
    $color = $mkifacts_color ? $mkifacts_color : 'blue';
    $icon = @$atts['icon'] ? $atts['icon'] : '';
    $content = do_shortcode($content);
    if ($icon) {
        $icon = <<<EOT
<div class="bottomright"><i data-icon="${icon}" class="icon" aria-hidden="true"></i></div>
EOT;
    } else if (@$atts['ionicon']) {
        $ionicon = @$atts['ionicon'] ? @$atts['ionicon'] : '';
        $icon = <<<EOT
<div class="bottomright"><i class="icon ${ionicon}" aria-hidden="true"></i></div>
EOT;
    }
    // optimise layout
    $optimise = '';
    if ($mkifacts_counter % 3 == 0) {
        // Print each 3 boxes
        $optimise = <<<EOT
<div class="clearfix ipadv"></div>
EOT;
    }
    if ($mkifacts_counter % 4 == 0) {
        // Print each 3 boxes
        $optimise = <<<EOT
<div class="clearfix desktop"></div>
EOT;
    }
    return <<<EOT
  <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
  <article class="${color}-box">${content}
  ${icon}</article>
  </div>${optimise}
EOT;
}

add_shortcode('mkifacts', 'mki_shortcode_mkifacts');
add_shortcode('mkifact', 'mki_shortcode_mkifact');
function mki_shortcode_mkifactlist($atts, $content = null)
{
    $content = do_shortcode($content);
    return <<<EOT
${content}
<div class="clearfix margin-bottom-40"></div>
EOT;
}

add_shortcode('mkifactlist', 'mki_shortcode_mkifactlist');
require_once dirname(__FILE__) . '/assets/includes/shortcode-wpautop-control.php';
chiedolabs_shortcode_wpautop_control(array('mkiinfobox', 'mkifactlist'));


// Breadcrumbs
function custom_breadcrumbs()
{

    // Settings
    $separator = '&gt;';
    $breadcrums_id = 'breadcrumbs';
    $breadcrums_class = 'breadcrumbs';
    $home_title = 'Home';

    // If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
    $custom_taxonomy = 'product_cat';

    // Get the query & post information
    global $post, $wp_query;

    // Do not display on the homepage
    if (!is_front_page()) {

        // Build the breadcrums
        echo '<ul id="' . $breadcrums_id . '" class="' . $breadcrums_class . '">';

        // Home page
        echo '<li class="item-home"><a class="bread-link bread-home" href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a></li>';
        echo '<li class="separator separator-home"> ' . $separator . ' </li>';

        if (is_archive() && !is_tax() && !is_category() && !is_tag()) {

            echo '<li class="item-current item-archive"><span class="bread-current bread-archive">' . post_type_archive_title($prefix, false) . '</span></li>';

        } else if (is_archive() && is_tax() && !is_category() && !is_tag()) {

            // If post is a custom post type
            $post_type = get_post_type();

            // If it is a custom post type display name and link
            if ($post_type != 'post') {

                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);

                echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
                echo '<li class="separator"> ' . $separator . ' </li>';

            }

            $custom_tax_name = get_queried_object()->name;
            echo '<li class="item-current item-archive"><span class="bread-current bread-archive">' . $custom_tax_name . '</span></li>';

        } else if (is_single()) {

            // If post is a custom post type
            $post_type = get_post_type();

            // If it is a custom post type display name and link
            if ($post_type != 'post') {

                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);

                echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
                echo '<li class="separator"> ' . $separator . ' </li>';

            }

            // Get post category info
            $category = get_the_category();

            if (!empty($category)) {

                // Get last category post is in
                $last_category = end(array_values($category));

                // Get parent any categories and create array
                $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','), ',');
                $cat_parents = explode(',', $get_cat_parents);

                // Loop through parent categories and store in variable $cat_display
                $cat_display = '';
                foreach ($cat_parents as $parents) {
                    $cat_display .= '<li class="item-cat">' . $parents . '</li>';
                    $cat_display .= '<li class="separator"> ' . $separator . ' </li>';
                }

            }

            // If it's a custom post type within a custom taxonomy
            $taxonomy_exists = taxonomy_exists($custom_taxonomy);
            if (empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {

                $taxonomy_terms = get_the_terms($post->ID, $custom_taxonomy);
                $cat_id = $taxonomy_terms[0]->term_id;
                $cat_nicename = $taxonomy_terms[0]->slug;
                $cat_link = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                $cat_name = $taxonomy_terms[0]->name;

            }

            // Check if the post is in a category
            if (!empty($last_category)) {
                echo $cat_display;
                echo '<li class="item-current item-' . $post->ID . '"><span class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</span></li>';

                // Else if post is in a custom taxonomy
            } else if (!empty($cat_id)) {

                echo '<li class="item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '"><a class="bread-cat bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a></li>';
                echo '<li class="separator"> ' . $separator . ' </li>';
                echo '<li class="item-current item-' . $post->ID . '"><span class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</span></li>';

            } else {

                echo '<li class="item-current item-' . $post->ID . '"><span class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</span></li>';

            }

        } else if (is_category()) {

            // Category page
            echo '<li class="item-current item-cat"><span class="bread-current bread-cat">' . single_cat_title('', false) . '</span></li>';

        } else if (is_page()) {

            // Standard page
            if ($post->post_parent) {

                // If child page, get parents
                $anc = get_post_ancestors($post->ID);

                // Get parents in the right order
                $anc = array_reverse($anc);

                // Parent page loop
                if (!isset($parents)) $parents = null;
                foreach ($anc as $ancestor) {
                    $parents .= '<li class="item-parent item-parent-' . $ancestor . '"><a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
                    $parents .= '<li class="separator separator-' . $ancestor . '"> ' . $separator . ' </li>';
                }

                // Display parent pages
                echo $parents;

                // Current page
                echo '<li class="item-current item-' . $post->ID . '"><span title="' . get_the_title() . '"> ' . get_the_title() . '</span></li>';

            } else {

                // Just display current page if not parents
                echo '<li class="item-current item-' . $post->ID . '"><span class="bread-current bread-' . $post->ID . '"> ' . get_the_title() . '</span></li>';

            }

        } else if (is_tag()) {

            // Tag page

            // Get tag information
            $term_id = get_query_var('tag_id');
            $taxonomy = 'post_tag';
            $args = 'include=' . $term_id;
            $terms = get_terms($taxonomy, $args);
            $get_term_id = $terms[0]->term_id;
            $get_term_slug = $terms[0]->slug;
            $get_term_name = $terms[0]->name;

            // Display the tag name
            echo '<li class="item-current item-tag-' . $get_term_id . ' item-tag-' . $get_term_slug . '"><span class="bread-current bread-tag-' . $get_term_id . ' bread-tag-' . $get_term_slug . '">' . $get_term_name . '</span></li>';

        } elseif (is_day()) {

            // Day archive

            // Year link
            echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
            echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';

            // Month link
            echo '<li class="item-month item-month-' . get_the_time('m') . '"><a class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</a></li>';
            echo '<li class="separator separator-' . get_the_time('m') . '"> ' . $separator . ' </li>';

            // Day display
            echo '<li class="item-current item-' . get_the_time('j') . '"><span class="bread-current bread-' . get_the_time('j') . '"> ' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</span></li>';

        } else if (is_month()) {

            // Month Archive

            // Year link
            echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
            echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';

            // Month display
            echo '<li class="item-month item-month-' . get_the_time('m') . '"><span class="bread-month bread-month-' . get_the_time('m') . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</span></li>';

        } else if (is_year()) {

            // Display year archive
            echo '<li class="item-current item-current-' . get_the_time('Y') . '"><span class="bread-current bread-current-' . get_the_time('Y') . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</span></li>';

        } else if (is_author()) {

            // Auhor archive

            // Get the author information
            global $author;
            $userdata = get_userdata($author);

            // Display author name
            echo '<li class="item-current item-current-' . $userdata->user_nicename . '"><span class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</span></li>';

        } else if (get_query_var('paged')) {

            // Paginated archives
            echo '<li class="item-current item-current-' . get_query_var('paged') . '"><span class="bread-current bread-current-' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '">' . __('Page') . ' ' . get_query_var('paged') . '</span></li>';

        } else if (is_search()) {

            // Search results page
            echo '<li class="item-current item-current-' . get_search_query() . '"><span class="bread-current bread-current-' . get_search_query() . '" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . '</span></li>';

        } elseif (is_404()) {

            // 404 page
            echo '<li>' . 'Error 404' . '</li>';
        }

        echo '</ul>';

    }

}


function childrenPages()
{
    if (is_page() && !is_front_page()) {
        global $post;
        $children = wp_list_pages(array(
            'child_of' => $post->ID,
            'depth' => 1,
            'show_date' => '',
            'title_li' => false,
            'link_after' => '',
            'link_before' => '',
            'echo' => 0
        ));

        if ($children != "") {
            echo "<div id=\"children-pages-nav\" >$post->post_title is including: <ul class=\"children-pages\">$children</ul></div>";
        }
    }
}

function my_custom_function($html)
{ //Alter final html
    preg_match('~<h3>([^{]*)</h3>~i', $html, $h);
    $head = "$h[1]: ";
    $dropOpen = "<div class=\"dropdown\" >";
    $dropClose = "</div>";
    $downloadButton = "<button class=\"btn btn-primary dropdown-toggle\" type=\"button\" id=\"menudownload-2062\" data-toggle=\"dropdown\" aria-expanded=\"true\">Download<span class=\"bs-caret\"><span class=\"caret\"></span></span></button>";
    $chartsButton = "<button class=\"btn btn-primary dropdown-toggle\" type=\"button\" id=\"menudownload-2062\" data-toggle=\"dropdown\" aria-expanded=\"true\">Preview<span class=\"bs-caret\"><span class=\"caret\"></span></span></button>";
    // get list
    preg_match('~<ul class="post-attachments">([^{]*)</ul>~i', $html, $match);
    $list = ($match[1]);
//    $listPreview = str_replace("", "", $list);
    preg_match_all('/href="([^{]*)"/', $list, $urls);
    $listPreview = $list;
    $urls = $urls[1];
    foreach ($urls as $url) {
        $id = attachment_url_to_postid($url);
        $link = "/chart-generator?data=$id";
        $listPreview = str_replace($url, $link, $listPreview);
    }
//    var_dump($urls);
    $new_html = "<div class=\"fileactions entry-meta-end\">$head$dropOpen$downloadButton<ul class='dropdown-menu'>$list</ul>$dropClose $dropOpen$chartsButton<ul class='dropdown-menu'>$listPreview</ul>$dropClose</div>";
    echo $new_html;
//    return $new_html;
    return "";
}

add_filter('wpatt_list_html', 'my_custom_function');