<?php get_header(); ?>
                    <section role="main">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            <header class="header">
                           <!-- 	<?php edit_post_link(); ?> -->
								<?php if ( is_front_page() ) : ?>
                                 <h1 class="home centered"><?php the_title(); ?></h1>
                                <?php else: ?>         
				<?php 
				if (strcmp(get_the_title(), "Charts")!==0){ ?>
                                     <h1 class="entry-title"><?php the_title(); ?></h1>
				 <?php  } ?>
                                <?php endif; ?>
                            </header>
                            <section class="entry-content">
                            <?php if ( has_post_thumbnail() ) { the_post_thumbnail(); } ?>
                            <?php the_content(); ?>
                            <div class="entry-links"><?php wp_link_pages(); ?></div>
                            </section>
                        </article>
                        <?php if ( ! post_password_required() ) comments_template( '', true ); ?>
                        <?php endwhile; endif; ?>
                    </section>
				</div>
            </div>
        </div>
    </div>                    
<?php if(!is_front_page()){get_sidebar();} ?>
<?php get_footer(); ?>