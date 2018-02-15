<footer class="entry-footer entry-meta">

    <?php
    $postcats = get_the_category();
    if ($postcats) { ?>
        <div class="cat-links">
            <?php _e('Categories: ', 'mki'); ?>
            <?php foreach ($postcats as $cat) {
                echo "<a class='button' href=\"/?s=&category%5B%5D=$cat->slug\">$cat->name</a>";
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

    <?php if (comments_open()) {
        echo '<span class="meta-sep">|</span> <span class="comments-link">
<a href="' . get_comments_link() . '">' . sprintf(__('Comments', 'mki')) . '</a>
</span>';
    } ?>
</footer>