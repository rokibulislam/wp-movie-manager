<?php 

/**
 * Plugin Name: Movie Manager
 * Description: Description
 * Plugin URI: http://#
 * Author: Rokibul islam
 * Author URI: http://#
 * Version: 1.0.0
 * License: GPL2
 * Text Domain: text-domain
 * Domain Path: domain/path
 */

/*
    Copyright (C) Year  Author  Email

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if ( !defined( 'ABSPATH' ) ) exit;

final class WPMovie {

    public $version    = '1.0.0';
    private $container = [];

    public function __construct() {
        $this->define_constants();        

        register_activation_hook( __FILE__,  [ $this, 'activation' ] );

        register_deactivation_hook( __FILE__, [ $this, 'deactivation' ] );

        add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );

    }


    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Self();
        }

        return $instance;
    }


    public function define_constants() {
        define( 'WPEM_VERSION', 1 );
        define( 'WPEM_SEPARATOR', ' | ');
        define( 'WPEM_FILE', __FILE__ );
        define( 'WPEM_ROOT', __DIR__ );
        define( 'WPEM_PATH', dirname( WPEM_FILE ) );
        define( 'WPEM_INCLUDES', WPEM_PATH . '/includes' );
        define( 'WPEM_URL', plugins_url( '', WPEM_FILE ) );
        define( 'WPEM_ASSETS', WPEM_URL . '/assets' );
    }

        /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin() {
        $this->init_hooks();

        $this->includs();
        $this->init_classes();
    }

    public function includs() {
        require_once WPEM_INCLUDES . '/class-assets.php';
        require_once WPEM_INCLUDES . '/class-frontend.php';
        require_once WPEM_INCLUDES . '/class-cron.php';
    }

    public function init_classes() {
        new WPMovie\Frontend();
        new WPMovie\Cron();
    }

    public function init_hooks() {
        add_action( 'init', [ $this, 'localization_setup' ] );
        add_action( 'init', [ $this, 'event_register_post_type' ] );
        add_filter('use_block_editor_for_post_type', [ $this, 'disable_gutenberg' ], 10, 2);
    }

    public function event_register_post_type() {
		register_post_type( 'movie',
			array(
				'label'            => __( 'Movies', 'wp-movie-manager' ),
				'supports'          => array( 'title', 'editor', 'thumbnail', 'comments' ),
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => [ 'slug' => 'movie', 'with_front' => false ],
				'menu_position'     => 51,
				'categories'        => array(),
				'menu_icon'         => 'dashicons-admin-home',
				'show_in_rest'		=> true,
			)
		);
	}

    public function disable_gutenberg($is_enabled, $post_type) {
        if ( $post_type === 'movie' ) return false; // change book to your post type
    
        return $is_enabled;    
    }

    public function localization_setup() {
        load_plugin_textdomain( 'wp-movie-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }


    public function activation() {
       add_role( 'viewer', 'Viewer', array( 'read' => true, 'level_0' => true ) );
       $this->setup_pages();
    }

    public function deactivation() {
        remove_role('viewer');
    }

    
    public function setup_pages() {
        // return if pages were created before
        $page_created = get_option( 'movie_pages_created', false );

        if ( $page_created ) {
            return;
        }

        $pages = [
            [
                'post_title' => __( 'Movies', 'wp-movie-manager' ),
                'slug'       => 'movies',
                'page_id'    => 'movies',
                'content'    =>  '<!-- wp:shortcode -->[movies] <!-- /wp:shortcode -->',
            ],

        ];

        $dokan_page_settings = [];

        if ( $pages ) {
            foreach ( $pages as $page ) {

                $page_id = wp_insert_post(
                    [
                        'post_title'     => $page['post_title'],
                        'post_name'      => $page['slug'],
                        'post_content'   => $page['content'],
                        'post_status'    => 'publish',
                        'post_type'      => 'page',
                        'comment_status' => 'closed',
                    ]
                );
            }
        }

        update_option( 'movie_pages_created', true );
    }
}

function wpmovie() {
    return WPMovie::init();
}

wpmovie();