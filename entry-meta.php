<section>
    <!--<span class="author vcard"><?php #the_author_posts_link(); ?></span> -->
    <div class="entry-meta">
        <?php if (get_the_terms(get_the_ID(), 'years')) : ?>
            <span class="entry-date"><?php
                _e("About ", "mki");
                $years = get_the_terms(get_the_ID(), 'years');

                foreach ($years as $year) {
                    $label = $year->slug;
                    echo "<a href=\"/categories/?ymin=$label&ymax=$label\">$label</a>";
                    if ($years[count($years) - 2]->slug == $label) {
                        echo " and ";
                    } else if (next($years) == true) {
                        echo ", ";
                    }
                }
                ?></span>
            <span class="meta-sep"> | </span>
        <?php endif; ?>
        <span class="entry-date">
            <?php the_time(get_option('date_format')); ?>
        </span>
    </div>
    <div class="entry-meta-end">
    <?php
    $postcats = get_the_category();
    if ($postcats) { ?>
        <div class="cat-links">
            <?php _e('Categories: ', 'mki'); ?>
            <?php foreach ($postcats as $cat) {
                echo "<a class='button' href=\"/categories/?term_id%5B%5D=$cat->term_id\">$cat->name</a>";
            } ?>
        </div>
    <?php } ?>
    <?php
    $posttags = get_the_tags();
    if ($posttags) { ?>
        <div class="tag-links">
            <?php _e('Tags: ', 'mki'); ?>

            <?php foreach ($posttags as $tag) {
                $tagQuery = str_replace(" ", "-", $tag->name);
                echo "<a href=\"/?s=&tag=$tagQuery\">$tag->name</a>";
            } ?>
        </div>
    <?php } ?>
    </div>
</section>