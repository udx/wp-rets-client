<?php
/**
 * Template for displaying video content.
 *
 * @package Madison
 * @author Justin Kopepasah
 * @since 1.0.0
*/
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<figure class="entry-video">
		<?php madison_post_format_video_first_video(); ?>
	</figure>

	<div class="entry-inner">
		<header class="entry-header">
			<span class="entry-meta entry-meta-categories"><?php the_category( ', ' ); ?></span>
			<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
			<p class="entry-meta entry-meta-time"><i class="fa fa-clock-o"></i><?php echo madison_get_time_difference( get_the_date( 'Y-m-d H:i:s' ) ); ?><p>
		</header>

		<div class="entry-content">
			<?php the_content( __( 'Continue Reading', 'madison' ) ); ?>
		</div>

		<footer class="entry-footer">
			<?php the_tags( '<p class="entry-meta entry-meta-tags">', false, '</p>' ); ?>
		</footer>
	</div>
</article>
