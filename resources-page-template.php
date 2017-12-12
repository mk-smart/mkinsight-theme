<?php /* Template Name: Resources Catalogue */ ?>
<?php get_header(); ?>
<section role="main">
  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <header class="header">
    <h1 class="entry-title">Resources Catalogue</h1>
    </header>
    <section class="entry-content">
    <?php if ( has_post_thumbnail() ) { the_post_thumbnail(); } ?>
    <?php the_content(); ?>
    <hr>

      <section>
        <h1 style="text-align:center">Icons</h1>
        <p>To geneate buttons:</p>
        <pre>[mkiicon icon="icon-name-here" text="Text to print below" link="page-name-or-full-link"]</pre>
        <p>For example:</p>
        <p><pre>[mkiicon icon="population" text="Population" link="population"]</pre>
        <p>is equivalent to:</p>
        <p><pre>[mkiicon icon="http://mkinsight.org/wp-content/uploads/2016/08/mk-basics.png" text="Population" link="http://mkinsight.org/population/"]</pre></p>

        <h1 style="text-align:center">Icons catalogue</h1>
        <?php $files = array_diff(preg_grep('/^([^.])/', scandir ( dirname(__FILE__) . '/assets/img/infographics/')), array('..', '.'));?>
        <?php foreach($files as $file):?>
        <div class="col-lg-3" style="text-align:center; padding: 10px">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/infographics/<?php print $file; ?>" width="" height="120px" alt="" />
        <br><pre style="display:inline-block; margin-top:20px"><?php print pathinfo($file, PATHINFO_FILENAME); ?></pre>
        </div>
        <?php endforeach;?>
      </section>

      <div class="clearfix"></div>
      <div class="entry-links"><?php wp_link_pages(); ?></div>
    </section>
  </article>
</section>
<?php get_footer(); ?>
