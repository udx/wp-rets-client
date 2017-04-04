<?php
/**
 *  Adds [wpp_listing_status] taxonomy.
 *
 * @since 2.3
 */
namespace UsabilityDynamics\WPP {

  use WPP_F;

  if( !class_exists( 'UsabilityDynamics\WPP\Taxonomy_WPP_Listing_Status' ) ) {

    class Taxonomy_WPP_Listing_Status {

      /**
       * Loads WPP_Listing_Status Taxonomy stuff
       */
      public function __construct(){

        // Break, if disabled.
        if ( !WPP_FEATURE_FLAG_WPP_LISTING_STATUS ) {
          return;
        }

        // Register taxonomy.
        add_filter('wpp_taxonomies', array( $this, 'define_taxonomies'), 10 );

        add_filter( 'wpp:elastic:title_suggest', array( $this, 'elastic_title_suggest' ), 10, 3 );

      }

      /**
       * Register WPP_Listing_Status Taxonomy
       *
       * @param array $taxonomies
       * @return array
       */
      public function define_taxonomies( $taxonomies = array() ) {

        $taxonomies['wpp_listing_status'] = array(
          'default' => true,
          'readonly' => true,
          'system' => true,
          'meta' => true,
          'hidden' => false,
          'hierarchical' => true,
          'unique' => false,
          'public' => true,
          'show_in_nav_menus' => false,
          'show_ui' => false,
          'show_tagcloud' => false,
          'add_native_mtbox' => false,
          'label' => sprintf(_x('%s Status', 'property type taxonomy', ud_get_wp_property()->domain), WPP_F::property_label()),
          'labels' => array(
            'name' => sprintf(_x('%s Status', 'property type taxonomy', ud_get_wp_property()->domain), WPP_F::property_label()),
            'singular_name' => sprintf(_x('%s Status', 'property type taxonomy', ud_get_wp_property()->domain), WPP_F::property_label()),
            'search_items' => _x('Search  Status', 'property type taxonomy', ud_get_wp_property()->domain),
            'all_items' => _x('All Status', 'property type taxonomy', ud_get_wp_property()->domain),
            'parent_item' => _x('Parent Status', 'property type taxonomy', ud_get_wp_property()->domain),
            'parent_item_colon' => _x('Parent Status', 'property type taxonomy', ud_get_wp_property()->domain),
            'edit_item' => _x('Edit Status', 'property type taxonomy', ud_get_wp_property()->domain),
            'update_item' => _x('Update Status', 'property type taxonomy', ud_get_wp_property()->domain),
            'add_new_item' => _x('Add New Status', 'property type taxonomy', ud_get_wp_property()->domain),
            'new_item_name' => _x('New Status', 'property type taxonomy', ud_get_wp_property()->domain),
            'not_found' => sprintf(_x('No %s Status found', 'property type taxonomy', ud_get_wp_property()->domain), WPP_F::property_label()),
            'menu_name' => sprintf(_x('%s Status', 'property type taxonomy', ud_get_wp_property()->domain), WPP_F::property_label()),
          ),
          'query_var' => 'property-status',
          'rewrite' => array('slug' => 'property-status')
        );

        return $taxonomies;

      }

      /**
       * We apply contexts for title_suggest based on the [wpp_listing_status] taxonomy
       *
       * @param $title_suggest
       * @param $args
       * @param $post_id
       * @return mixed
       */
      public function elastic_title_suggest( $title_suggest, $args, $post_id ) {

        /*
                $listing_status = "test";
                $sale_type = "test";

                $contexts = array_filter( array(
                  "listing_status" => $listing_status,
                  "sale_type" => $sale_type
                ) );
                */



        return $title_suggest;
      }

    }

  }

}
