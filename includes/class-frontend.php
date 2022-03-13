<?php
namespace WPMovie;
use WPMovie\Assets;

class Frontend {

	public function __construct() {
		add_shortcode( 'movies', [ $this, 'render_movies' ] );
		add_filter( 'the_title', [ $this, 'modify_single_post' ], 10, 2 );
        // add_filter( 'single_template', [ $this, 'movie_template' ], 11 );
        // add_filter( 'template_include', [ $this, 'templates' ], 11 );
        // add_filter( 'pre_render_block', [ $this, 'pre_render' ], 10, 2 );

	}

	public function render_movies() {
		ob_start();
		Assets::init()->enqueue_frontend_scripts();
		include WPEM_PATH . '/templates/movies.php';
		$contents = ob_get_contents();
		ob_end_clean();
		echo $contents; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}


	public function modify_single_post($title, $id) {
		$post = get_post( $id );
        
        if ( 'movie' === $post->post_type && is_singular('movie')  ) {
			return $title . '- Upcoming this year';
		}

		return $title;
	}


	public function movie_template( $single_template ) {
        global $post;

        if ( 'movie' === $post->post_type  ) {
            $single_template = WPEM_PATH . '/templates/movie.php';
        }

        return $single_template;
    }

    
    public function templates( $template ) {
		$post_type = get_query_var('post_type');

		if ( $post_type == 'movie' ) {
			
			if ( is_archive() ) {
				return WPEM_PATH . '/templates/movies.php';
			}

			if ( is_single() ) {
				return WPEM_PATH . '/templates/movie.php';
			}
		}

		return $template;
	}

	public function pre_render( $content, $parsed_block ) {
		return $content;
	}
}