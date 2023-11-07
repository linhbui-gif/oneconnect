<?php
/**
 * Post-entry title.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */
?>

<div class="category-date-group"><h6 class="entry-category is-xsmall">
	<?php
		foreach((get_the_category()) as $cat) {
		$category_link = get_category_link($cat->cat_ID);
			echo '<a href="'.esc_url( $category_link ).'">';
			echo '<span class="'.$cat->slug.'">'.$cat->cat_name.' '.'</span>';
			echo '</a>';
		}
	?>

</h6>
<?php
$single_post = is_singular( 'post' );
if ( $single_post && get_theme_mod( 'blog_single_header_meta', 1 ) ) : ?>
	<div class="entry-meta uppercase is-small">
		<?php flatsome_posted_on(); ?>
	</div>
<?php elseif ( ! $single_post && 'post' == get_post_type() ) : ?>
	<div class="entry-meta uppercase is-small">
		<?php flatsome_posted_on(); ?>
	</div>
<?php endif; ?>
</div>
<?php
if ( is_single() ) {
    
	echo '<h1 class="entry-title">' . get_the_title() . '</h1>';
} else {
	echo '<h2 class="entry-title"><a href="' . get_the_permalink() . '" rel="bookmark" class="plain">' . get_the_title() . '</a></h2>';
}
?>

<div class="entry-divider is-divider small"></div>
