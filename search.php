<?php get_header(); ?>
                    <section role="main">
                    <header class="header">
                    <h1 class="entry-title">Search</h1>
                    </header>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 advanced-search">
					<p>You can use the filters to show only results that match your interests</p>
					<form class="" role="search" method="get" action="<?php print home_url(); ?>">
					  <div class="form-group">
					    <label for="s">Contains:</label>
					      <input type="text" class="form-control" value="<?php print @$_GET['s'];?>" name="s" id="s" placeholder="keywords">
					  </div>
					  
					 <div class="form-group">
						<label>Categories:</label>
					      <div class="checkbox">
		  					<?php
		  					// generate list of categories
		  					$categories = get_categories();
		  					foreach ($categories as $category) {
								$cslug= $category->slug;
								$cname= $category->name;
								$checked = (@in_array($cslug,$_GET['category']))? 'checked="checked"':"";
		  						print <<<INPUT
				<label><input type="checkbox" value="$cslug" $checked name="category[]" >$cname</label>
INPUT;
		  					}
		  					?>
					      </div>
					  </div>
  					 <div class="form-group">
  						<label>About year:</label>
  					      <div class="checkbox">
  		  					<?php
  		  					// generate list of categories
  		  					$categories = get_categories(array('taxonomy' => 'years','order'=>'DESC'));
  		  					foreach ($categories as $category) {
  								$cslug= $category->slug;
  								$cname= $category->name;
								$checked = (@in_array($cslug,$_GET['years']))? 'checked="checked"':"";
  		  						print <<<INPUT
  				<label><input type="checkbox" value="$cslug" name="years[]">$cname</label>
INPUT;
  		  					}
  		  					?>
  					      </div>
  					  </div>
					  <div class="form-group">
							<input type="submit" id="searchsubmit" value="Search">
					  </div>
					</form>
				</div>
					<?php ?>
					<?php
					// Prepare query
					global $wp_query; 
					$total=$wp_query->found_posts;
					$paginationArgs=array();
					?>
                    <?php if (have_posts() ) : ?>
					<div class="col-lg-8 col-md-8 col-sm-6 col-xs-12 results">
                    <header class="header">
                    <h2 class="entry-title"><?php echo $total , ' item', ($total>1)?'s':'','.'; ?></h2>
					<p><?php if( isset($_GET['s']) && $_GET['s']!=''){ echo 'Keywords: ' , get_search_query(), '<br/>'; } ?>
						<?php if( isset($_GET['category'])){ 
							$trms = get_terms(array('slug'=>$_GET['category'],'fields'=>'names'));
							echo 'Categories: ' , implode(', ',$trms), '<br/>'; } ?>
						<?php if( isset($_GET['years'])){ 
//							$trms = get_terms(array('taxonomy'=>'years','slug'=>$_GET['years'],'fields'=>'names'));
							echo 'About years: ' , implode(', ',$_GET['years']), '<br/>'; } ?>
					</p>
                    </header>
					<div class="results_pagination">
					<?php echo paginate_links( $paginationArgs ); ?>
					</div>
                    <?php while ( have_posts() ) : the_post(); ?>
                    <?php //get_template_part( 'entry' ); ?>
					<div>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<?php include 'entry-meta.php'; ?>
						<footer class="entry-footer">
						<span class="cat-links"><?php the_category( ', ' ); ?></span>
						</footer> 
					</div>
                    <?php endwhile; ?>
					<div class="results_pagination">
					<?php echo paginate_links( $paginationArgs ); ?>
					</div>
					</div>
                    <?php //get_template_part( 'nav', 'below' ); ?>
                    <?php else : ?>
                    <article id="post-0" class="post no-results not-found">
                    <header class="header">
                    <h2 class="entry-title"><?php _e( 'Nothing Found', 'blankslate' ); ?></h2>
                    </header>
                    <section class="entry-content">
                    <p><?php _e( 'Sorry, nothing matched your search. Please try again.', 'blankslate' ); ?></p>
                    <?php get_search_form(); ?>
                    </section>
                    </article>
                    <?php endif; ?>
                    </section>
				</div>
            </div>
        </div>
    </div>                    
<?php get_sidebar(); ?>
<?php get_footer(); ?>