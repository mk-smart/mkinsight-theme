<?php get_header(); ?>
                    <section role="main">
                    <header class="header">
                    <h1 class="entry-title">Search</h1>
                    </header>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 advanced-search">
					<p><small>You can use the filters to show only results that match your interests</small></p>
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
<!-- YEAR RANGE -->
  					 <div class="form-group">
  						<label>About years:</label>
<label>From
<select class="min year" id="minYear" name="ymin">
  <option> --- </option>
  <?php $categories = get_categories(array('taxonomy' => 'years','order'=>'ASC'));
  foreach ($categories as $category):
    $cslug= $category->slug;
    $checked = ($cslug == $_GET['ymin'])? 'selected="selected"':""; ?>
    <option value="<?php print $cslug; ?>" <?php print $checked; ?> ><?php print $category->name; ?></option>
  <?php endforeach; ?>
</select>
to
<select class="max year" id="maxYear" name="ymax">
  <option> --- </option>
  <?php $categories = get_categories(array('taxonomy' => 'years','order'=>'DESC'));
  foreach ($categories as $category):
    $cslug= $category->slug;
    $checked = ( $cslug == $_GET['ymax'])? 'selected="selected"':""; ?>
  <option value="<?php print $cslug; ?>" <?php print $checked; ?> ><?php print $category->name; ?></option>
  <?php endforeach; ?>
</select>
</label>
            </div>

<!-- END YEAR RANGE -->
<?php /* OLD YEAR CHECKBOXES
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
  				<label><input type="checkbox" value="$cslug" name="years[]" $checked >$cname</label>
INPUT;
  		  					}
  		  					?>
  					      </div>
  					  </div>
*/ ?>
					  <div class="form-group">
							<input type="submit" id="searchsubmit" value="Search">
					  </div>
					</form>
				</div>
					<?php ?>
					<?php
					// Prepare query
					global $wp_query;
					$total = $wp_query->found_posts;
					$paginationArgs=array();
          $keyword_search_type = 'all';
          $search_query_text = get_search_query();
          // If no results, try changing query terms in OR
          if($total == 0){
            $keyword_search_type = 'any';
            $query_vars = $wp_query->query_vars;
            // Collect post ids
            $post_ids = array();
            foreach ($query_vars['search_terms'] as $key => $keyword) {
              # get posts with only this term
              $result = new WP_Query(array('s'=>$keyword, 'fields'=>'ids'));
              $post_ids = array_merge($post_ids,$result->posts);
            }
            // If any result
            if(!empty($post_ids)){
              // Override $wp_query
              $wp_query = new WP_Query(array('post__in'=>$post_ids));
              $total = $wp_query->found_posts;
            }
          }
/*
<pre><?php print_r($wp_query->query_vars);?></pre>
*/
					?>
          <?php if (have_posts() ) : ?>
					<div class="col-lg-8 col-md-8 col-sm-6 col-xs-12 results">
                    <header class="header">
                    <h2 class="entry-title"><?php echo $total , ' item', ($total>1)?'s':'','.'; ?></h2>
					<p><?php if( isset($_GET['s']) && $_GET['s']!=''){
            // Change the message if query rewritten to 'any'
            if($keyword_search_type == 'any'){
              echo '<small><i>We could not find any result including all the search terms. The list below includes pages that contain any of them.</i></small><br/>';
            }
            echo 'Keywords: ' , $search_query_text, '<br/>';
          }
            ?>
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
						<h3>
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                /* add icon to title
                                 * folder: img/infographics/
                                 * report: pie-chart3.png
                                 * data: data-green.png
                                 * page: document.png
                                */
                                // get post categories and filters for data or report
                                $cat = array_reduce(get_the_category(), function ($carry, $cat){
                                    // if category found
                                    if($carry){return $carry;}
                                    // search for category
                                    if($cat->slug === 'data'){return 'data';}
                                    if($cat->slug === 'report'){return 'report';}
                                    if($cat->slug === 'news'){return 'news';}
                                    if($cat->slug === 'essential'){return 'essential';}
                                    // default false
                                    return false;
                                });

                                switch($cat) {
                                    case 'report':
                                      echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="'.get_template_directory_uri().'/assets/img/infographics/pie-chart3.png">';
                                    break;
                                    case 'data':
                                      echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="'.get_template_directory_uri().'/assets/img/infographics/data-green.png">';
                                    break;
                                    default:
                                        if(get_post_type() == 'idea'){
                                            echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="'.get_template_directory_uri().'/assets/img/infographics/light-bulb-green.png">';
                                        } else {
                                            echo '<img class="title-icon" style="height:.8em;vertical-align: baseline;" src="'.get_template_directory_uri().'/assets/img/infographics/document.png">';
                                        }
                                }

                                ?>
                                <?php the_title(); ?>
                            </a>
                        </h3>
						<?php include 'entry-meta.php'; ?>
            <?php include 'entry-footer.php'; /*?>
						<footer class="entry-footer">
						<span class="cat-links"><?php the_category( ', ' ); ?></span>
          </footer> <?php */ ?>
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
