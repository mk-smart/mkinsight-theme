<?php
/**
 * Template Name: Help Page
 */
?>
<?php get_header(); ?>
<section role="main">
    <?php custom_breadcrumbs(); ?>
    <h1 class="entry-title">
        <?php _e("Instructions and Support", "mki"); ?>
    </h1>
    <article style="margin-bottom: 80px;">
        <p>
            This page explains the basic functions of the MK Insight website, including how to navigate it and find
            content and data, as well as how to contribute new content, visualise the data, and create shareable “data pages”.
        </p>
        <p>
            If you are experiencing problems and cannot find answer on this page, please use the <a href="#contact">contact
                form</a> at the bottom of the page. Please describe your problem precisely.
        </p>
    </article>
    <?php get_template_part('quickfacts-page'); ?>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <h2 id="qea"><?php _e("Frequently Asked Questions", "mki"); ?></h2>
            <section class="entry-content">
                <?php if (has_post_thumbnail()) {
                    the_post_thumbnail();
                } ?>
                <?php the_content(); ?>
                <div class="entry-links"><?php wp_link_pages(); ?></div>
            </section>
        </article>

        <?php if (!post_password_required()) comments_template('', true); ?>
    <?php endwhile; endif; ?>

    <h2 id="contact">Contact</h2>
    <p>
        If you didn't find an answer to your question or a solution to your problem above, please use to contact form
        below to describe what you are trying to do and why it does not work.
    </p>
    <p><?php echo do_shortcode("[contact]"); ?></p>
</section>
</div>
</div>
</div>
</div>
<?php if (!is_front_page()) {
    get_sidebar();
} ?>
<?php get_footer(); ?>
