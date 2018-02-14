<?php
/**
 * Quick facts about mk:insight
 * Used by 404 page and help-page template.
 */
?>
<article>
<h2 id="quick-facts"><?php _e("Quick facts about MK:Insight","mki"); ?></h2>
<p>
    Here some quick facts about MK:Insight portal.
</p>
<div class="row">
    <?php

    // todo build facts about MKInsight


    // section about contents
    $sourcesMKI = "[mkifactlist]";
    $sourcesMKI = $sourcesMKI . "[mkifacts title=\"Which contents are available in MK:Insigts?\" color=\"blue\"]";
    // facts
    $facts = __("From the <a href='/'>homepage</a> You can browse insights about Milton Keynes.<br/>Start selecting a macro area to explore facts about MK.","mki");
    $sourcesMKI = $sourcesMKI . "[mkifact ionicon=\"ion-star\"]<p>$facts</p>[/mkifact]";
    // reports
    // number of reports and link to reports;
    $report = get_term_by('slug', 'report', 'category');
    $nReport = $report->count;
    $reportID = $report->term_id;
    $reportURL ="/categories/?term_id[]=$reportID";
    $searchURL ="/?s=&category%5B%5D=report";
    $reports = __('Currently, there are <a href="%s2">%s1 reports</a> available.<br/>Want to know more? Start from the <a href="%s2">browse</a> feature or the <a href="%s3">search page</a>.','mki');
    $reports = str_replace("%s1","$nReport", $reports);
    $reports = str_replace("%s2","$reportURL", $reports);
    $reports = str_replace("%s3","$searchURL", $reports);
    $sourcesMKI = $sourcesMKI . "[mkifact ionicon=\"ion-document-text\"]<p>$reports</p>[/mkifact]";
    // number of datasets and link to datasets
    $data = get_term_by('slug', 'data', 'category');
    $nDatasets = $data->count;
    $datasetsID = $data->term_id;
    $datasetsURL ="/categories/?term_id[]=$datasetsID";
    $datasets = __('MK:Insigts includes <a href="%s2">%s1 data sources</a>. You can either have a <strong>preview</strong> within MK:Insight or <strong>download</strong> the data source files.','mki');
    $datasets = str_replace("%s1","$nDatasets", $datasets);
    $datasets = str_replace("%s2","$datasetsURL", $datasets);
    $sourcesMKI = $sourcesMKI . "[mkifact ionicon=\"ion-pie-graph\"]<p>$datasets</p>[/mkifact]";
    $sourcesMKI = $sourcesMKI . "[/mkifacts]";
    // closing contents section


    // todo section about highlighted categories
    $sourcesMKI = $sourcesMKI . "[mkifacts title=\"What can i know about Milton Keynes?\" color=\"green\"]";
    $hightlightText = __("MK:Insight provide data supported facts about Milton Keynes. You can start browsing among the macro areas on <a href='/'>MK:Insight homepage</a>.","mki");
    $sourcesMKI = $sourcesMKI . "[mkifact ionicon=\"ion-stats-bars\"]<p>$hightlightText</p>[/mkifact]";
    //$sourcesMKI = $sourcesMKI . "[mkifact ionicon=\"ion-pie-graph\"]<p>test</p>[/mkifact]";
    $sourcesMKI = $sourcesMKI . "[/mkifacts]";


    // todo section about mki features
    $sourcesMKI = $sourcesMKI . "[mkifacts title=\"What can i do with MK:Insight?\" color=\"blue\"]";
    // facts feature
    $searchFeature = __("From the <a href='/'>homepage</a> is possible to browse a selection of facts about MK extracted by the datasources within MK:insight.","mki");
    $sourcesMKI = $sourcesMKI . "[mkifact ionicon=\"ion-home\"]<p>$searchFeature</p>[/mkifact]";
    // browse feature
    $browseFeature = __("From the <a href='/categories/'>browse page</a> it is possible to filter datasources by category.<br/>From browse it is possible to download sources or have a preview of their contents.","mki");
    $sourcesMKI = $sourcesMKI . "[mkifact ionicon=\"ion-paperclip\"]<p>$browseFeature</p>[/mkifact]";
    // chart feature
    $searchFeature = __("Selecting to <strong>preview</strong> a datasource is possible to look to the file content and extract figures from it.","mki");
    $sourcesMKI = $sourcesMKI . "[mkifact ionicon=\"ion-pie-graph\"]<p>$searchFeature</p>[/mkifact]";
    // search feature
    $searchFeature = __("Within the <a href='/?s='>search page</a> is possible to filter contents based on keywords, tags and categories.","mki");
    $sourcesMKI = $sourcesMKI . "[mkifact ionicon=\"ion-pound\"]<p>$searchFeature</p>[/mkifact]";
    $sourcesMKI = $sourcesMKI . "[/mkifacts]";


    // render section
    $sourcesMKI = $sourcesMKI . "[/mkifactlist]";
    echo do_shortcode($sourcesMKI);
    ?>
</div>
</article>