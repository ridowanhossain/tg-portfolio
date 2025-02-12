<?php
/**
 * Single Portfolio Template
 */

get_header(); // Include the theme's header

// Get the saved layout for this portfolio item
$portfolio_layout = get_post_meta(get_the_ID(), 'tg_portfolio_layout', true);

// If the layout is set to "default", use the global layout from settings
if ($portfolio_layout === 'default') {
    $portfolio_layout = get_option('tg_portfolio_layout', 'left_media'); // Default to "Left Media"
}

// Load the appropriate layout template based on the selected layout
switch ($portfolio_layout) {
    case 'left_media':
        include plugin_dir_path(__FILE__) . 'templates/left-media.php';
        break;
    case 'center_media':
        include plugin_dir_path(__FILE__) . 'templates/center-media.php';
        break;
    case 'right_media':
        include plugin_dir_path(__FILE__) . 'templates/right-media.php';
        break;
    default:
        include plugin_dir_path(__FILE__) . 'templates/left-media.php'; // Default to left media
}

get_footer(); // Include the theme's footer