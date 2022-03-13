<?php
	
	$paged   = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	$args = array(
		'post_type'      => 'movie',
		'post_status'    => 'publish',
		'posts_per_page' => !empty( get_option('posts_per_page') ) ? get_option('posts_per_page') : 10,
		'paged'	=> $paged
	);

	$movie_query = new WP_Query( $args );
?>


<?php if( $movie_query->have_posts() ) :?>

<div class="movies_container">
	<?php	
		while ( $movie_query->have_posts() ) : $movie_query->the_post();
		global $post;
	?>
		<div class="movie_box">
			<?php the_post_thumbnail(); ?>
			<h4> <a href="<?php echo esc_url( get_permalink() ); ?>" >  <?php the_title(); ?> </a> </h4>
			<p> <?php echo wp_trim_words( get_the_content(), 12 ); ?> </p>
			<a href="<?php the_permalink() ?>" > <?php echo __( 'Read More', 'wp-movie-manager' ); ?> </a>
		</div>
	<?php  
		endwhile;
    	wp_reset_postdata(); 
    ?>
</div>


	<div class="movie-pagination">
		<?php
			$big = 999999999;
			echo paginate_links( array(  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			    'format' => '?paged=%#%',
			    'current' => max( 1, get_query_var('paged') ),
			    'total' => $movie_query->max_num_pages
			) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>

<?php else :
	esc_html_e( 'There is no Movie found', 'wp-movie-manager');
endif;  wp_reset_postdata(); ?>
