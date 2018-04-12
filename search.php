<?php
global $post;
// Prepare query
global $wp_query;
$total = $wp_query->found_posts;
?>
<?php get_header(); ?>
<section role="main">
    <label class="counter"><?php echo $total, ' entries', ($total > 1) ? 's' : ''; ?></label>
    <?php if (have_posts()) : ?>
        <div class="results">
            <?php while (have_posts()) : the_post(); ?>
                <div class="row">
                    <div class="col-lg-4 col">
                        <h3>
                            <?php
                            $permalink = get_the_permalink();
                            // if attachment switch permalink
                            if (get_post_type() == 'attachment') {
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
                    </div>
                    <div class="col-lg-2 col">
                        <?php if (get_the_terms(get_the_ID(), 'years')) : ?>
                            <div class="entry-date">
                                <?php
                                echo 'About ';
                                $years = get_the_terms(get_the_ID(), 'years');
                                $yList = array();
                                foreach ($years as $year) {
                                    $yearName = $year->name;
                                    array_push($yList, "<a href=\"#\" onclick=\"setYear($yearName)\">$yearName</a>");
                                }
                                echo implode(", ", $yList);
                                ?>
                            </div>
                        <?php else: ?>
                            <div class="entry-date">
                                <?php the_time(get_option('date_format')); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-4 col">
                        <?php
                        $postcats = get_the_category();
                        $posttags = get_the_tags();
                        $tags = array_filter(array_map('trim', explode(",", @$_GET['tags'])), function ($value) {
                            return $value !== '';
                        });
                        if ($posttags || $postcats):
                            ?>
                            <?php if ($postcats) { ?>
                            <div class="cat-links">
                                <?php _e('Categories: ', 'mki'); ?>
                                <?php
                                foreach ($postcats as $cat) {
                                    $checked = (!in_array($cat->name, $tags));
                                    $cSlug = '\'' . trim($cat->slug) . '\'';
                                    if ($checked) {
                                        echo "<button onclick=\"setCat($cSlug)\">$cat->name</button>";
                                    } else {
                                        echo "<button class='unset' onclick=\"unsetCat($cSlug)\"><i class='icon ion-close-round'></i>$cat->name</button>";
                                    }

                                }
                                ?>
                            </div>
                        <?php } ?>
                            <?php if ($posttags) { ?>
                            <div class="tag-links">
                                <?php _e('Tags: ', 'mki'); ?>
                                <?php

                                foreach ($posttags as $tag) {
                                    $checked = (!in_array(str_replace(" ", "-", $tag->name), $tags));
                                    $tSlug = '\'' . trim($tag->name) . '\'';
                                    if ($checked) {
                                        echo "<button onclick=\"setTag($tSlug)\">$tag->name</button>";
                                    } else {
                                        echo "<button class='unset' onclick=\"unsetTag($tSlug)\"><i class='icon ion-close-round'></i>$tag->name</button>";
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
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-2 col">
                        <?php
                        if ($attachmentDownload) {
                            echo "<h4>" . __("Attachment found: ", "mki") . " $attachmentDownload";
                        }
                        ?>
                    </div>
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
    // timeline
    <?php
    $years = array_map(function ($y) {
        return $y->name;
    }, get_categories(array('taxonomy' => 'years', 'order' => 'ASC')));
    //var_dump($years);
    ?>
    var years = <?php echo "[" . implode(",", $years) . "]"; ?>;
    var ymin = <?php echo $_GET['ymin'] ? $_GET['ymin'] : $years[0]; ?>;
    var ymax = <?php echo $_GET['ymax'] ? $_GET['ymax'] : end($years); ?>;
    // console.log(years,ymin,ymax);
    var handleFrom = $("#year-from");
    var handleTo = $("#year-to");
    $("#slider-range").slider({
        range: true,
        create: function (event, ui) {
            // console.log($(this).slider("values"));
            var values = $(this).slider("values");
            handleFrom.text(values[0]);
            handleTo.text(values[1]);
        },
        min: years[0],
        max: years[years.length - 1],
        values: [ymin, ymax],
        slide: function (event, ui) {
            // console.log(ui.values);
            handleFrom.text(ui.values[0]);
            $('#advanced-search-form input[name="ymin"]').val(ui.values[0]);
            handleTo.text(ui.values[1]);
            $('#advanced-search-form input[name="ymax"]').val(ui.values[1]);
            // $( "#year-range-from" ).val( ui.values[ 0 ]);
            // $( "#year-range-to" ).val( ui.values[ 1 ] );
        },
        stop: function (event, ui) {
            // update page
            $('#advanced-search-form').submit();
        }
    });
    $("#year-range-from").val($("#slider-range").slider("values", 0));
    $("#year-range-to").val($("#slider-range").slider("values", 1));
    // end timeline


    // timeless toggler
    $('#timestamp input[name="stamped"]').change(function () {
        console.log($(this).val());
        setTimeout(function () {
            $('#advanced-search-form').submit();
        }, 500);
    });
    // end timeless toggler


    // text/tag switch management
    $('#text-switch .btn').click(function () {
        // console.log('text');
        $(this).addClass('active');
        $('#tag-switch .btn').removeClass('active');
        $('#searchbox input[name="s"]').attr('type', 'text');
        $('#searchbox input[name="tags"]').attr('type', 'hidden');
        $('#searchbox input[name="tags"]').val('');
    });
    $('#tag-switch .btn').click(function () {
        // console.log('tags');
        $(this).addClass('active');
        $('#text-switch .btn').removeClass('active');
        $('#searchbox input[name="s"]').attr('type', 'hidden');
        $('#searchbox input[name="s"]').val('');
        $('#searchbox input[name="tags"]').attr('type', 'text');
    });
    // end text/tag switch management

    // autocomplete
    <?php
    //list of categories
    $cats = array_map(function ($c) {
        return "\"$c->cat_name\"";
    }, get_categories());
    // list of tags
    $tags = array_map(function ($term) {
        return "\"$term->name\"";
    }, get_tags());
    $listAutocomplete = array_merge($cats, $tags);
    $terms = implode(',', $listAutocomplete);
    // var_dump($terms);
    ?>
    var availableTags = <?php echo "[${terms}]"; ?>;
    $('#searchbox input[name="tags"]').on("keydown", function (event) {
        // console.log(event.keyCode);
        if (event.keyCode === $.ui.keyCode.TAB &&
            $(this).autocomplete("instance").menu.active) {
            event.preventDefault();
        }
    }).autocomplete({
        minLength: 0,
        source: function (request, response) {
            // delegate back to autocomplete, but extract the last term
            response($.ui.autocomplete.filter(
                availableTags, extractLast(request.term)));
        },
        focus: function () {
            // prevent value inserted on focus
            return false;
        },
        select: function (event, ui) {
            var terms = split(this.value);
            // remove the current input
            terms.pop();
            // add the selected item
            terms.push(ui.item.value);
            // add placeholder to get the comma-and-space at the end
            terms.push("");
            this.value = terms.join(", ");
            return false;
        }
    });

    function split(val) {
        return val.split(/,\s*/);
    }

    function extractLast(term) {
        return split(term).pop();
    }

    // end autocomplete


    // sorting
    $('#sorting button').each(function () {
        $(this).click(function () {
            var field = $(this).attr('sorting');
            var input = $(this).children('input');
            var old = input.val();
            // toggle value
            var newVal = (old === 'ASC' ? 'DESC' : 'ASC');
            console.log(field, old, newVal);
            // set new value
            input.val(newVal);
            // scramble sort ordering
            var oldOrder = $('#sorting').children('input[name="orderby"]').val().split(',');
            var newOrder = [field].concat(oldOrder.filter(function (f) {
                return f !== field;
            }));
            // set new sort ordering
            $('#sorting').children('input[name="orderby"]').val(newOrder);
            // update page
            $('#advanced-search-form').submit();
        });
    });

    // end sorting

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
