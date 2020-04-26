<?php

/*
 * Copyright 2012-2018 WeCodePixels, http://wecodepixels.com
 */
 
namespace WeCodePixels\TheiaPostSliderFramework;

abstract class Freemius {
    private static $menuSlug;
    private static $originalPluginPage = null;
    private static $adminClass;

    public static function init( $freemius, $adminClass ) {
        self::$menuSlug   = $freemius->get_menu_slug();
        self::$adminClass = $adminClass;

        // Override Freemius templates with our own.
        $freemius->add_filter( 'templates/account.php', function ( $html ) {
            return Freemius::templateFilter( $html, 'account', 'account' );
        } );
        $freemius->add_filter( 'templates/add-ons.php', function ( $html ) {
            return Freemius::templateFilter( $html, 'addons', 'addons' );
        } );

        // Highlight our own settings page instead of Freemius'.
        add_action( 'admin_head', __NAMESPACE__ . '\\Freemius::admin_head' );
        add_action( 'adminmenu', __NAMESPACE__ . '\\Freemius::adminmenu' );

        // Forcefully remove Freemius submenus, even when we are viewing that specific submenu.
        add_action( 'admin_init', __NAMESPACE__ . '\\Freemius::admin_init' );
    }

    public static function templateFilter( $html, $slug, $tab ) {
        // Check if we're on the Freemius page instead of our own settings page.
        global $plugin_page;
        if ( $plugin_page !== self::$menuSlug . '-' . $slug ) {
            return $html;
        }

        // Prevent recursive loop.
        static $preventLoop = false;
        if ( $preventLoop ) {
            return $html;
        }
        $preventLoop = true;

        // Set the active tab.
        $_GET['tab'] = $tab;

        // Return our own admin page.
        ob_start();
        $adminClass = self::$adminClass;
        $adminClass::do_page();
        $html = ob_get_clean();

        return $html;
    }

    public static function admin_head() {
        global $plugin_page;

        // Temporarily change $plugin_page to highlight our own settings page instead of Freemius'.
        if ( in_array( $plugin_page, array( self::$menuSlug . '-account', self::$menuSlug . '-contact', self::$menuSlug . '-addons' ) ) ) {
            self::$originalPluginPage = $plugin_page;
            $plugin_page              = self::$menuSlug;
        }
    }

    public static function adminmenu() {
        global $plugin_page;

        // Set $plugin_page to its original value.
        if ( self::$originalPluginPage ) {
            $plugin_page              = self::$originalPluginPage;
            self::$originalPluginPage = null;
        }
    }

    public static function admin_init() {
        global $submenu;

        if ( ! is_array( $submenu ) || ! is_array( $submenu['options-general.php'] ) ) {
            return;
        }

        foreach ( $submenu['options-general.php'] as $key => $item ) {
            if ( in_array( $item[2], array( self::$menuSlug . '-account', self::$menuSlug . '-contact', self::$menuSlug . '-addons' ) ) ) {
                unset ( $submenu['options-general.php'][ $key ] );
            }
        }
    }
}
