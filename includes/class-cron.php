<?php 
namespace WPMovie;

class Cron {

	public function __construct() {
    	add_action( 'init', [ $this, 'prefix_add_scheduled_event' ] );
	    add_action( 'movie_cron_hook', [ $this, 'movie_cron_hook' ] );
	}

	public function prefix_add_scheduled_event() {

        // Schedule the event if it is not scheduled.
        if ( ! wp_next_scheduled( 'movie_cron_hook' ) ) {
            wp_schedule_event( time(), 'daily', 'movie_cron_hook' );
        }

    }

	public function movie_cron_hook() {

		$args = array(
		    'role'    => 'viewer',
		    'orderby' => 'user_nicename',
		    'order'   => 'ASC'
		);
		
		$users = get_users( $args );


		$emails = wp_list_pluck( $users, 'user_email' );

	   	
	   	wp_mail( $emails, 'News of the jungle', 'Something happening somewhere');
	}
}