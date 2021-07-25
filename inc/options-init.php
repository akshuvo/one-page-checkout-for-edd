<?php

    /**
     * ReduxFramework Config File for EddNstant
     * For full documentation, please visit: http://docs.reduxframework.com/
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

    // This is your option name where all the Redux data is stored.
    $opt_name = "opcfedd_opt";

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $args = array(
        // TYPICAL -> Change these values as you need/desire
        'opt_name'             => $opt_name,
        // This is where your data is stored in the database and also becomes your global variable name.
        'display_name'         => __( 'One Page Checkout for Easy Digital Downloads Settings', 'one_page_checkout_for_edd' ),
        // Name that appears at the top of your panel
        //'display_version'      => __( '1.0.0', 'one_page_checkout_for_edd' ),
        // Version that appears at the top of your panel
        'menu_type'            => 'submenu',
        //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
        'allow_sub_menu'       => true,
        // Show the sections below the admin menu item or not
        'menu_title'           => __( 'One Page Checkout', 'one_page_checkout_for_edd' ),
        'page_title'           => __( 'One Page Checkout for Easy Digital Downloads Settings', 'one_page_checkout_for_edd' ),
        // You will need to generate a Google API key to use this feature.
        // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
        'google_api_key'       => '',
        // Set it you want google fonts to update weekly. A google_api_key value is required.
        'google_update_weekly' => false,
        // Must be defined to add google fonts to the typography module
        'async_typography'     => true,
        // Use a asynchronous font on the front end or font string
        //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
        'admin_bar'            => false,
        // Show the panel pages on the admin bar
        'admin_bar_icon'       => 'dashicons-cart',
        // Choose an icon for the admin bar menu
        'admin_bar_priority'   => 50,
        // Choose an priority for the admin bar menu
        'global_variable'      => '',
        // Set a different name for your global variable other than the opt_name
        'dev_mode'             => false,
        // Show the time the page took to load, etc
        'update_notice'        => false,
        // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
        'customizer'           => false,
        // Enable basic customizer support
        //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
        'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

        // OPTIONAL -> Give you extra features
        'page_priority'        => null,
        // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
        'page_parent'          => 'edit.php?post_type=download',
        // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
        'page_permissions'     => 'manage_options',
        // Permissions needed to access the options panel.
        'menu_icon'            => ONE_PAGE_CHECKOUT_FOR_EDD_PLUGIN_URL . 'assets/eddnstant-menu.svg',
        // Specify a custom URL to an icon
        'last_tab'             => '',
        // Force your panel to always open to a specific tab (by id)
        'page_icon'            => 'icon-themes',
        // Icon displayed in the admin panel next to your menu_title
        'page_slug'            => '_opcfedd',
        // Page slug used to denote the panel
        'save_defaults'        => true,
        // On load save the defaults to DB before user clicks save or not
        'default_show'         => false,
        // If true, shows the default value next to each field that is not the default value.
        'default_mark'         => '',
        // What to print by the field's title if the value shown is default. Suggested: *
        'show_import_export'   => true,
        // Shows the Import/Export panel when not used as a field.

        // CAREFUL -> These options are for advanced use only
        'transient_time'       => 60 * MINUTE_IN_SECONDS,
        'output'               => true,
        // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
        'output_tag'           => true,
        // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
        'footer_credit'     => ' ',
        // Disable the footer credit of Redux. Please leave if you can help it.

        // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
        'database'             => '',
        // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!

        'use_cdn'              => true,
        // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.

        //'compiler'             => true,

        // HINTS
        'hints'                => array(
            'icon'          => 'el el-question-sign',
            'icon_position' => 'right',
            'icon_color'    => 'lightgray',
            'icon_size'     => 'normal',
            'tip_style'     => array(
                'color'   => 'light',
                'shadow'  => true,
                'rounded' => false,
                'style'   => '',
            ),
            'tip_position'  => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect'    => array(
                'show' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'mouseover',
                ),
                'hide' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'click mouseleave',
                ),
            ),
        )
    );

    Redux::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */

    // -> START Basic Fields
    Redux::setSection( $opt_name, array(
        'title'  => __( 'General', 'one_page_checkout_for_edd' ),
        'desc'   => __( 'General Options', 'one_page_checkout_for_edd' ),
        'icon'   => 'el el-cogs',
        'fields' => array(
            array(
                'id'       => 'cart-position',
                'type'     => 'button_set',
                'title'    => __( 'Cart Position', 'one_page_checkout_for_edd' ),
                'options' => array(
                    '0' => __( 'Left', 'one_page_checkout_for_edd' ),
                    '1' => __( 'Right', 'one_page_checkout_for_edd' ),
                 ),
                'default'   => '0', 
            ),
            array(
                'id'       => 'show-close-btn',
                'type'     => 'button_set',
                'title'    => __( 'Show Close Button?', 'one_page_checkout_for_edd' ),
                'subtitle' => __( 'This will show a close button top of the panel ', 'one_page_checkout_for_edd' ),
                'options' => array(
                    '1' => __( 'Yes', 'one_page_checkout_for_edd' ),
                    '0' => __( 'No', 'one_page_checkout_for_edd' ),
                 ),
                'default'   => '0',
            ),
            array(
                'id'       => 'panel-width',
                'type'     => 'slider',
                'title'    => __( 'Panel Width', 'one_page_checkout_for_edd' ),
                'subtitle' => __( 'Control panel size. Default: 90%', 'one_page_checkout_for_edd' ),
                'min' => 0,
                'step' => 1,
                'max' => 100,
                'default'  => 90,
            ),
            array(
                'id'       => 'panel-height',
                'type'     => 'slider',
                'title'    => __( 'Panel Height', 'one_page_checkout_for_edd' ),
                'subtitle' => __( 'Control panel size. Default: 90%', 'one_page_checkout_for_edd' ),
                'min' => 0,
                'step' => 1,
                'max' => 100,
                'default'  => 90,
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'  => __( 'Design', 'one_page_checkout_for_edd' ),
        'desc'   => __( 'Design Options', 'one_page_checkout_for_edd' ),
        'icon'   => 'el el-magic',
        'fields' => array(
            array(
                'id'       => 'panel-bg',
                'type'     => 'color',
                'title'    => __( 'Panel Background Color', 'one_page_checkout_for_edd' ),
                'default'  => '',
                'transparent'  => false,
            ),
             
            // Section Start
            array(
                'id'       => 'sticky-cart-section-start',
                'type'     => 'section',
                'title'    => __( 'Bottom Sticky Cart', 'one_page_checkout_for_edd' ),
                'indent' => true,
            ),
            array(
                'id'       => 'sticky-cart-bg',
                'type'     => 'color',
                'title'    => __( 'Background Color', 'one_page_checkout_for_edd' ),
                'default'  => '',
                'transparent'  => false,
            ),
            array(
                'id'       => 'sticky-cart-color',
                'type'     => 'color',
                'title'    => __( 'Cart Count Text Color', 'one_page_checkout_for_edd' ),
                'default'  => '',
                'transparent'  => false,
            ),
            array(
                'id'       => 'sticky-cart-count-bg',
                'type'     => 'color',
                'title'    => __( 'Cart Count Background Color', 'one_page_checkout_for_edd' ),
                'default'  => '',
                'transparent'  => false,
            ),
            array(
                'id'       => 'sticky-cart-icon-color',
                'type'     => 'color',
                'title'    => __( 'Icon Color', 'one_page_checkout_for_edd' ),
                'default'  => '',
                'transparent'  => false,
            ),
            // Section End
            array(
                'id'       => 'section-reset',
                'type'     => 'section',
                'indent'   => false,
            ),
            array(
                'id'       => 'panel-zindex',
                'type'     => 'slider',
                'title'    => __( 'Panel z-index', 'one_page_checkout_for_edd' ),
                'subtitle' => sprintf( __( 'Control panel z-index from this option. More about %s', 'one_page_checkout_for_edd' ), '<a target="_blank" href="https://css-tricks.com/almanac/properties/z/z-index/">z-index</a>' ),
                'default' => 99999,
                'min' => 99999,
                'step' => 10,
                'max' => 999999,
            ),
            array(
                'id'       => 'custom-css',
                'type'     => 'ace_editor',
                'title'    => __( 'Custom CSS', 'one_page_checkout_for_edd' ),
                'subtitle' => __( 'If you want to make extra CSS then you can do it from here', 'one_page_checkout_for_edd' ),
                'mode'   => 'css',
                'theme'    => 'monokai',
            ),
        )
    ) );


    /*
     * <--- END SECTIONS
     */
