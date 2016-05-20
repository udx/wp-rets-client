<?php
$query = siteorigin_widget_post_selector_process_query( $instance['posts'] );
$the_query = new WP_Query( $query );

/**
 * Filter the number of words in an excerpt.
 */
add_filter( 'excerpt_length', 'rdc_selectedposts_excerpt_length', 99, 1 );
/**
 * Filter the string in the "more" link displayed after a trimmed excerpt.
 */
add_filter( 'excerpt_more', 'rdc_selectedposts_excerpt_more', 99, 1 );

if ( $the_query->have_posts() ) {
	?>
	<div class="rdc-selected-posts">
		<div class="section-content">

			<h3 class="section-title"><?php echo $instance['label']; ?></h3>

			<p class="section-tagline"><?php echo $instance['tagline']; ?></p>
			<hr class="section-delimiter"/>

			<div class="posts-loop">
				<ul class="section-selected-posts row">
					<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
						<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium' ); ?>
						<li class="column col-4-12">
							<div class="sbox-item hvr-buzz-out">
								<a href="<?php the_permalink(); ?>" class="icon-link"><div class="icon-box thumb" <?php echo !empty( $image[0] ) ? 'style="background-image: url(\'' . $image[0] . '\');"' : ''; ?>></div></a>
								<div class="content">
									<a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?></h4></a>
									<?php the_excerpt(); ?>
								</div>
							</div>
						</li>
					<?php endwhile; wp_reset_postdata(); ?>
				</ul>
			</div>

		</div>
	</div>
	<?php
}

remove_action( 'excerpt_length', 'rdc_selectedposts_excerpt_length', 99 );
remove_action( 'excerpt_more', 'rdc_selectedposts_excerpt_more', 99 );
