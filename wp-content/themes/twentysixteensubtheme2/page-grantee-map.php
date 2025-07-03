<?php
 /*
   Template Name: Grantee Map Template     
 */


get_header();

wp_head();
?>


<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
      </header><!-- .entry-header -->

      <?php twentysixteen_post_thumbnail(); ?>

      <div class="entry-content">
        <?php
		the_content();
		?>

      <div id="grantee-map"></div>
      </div><!-- .entry-content -->

      <?php
      edit_post_link(
        sprintf(
        /* translators: %s: Post title. */
          __( 'Edit<span class="screen-reader-text"> "%s"</span>', 'twentysixteen' ),
          get_the_title()
        ),
        '<footer class="entry-footer"><span class="edit-link">',
        '</span></footer><!-- .entry-footer -->'
      );
      ?>

    </article><!-- #post-<?php the_ID(); ?> -->

  </main><!-- .site-main -->

	<?php get_sidebar( 'content-bottom' ); ?>

</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
