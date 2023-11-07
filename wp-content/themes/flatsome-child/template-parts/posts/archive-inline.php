<?php
/**
 * Posts archive inline.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

if ( have_posts() ) : ?>
<div id="post-list">

<?php /* Start the Loop */ ?>
<?php while ( have_posts() ) : the_post(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="article-inner <?php flatsome_blog_article_classes(); ?>">

		
		<?php if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it. ?>
		<div class="entry-image-float">
	 		<?php get_template_part( 'template-parts/posts/partials/entry-image', 'default'); ?>
			<?php if ( get_theme_mod( 'blog_badge', 1 ) ) get_template_part( 'template-parts/posts/partials/entry', 'post-date' ); ?>
	 	</div>
	 	<div class="entry-content entry-content-cat-tag">
	 	    <header class="entry-header">
	  	<div class="entry-header-text text-<?php echo get_theme_mod( 'blog_posts_title_align', 'center' );?>">
			   	<?php get_template_part( 'template-parts/posts/partials/entry', 'title');  ?>
			</div>
		</header>
	 	   <?php
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( __( ', ', 'flatsome' ) );
		?>
	 	</div>
 		<?php } ?>
		<?php get_template_part('template-parts/posts/content', 'default' ); ?>
		<div class="clearfix"></div>
		<?php get_template_part('template-parts/posts/partials/entry-footer', 'default' ); ?>
	</div>
</article>

<?php endwhile; ?>

<?php flatsome_posts_pagination(); ?>

</div>

<?php else : ?>

	<?php get_template_part( 'template-parts/posts/content','none'); ?>

<?php endif; ?>
