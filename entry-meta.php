<section class="entry-meta">
<!--<span class="author vcard"><?php #the_author_posts_link(); ?></span>
-->
<?php if(get_the_terms(get_the_ID(), 'years')) : ?>
<span class="entry-date">About <?php the_terms(get_the_ID(), 'years'); ?></span>
<span class="meta-sep"> | </span>
<?php endif; ?>
<span class="entry-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
</section>