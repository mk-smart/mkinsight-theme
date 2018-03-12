<?php get_header();  global $post; ?>
<section role="main">
    <header class="header">
        <div class="col-xl-offset-2 col-xl-8 col-lg-10 col-lg-offset-1 col-md-12 col-sm-12 col-xs-12 advanced-search">
            <form id="advanced-search-form" class="" role="search" method="get" action="<?php print home_url(); ?>">
                <h1 class="entry-title form-group" id="search-title">
                    <span for="s"><?php _e("Results:", "mki"); ?></span>
                    <input type="text" class="form-control" value="<?php print @$_GET['s']; ?>" name="s" id="s"
                           placeholder="keywords">
                </h1>
                <div id="advanced-filter-wrapper">
                    <div class="collapse" id="advanced-filters">
                        <div class="form-group">
                            <label style="display:inline-block"><?php _e("Tags:", "mki"); ?></label>
                            <input id="tag-input" type="text"
                                   value="<?php echo str_replace("-", " ", @$_GET['tag']); ?>" data-role="tagsinput"
                                   name="tag"/>
                        </div>
                        <!-- YEAR RANGE -->
                        <div class="form-group">
                            <label style="display:inline-block"><?php _e("About years:", "mki"); ?></label>
                            <span>
                                <?php _e("from", "mki"); ?>
                                <select class="min year" id="minYear" name="ymin">
                                    <option> ---</option>
                                    <?php $categories = get_categories(array('taxonomy' => 'years', 'order' => 'ASC'));
                                    foreach ($categories as $category):
                                        $cslug = $category->slug;
                                        $checked = ($cslug == $_GET['ymin']) ? 'selected="selected"' : ""; ?>
                                        <option value="<?php print $cslug; ?>" <?php print $checked; ?> ><?php print $category->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php _e("to", "mki"); ?>
                                <select class="max year" id="maxYear" name="ymax">
                                    <option> ---</option>
                                    <?php $categories = get_categories(array('taxonomy' => 'years', 'order' => 'DESC'));
                                    foreach ($categories as $category):
                                        $cslug = $category->slug;
                                        $checked = ($cslug == $_GET['ymax']) ? 'selected="selected"' : ""; ?>
                                        <option value="<?php print $cslug; ?>" <?php print $checked; ?> ><?php print $category->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </span>
                        </div>
                        <div class="form-group">
                            <label style="display: inline-block;"><?php _e("Sorting by: ", "mki"); ?></label>
                            <label class="radio-inline" style="font-weight: 500;">
                                <input type="radio" value="DESC" name="order" <?php echo @$_GET['order'] != 'ASC' ? 'checked': ''; ?> />
                                <?php _e("Newer to Older", "mki"); ?>
                            </label>
                            <label class="radio-inline"" style="font-weight: 500;">
                            <input type="radio" value="ASC" name="order" <?php echo @$_GET['order'] == 'ASC' ? 'checked': ''; ?> />
                            <?php _e("Older to Newer", "mki"); ?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label><?php _e("Categories:", "mki"); ?></label>
                            <ul class="checkboxes list-unstyled row">
                                <?php
                                // generate list of categories
                                $categories = get_categories();
                                $checkedCats = [];
                                foreach ($categories as $category) {
                                    $cslug = $category->slug;
                                    $cname = $category->name;
                                    $checked = (@in_array($cslug, $_GET['category'])) ? 'checked="checked"' : "";
                                    if ($checked != "") {
                                        array_push($checkedCats, $cslug);
                                    }
                                    echo "<li class='checkbox col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6'><input id='check-$cslug' type='checkbox' value='$cslug' $checked name='category[]' class='form-check-input'><label for='check-$cslug' class=\"form-check-label\">$cname</label></li>";
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="form-group" style="text-align:right;">
                            <input style="display: inline-block;width: auto;" type="submit" id="searchsubmit"
                                   value="<?php _e("Apply Filters", "mki"); ?>">
                        </div>
                    </div>
                    <a id="advance-search-toggler" class="collapsed" data-toggle="collapse" href="#advanced-filters"
                       role="button" aria-expanded="false" aria-controls="advanced-filters"><i class="icon"></i>
                        <?php _e("Advanced Search", "mki"); ?>
                    </a>
                </div>
            </form>
        </div>

    </header>
    <?php
    // Prepare query
    global $wp_query;
    $total = $wp_query->found_posts;
    $paginationArgs = array();
    $keyword_search_type = 'all';
    $search_query_text = get_search_query();
    // If no results, try changing query terms in OR
    if ($total == 0) {
        $keyword_search_type = 'any';
        $query_vars = $wp_query->query_vars;
        // Collect post ids
        $post_ids = array();
        foreach ($query_vars['search_terms'] as $key => $keyword) {
            # get posts with only this term
            $result = new WP_Query(array('s' => $keyword, 'fields' => 'ids'));
            $post_ids = array_merge($post_ids, $result->posts);
        }
        // If any result
        if (!empty($post_ids)) {
            // Override $wp_query
            $wp_query = new WP_Query(array('post__in' => $post_ids));
            $total = $wp_query->found_posts;
        }
    }
    ?>
    <?php if (have_posts()) : ?>
        <div class="col-xl-8 col-xl-offset-2 col-lg-10 col-lg-offset-1 col-md-12 col-sm-12 col-xs-12 results">
            <header class="header">
                <h2 class="entry-title"><?php echo $total, ' item', ($total > 1) ? 's' : '', '.'; ?></h2>
                <p><?php if (isset($_GET['s']) && $_GET['s'] != '') {
                        // Change the message if query rewritten to 'any'
                        if ($keyword_search_type == 'any') {
                            echo '<small><i>We could not find any result including all the search terms. The list below includes pages that contain any of them.</i></small><br/>';
                        }
                        echo 'Keywords: ', $search_query_text, '<br/>';
                    }
                    ?>
                    <?php if (isset($_GET['category'])) {
                        $trms = get_terms(array('slug' => $_GET['category'], 'fields' => 'names'));
                        echo 'Categories: ', implode(', ', array_unique($trms)), '<br/>';
                    } ?>
                    <?php if (isset($_GET['years'])) {
//							$trms = get_terms(array('taxonomy'=>'years','slug'=>$_GET['years'],'fields'=>'names'));
                        echo 'About years: ', implode(', ', $_GET['years']), '<br/>';
                    } ?>
                </p>
            </header>
            <div class="results_pagination">
                <?php //echo paginate_links($paginationArgs); ?>
            </div>
            <?php while (have_posts()) : the_post(); ?>
                <?php //get_template_part( 'entry' ); ?>
                <div>
                    <h3>
                        <?php
                            $permalink = get_the_permalink();
                            // if attachment switch permalink
                            if(get_post_type() == 'attachment'){
                             $permalink = wp_get_attachment_url(get_the_ID());
                            }
                        ?>
                        <a href="<?php echo $permalink; ?>">
                            <?php
                            /* add icon to title
                             * folder: img/infographics/
                             * report: pie-chart3.png
                             * data: data-green.png
                             * page: document.png
                            */
                            // get post categories and filters for data or report
                            $cat = array_reduce(get_the_category(), function ($carry, $cat) {
                                // if category found
                                if ($carry) {
                                    return $carry;
                                }
                                // search for category
                                if ($cat->slug === 'data') {
                                    return 'data';
                                }
                                if ($cat->slug === 'report') {
                                    return 'report';
                                }
                                if ($cat->slug === 'news') {
                                    return 'news';
                                }
                                if ($cat->slug === 'essential') {
                                    return 'essential';
                                }
                                // default false
                                return false;
                            });


                            switch ($cat) {
                                case 'report':
                                    echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="' . get_template_directory_uri() . '/assets/img/svg/pie-chart3.svg">';
                                    break;
                                case 'data':
                                    echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="' . get_template_directory_uri() . '/assets/img/svg/data-green.svg">';
                                    break;
                                default:
                                    if (get_post_type() == 'idea') {
                                        echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="' . get_template_directory_uri() . '/assets/img/svg/light-bulb-green.svg">';
                                    } else {
                                        echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="' . get_template_directory_uri() . '/assets/img/svg/document.svg">';
                                    }
                            }
                            ?>
                            <?php
                                 the_title();
                            ?>
                        </a>
                    </h3>
                    <?php
                        if($attachmentDownload){
                            echo  "<h4>".__("Attachment found: ","mki")." $attachmentDownload";
                        }
                    ?>
                    <section class="entry-meta">
                        <?php if (get_the_terms(get_the_ID(), 'years')) : ?>
                            <span class="entry-date">
                                <?php
                                echo 'About ';
                                $years = get_the_terms(get_the_ID(), 'years');
                                $year = $years[0]->name;
                                echo "<a href=\"#\" onclick=\"setYear($year)\">$year</a>";
                                ?>
                            </span>
                            <span class="meta-sep"> | </span>
                        <?php endif; ?>
                        <span class="entry-date">
                            <?php the_time(get_option('date_format')); ?>
                        </span>
                    </section>
                    <?php
                        $postcats = get_the_category();
                        $posttags = get_the_tags();
                        if($posttags || $postcats):
                    ?>
                    <footer class="entry-footer">
                        <?php if($postcats){?>
                        <div class="cat-links">
                            <?php _e('Categories: ', 'mki'); ?>
                            <?php
                                foreach ($postcats as $cat) {
                                    $checked = in_array($cat->slug, $checkedCats);
                                    $cSlug = '\'' . trim($cat->slug) . '\'';
                                    if ($checked) {
                                        echo "<button class='unset' onclick=\"unsetCat($cSlug)\">- $cat->name</button>";
                                    } else {
                                        echo "<button onclick=\"setCat($cSlug)\">+ $cat->name</button>";
                                    }

                                }
                            ?>
                        </div>
                        <?php } ?>
                        <?php if($posttags){ ?>
                        <div class="tag-links">
                            <?php _e('Tags: ', 'mki'); ?>
                            <?php

                                foreach ($posttags as $tag) {
                                    $tags = explode(",", @$_GET['tag']);
                                    $check = (!in_array(str_replace(" ", "-", $tag->name), $tags));
                                    $tSlug = '\'' . trim($tag->name) . '\'';
                                    if ($check) {
                                        echo "<button onclick=\"setTag($tSlug)\">+ $tag->name</button>";
                                    } else {
                                        echo "<button class='unset' onclick=\"unsetTag($tSlug)\">- $tag->name</button>";
                                    }
                                }
                            ?>
                        </div>
                        <?php } ?>
                        <?php if (comments_open()) {
                            echo '<span class="meta-sep">|</span> <span class="comments-link">
<a href="' . get_comments_link() . '">' . sprintf(__('Comments', 'mki')) . '</a>
</span>';
                        } ?>
                    </footer>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            <div class="results_pagination">
                <?php echo paginate_links($paginationArgs); ?>
            </div>
        </div>
        <?php //get_template_part( 'nav', 'below' ); ?>
    <?php else : ?>
        <div class="col-xl-8 col-xl-offset-2 col-lg-10 col-lg-offset-1 col-md-12 col-sm-12 col-xs-12 results">
            <article id="post-0" class="post no-results not-found">
                <header class="header">
                    <h2 class="entry-title"><?php _e('Nothing Found', 'blankslate'); ?></h2>
                </header>
                <section class="entry-content">
                    <p><?php _e('Sorry, nothing matched your search. Please try again.', 'blankslate'); ?></p>
                    <?php //get_search_form(); ?>
                </section>
            </article>
        </div>
    <?php endif; ?>
</section>
</div>
</div>
</div>
</div>
<script type="text/javascript">
    // general approach
    // set search params and reload

    // apply time filter and reload
    function setYear(year) {
        if (year) {
            $('#minYear').val(year);
            $('#maxYear').val(year);
            $('#advanced-search-form').submit();
        }
    }

    // add category filter
    function setCat(cat) {
        if (cat) {
            // unckeck checkbox
            $('#check-' + cat).prop('checked', true);
            // submit form
            $('#advanced-search-form').submit();
        }
    }

    // add tag to filter and apply
    function setTag(tag) {
        if (tag) {
            // $('#s').val(tag);
            $("#tag-input").tagsinput("add", tag);
            $('#advanced-search-form').submit();

        }
    }

    // uncheck the category and submit the form
    function unsetCat(cat) {
        if (cat) {
            // unckeck checkbox
            $('#check-' + cat).prop('checked', false);
            // submit form
            $('#advanced-search-form').submit();
        }
    }

    // add tag to filter and apply
    function unsetTag(tag) {
        $("#tag-input").tagsinput("remove", tag);
        $('#advanced-search-form').submit();
    }
</script>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
