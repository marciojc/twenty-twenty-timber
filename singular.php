<?php
/**
 * The Template for displaying all singular posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context         = Timber::context();
$timber_post     = Timber::get_post();
$context['post'] = $timber_post;

if ( post_password_required( $timber_post->ID ) ) {
	Timber::render( 'singular-password.twig', $context );
} else {
	Timber::render( array( 'singular-' . $timber_post->ID . '.twig', 'singular-' . $timber_post->post_type . '.twig', 'singular-' . $timber_post->slug . '.twig', 'singular.twig' ), $context );
}
