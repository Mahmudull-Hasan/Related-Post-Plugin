<?php 

/*
 * Plugin Name:       Related Post Plugin
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Related Post plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Hasan Mahmud
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       related-post
 * Domain Path:       /languages
 */


 class related_post {

    function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }

    function init() {
        add_filter( 'the_content', array($this, 'related_post_output' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'add_assets_file' ) );
    }

    // Assets File Path
    function add_assets_file() {
        wp_enqueue_style( 'main-style', PLUGINS_URL('assets/css/style.css', __FILE__) );
    }

    // Callback function for Related Post
    function related_post_output( $default ) {

        // Single Post Page Check
        if ( is_single() ) {

            $category = get_the_category( get_the_ID(), 'category' );

            $category_in = array();

            foreach ( $category as $term ) {
                $category_in[] = $term->term_id;
            }


            //related_post_query

            $related_post_query = new WP_Query( array( 
                'post_type'       => 'post',
                'posts_per_page'  => 5,
                'orderby'         => 'rand',
                'category__in'    => $category_in,
                'post__not_in'    => array(get_the_ID()),
            ) );


            // Related Post Condition
            if ( $related_post_query->have_posts() ) :

                while( $related_post_query->have_posts()) : $related_post_query->the_post();

                    $thumbnail       = esc_url( get_the_post_thumbnail_url() );
                    $current_link    = esc_url( get_permalink() );
                    $title           = esc_html( get_the_title() );

                    $default .= '<div class="post-box">';
                    $default .= '<ul >';
                    $default .= "<li> <img src='$thumbnail'> </li>";
                    $default .= "<li><a href=' $current_link '> $title </a> </li>";
                    $default .= '</ul>';
                    $default .= '</div>';

                endwhile;

            endif;
            // Related Post condition end

            return $default;
        }
        
    }
 }

 new related_post();