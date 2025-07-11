<?php
 /*
   Template Name: Grantee Map Dashboard    
 */


get_header();

wp_head();
?>


    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
      </header><!-- .entry-header -->

      <?php twentysixteen_post_thumbnail(); ?>

      <div>
        <?php
		the_content();
		?>
  </div>
    </article><!-- #post-<?php the_ID(); ?> -->


<?php get_sidebar(); ?>
<?php get_footer(); ?>
