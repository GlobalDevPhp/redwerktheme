<?php
/**
 * Template Name: Add advertising form Template
 * Template Post Type: page
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */
// leave parent styles and add new
add_action('wp_enqueue_scripts', 'enqueue_ev_scripts_add');

function enqueue_ev_scripts_add() {
    wp_enqueue_script( 'ev-custom', get_stylesheet_directory_uri(). '/js/custom.min.js', array('jquery'), '1.1', true );
    wp_enqueue_style( 'ev-form-style', get_stylesheet_directory_uri() . '/css/form.min.css', array(), '1.1');
}

get_header();
?>

<main id="site-content add-page">

	<?php

	if ( have_posts() ) {

		while ( have_posts() ) {
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );
		}
	}
        get_template_part( 'template-parts/form', get_post_type() );
	?>

</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>    