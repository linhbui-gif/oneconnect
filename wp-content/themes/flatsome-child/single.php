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

<div <?php post_class(); ?>>
<div id="content" class="blog-wrapper blog-single page-wrapper " >
	<?php get_template_part( 'template-parts/posts/layout', get_theme_mod('blog_post_layout','right-sidebar') ); ?>
</div>
</div>

<?php get_footer();
