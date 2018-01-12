<?php get_header(); ?>
<?php
$term = get_queried_object();
?>
<section role="main" style="display:none" id="category-data-page">
    <header class="header">
        <h1 class="entry-title">
            <?php
            _e('Category: ', 'blankslate');
            single_cat_title();
            ?>
        </h1>
        <?php if ('' != category_description()) echo apply_filters('archive_meta', '<div class="archive-meta">' . category_description() . '</div>'); ?>
    </header>
    <?php /*
  <pre><?php print_r(wpdt_get_categories_defaults()); ?></pre>
  <?php wpdt_list_categories('showcount=1&listposts=0&child_of='.$term->term_id); ?>

  <h2><?php _e( 'Categories:' ); ?></h2>
  	<form id="category-select" class="category-select" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
  		<?php wp_dropdown_categories( 'show_count=1&hierarchical=1' ); ?>
  		<input type="submit" name="submit" value="view" />
  	</form>
<?php */
    $categories = array($term);
    $category__and = array();
    foreach ($categories as $category) {
        $category__and[] = $category->term_id;
    }
    if (isset($_GET['term_id'])) {
        foreach (@$_GET['term_id'] as $term_id) {
            if (isset($_GET['exclude'])) {
                if (intval($_GET['exclude']) == intVal($term_id))
                    continue;
            }
            $category__and[] = intVal($term_id);
            $categories[] = get_term($term_id);
        }
    }
    ?>
    <?php /*wp_tag_cloud(); */ ?>

    <?php
    $query = new WP_Query(array('category__and' => $category__and, 'nopaging' => TRUE));
    ?>
    <form action="<?php print get_category_link($term->term_id); ?>">
        <div id="categoryDataFilter">
            <div>
                <strong>Categories:</strong>
                <div id="categoryDataList">
                    <?php
                    // generate list of categories
                    foreach ($categories as $category) {
                        $cslug = $category->slug;
                        $cname = $category->name;
                        $cid = $category->term_id;
                        if ($term->slug == $category->slug) {
                            // Immutable
                            ?>
                            <button class="btn btn-danger"><?php print $cname; ?></button> <?php
                        } else {
                            // Mutable
                            ?>
                            <button class="btn badge-category" type="submit" name="exclude"
                                    value="<?php print $cid; ?>"> - <?php print $cname; ?></button> <input type="hidden"
                                                                                                           name="term_id[]"
                                                                                                           value="<?php print $cid; ?>"/><?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- add event trigger at select -->
        <div id="yearDataFilter">
            <div>
                <label>About years </label>
                <label>from
                    <select class="min year" id="minYear" name="ymin">
                        <option> ---</option>
                        <?php $categories = get_categories(array('taxonomy' => 'years', 'order' => 'ASC'));
                        foreach ($categories as $category):
                            $cslug = $category->slug;
                            $checked = ($cslug == $_GET['ymin']) ? 'selected="selected"' : ""; ?>
                            <option value="<?php print $cslug; ?>" <?php print $checked; ?> ><?php print $category->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    to
                    <select class="max year" id="maxYear" name="ymax">
                        <option> ---</option>
                        <?php $categories = get_categories(array('taxonomy' => 'years', 'order' => 'DESC'));
                        foreach ($categories as $category):
                            $cslug = $category->slug;
                            $checked = ($cslug == $_GET['ymax']) ? 'selected="selected"' : ""; ?>
                            <option value="<?php print $cslug; ?>" <?php print $checked; ?> ><?php print $category->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
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
                                if (!in_array($cat->term_id, $category__and)): ?>
                                    <button type="submit" class="btn badge-category" name="term_id[]"
                                            value="<?php print $cat->term_id; ?>">
                                        +
                                        <?php print $cat->name; ?></button>
                                <?php endif;
                            } ?></td>
                        <td><?php
                            $years = get_the_terms(get_the_ID(), 'years');
                            for ($i = 0; $i < sizeof($years); $i++) {
                                $year = $years[$i]->name;
                                if($i > 0) echo ', ';
                                echo '<a href="#" class="year-filter" onclick="setDate('.$year.')">'.$year.'</a>';
                            }
                            ?></td>
                        <td><?php the_date(); ?></td>
                        <td>
                            <?php
                            $files = get_attached_media('', $query->post->ID);
                            foreach ($files as $fid => $file):
                                ?><a href="<?php print $file->guid; ?>"><?php print $file->post_title; ?></a>
                                [<?php print $file->post_mime_type; ?>]<br/>
                            <?php
                            endforeach;
                            ?>
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
    function setDate(year) {};

    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|#|$)", "i");
        if( value === undefined ) {
            if (uri.match(re)) {
                return uri.replace(re, '$1$2');
            } else {
                return uri;
            }
        } else {
            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            } else {
                var hash =  '';
                if( uri.indexOf('#') !== -1 ){
                    hash = uri.replace(/.*#/, '#');
                    uri = uri.replace(/#.*/, '');
                }
                var separator = uri.indexOf('?') !== -1 ? "&" : "?";
                return uri + separator + key + "=" + value + hash;
            }
        }
    }

    $(document).ready(function () {
        var table = $('#categoryDataTable').DataTable({
            language: {
                search: "Filter:"
            }
        });
        $('#category-data-page').fadeIn();


        // custom filter for time column
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                var min = parseInt($('#minYear').val(), 10);
                var max = parseInt($('#maxYear').val(), 10);
                var year = parseFloat(data[2]) || 0; // use data for the age column
                // console.log('check filter', data, min, max);
                if ((isNaN(min) && isNaN(max)) ||
                    (isNaN(min) && year <= max) ||
                    (min <= year && isNaN(max)) ||
                    (min <= year && year <= max)) {
                    return true;
                }
                return false;
            }
        );

        // init value management
        if ($('#minYear').val()) {
            table.draw();
        }
        if ($('#maxYear').val()) {
            table.draw();
        }

        // date interval selection event handlers
        $('#minYear').change(function () {
            if ('URLSearchParams' in window) {
                var year = $('#minYear').val();

                var searchParams = new URLSearchParams(window.location.search);

                if(!parseInt(year)){
                    searchParams.delete("ymin");
                }else{
                    searchParams.set("ymin", year);
                }

                window.location.search = searchParams.toString();
            }
        })
        ;$('#maxYear').change(function () {
            if ('URLSearchParams' in window) {
                var year = $('#maxYear').val()

                var searchParams = new URLSearchParams(window.location.search);

                if(!parseInt(year)){
                    searchParams.delete("ymax");
                }else{
                    searchParams.set("ymax", year);
                }

                window.location.search = searchParams.toString();
            }
        });

        // year link click handler
        // sets both dates
        setDate = function(year) {
            if ('URLSearchParams' in window) {
                var searchParams = new URLSearchParams(window.location.search);
                searchParams.set("ymin", year);
                searchParams.set("ymax", year);
                window.location.search = searchParams.toString();
            }
        }

    });
</script>
