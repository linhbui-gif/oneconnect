<?php
/**
 * Posts archive 3 column.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

if ( have_posts() ) :?>

<?php
	// Create IDS
	$ids = array();
	while ( have_posts() ) : the_post();
		array_push($ids, get_the_ID());
	endwhile; // end of the loop.
	$ids = implode(',', $ids);
?>

	<?php
	echo flatsome_apply_shortcode( 'blog_posts', array(
		'type'        => 'row',
		'depth'       => 0,
		'depth_hover' => 0,
		'text_align'  => 'left',
		'title_size'       => 'larger',
		'columns'     => '3',
		'columns__sm' => '1',
		'columns__md' => '2',
		'col_spacing' => 'normal',
		'show_date'   => 'hidden',
		'excerpt'     => 'visible',
		'excerpt_length' => 25,
		'ids'         => $ids,
		'read_more_button' => '',
		'equalize_box' => 'true',
	) );
	?>

<?php flatsome_posts_pagination(); ?>

<?php else : ?>

	<?php get_template_part( 'template-parts/posts/content','none'); ?>

<?php endif; ?>
