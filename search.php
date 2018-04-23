<?php
global $post;
// Prepare query
global $wp_query;
$total = $wp_query->found_posts;

$years = array_map(function ($y) {
    return $y->name;
}, get_categories(array('taxonomy' => 'years', 'order' => 'ASC')));
//asort($years);
$ymin = $_GET['ymin'] ? intval($_GET['ymin']) : intval($years[0]);
$ymax = $_GET['ymax'] ? intval($_GET['ymax']) : intval(end($years));

// todo to be fixed somehow
// failsafe in casae of empty taxonomy
if(!isset($ymin)){$ymin = 1981;}
if(!isset($ymax)){$ymin = 2018;}

//var_dump($years);
?>
<?php get_header(); ?>
<section role="main">
    <?php if (have_posts()) : ?>
        <div class="results">
            <?php while (have_posts()) : the_post(); ?>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col">
                        <h3 class="entry-title">
                            <?php
                            $permalink = get_the_permalink();
                            // if attachment switch permalink
                            ?>
                            <a href="<?php echo $permalink; ?>" target="_blank">
                                <?php
                                /* add icon to title
                                 * folder: img/infographics/
                                 * report: certified-document.png
                                 * data: pie-chart3.png
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
                                        echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="' . get_template_directory_uri() . '/assets/img/svg/certified-document.svg">';
                                        break;
                                    case 'data':
                                        echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="' . get_template_directory_uri() . '/assets/img/svg/pie-chart3.svg">';
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
                                $title = get_the_title();
                                $s = @$_GET['s'];
                                if (isset($s)) {
//                                    echo 'found '.$s.' '.strpos($title, $s)." ".strlen($s).' || ';

                                    $pos = strpos(strtolower($title), $s);
                                    if ($pos) {
                                        $label = substr($title, $pos, strlen($s));
                                        $title = substr_replace($title, "<u>$label</u>", $pos, strlen($s));
                                    }
                                }
                                echo $title;
                                ?>
                            </a>
                        </h3>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 col">
                        <?php if (get_the_terms(get_the_ID(), 'years')) : ?>
                            <div class="entry-date">
                                <?php
                                // year range
//                                $ymin = intval(@$_GET['ymin']);
//                                $ymax = intval(@$_GET['ymax']);
                                $postYears = array_map(function ($y) {
                                    return $y->name;
                                }, get_the_terms(get_the_ID(), 'years'));

                                // interval
                                if (count($postYears) > 1) {
                                    asort($postYears);
                                    $postMinY = intval($postYears[0]);
                                    $postMaxY = intval(end($postYears));
                                    $class = ($postMinY <= $ymax && $postMinY >= $ymin) && ($postMaxY <= $ymax && $postMaxY >= $ymin) ? 'selected' : '';
                                    echo "<a href=\"#\" class=\"${class}\" onclick=\"setInterval($minY,$maxY)\">";
                                    echo $postMinY . ' - ' . $postMaxY;
                                    echo '</a>';
                                } else if (count($postYears)) {
                                    $postYear = intval($postYears[0]);
                                    $class = ($postYear <= $ymax && $postYear >= $ymin) ? 'selected' : '';
//                                    var_dump($postYear);
                                    echo "<a href=\"#\" class=\"${class}\" onclick=\"setYear($postYear)\">$postYear</a>";
                                }
                                ?>
                            </div>
                        <?php else: ?>
                            <div class="entry-date">
                                <?php the_time(get_option('date_format')); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col">
                        <?php
                        $postcats = get_the_category();
                        $posttags = get_the_tags();

                        $tags = array_filter(array_map('trim', explode(",", @$_GET['tags'])), function ($value) {
                            return $value !== '';
                        });

                        //                        $keywords = array_unique(array_merge($postcats,$posttags));
                        $keywords = array_merge($postcats, $posttags);
                        if ($keywords):
                            ?>
                            <div class="tag-links">
                                <?php
                                foreach ($keywords as $tag) {
                                    $checked = (!in_array($tag->name, $tags));
                                    $tSlug = '\'' . trim($tag->name) . '\'';
                                    if ($checked) {
                                        echo "<button onclick=\"setTag($tSlug)\">$tag->name</button>";
                                    } else {
                                        echo "<button class='unset' onclick=\"unsetTag($tSlug)\"><i class='icon ion-close-round'></i>$tag->name</button>";
                                    }
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 col fileactions">
                        <?php
                        // list of files
                        $files = get_attached_media('', $query->post->ID);
                        // list of files csv like
                        $filesPreview = array_filter($files, function ($file) {
                            switch ($file->post_mime_type) {
                                case 'application/vnd.ms-excel':
                                    return true;
                                case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                                    return true;
                                default:
                                    return false;
                            }
                        });
                        // disabling chart feature if user is not editor
                        $chart = false;
                        if (current_user_can('edit_post', $query->post->ID)) {
                            $chart = true;
                        }
                        ?>
                        <div class="dropdown" style="">
                            <button class="btn btn-primary dropdown-toggle <?php echo (count($filesPreview) > 0) ? '' : 'btn-block'; ?>"
                                    type="button"
                                    id="menudownload-<?php print $file->ID; ?>"
                                    data-toggle="dropdown">
                                <?php _e('Download', 'mki'); ?>
                                <span class="bs-caret"><span class="caret"></span></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php

                                foreach ($files as $fid => $file):
                                    ?>

                                    <li role="presentation">
                                        <a href="<?php print $file->guid; ?>"
                                           title="Download file: <?php print $file->post_title; ?>"
                                           id="file-<?php print $file->ID; ?>">
                                            <?php print $file->post_title; ?>
                                            <?php echo "[$file->post_mime_type]"; ?>
                                        </a>
                                    </li>

                                <?php
                                endforeach;
                                ?>
                            </ul>
                        </div>
                        <?php
                        if ($chart && count($filesPreview) > 0):
                            ?>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button"
                                        id="menucharts-<?php print $file->ID; ?>"
                                        data-toggle="dropdown">
                                    <?php echo $chart ? __('Charts', 'mki') : __('Preview', 'mki'); ?>
                                    <span class="bs-caret"><span class="caret"></span></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu"
                                    aria-labelledby="menucharts-<?php print $file->ID; ?>">
                                    <?php
                                    foreach ($filesPreview as $fid => $file): ?>
                                        <li role="presentation">
                                            <a title="Chart generator: <?php print $file->post_title; ?>"
                                               role="menuitem"
                                               id="file-<?php print $file->ID; ?>"
                                               href="/chart-generator/?data=<?php print $file->ID; ?>">
                                                <?php print $file->post_title; ?>
                                                <?php
                                                /*
                                                 * MIMEtypes
                                                 * - application/vnd.ms-excel > XLS
                                                 * - application/vnd.openxmlformats-officedocument.spreadsheetml.sheet > CSV
                                                 */
                                                echo '[';
                                                if ($file->post_mime_type == "application/vnd.ms-excel") {
                                                    print "Excel";
                                                } else {
                                                    print "CSV";
                                                }
                                                echo ']';
                                                ?>
                                            </a>
                                        </li>
                                    <?php
                                    endforeach;
                                    ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            <div class="results_pagination">
                <?php echo paginate_links($paginationArgs); ?>
            </div>
        </div>
        <?php //get_template_part( 'nav', 'below' ); ?>
    <?php else : ?>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 results">
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
    var years = <?php echo "[" . implode(",", $years) . "]"; ?>;
    var ymin = <?php echo $ymin; ?>;
    var ymax = <?php echo $ymax; ?>;
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
        // console.log($(this).val());
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
        // $('#searchbox input[name="tags"]').val('');
    });
    $('#tag-switch .btn').click(function () {
        // console.log('tags');
        $(this).addClass('active');
        $('#text-switch .btn').removeClass('active');
        $('#searchbox input[name="s"]').attr('type', 'hidden');
        // $('#searchbox input[name="s"]').val('');
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


    // tooltips
    $('.tooltip-toggle').each(function () {
        $(this).click(function () {
            // console.log('click');
            $(this).tooltip('toggle');
        });
    });

    function toggleTooltip(tip) {
        console.log(tip);
        switch (tip) {
            case 'stamped':
                $('#tooltip-stamped').tooltip('toggle');
                break;
        }
    }

    // counter results
    <?php
    $counterLabel = $total . ' entr';
    $counterLabel = $counterLabel . (($total > 1) ? 'ies' : 'y');
    $counterLabel = $counterLabel . __(" of ", "mki");
    $count_posts = wp_count_posts();
    $counterLabel = $counterLabel . $count_posts->publish;
    $counterLabel = $counterLabel . __(" in total", "mki");
    //    echo $counterLabel;

    ?>
    var counterLabel = "<?php echo $counterLabel; ?>";

    $('#timestamp').prepend("<label class='counter'>" + counterLabel + "</label>");

    // end counter results


    // general approach
    // set search params and reload

    // apply time filter and reload
    function setYear(year) {
        if (year) {
            $('input[name="ymin"]').val(year);
            $('input[name="ymax"]').val(year);
            $('#advanced-search-form').submit();
        }
    }

    // apply time filter and reload
    function setInterval(ymin, ymax) {
        if (ymin && ymax) {
            $('input[name="ymin"]').val(ymin);
            $('input[name="ymax"]').val(ymax);
            $('#advanced-search-form').submit();
        }
    }

    // add category filter
    function setCat(cat) {
        if (cat) {
            var val = $('input[name="tags"]').val().split(',').map(function (value) {
                return value.trim()
            }).filter(function (value) {
                return value !== "";
            });
            // check for duplicates
            if (val.indexOf(cat) < 0) {
                val.push(cat);
            }
            // console.log('check ',val);
            // update value
            var newVal = val.join(", ");
            $('input[name="tags"]').val(newVal);
            // submit form
            $('#advanced-search-form').submit();
        }
    }

    // add tag to filter and apply
    function setTag(tag) {
        if (tag) {
            var val = $('input[name="tags"]').val().split(',').map(function (value) {
                return value.trim()
            }).filter(function (value) {
                return value !== "";
            });
            // check for duplicates
            if (val.indexOf(tag) < 0) {
                val.push(tag);
            }
            // update value
            var newVal = val.join(", ");
            $('input[name="tags"]').val(newVal);
            // submit form
            $('#advanced-search-form').submit();

        }
    }

    // uncheck the category and submit the form
    function unsetCat(cat) {
        if (cat) {
            var val = $('input[name="tags"]').val().split(',').map(function (value) {
                return value.trim()
            }).filter(function (value) {
                return value !== "";
            });
            var index = val.indexOf(cat);
            if (index > -1) {
                val.splice(index, 1);
            }
            // update value
            var newVal = val.join(", ");
            $('input[name="tags"]').val(newVal);
            // submit form
            $('#advanced-search-form').submit();
        }
    }

    // add tag to filter and apply
    function unsetTag(tag) {
        var val = $('input[name="tags"]').val().split(',').map(function (value) {
            return value.trim()
        }).filter(function (value) {
            return value !== "";
        });
        var index = val.indexOf(tag);
        if (index > -1) {
            val.splice(index, 1);
        }
        // update value
        var newVal = val.join(", ");
        $('input[name="tags"]').val(newVal);
        // submit form
        $('#advanced-search-form').submit();
    }
</script>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
