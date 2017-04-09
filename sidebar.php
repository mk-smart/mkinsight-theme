<?php if ( is_front_page() || is_home() ) : ?>
    <div id="highlights">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-12" id="latest-data">
                    <h2>Latest Data and Reports</h2>
                    <hr>
                    <ul>
                    	<?php 
       $args = array( 'numberposts' => 5, 'offset'=> 0, 'category_and' => array(3,18) );
	   $args = array_merge ($args, mki_orderby_args());
	   // echo '<pre>';
	   // var_dump($args);
	   // echo '<pre>';die;
       $postslist = get_posts( $args );
       foreach ($postslist as $post) :  setup_postdata($post); ?>
<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
<?php include 'entry-meta.php'; ?>
<?php include 'entry-summary.php'; ?>
</li>
<?php endforeach; ?>
</ul>
</div>
<div class="col-lg-6 col-md-6 col-sm-5 col-xs-12" id="latest-news">
  <h2>Latest News</h2>
                    <hr>
                    <?php 
	 $args = array( 'numberposts' => 5, 'offset'=> 0, 'order'=> 'DESC', 'orderby'=> 'date', 'cat' => 8 );
         $postslist = get_posts( $args );
         foreach ($postslist as $post) :  setup_postdata($post); ?>
						<article>
							<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
							<?php include 'entry-meta.php'; ?>
							<?php include 'entry-summary.php'; ?>
						</article>
					<?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
		<?php else: ?>
			<div id="highlights">
	   <div class="container">
	   <div class="row">
			   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="resources">
<!--			   <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12" id="resources">-->

<?php 
  global $post;
  if(!$post) {
	  // 404 page
	  return;
  }
  $post_slug=$post->post_name;
  $args = array( 'numberposts' => 10, 'offset'=> 0, 'category_name' => $post_slug );
 // if($post_slug == 'data'){
 //  	  $args['meta_query'] =  array(
 //             'relation' => 'OR',
 //             'years_not' => array(
 //                 'key' => 'years',
 //                 'compare' => 'NOT EXISTS'
 //             ),
 //  			'years' => array(
 //                 'key' => 'years',
 //  				'type'    => 'NUMERIC',
 //                 'compare' => 'EXISTS'
 //             ),
 //         );
 //  	  $args['orderby'] = array('meta_value_num'=>'DESC','date'=>'DESC');
 //   }else{
 //      $args['orderby'] = 'date';
 // }
  $postslist = get_posts( $args );
if (count($postslist)!=0){
?>
  
  <?php if (strcmp($post_slug, "data")===0) { ?>
    <h2>Latest Datasets</h2>
      <?php } else if (strcmp($post_slug, "news")===0) { ?>
    <h2>Latest News</h2>
      <?php } else if (strcmp($post_slug, "report")===0) { ?>
    <h2>Latest Reports</h2>
      <?php } else { ?>
    <h2>Latest resources in the category <?php echo $post_slug; ?></h2>
								      <?php } ?>
    
                    <hr>
                    <ul>
			   <?php 
foreach ($postslist as $post) :  setup_postdata($post); ?>
<li>
<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
<?php include 'entry-meta.php'; ?>
<?php include 'entry-summary.php'; ?>
</li>
<?php endforeach; ?>
<li><a style="float: right;" href="http://mkinsight.org/category/<?php echo $post_slug; ?>">More...</a></li>
</ul>
</div>
<!--
<div class="col-lg-4 col-md-4 col-sm-5 col-xs-12" id="themes">
  <h2>General Themes</h2>
  <hr>
  <ul>
  <li>Test</li>
  </ul>
  </div>
-->
    <?php } ?>
  </div>
  </div>
  </div>
  <?php endif; ?>