<?php
// Add the View Project Button URL meta box
function tg_portfolio_add_view_project_meta_box() {
    add_meta_box(
        'tg_portfolio_view_project', // Meta box ID
        'View Project Button URL',   // Meta box title
        'tg_portfolio_render_view_project_meta_box', // Callback function
        'tg_portfolio',              // Post type
        'normal',                    // Context
        'default'                    // Priority
    );
}
add_action('add_meta_boxes', 'tg_portfolio_add_view_project_meta_box');

// Render the View Project Button URL meta box
function tg_portfolio_render_view_project_meta_box($post) {
    // Get saved View Project URL or default to empty
    $view_project_url = get_post_meta($post->ID, 'tg_portfolio_view_project_url', true);
    wp_nonce_field('tg_portfolio_save_meta_box', 'tg_portfolio_nonce');
?>
    <div id="tg-portfolio-view-project-wrapper">
        <label for="tg_portfolio_view_project_url">Button URL:</label>
        <input type="url" id="tg_portfolio_view_project_url" name="tg_portfolio_view_project_url" value="<?php echo esc_url($view_project_url); ?>" style="width: 100%;" />
        <p class="description">Enter the URL for the "View Project" button. This will link to an external project or page.</p>
    </div>
<?php
}

// Save the View Project Button URL meta box data
function tg_portfolio_save_view_project_meta_box($post_id) {
    if (!isset($_POST['tg_portfolio_nonce']) || !wp_verify_nonce($_POST['tg_portfolio_nonce'], 'tg_portfolio_save_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save View Project URL
    if (isset($_POST['tg_portfolio_view_project_url'])) {
        update_post_meta($post_id, 'tg_portfolio_view_project_url', esc_url_raw($_POST['tg_portfolio_view_project_url']));
    } else {
        delete_post_meta($post_id, 'tg_portfolio_view_project_url');
    }
}
add_action('save_post', 'tg_portfolio_save_view_project_meta_box');