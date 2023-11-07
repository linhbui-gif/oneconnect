<?php
/**
 * The blog template file.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

get_header();

?>

	
<?php echo do_shortcode('[block id="first-section-about"]'); ?>
<?php echo do_shortcode('[block id="latest-article-block"]'); ?>


<div id="content" class="blog-wrapper blog-archive page-wrapper">
		<?php get_template_part( 'template-parts/posts/layout', get_theme_mod('blog_layout','right-sidebar') ); ?>
</div>



<?php get_footer(); ?>