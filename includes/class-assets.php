<?php
namespace WPMovie;

class Assets {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_frontend' ] );
	}

	public static function init() {
		static $instance = false;

		if( !$instance ) {
			$instance = new self();
		}

		return $instance;
	}

	public static function enqueue_frontend_scripts() {
		wp_enqueue_style( 'movie-frontend' );
	}

	public function register_frontend() {
		wp_register_style( 'movie-frontend', WPEM_ASSETS . '/css/frontend.css', false, false, 'all' );
	}
}

Assets::init();