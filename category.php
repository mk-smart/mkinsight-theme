<?php get_header(); ?>
<?php
  $term = get_queried_object();
?>
<section role="main" style="display:none" id="category-data-page">
  <header class="header">
    <h1 class="entry-title"><?php _e( 'Category: ', 'blankslate' ); ?><?php single_cat_title(); ?></h1>
    <?php if ( '' != category_description() ) echo apply_filters( 'archive_meta', '<div class="archive-meta">' . category_description() . '</div>' ); ?>
  </header>
<?php /*
  <pre><?php print_r(wpdt_get_categories_defaults()); ?></pre>
  <?php wpdt_list_categories('showcount=1&listposts=0&child_of='.$term->term_id); ?>

  <h2><?php _e( 'Categories:' ); ?></h2>
  	<form id="category-select" class="category-select" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
  		<?php wp_dropdown_categories( 'show_count=1&hierarchical=1' ); ?>
  		<input type="submit" name="submit" value="view" />
  	</form>
<?php */
$categories = array($term);
$category__and = array();
foreach($categories as $category){
  $category__and[] = $category->term_id;
}
if(isset($_GET['term_id'])){
foreach(@$_GET['term_id'] as $term_id){
  if(isset($_GET['exclude'])){
    if(intval($_GET['exclude']) == intVal($term_id))
      continue;
  }
  $category__and[]  = intVal($term_id);
  $categories[] = get_term($term_id);
}}
?>
<?php /*wp_tag_cloud(); */ ?> 

<?php
  $query = new WP_Query(array('category__and' => $category__and, 'nopaging'=> TRUE));
  ?>
  <form action="<?php print get_category_link($term->term_id);?>">
  <div id="categoryDataFilter">
    <div>
      <strong>Categories:</strong>
         <div id="categoryDataList">
         <?php
         // generate list of categories
         foreach ($categories as $category) {
           $cslug= $category->slug;
           $cname= $category->name;
           $cid= $category->term_id;
           if($term->slug == $category->slug){
             // Immutable
             ?><button class="btn btn-danger"><?php print $cname; ?></button> <?php
           }else{
             // Mutable
             ?><button class="btn badge-category" type="submit" name="exclude" value="<?php print $cid; ?>"> - <?php print $cname; ?></button> <input type="hidden" name="term_id[]" value="<?php print $cid; ?>"/><?php
           }
         }
         ?>
         </div>
     </div>
  </div>
  <div id="yearDataFilter">
    <div >
     <label>About years </label>
<label>from
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
  </div>
  <table id="categoryDataTable">
    <thead>
      <th>Title</th>
      <th>Categories</th>
      <th>About Years</th>
      <th>Published</th>
      <th>Files</th>
    </thead>
    <tbody>
  <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
    <tr>
      <td><a href="<?php the_permalink();?>"><?php the_title();?></a></td>
      <td><?php $cats = get_the_category();
      foreach($cats as $cat){
        if(!in_array($cat->term_id,$category__and)): ?>
        <button type="submit" class="btn badge-category" name="term_id[]" value="<?php print $cat->term_id;?>">
          +
          <?php print $cat->name;?></button>
      <?php endif;
      } ?></td>
      <td><?php the_terms(get_the_ID(), 'years'); ?></td>
      <td><?php the_date();?></td>
      <td>
        <?php
        $files = get_attached_media('',$query->post->ID);
        foreach($files as $fid=>$file):
        ?><a href="<?php print $file->guid; ?>"><?php print $file->post_title; ?></a>
        [<?php print $file->post_mime_type; ?>]<br/>
        <?php
        endforeach;
        ?>
      </td>
    </tr>
  <?php endwhile; wp_reset_postdata(); endif; ?>
    </tbody>
  </table>
</form>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>


<script>
$(document).ready(function() {
    $('#categoryDataTable').DataTable();
    $('#category-data-page').fadeIn();
} );
</script>
