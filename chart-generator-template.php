<?php /* Template Name: Chart Generator */ ?>
<?php require_once('PHPExcel/Classes/PHPExcel.php'); ?>
<?php get_header(); ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <section role="main">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="header">
                <h1 class="entry-title">Chart Generator</h1>
            </header>
            <section class="entry-content">
                <?php if (has_post_thumbnail()) {
                    the_post_thumbnail();
                } ?>
                <?php the_content(); ?>
                <hr>
                <?php do { ?>

                <?php /* BEGIN SELECT FILE */ ?>
                <?php if (!@$_GET['data'] && !@$_GET['build-chart'] && !@$_GET['chart'] && !@$_GET['chart-editor']) : ?>
                    <section>
                        <h2>Select data file</h2>
                        <select class="selectpicker show-tick" style="color: #000;" id="mki_select_data">
                            <option> -- select a data file --</option>
                            <?php $last_parent = 0; ?>
                            <?php foreach (mki_data_files() as $data_file): ?>
                            <?php if ($data_file->post_parent != $last_parent): ?>
                            <?php if ($last_parent !== 0): ?>
                                </optgroup>
                            <?php endif; ?>
                            <optgroup
                                    label="<?php print $data_file->parent_title; ?>"
                                    data-post-parent="<?php print $data_file->post_parent;
                                    $last_parent = $data_file->post_parent; ?>">
                                <?php else: ?>
                                <?php endif; ?>
                                <option
                                        value="<?php print $data_file->ID; ?>"
                                        data-post-id="<?php print $data_file->ID; ?>"
                                        data-file="<?php print $data_file->guid; ?>"
                                        data-post-parent="<?php print $data_file->post_parent; ?>"
                                        data-title="<?php print $data_file->post_title; ?>"
                                        data-parent-title="<?php print $data_file->parent_title; ?>">
                                    <?php print $data_file->post_title; ?>
                                </option>
                                <?php endforeach; ?>
                        </select>
                        <button class="btn btn-default" style="color: #000;" type="button" onClick="mki_goto_table()">
                            Start
                        </button>
                    </section>
                <?php endif; ?>
                <?php /* END SELECT FILE */ ?>


                <?php /* START DATA SPEC */ ?>
                <?php if ($attachment_id = @$_GET['data']): ?>
                <?php
                $post = get_post($attachment_id);
                // Only if it is an attachment
                if (!$post || !$post->post_type == 'attachment') {
                    mki_error('Not a data file');
                    break;
                }
                $parent = get_post($post->post_parent);
                $file = get_attached_file($attachment_id);
                // Prepare the list of columns
                $columns = array();
                $types = array(); // TODO Try to guess column type. This is not used at the moment. I don't remove it because I think it can make sense (ED)
                $examples = array(); // collect up to 6 examples
                $cr = (is_numeric(@$_GET['cr']) ? $_GET['cr'] : 0);

                // loading data
                // support to multiple formats with PHPExcel
                $excelReader = PHPExcel_IOFactory::createReaderForFile($file);
                $excelReader->setReadDataOnly();
                $excelObj = $excelReader->load($file);
                $tmp = $excelObj->getActiveSheet()->toArray(null, true,true,true);
                function objectToArray($data) {
                    if (is_array($data) || is_object($data))
                    {
                        $result = array();
                        foreach ($data as $key => $value)
                        {
                            array_push($result,objectToArray($value));
                        }
                        return $result;
                    }
                    return $data;
                }
                if ($tmp) {
                    // $cr = index of row where data starts
                    // $cr - 1 = index of row of columns labels
                    // max($cr-1, 0) = to force columns index as positive integer
                    $columns = objectToArray($tmp[max($cr-1,0)]);

                } else {
                    mki_error('A problem occurred while reading the data file.');
                }

                // Prepare the data url for the table
                $nonce = wp_create_nonce("mki_data_file_get_nonce");
                $dataFormat = (isset($_GET['ce'])) ? "twocols" : "default";
                $dataUrl = admin_url('admin-ajax.php?action=mki_data_file_get&format=' . $dataFormat . '&post_id=' . $attachment_id . '&co=' . @$_GET['co'] . '&ce=' . @$_GET['ce'] . '&cv=' . @$_GET['cv'] . '&vt=' . @$_GET['vt'] . '&cr=' . @$_GET['cr'] . '&nonce=' . $nonce);
                ?>
                <script type="text/javascript">
                    // Load the Visualization API and the piechart package.
                    google.charts.load('current', {'packages': ['table']});

                    // Set a callback to run when the Google Visualization API is loaded.
                    google.charts.setOnLoadCallback(drawChart);

                    function drawChart() {
                        var data = $.ajax({
                            url: "<?php print $dataUrl;?>",
                            dataType: "json",
                            async: false
                        }).responseText;
                        var jsonData = JSON.parse(data);
                        // console.log(jsonData);
                        var data = new google.visualization.DataTable(jsonData);

                        // Instantiate and draw our chart, passing in some options.
                        var chart = new google.visualization.Table(document.getElementById('mki_data_div'));
                        chart.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
                    }

                </script>
                <section>
                    <h2>Specify chart data</h2>
                    <h3><?php print $parent->post_title; ?></h3>
                    <h4>Data file: <?php print $post->post_title; ?></h4>
                    <section>
                        <form method="GET" class="form-horizontal">
                            <div class="form-group"><p class="col-sm-12">
                                    Indicate whether the data starts at a specific row. Tip: the first row should
                                    include columns names (see below preview).</p></div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="cr">Data starts at row</label>
                                <div class="col-sm-10">
                                    <input size="5" class="" name="cr"
                                           value="<?php print (is_numeric(@$_GET['cr'])) ? $_GET['cr'] : 0; ?>"/>
                                </div>
                            </div>
                            <div class="form-group"><p class="col-sm-12">Select one column containing the entities and
                                    one containing the values.</p></div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="cv">Entities from column:</label>
                                <div class="col-sm-10">
                                    <select class="selectpicker show-tick " name="ce"
                                            onChange="$('#mki_use_entity_example').html($(this).find(':selected').data('examples'));">
                                        <?php foreach ($columns as $cix => $col): ?>
                                            <option <?php print ($cix == @$_GET['ce']) ? 'SELECTED' : ''; ?>
                                                    value="<?php print $cix; ?>"
                                                    data-subtext="<?php print $examples[$cix][0]; ?>"
                                                    data-examples="E.g.: <?php print implode(', ', $examples[$cix]); ?>"><?php print $col; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-offset-2 col-sm-10">
                                    <small id="mki_use_entity_example">&nbsp;</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="cv">Values from column:</label>
                                <div class="col-sm-10">
                                    <select class="selectpicker show-tick " name="cv"
                                            onChange="$('#mki_use_value_example').html($(this).find(':selected').data('examples'));">
                                        <?php foreach ($columns as $cix => $col): ?>
                                            <option <?php print ($cix == @$_GET['cv']) ? 'SELECTED' : ''; ?>
                                                    value="<?php print $cix; ?>"
                                                    data-subtext="<?php print $examples[$cix][0]; ?>"
                                                    data-examples="E.g.: <?php print implode(', ', $examples[$cix]); ?>"><?php print $col; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-offset-2 col-sm-10">
                                    <small id="mki_use_value_example">&nbsp;</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="vt">Value Type</label>
                                <div class="col-sm-10">
                                    <select class="selectpicker show-tick " name="vt">
                                        <option <?php print ('number' == @$_GET['vt']) ? 'SELECTED' : ''; ?>
                                                value="number">Number
                                        </option>
                                        <option <?php print ('string' == @$_GET['vt']) ? 'SELECTED' : ''; ?>
                                                value="string">String
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <!-- div class="form-group">
        <label class="control-label col-sm-2" for="co">Options</label>
        <div class="col-sm-10">
          <select class="selectpicker show-tick " name="co">
             <option <?php print ('d' == @$_GET['co']) ? 'SELECTED' : ''; ?> value="d">Default</option>
             <option <?php print ('c' == @$_GET['co']) ? 'SELECTED' : ''; ?> value="c">Count &amp; Group (10 slices)</option>
             <option <?php print ('c' == @$_GET['co']) ? 'SELECTED' : ''; ?> value="p">Count &amp; Group (Percentages)</option>
          </select>
        </div>
      </div -->
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button class="btn btn-default" type="submit" name="data"
                                            value="<?php print $_GET['data']; ?>">Preview data
                                    </button>
                                    <button name="chart-editor" value="<?php print $post->ID; ?>"
                                            class="btn btn-default" type="submit">Edit chart
                                    </button>
                                </div>
                            </div>
                        </form>
                        <h3>Preview</h3>
                        <div style="color: #000; border: 1px solid #AAA; padding:0px" id="mki_data_div">Loading table
                            ...
                        </div>
                    </section>
                    <?php endif; ?>
                    <?php /* END DATA SPEC */ ?>


                    <?php /* START CHART EDITOR */ ?>
                    <?php if ($attachment_id = @$_GET['chart-editor']): ?>
                        <section>
                            <?php
                            $post = get_post($attachment_id);
                            // Only if it is an attachment
                            if (!$post || !$post->post_type == 'attachment') {
                                mki_error('Not a data file');
                                break;
                            }
                            $parent = get_post($post->post_parent);
                            $nonce = wp_create_nonce("mki_data_file_get_nonce");
                            $nonce1 = wp_create_nonce("mki_save_as_attachment_nonce");
                            $dataUrl = admin_url('admin-ajax.php?action=mki_data_file_get&post_id=' . $attachment_id . '&nonce=' . $nonce);
                            $saveUrl = admin_url('admin-ajax.php?action=mki_save_chart&post_id=' . $attachment_id . '&nonce=' . $nonce1); ?>
                            <h2>Edit chart</h2>
                            <h3><?php print $parent->post_title; ?></h3>
                            <h4>Data file: <?php print $post->post_title; ?></h4>
                            <div id="vis_div" style="height: 400px; width: 600px;"></div>
                            <canvas style="display:none" id="hidden_canvas"
                                    style="height: 400px; width: 600px;"></canvas>
                            <div id="chart_buttons" style="display:none">
                                <button class="btn btn-default" onClick="gotoBack();">Back</button>
                                <button class="btn btn-default" onClick="openEditor();">Edit</button>
                                <a id="downloadSVG" download="chart.svg" class="btn btn-default">Download SVG</a>
                                <a id="downloadPNG" download="chart.png" class="btn btn-default">Download PNG</a>
                                <!-- <a id="downloadJPEG" download="chart.jpeg" class="btn btn-default">JPEG</a> -->
                                <?php if (is_user_logged_in() && current_user_can('edit_post', $parent->ID)): ?>
                                    <a id="attachSvgToPost" onClick="saveAsPostAttachment('svg')"
                                       class="btn btn-default">Attach SVG to page</a>
                                    <a id="attachPngToPost" onClick="saveAsPostAttachment('png')"
                                       class="btn btn-default">Attach PNG to page</a>
                                    <a id="gotoPage" onClick="gotoEditPost()" class="btn btn-default">Edit post page</a>
                                <?php endif; ?>
                            </div>
                        </section>
                        <style>
                            .google-visualization-charteditor-dialog input,
                            .google-visualization-charteditor-dialog textarea {
                                font-size: inherit;
                                min-height: inherit;
                                line-height: inherit;
                                padding: inherit;
                                margin: 0px;
                            }

                            .google-visualization-charteditor-dialog .goog-flat-menu-button, .google-visualization-clickeditor-bubble .goog-flat-menu-button, .google-visualization-charteditor-custom-panel .goog-flat-menu-button {
                                height: 28px;
                            }

                            #google-visualization-charteditor-options-panel .google-visualization-charteditor-section {
                                padding-left: 5px;
                                padding-right: 5px;
                            }

                            .google-visualization-charteditor-dialog table tbody tr td {
                                line-height: inherit;
                            }

                            .google-visualization-charteditor-settings-td {
                                padding-top: 5px;
                            }

                            #google-visualization-charteditor-input-chart-name {
                                height: 25px;
                                padding: 0px 2px 0px;
                                border: 1px solid #ccc;
                                display: none;
                            }

                            .google-visualization-charteditor-dialog {
                                width: 1000px;
                            }

                            #google-visualization-charteditor-preview-div-wrapper {

                            }
                        </style>
                        <script type="text/javascript">
                            google.charts.load('current', {packages: ['charteditor']});
                        </script>
                        <script type="text/javascript">
                            google.charts.setOnLoadCallback(loadData);
                            var chartEditor = null;
                            var wrapper = null;
                            var okHit = false;
                            var attachmentSaved = false;

                            function loadData() {
                                $.ajax({
                                    url: "<?php print $dataUrl . '&format=twocols&ce=' . $_GET['ce'] . '&cv=' . $_GET['cv'] . '&vt=' . $_GET['vt'] . '&cr=' . @$_GET['cr'];?>",
                                    dataType: "json",
                                    success: function (jsonData) {
                                        loadEditor(jsonData);
                                    }
                                });
                            }

                            function loadEditor(jsonData) {
                                // Create the chart to edit.
                                wrapper = new google.visualization.ChartWrapper();
                                wrapper.setDataTable(jsonData);
                                chartEditor = new google.visualization.ChartEditor();
                                google.visualization.events.addListener(chartEditor, 'ok', redrawChart);
                                google.visualization.events.addListener(chartEditor, 'cancel', cancelChart);
                                chartEditor.openDialog(wrapper, {});
                            }

                            function openEditor() {
                                chartEditor.openDialog(wrapper, {});
                            }

                            // On "Cancel" save the chart to a <div> on the page.
                            function cancelChart() {
                                if (!okHit) gotoBack();
                            }

                            // On "OK" save the chart to a <div> on the page.
                            function redrawChart() {
                                okHit = true;
                                chartEditor.getChartWrapper().draw(document.getElementById('vis_div'));
                                if ($(document.getElementById('vis_div')).find('svg').length) {
                                    var svg = $(document.getElementById('vis_div')).find('svg')[0];
                                    var svg64 = btoa(svg);
                                    var b64Start = 'data:image/svg+xml;base64,';
                                    var image64 = b64Start + svg64;
                                    $('#downloadSVG').attr('href', image64);
                                    var canvas = document.createElement("canvas"),
                                        context = canvas.getContext("2d"),
                                        //img = document.querySelector('#img'),
                                        loader = new Image;
                                    loader.width = canvas.width = 600 * 11;
                                    loader.height = canvas.height = 400 * 11;
                                    context.webkitImageSmoothingEnabled = false;
                                    context.mozImageSmoothingEnabled = false;
                                    context.imageSmoothingEnabled = false;
                                    loader.onload = function () {
                                        context.drawImage(loader, 0, 0, loader.width, loader.height);
                                        // img.src = canvas.toDataURL();
                                        var pngHref = canvas.toDataURL("image/png", 1.0);
                                        $('#downloadPNG').attr('href', pngHref);
                                        // var jpegHref = canvas.toDataURL("image/jpeg", 1.0);
                                        // $('#downloadJPEG').attr('href', jpegHref);
                                        $('#chart_buttons').show();
                                    };

                                    var svgAsXML = (new XMLSerializer).serializeToString(svg);
                                    loader.src = 'data:image/svg+xml,' + encodeURIComponent(svgAsXML);
                                } else {
                                    $('#chart_buttons').show();
                                    $('#chart_buttons a').hide();
                                }


                            }

                            function saveAsPostAttachment(format) {
                                if (!(name = prompt('Enter attachment file name:'))) {
                                    return;
                                }
                                var svgdata = '<?xml version = "1.0"?>' + $(document.getElementById('vis_div')).find('svg').prop('outerHTML');
                                $.ajax({
                                    url: "<?php print $saveUrl;?>" + '&format=' + format + "&name=" + encodeURIComponent(name),
                                    type: "POST",
                                    data: svgdata,
                                    contentType: 'image/svg+xml',
                                    success: function (msg) {
                                        // go to page?
                                        attachmentSaved = true;
                                        alert(msg);
                                    },
                                    error: function (xhr) {
                                        if (xhr.responseText) {
                                            alert(xhr.responseText);
                                        } else {
                                            alert('An error occurred');
                                        }
                                    }
                                });
                            }

                            function gotoEditPost() {
                                if (attachmentSaved || confirm('Are you sure you want to leave this page?')) {
                                    window.location.href = ("<?php print get_edit_post_link($parent->ID, ''); ?>");
                                }
                            }

                            function gotoBack() {
                                window.location.href = '?data=' + '<?php print $_GET['chart-editor'] . '&ce=' . $_GET['ce'] . '&cv=' . $_GET['cv'] . '&vt=' . $_GET['vt'] . '&cr=' . @$_GET['cr']; ?>';
                            }
                        </script>
                    <?php endif; ?>
                    <?php /* END CHART EDITOR */ ?>


                    <?php } while (0); ?>
                    <div class="entry-links"><?php wp_link_pages(); ?></div>
                </section>
        </article>
    </section>
