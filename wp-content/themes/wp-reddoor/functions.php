<?php

require_once get_template_directory() . '/lib/classes/class-utils.php';
require_once get_template_directory() . '/lib/classes/class-bootstrap.php';
require_once get_template_directory() . '/lib/classes/class-widgets.php';
require_once get_template_directory() . '/lib/classes/class-property-hooks.php';
require_once get_template_directory() . '/lib/classes/class-customizer.php';
require_once get_template_directory() . '/lib/classes/class-ajax.php';
require_once get_template_directory() . '/lib/classes/class-shortcodes.php';

## Post Types
require_once get_template_directory() . '/lib/post-types/guide.php';

if(class_exists('SiteOrigin_Widget')) {
  require_once 'lib/widgets/rdc-post-carousel/post-carousel.php';
  require_once 'lib/widgets/rdc-hero/hero.php';
}

add_action( 'admin_enqueue_scripts', function () {
  wp_enqueue_style( 'rdc-admin', get_template_directory_uri() . '/static/styles/admin/style.css', false, '1.0.0' );
} );

new \UsabilityDynamics\RDC\Bootstrap();

/**
 *
 * @author potanin@UD
 * @param $name
 * @return array
 */
function rdc_get_attribute_group( $name ) {
  global $wp_properties, $post, $property;

  $listAttributes = array();
  $taxonomies = ud_get_wpp_terms( 'config.taxonomies', array() );
  foreach( $wp_properties[ 'property_stats_groups' ] as $key => $value ) {
    if( $value == $name ) {
      if( array_key_exists( $key, $taxonomies ) ) {
        $get_term_value = get_the_terms( $property[ 'ID' ], $key );
        if( !empty( $get_term_value[ 0 ]->name ) ) {
          $listAttributes[] = '<li><span class="field-label">' . str_replace( '_', ' ', ucwords( $key ) ) . ':</span> <span class="field-value">' . $get_term_value[ 0 ]->name . '</span></li>';
        }
      } else {
        if( isset( $property[ "$key" ] ) && $property[ $key ] == true ) {
          $listAttributes[] = '<li><span class="field-label">' . str_replace( '_', ' ', ucwords( $key ) ) . ':</span> <span class="field-value">Yes</span></li>';
        }
      }
    }
  }

  return $listAttributes;

}