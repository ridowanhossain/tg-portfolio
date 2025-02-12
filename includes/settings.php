<?php
// Add a settings page for TG Portfolio
function tg_portfolio_add_settings_page() {
    add_submenu_page(
        'edit.php?post_type=tg_portfolio', // Parent slug (TG Portfolio custom post type)
        'TG Portfolio Settings',           // Page title
        'Settings',                        // Menu title
        'manage_options',                  // Capability required
        'tg-portfolio-settings',           // Menu slug
        'tg_portfolio_render_settings_page' // Callback function to render the page
    );
}
add_action('admin_menu', 'tg_portfolio_add_settings_page');

// Render the settings page
function tg_portfolio_render_settings_page() {
    // Check if the user has the required capability
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings if the form is submitted
    if (isset($_POST['tg_portfolio_settings_nonce']) && wp_verify_nonce($_POST['tg_portfolio_settings_nonce'], 'tg_portfolio_save_settings')) {
        // Sanitize and save the portfolio layout option
        $layout_value = sanitize_text_field($_POST['tg_portfolio_layout']);
        update_option('tg_portfolio_layout', $layout_value);

        // Sanitize and save the posts per page option
        $posts_per_page = intval($_POST['tg_portfolio_posts_per_page']);
        update_option('tg_portfolio_posts_per_page', $posts_per_page);

        // Display a success message
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }

    // Retrieve the saved options
    $layout_value = get_option('tg_portfolio_layout', 'left_media'); // Default to "Left Media"
    $posts_per_page = get_option('tg_portfolio_posts_per_page', 10); // Default to 10 posts per page
?>
    <div class="wrap">
        <h1>TG Portfolio Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('tg_portfolio_save_settings', 'tg_portfolio_settings_nonce'); ?>
            <table class="form-table">
                <!-- Portfolio Layout Option -->
                <tr>
                    <th scope="row">
                        <label for="tg_portfolio_layout">Portfolio Layout</label>
                    </th>
                    <td>
                        <select name="tg_portfolio_layout" id="tg_portfolio_layout">
                            <option value="left_media" <?php selected($layout_value, 'left_media'); ?>>Left Media</option>
                            <option value="center_media" <?php selected($layout_value, 'center_media'); ?>>Center Media</option>
                            <option value="right_media" <?php selected($layout_value, 'right_media'); ?>>Right Media</option>
                        </select>
                        <p class="description">Choose the default layout for displaying portfolio media (images/videos).</p>
                    </td>
                </tr>

                <!-- Posts Per Page Option -->
                <tr>
                    <th scope="row">
                        <label for="tg_portfolio_posts_per_page">Posts Per Page</label>
                    </th>
                    <td>
                        <input type="number" name="tg_portfolio_posts_per_page" id="tg_portfolio_posts_per_page" value="<?php echo esc_attr($posts_per_page); ?>" min="0" />
                        <p class="description">Set the number of posts to display per page. Use "0" to show all posts.</p>
                    </td>
                </tr>

                <!-- Shortcode Section -->
                <tr>
                    <th scope="row">
                        <label for="tg_portfolio_shortcode">Portfolio Shortcode</label>
                    </th>
                    <td>
                        <input type="text" id="tg_portfolio_shortcode" value="[tg_portfolio]" readonly class="regular-text" />
                        <button type="button" id="copy-shortcode" class="button">Copy Shortcode</button>
                        <p class="description">Use this shortcode to display the portfolio on any page or post.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>

    <script>
        // Add JavaScript to handle the shortcode copy button
        document.addEventListener('DOMContentLoaded', function() {
            const copyButton = document.getElementById('copy-shortcode');
            const shortcodeInput = document.getElementById('tg_portfolio_shortcode');

            copyButton.addEventListener('click', function() {
                shortcodeInput.select();
                document.execCommand('copy');
                alert('Shortcode copied to clipboard!');
            });
        });
    </script>
<?php
}