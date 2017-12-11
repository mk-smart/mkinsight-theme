<?php get_header(); ?>
                    <section role="main">
                    <header class="header">
                    <h1 class="entry-title"><?php
                    if ( is_day() ) { printf( __( 'Daily Archives: %s', 'mki' ), get_the_time( get_option( 'date_format' ) ) ); }
                    elseif ( is_month() ) { printf( __( 'Monthly Archives: %s', 'mki' ), get_the_time( 'F Y' ) ); }
                    elseif ( is_year() ) { printf( __( 'Yearly Archives: %s', 'mki' ), get_the_time( 'Y' ) ); }
                    elseif ( is_tax('years') ) { ?>About year: <?php single_cat_title(); }
                    else { _e( 'Archives', 'mki' ); }
                    ?></h1>
                    </header>
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'entry' ); ?>
                    <?php endwhile; endif; ?>
                    <?php get_template_part( 'nav', 'below' ); ?>
                    </section>
				</div>
            </div>
        </div>
    </div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
