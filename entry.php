<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php edit_post_link(); ?>
<header>
    <?php if ( is_singular() ) { echo '<h1 class="entry-title">'; } else { echo '<h3 class="entry-title">'; } ?>
        <?php
            /* add icon to title
             * folder: img/infographics/
             * report: pie-chart3.png / chart.png / certified-document.png (tag_ID=18)
             * data: data-blue.png / data-green.png (tag_ID=3)
            */
            // get post categories and filters for data or report
            $cat = array_reduce(get_the_category(), function ($carry, $cat){
                // if category found
                if($carry){return $carry;}
                // search for category
                if($cat->slug === 'data'){return 'data';}
                if($cat->slug === 'report'){return 'report';}
                if($cat->slug === 'news'){return 'news';}
                if($cat->slug === 'essential'){return 'essential';}
                // default false
                return false;
            });
            switch($cat) {
                case 'report':
                    echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="'.get_template_directory_uri().'/assets/img/infographics/pie-chart3.png">';
                    break;
                case 'data':
                    echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="'.get_template_directory_uri().'/assets/img/infographics/data-green.png">';
                    break;
                default:
                    if(get_post_type() == 'idea'){
                        echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="'.get_template_directory_uri().'/assets/img/infographics/light-bulb-green.png">';
                    } else {
                        echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="'.get_template_directory_uri().'/assets/img/infographics/document.png">';
                    }
            }
        ?>
        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a>
    <?php if ( is_singular() ) { echo '</h1>'; } else { echo '</h3>'; } ?>
<?php if ( !is_search() ) get_template_part( 'entry', 'meta' ); ?>
</header>
<?php get_template_part( 'entry', ( is_archive() || is_search() ? 'summary' : 'content' ) ); ?>
<?php //if ( !is_search() ) get_template_part( 'entry-footer' ); ?>
</article>
