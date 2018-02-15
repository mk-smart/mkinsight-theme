<?php get_header(); ?>
    <section role="main">
        <article id="post-0" class="post not-found">
            <header class="header">
                <h1 class="entry-title"><?php _e('Sorry, Page Not Found', 'mki'); ?></h1>
            </header>
            <p>
                We are sorry that the page you are trying to access does not seem to be available.
            </p>
            <p>
                To find what you are looking for you can:
            <ul>
                <li>Start from the <a href="/">homepage</a> and select a topic area to explore facts about MK.</li>
                <li>Go to <a href="/categories">browse page</a> to filter and download/view datasources within
                    MK:Insight.
                </li>
                <li>If you don't know what MK:Insight is or what you can do with it, have a look at some <a
                            href="#quick-facts">quick facts about MK:Insight</a></li>
                <li>
                    As an alternative, search for a keyword within MK:Insight using the search
                        below<br/>
                    <div class="search-box">
                        <?php get_search_form(); ?>
                    </div>
                </li>
            </ul>

            </p>
        </article>
        <article class="entry-content">
            <h2>MK Intelligence Observatory</h2>
            <p>
                If you have arrived here following a link to the MK Intelligence Observatory, please be aware that
                this facility does no longer exists and has been replaced by the current portal: <a
                        href="http://mkinsight.org">MK Insight</a>. It is likely that the information you were
                trying to access on the MK Intelligence Observatory has been transferred to MK Insight. Please use
                the search facility (search box below) or browse the data and reports available through the
                corresponding menu items above.
            </p>
        </article>
    </section>
    </div>
    </div>
<?php get_template_part('quickfacts-page'); ?>
    </div>
    </div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>