<?php
/*
Plugin Name: TG Portfolio
Plugin URI: http://yourwebsite.com/tg-portfolio
Description: A portfolio plugin for showcasing your work with custom post types and taxonomies.
Version: 1.2
Author: Your Name
Author URI: http://yourwebsite.com
Text Domain: tg-portfolio
*/


// Hook into WordPress initialization
function tg_portfolio_init() {
    register_post_type('tg_portfolio', array(
        'labels' => array(
            'name'               => 'Portfolios',
            'singular_name'      => 'Portfolio',
            'menu_name'          => 'TG Portfolio',
            'name_admin_bar'     => 'TG Portfolio',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Portfolio',
            'new_item'           => 'New Portfolio',
            'edit_item'          => 'Edit Portfolio',
            'view_item'          => 'View Portfolio',
            'all_items'          => 'All Portfolios',
            'search_items'       => 'Search Portfolios',
            'not_found'          => 'No portfolios found.',
            'not_found_in_trash' => 'No portfolios found in Trash.',
            'featured_image'     => 'Portfolio Image',
            'set_featured_image' => 'Set portfolio image',
            'remove_featured_image' => 'Remove portfolio image',
            'use_featured_image' => 'Use as portfolio image',
            'archives'           => 'Portfolio archives',
            'insert_into_item'   => 'Insert into portfolio',
            'uploaded_to_this_item' => 'Uploaded to this portfolio',
            'filter_items_list'  => 'Filter portfolios list',
            'items_list_navigation' => 'Portfolios list navigation',
            'items_list'         => 'Portfolios list',
        ),
        'public'              => true,
        'has_archive'         => true,
        'supports'            => array('title', 'editor', 'thumbnail'),
        'show_in_rest'        => true,
        'rewrite'             => array('slug' => 'portfolio'),
        'taxonomies'          => array('category', 'post_tag'),
    ));
}
add_action('init', 'tg_portfolio_init');


// Force the portfolio archive to use the custom template
function tg_portfolio_archive_template($template) {
    if (is_post_type_archive('tg_portfolio')) {
        // Use the custom archive template from the plugin
        $template = plugin_dir_path(__FILE__) . 'archive-tg_portfolio.php';
    }
    return $template;
}
add_filter('template_include', 'tg_portfolio_archive_template');


// Register the single portfolio template
function tg_portfolio_single_template($template) {
    global $post;

    // Check if this is a single portfolio post
    if ($post->post_type === 'tg_portfolio' && is_single()) {
        // Use the custom template from the plugin
        $template = plugin_dir_path(__FILE__) . 'single-tg_portfolio.php';
    }

    return $template;
}
add_filter('single_template', 'tg_portfolio_single_template');


// Register the portfolio shortcode
function tg_portfolio_shortcode($atts) {
    ob_start(); // Start output buffering

    // Include the portfolio shortcode template
    include plugin_dir_path(__FILE__) . 'templates/portfolio-shortcode.php';

    return ob_get_clean(); // Return the buffered content
}
add_shortcode('tg_portfolio', 'tg_portfolio_shortcode');



require_once('includes/settings.php');
require_once('includes/preview-type.php');
require_once('includes/excerpt.php');
require_once('includes/information-list.php');
require_once('includes/gallery-fields.php');
require_once('includes/view-project.php');
require_once('includes/portfolio-layout-field.php');


