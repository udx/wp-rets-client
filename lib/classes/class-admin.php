<?php
/**
 * Admin Panel UI functionality
 *
 * @since 2.0.0
 * @author peshkov@UD
 */
namespace UsabilityDynamics\WPP {

  use WPP_F;

  if (!class_exists('UsabilityDynamics\WPP\Admin')) {

    class Admin extends Scaffold
    {

      /**
       * Adds all required hooks
       */
      public function __construct()
      {

        parent::__construct();

        /**
         * Init 'All Properties' page.
         */
        new Admin_Overview();


        if( defined( 'WP_PROPERTY_SETUP_ASSISTANT' ) && WP_PROPERTY_SETUP_ASSISTANT ) {
          new Setup_Assistant();
        }

        //** Load admin header scripts */
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

        /** Admin interface init */
        add_action("admin_init", array($this, "admin_init"));

        // @todo Move back to Settings -> Properties as it was years ago.
        add_action("admin_menu", array($this, 'admin_menu'), 150);

        // @todo Make directory names dynamic, since it may change.
        add_action("in_plugin_update_message-wp-property/wp-property.php", array($this, 'product_update_message'), 20, 2);

        // wpp-disable-term-editing
        add_filter( 'admin_body_class', array($this, 'admin_body_class'), 20, 2);

        // If wpp_categorical used, add term page UI.
        if( defined( 'WPP_FEATURE_FLAG_WPP_CATEGORICAL' ) && WPP_FEATURE_FLAG_WPP_CATEGORICAL ) {
          add_action( 'wpp_categorical_edit_form_fields', array( $this, 'edit_form_fields' ), 20, 2 );
          add_action( 'manage_wpp_categorical_custom_column', array( $this, 'wpp_categorical_custom_column' ), 20, 3 );
          add_filter( 'manage_edit-wpp_categorical_columns', array( $this, 'wpp_categorical_columns' ), 20 );
        }

        if( defined( 'WPP_FEATURE_FLAG_WPP_SCHOOLS' ) && WPP_FEATURE_FLAG_WPP_SCHOOLS ) {
          add_action( 'wpp_schools_edit_form_fields', array( $this, 'edit_form_fields' ), 20, 2 );
          //add_action( 'manage_wpp_schools_custom_column', array( $this, 'wpp_schools_custom_column' ), 20, 3 );
          //add_filter( 'manage_edit-wpp_schools_columns', array( $this, 'wpp_schools_columns' ), 20 );
        }

        // Add custom columns to Taxonomy table.
        if( defined( 'WPP_FEATURE_FLAG_WPP_LISTING_LOCATION' ) && WPP_FEATURE_FLAG_WPP_LISTING_LOCATION ) {
          add_filter( 'manage_wpp_listing_location_custom_columns', array( $this, 'wpp_listing_location_custom_columns' ), 20  );
          //add_filter( 'manage_edit-wpp_listing_location_columns', array( $this, 'wpp_listing_location_columns' ), 20, 3 );
        }

      }

      /**
       * Categorical Term Data
       *
       * @author potanin@UD
       *
       * @param $nothing
       * @param $column_name
       * @param $term_id
       */
      public function wpp_categorical_custom_column( $nothing, $column_name, $term_id ) {

        if( $column_name === 'source' ) {
          $source = get_term_meta( $term_id, 'source', true );
          echo $source ? $source : '-';
        }

        if( $column_name === '_id' ) {
          $type = get_term_meta( $term_id, '_id', true );
          echo $type ? $type : '-';
        }

      }

      /**
       * Display values for custom meta fields.
       *
       * @param $nothing
       * @param $column_name
       * @param $term_id
       */
      public function wpp_listing_location_columns( $nothing, $column_name, $term_id ) {

        if( $column_name === 'source' ) {
          $source = get_term_meta( $term_id, 'source', true );
          echo $source ? $source : '-';
        }

        if( $column_name === '_id' ) {
          $type = get_term_meta( $term_id, '_id', true );
          echo $type ? $type : '-';
        }

      }

      /**
       * Term Overview Columns
       *
       * @author potanin@UD
       *
       * @param $columns
       * @return mixed
       */
      public function wpp_categorical_columns( $columns ) {
        $columns['source'] = 'Source';
        //$columns['_id'] = 'ID';
        return $columns;
      }

      /**
       * Term Overview Columns
       *
       * @author potanin@UD
       *
       * @param $columns
       * @return mixed
       */
      public function wpp_listing_location_custom_columns( $columns ) {
        $columns['source'] = 'Source';
        //$columns['_id'] = 'ID';
        return $columns;
      }

      /**
       * Render Term Editor fields.
       *
       * @author potanin@UD
       * @param $tag
       * @param $taxonomy
       */
      public function edit_form_fields( $tag, $taxonomy ) {
        include ud_get_wp_property()->path( "static/views/admin/edit-term-fields.php", 'dir' );
      }

      /**
       * Manipulates admin body class for WPP UX.
       *
       * - Disable term-editing on WP-Property term pages.
       *
       * @todo Make this automatic for "readonly" taxonomies. - potanin@UD
       *
       * @author potanin@UD
       * @return string
       */
      public function admin_body_class() {
        global $current_screen, $wp_properties;

        // Do nothing.
        if( !isset( $current_screen->base ) || !isset( $current_screen->post_type ) || !isset( $current_screen->taxonomy ) || $current_screen->post_type !== 'property' ) {
          return;
        }

        // When developer mode is enabeld, allow editing.
        if( isset( $wp_properties['configuration'] ) && isset( $wp_properties['configuration']['developer_mode'] ) && $wp_properties['configuration']['developer_mode'] === 'true' ) {
          return;
        }

        // Hide term editing UI.
        if( $current_screen->base === 'edit-tags' && $current_screen->taxonomy === 'wpp_categorical' ) {
          return 'wpp-disable-term-editing';
        }

        if( $current_screen->base === 'edit-tags' && $current_screen->taxonomy === 'wpp_listing_location' ) {
          return 'wpp-disable-term-editing';
        }

      }

      /**
       * Can enqueue scripts on specific pages, and print content into head
       *
       *
       * @uses $current_screen global variable
       * @since 0.53
       *
       */
      public function enqueue_scripts()
      {
        global $current_screen;

        wp_localize_script('wpp-localization', 'wpp', array(
          'instance' => apply_filters( 'wpp::localization::instance', $this->instance->core->get_instance() )
        ));

        switch ($current_screen->id) {

          //** Edit Property page */
          case 'property':
            global $post;

            $post_type_object = get_post_type_object('property');
            if (current_user_can($post_type_object->cap->create_posts)) {
              wp_enqueue_script('wpp-clone-property', $this->instance->path('static/scripts/wpp.admin.clone.js', 'url'), array('jquery', 'wp-property-global'), $this->instance->get('version'), true);
            }

            wp_enqueue_script('wp-property-global');
            wp_enqueue_script('wp-property-backend-editor');
            //** Enabldes fancybox js, css and loads overview scripts */
            wp_enqueue_script('post');
            wp_enqueue_script('postbox');
            wp_enqueue_script('wpp-jquery-fancybox');
            wp_enqueue_style('wpp-jquery-fancybox-css');
            wp_enqueue_script('wp-property-backend-global');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_style('jquery-ui');
            wp_enqueue_script('wp-property-admin-widgets');
            break;

          //** Settings Page */
          case 'property_page_property_settings':
            wp_enqueue_script('wp-property-backend-global');
            wp_enqueue_script('wp-property-global');
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('wpp-jquery-colorpicker');
            wp_enqueue_script('wpp-select2');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script('jquery-cookie');
            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_script('wp-property-admin-settings');

            wp_enqueue_script('custom-jqueryui-script', '//code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery'));
            wp_enqueue_style('jquery-ui');
            wp_enqueue_style('wpp-jquery-ui-dialog');
            wp_enqueue_style('wpp-jquery-colorpicker-css');
            wp_enqueue_style('select2');
            wp_enqueue_script('jquery-jjsonviewer', $this->instance->path('static/scripts/vendor/jjsonviewer.js', 'url'), array( 'jquery' ), WPP_Version, true );
            // This will enqueue the Media Uploader script
            wp_enqueue_media();
            break;

          //** Widgets Page */
          case 'widgets':
          case 'customize':
          case 'wpp_layout':
            wp_enqueue_script('wp-property-backend-global');
            wp_enqueue_script('wp-property-global');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_style('jquery-ui');
            wp_enqueue_script('wp-property-admin-widgets');
            break;

        }

        //** Automatically insert styles sheet if one exists with $current_screen->ID name */
        if (file_exists($this->instance->path("static/styles/{$current_screen->id}.css", 'dir'))) {
          wp_enqueue_style($current_screen->id . '-style', $this->instance->path("static/styles/{$current_screen->id}.css", 'url'), array(), WPP_Version, 'screen');
        }

        //** Automatically insert JS sheet if one exists with $current_screen->ID name */
        if (file_exists($this->instance->path("static/scripts/{$current_screen->id}.js", 'dir'))) {
          wp_enqueue_script($current_screen->id . '-js', $this->instance->path("static/scripts/{$current_screen->id}.js", 'url'), array('jquery'), WPP_Version, 'wp-property-backend-global');
        }

        //** Enqueue CSS styles on all pages */
        if (file_exists($this->instance->path('static/styles/wpp.admin.css', 'dir'))) {
          wp_register_style('wpp-admin-styles', $this->instance->path('static/styles/wpp.admin.css', 'url'), array(), WPP_Version);
          wp_enqueue_style('wpp-admin-styles');
        }

      }

      /**
       * Runs pre-header functions on admin-side only
       *
       * Checks if plugin has been updated.
       *
       * @since 1.10
       *
       */
      public function admin_init()
      {
        global $wp_properties;

        // Add metaboxes
        do_action('wpp_metaboxes');

        // Download backup of configuration or fields
        if ( isset($_REQUEST['page']) && $_REQUEST['page'] == 'property_settings' && isset($_REQUEST['wpp_action']) && $_REQUEST['wpp_action'] == 'download-wpp-backup' && isset($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'download-wpp-backup') ) {
          $sitename = sanitize_key(get_bloginfo('name'));

          header("Cache-Control: private,no-cache,no-store");
          header("Content-Description: File Transfer");
          header("Content-Transfer-Encoding: binary");
          header('Content-Type: text/plain; charset=' . get_option('blog_charset'), true);

          $_options = array(
            'type' => 'full',
            'timestamp' => time(),
            'filename' => $sitename . '-wp-property.' . date('Y-m-d') . '.json'
          );

          //if backup of data from setup-assistant
          // get backed-up data for download
          if (isset($_REQUEST['timestamp'])) {
            $data = get_option('wpp_property_backups');
            $wp_properties = $data[$_REQUEST['timestamp']];
          }

          // May be extend backup data by add-ons options.
          if( isset( $_GET['wpp-backup-type'] ) && $_GET['wpp-backup-type'] === 'fields' ) {

            // overwrite some backup options.
            $_options['type'] = 'fields';
            $_options['filename'] = $sitename . '-wp-property.fields.' . date('Y-m-d') . '.json';

            $data = apply_filters('wpp::backup::data', array('wpp_settings' => array(
              'location_matters' => $wp_properties['location_matters'],
              'hidden_attributes' => $wp_properties['hidden_attributes'],
              'searchable_attributes' => $wp_properties['searchable_attributes'],
              'searchable_property_types' => $wp_properties['searchable_property_types'],
              'property_inheritance' => $wp_properties['property_inheritance'],
              'property_stats' => $wp_properties['property_stats'],
              'property_types' => $wp_properties['property_types'],
              'property_stats_groups' => $wp_properties['property_stats_groups'],
              'sortable_attributes' => $wp_properties['sortable_attributes'],
              'searchable_attr_fields' => $wp_properties['searchable_attr_fields'],
              'predefined_search_values' => $wp_properties['predefined_search_values'],
              'admin_attr_fields' => $wp_properties['admin_attr_fields'],
              'predefined_values' => $wp_properties['predefined_values'],
              'default_values' => $wp_properties['default_values'],
              'property_groups' => $wp_properties['property_groups'],
              'geo_type_attributes' => $wp_properties['geo_type_attributes'],
              'numeric_attributes' => $wp_properties['numeric_attributes'],
              'currency_attributes' => $wp_properties['currency_attributes']
            )), $_options );

          }

          if( isset( $_GET['wpp-backup-type'] ) && $_GET['wpp-backup-type'] === 'full' ) {
            $data = apply_filters('wpp::backup::data', array('wpp_settings' => $wp_properties), $_options );
          }

          if( isset( $_options['filename'] ) ) {
            header("Content-Disposition: attachment; filename=" . $_options['filename'] );
          }

          if( isset( $_options['filename'] ) && isset( $data ) ) {
            die(json_encode($data, JSON_PRETTY_PRINT));
          }

          die();

        }

      }

      /**
       *
       */
      public function admin_menu()
      {

        $settings_page = add_submenu_page('edit.php?post_type=property', __('Settings', ud_get_wp_property()->domain), __('Settings', ud_get_wp_property()->domain), 'manage_wpp_settings', 'property_settings', array( 'UsabilityDynamics\WPP\Settings', 'render_page' ) );

      }

      /**
       * Display a hopefully helpful message next to "there's an update available" message, in particular if pre-release updates are enabled.
       *
       * @author potanin@UD
       * @param $plugin_data
       * @param $response
       */
      public function product_update_message($plugin_data, $response)
      {
        global $wp_properties;

        // pre-release updates not enabled or no update.
        if (!isset($wp_properties['configuration']['pre_release_update']) || $wp_properties['configuration']['pre_release_update'] !== 'true' || !$plugin_data['update']) {
          return;
        }

        if (isset($response) && isset($response->message)) {
          echo ' <span class="wpp-update-message">' . $response->message . '</span>';
        } else {
          echo ' <span class="wpp-update-message">' . __('You are seeing this because you subscribed to latest updates.', ud_get_wp_property()->domain) . '</span>';
        }

      }

      /**
       * @param $input
       * @return mixed
       */
      public function wp_property_sanitize_callback($input)
      {
        return $input;
      }

    }

  }

}