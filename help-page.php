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
            <?php _e('This page explains the basic functions of the MK Insight website, including how to navigate it and find
            content and data, as well as how to contribute new content, visualise the data, and create shareable “data pages”.',"mki"); ?>
        </p>
        <ul>
            <li>
                <?php _e("<a href='#quick-facts'>Quick facts</a> about MK Insight.","mki"); ?>
            </li>
            <li>
                <?php _e("<a href='#faq'>Frequent Asked Questions</a> section.","mki"); ?>
            </li>
            <li>
                <?php _e('If you are experiencing problems and cannot find answer on this page, please use the <a href="#contact">contact
                    form</a> at the bottom of the page. Please describe your problem precisely.',"mki"); ?>
            </li>
        </ul>
    </article>
    <?php get_template_part('quickfacts-page'); ?>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <div id="faq">
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
    </div>
        <?php if (!post_password_required()) comments_template('', true); ?>
    <?php endwhile; endif; ?>

    <h2 id="contact"><?php _e("Contact","mki");?></h2>
    <p>
        <?php _e('If you didn\'t find an answer to your question or a solution to your problem above, please use to contact form
        below to describe what you are trying to do and why it does not work.', "mki"); ?>
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
