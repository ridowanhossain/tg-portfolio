<?php
// Add a meta box for Portfolio Layout
function tg_portfolio_add_layout_meta_box() {
    add_meta_box(
        'tg_portfolio_layout_meta_box', // Meta box ID
        'Portfolio Layout',             // Meta box title
        'tg_portfolio_render_layout_meta_box', // Callback function
        'tg_portfolio',                 // Post type
        'side',                         // Context
        'default'                       // Priority
    );
}
add_action('add_meta_boxes', 'tg_portfolio_add_layout_meta_box');

// Render the Portfolio Layout meta box
function tg_portfolio_render_layout_meta_box($post) {
    // Get saved layout or default to 'default'
    $saved_layout = get_post_meta($post->ID, 'tg_portfolio_layout', true) ?: 'default';
    wp_nonce_field('tg_portfolio_save_layout', 'tg_portfolio_layout_nonce');
?>
    <label for="tg_portfolio_layout">Select Layout:</label>
    <select name="tg_portfolio_layout" id="tg_portfolio_layout">
        <option value="default" <?php selected($saved_layout, 'default'); ?>>Default</option>
        <option value="left_media" <?php selected($saved_layout, 'left_media'); ?>>Left Media</option>
        <option value="center_media" <?php selected($saved_layout, 'center_media'); ?>>Center Media</option>
        <option value="right_media" <?php selected($saved_layout, 'right_media'); ?>>Right Media</option>
    </select>
    <p class="description">Choose the layout for this portfolio item. If "Default" is selected, the global layout from settings will be used.</p>
<?php
}

// Save the Portfolio Layout meta box data
function tg_portfolio_save_layout_meta_box($post_id) {
    if (!isset($_POST['tg_portfolio_layout_nonce']) || !wp_verify_nonce($_POST['tg_portfolio_layout_nonce'], 'tg_portfolio_save_layout')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the selected layout
    if (isset($_POST['tg_portfolio_layout'])) {
        $layout = sanitize_text_field($_POST['tg_portfolio_layout']);
        update_post_meta($post_id, 'tg_portfolio_layout', $layout);
    }
}
add_action('save_post', 'tg_portfolio_save_layout_meta_box');