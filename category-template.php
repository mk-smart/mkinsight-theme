<?php
/*
 * Template Name: Categories Browser
 */
get_header(); ?>
<?php
$term = get_queried_object();
// term_id
$category__and = @$_GET['term_id'];
?>
<section role="main" id="category-data-page">
    <header class="header">
        <h1 class="entry-title">
            <?php
            _e('Browse Sources', 'mki');
            single_cat_title();
            ?>
        </h1>
        <?php if ('' != category_description()) echo apply_filters('archive_meta', '<div class="archive-meta">' . category_description() . '</div>'); ?>
        <?php // list of highlighted categories ?>
        <div class="ipad desktop">
            <div id="highlightedCategories" style="display: flex;">
                <?php
                $categories = get_categories(array('taxonomy' => 'category', 'order' => 'ASC'));

                //todo get by slug

                function catFilter($val)
                {
                    $highlights = ["data", "report", "health-and-social-care", "education-and-skills", "environment", "economy-and-business", "population", "housing"];
                    return (in_array($val->category_nicename, $highlights));
                }

                $highlightCats = array_filter($categories, "catFilter");
                //            var_dump($highlightCats);
                // generate list of categories

                foreach ($highlightCats as $category) {
                    $cslug = $category->slug;
                    $cname = $category->name;
                    $cid = $category->term_id;
                    $img_url = get_template_directory_uri() . '/assets/img/svg/' . $cslug . '.svg';
                    ?>
                    <div class="box">
                        <?php if (in_array($cid, $category__and)) { ?>
                            <a href="#" class="btn btn-default vcenter selected"
                               onclick="removeCat(<?php echo $cid; ?>)">
                                <div class="align-middle">
                                    <img class="aligncenter mkicons size-full" src="<?php echo $img_url; ?>"
                                         alt="<?php print $cname; ?>">
                                    <span class="strapline"><?php print $cname; ?></span>
                                </div>
                            </a>

                            <?php
                        } else { ?>
                            <a href="#" class="btn btn-default vcenter"
                               onclick="addCat(<?php echo $cid; ?>)">
                                <div class="align-middle">
                                    <img class="aligncenter mkicons size-full" src="<?php echo $img_url; ?>"
                                         alt="<?php print $cname; ?>">
                                    <span class="strapline"><?php print $cname; ?></span>
                                </div>
                            </a>
                            <?php
                        } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </header>
    <?php

    //    $tags =  str_replace(",","+",@$_GET['tag']);
    ?>
    <?php /*wp_tag_cloud(); */ ?>

    <?php
    // manage search terms
    //    $query = new WP_Query(array('category__and' => $category__and, 'nopaging' => TRUE));
    $query = new WP_Query(array('nopaging' => TRUE));
    if ($category__and == null) {
        $category__and = array();
    }
    ?>
    <form style="clear:both;overflow:visible;width: fit-content;"
          action="<?php print get_category_link($term->term_id); ?>">
        <?php
            // todo manage categories
        // print hidden field checkbox
//            var_dump($categories);
//            foreach ($categories as $cat){
//                echo "<input class='categories' name=\"category-$cat->slug\" type=\"hidden\" value=\"$cat->term_id\" >";
//            }
        ?>
        <!--        https://datatables.net/manual/options -->
        <!-- add event trigger at select -->
        <div id="yearDataFilter">
            <div class="years">
                <label>From
                    <select class="min year selectpicker" id="minYear" name="ymin">
                        <option>---</option>
                        <?php $categories = get_categories(array('taxonomy' => 'years', 'order' => 'ASC'));
                        foreach ($categories as $category):
                            $cslug = $category->slug;
                            $checked = ($cslug == $_GET['ymin']) ? 'selected="selected"' : ""; ?>
                            <option value="<?php print $cslug; ?>" <?php print $checked; ?> ><?php print $category->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    to
                    <select class="max year selectpicker" id="maxYear" name="ymax">
                        <option>---</option>
                        <?php $categories = get_categories(array('taxonomy' => 'years', 'order' => 'DESC'));
                        foreach ($categories as $category):
                            $cslug = $category->slug;
                            $checked = ($cslug == $_GET['ymax']) ? 'selected="selected"' : ""; ?>
                            <option value="<?php print $cslug; ?>" <?php print $checked; ?> ><?php print $category->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

            </div>
            <div class="textfilter">
                <input id="textfilter" type="text" name="s"/>
                <i class="icon ion-search"></i>
            </div>
        </div>
        <div id="timeless" style="background-color: #e6e6e6;padding: 10px;">
            <input id="timelesscheck" type="checkbox"
                   name="timeless" <?php echo $_GET['timeless'] ? 'checked' : ''; ?> />
            <span style="color:black;font-weight: bold;"><?php _e("Include results with no time stamp") ?></span>
        </div>
        <table id="categoryDataTable">
            <thead>
            <th>Title</th>
            <th>Categories</th>
            <th>About Years</th>
            <th>Published</th>
            <th>Files</th>
            </thead>
            <tbody>
            <?php
            if ($query->have_posts()) :
                while ($query->have_posts()) :
                    $query->the_post(); ?>
                    <tr>
                        <td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
                        <td><?php $cats = get_the_category();
                            foreach ($cats as $cat) {
                                if (!in_array($cat->term_id, $category__and)):
                                    ?>
                                    <a onclick="addCat(<?php print $cat->term_id; ?>);" href=#"
                                       class="btn badge-category" name="term_id[]"
                                       value="<?php print $cat->term_id; ?>">
                                        +
                                        <?php print $cat->name; ?></a>
                                <?php else: ?>
                                    <a onclick="removeCat(<?php print $cat->term_id; ?>);"
                                       class="btn badge-category remove" name="term_id[]"
                                       value="<?php print $cat->term_id; ?>">
                                        -
                                        <?php print $cat->name; ?></a>
                                <?php endif;
                            } ?></td>
                        <td><?php
                            $years = get_the_terms(get_the_ID(), 'years');
                            for ($i = 0; $i < sizeof($years); $i++) {
                                $year = $years[$i]->name;
                                if ($i > 0) echo ', ';
                                echo '<a value="' . $year . '" href="#" class="year-filter" onclick="setDate(' . $year . ')">' . $year . '</a>';
                            }
                            ?></td>
                        <td><?php the_date(); ?></td>
                        <td class="fileactions">
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

                            // todo disabling chart feature
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
                        </td>
                    </tr>
                <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
            </tbody>
        </table>
    </form>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
<script>
    function addCat(catId) {
        if ('URLSearchParams' in window) {
            var searchParams = new URLSearchParams(window.location.search);
            searchParams.append("term_id[]", catId);
            window.location.search = searchParams.toString();
            return false;
        }
    }

    function removeCat(catId) {
        if ('URLSearchParams' in window) {
            var searchParams = new URLSearchParams(window.location.search);
            var cats = searchParams.getAll("term_id[]");
            var index = cats.indexOf(catId + "");
            // console.log('check index', index, catId, cats);
            if (index >= 0) {
                cats.splice(index, 1);
                searchParams.delete("term_id[]");
                cats.forEach(function (value) {
                    searchParams.append("term_id[]", value);
                });
                window.location.search = searchParams.toString();
            }
            return false;
        }
    }

    function setDate(year) {
    };

    $(document).ready(function () {
        var table = $('#categoryDataTable').DataTable({
            searching: true,
            "lengthChange": false,
            "pageLength": 50,
            language: {
                search: "Filter:"
            }
        });
        $('#category-data-page').fadeIn();


        // text filter
        $('#textfilter').on('keyup', function () {
            // console.log('asd ',this.value);
            table.search(this.value).draw();
        });

        // custom filter for time column
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                var min = parseInt($('#minYear').val(), 10);
                var max = parseInt($('#maxYear').val(), 10);

                // split years
                var years = data[2].split(', ');

                // var year = parseFloat(data[2]) || 0; // use data for the age column
                // console.log('check filter', year, min, max);
                for(var i = 0; i < years.length; i++) {
                    var year = parseInt(years[i].trim());
                    if ((isNaN(min) && isNaN(max)) ||
                        (isNaN(min) && year <= max) ||
                        (min <= year && isNaN(max)) ||
                        (min <= year && year <= max)) {
                        return true;
                    }
                };
                // include timeless
                if($('#timelesscheck').is(':checked')){
                    console.log((years.length === 0 || (years.length ===1 && !parseInt(years[0] ) ) ));
                    if(years.length === 0 || (years.length ===1 && !parseInt(years[0] ) ) ){return true;}
                }

                // todo add category filter

                return false;
            }
        );

        // on change trigger search
        $('#minYear').change(function () {table.draw();});
        $('#maxYear').change(function () {table.draw();});
        $('#timelesscheck').change(function () {table.draw();});


        // year link click handler
        // sets both dates
        // search table
        setDate = function (year) {
            $(".selectpicker").selectpicker('val',year);
            table.draw();
        }


    })
    ;
</script>
