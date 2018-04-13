<section>
    <!--<span class="author vcard"><?php #the_author_posts_link(); ?></span> -->
    <div class="entry-meta">
        <?php if (get_the_terms(get_the_ID(), 'years')) : ?>
            <span class="entry-date"><?php
                _e("About ", "mki");
                $years = get_the_terms(get_the_ID(), 'years');

                foreach ($years as $year) {
                    $label = $year->slug;
                    echo "<a href=\"/?s=&ymin=$label&ymax=$label\">$label</a>";
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
        $posttags = get_the_tags();

        $tags = array_filter(array_map('trim', explode(",", @$_GET['tags'])), function ($value) {
            return $value !== '';
        });
        $keywords = array_merge($postcats, $posttags);
        if ($keywords):
            ?>
            <?php _e('Tags: ', 'mki'); ?>
            <span class="tag-links">
                <?php
                foreach ($keywords as $tag) {
                    $tSlug = trim($tag->name);
                    echo "<a class='btn' href=\"/?s=&tags=${tSlug}\">$tag->name</a>";
                }
                ?>
            </span>
        <?php endif; ?>
    </div>
</section>