<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Twelve already
 * has tag.php for Tag archives, category.php for Category archives, and
 * author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
$term_value = $wp->query_vars[high_school];
get_header(); ?>
<?php echo $term_value; ?>
  <div class="container-fluid">
    <div class="row">
      <?php echo do_shortcode("[supermap mode=advanced high_school=wake-cty-school-district ]"); ?>
    </div><!-- .row -->
  </div>


<?php get_footer(); ?>