<?php get_footer(); ?>
<?php /* FUNCTIONS */ ?>
    <script>
        function mki_goto_table() {
            var post_id = $('#mki_select_data').find(":selected").data('post-id');
            if (typeof post_id !== 'undefined') {
                window.location.href = "?data=" + post_id;
                return true;
            }
            return false;
        }

        function mki_goto_chart() {
            var post_id = $('#mki_select_data').find(":selected").data('post-id');
            if (typeof post_id !== 'undefined') {
                window.location.href = "?build-chart=" + post_id;
                return true;
            }
            return false;
        }
    </script>
<?php
/* Functions */
function mki_data_files()
{
    global $wpdb;

    $my_query = new WP_Query(array(
        //'meta_key' => 'my_hash',
        'nopaging' => true,
        //'orderby' => 'meta_value',
        'fields' => 'ids',
        'cat' => 'data',
    ));
    if ($post_ids = $my_query->get_posts()) {
        $post_ids = implode(',', $post_ids);
        $atts = $wpdb->get_results("SELECT A.ID, B.POST_TITLE FROM $wpdb->posts as A LEFT JOIN $wpdb->posts AS B ON B.ID = A.post_parent WHERE A.post_type = 'attachment' ORDER BY B.POST_TITLE ASC;");
        $data_files = array();
        foreach ($atts as $att) {
            $p = get_post($att->ID);
            if (get_post_mime_type($p->ID) == 'text/csv') {
                $p->parent_title = $att->POST_TITLE;
                array_push($data_files, $p);
            }
        }
        return $data_files;
    }
    return array();
}

function mki_error($msg)
{
    ?>
    <div class="alert" role="alert"><?php print $msg; ?></div><?php
}
