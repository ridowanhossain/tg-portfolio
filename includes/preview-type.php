<?php
// Add the Preview Type meta box
function tg_portfolio_add_preview_type_meta_box() {
    add_meta_box(
        'tg_portfolio_preview_type',
        'Preview Type',
        'tg_portfolio_render_preview_type_meta_box',
        'tg_portfolio',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'tg_portfolio_add_preview_type_meta_box');

// Render Preview Type meta box
function tg_portfolio_render_preview_type_meta_box($post) {
    // Get saved preview type or default to 'post'
    $preview_type = get_post_meta($post->ID, 'tg_portfolio_preview_type', true) ?: 'post';
    wp_nonce_field('tg_portfolio_save_meta_box', 'tg_portfolio_nonce');
?>
    <p>
        <label>
            <input type="radio" name="tg_portfolio_preview_type" value="post" <?php checked($preview_type, 'post'); ?> />
            Post
        </label>
    </p>
    <p>
        <label>
            <input type="radio" name="tg_portfolio_preview_type" value="custom_link" <?php checked($preview_type, 'custom_link'); ?> />
            Custom Link
        </label>
    </p>
    <div id="custom-url-field" style="<?php echo ($preview_type === 'custom_link') ? 'display: block;' : 'display: none;'; ?>">
        <label for="tg_portfolio_custom_url">Custom URL:</label>
        <input type="url" id="tg_portfolio_custom_url" name="tg_portfolio_custom_url" value="<?php echo esc_url(get_post_meta($post->ID, 'tg_portfolio_custom_url', true)); ?>" style="width: 100%;" />
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const previewTypeRadios = document.querySelectorAll('input[name="tg_portfolio_preview_type"]');
            const customUrlField = document.getElementById('custom-url-field');
            const customFields = document.querySelectorAll('#tg_portfolio_information_list, #tg_portfolio_project_gallery');

            function toggleFields() {
                const selectedValue = document.querySelector('input[name="tg_portfolio_preview_type"]:checked').value;
                if (selectedValue === 'custom_link') {
                    customUrlField.style.display = 'block';
                    customFields.forEach(field => field.style.display = 'none');
                } else {
                    customUrlField.style.display = 'none';
                    customFields.forEach(field => field.style.display = 'block');
                }
            }

            previewTypeRadios.forEach(radio => {
                radio.addEventListener('change', toggleFields);
            });

            // Initial toggle based on saved value
            toggleFields();
        });
    </script>
<?php
}

// Save Preview Type meta box data
function tg_portfolio_save_preview_type_meta_box($post_id) {
    if (!isset($_POST['tg_portfolio_nonce']) || !wp_verify_nonce($_POST['tg_portfolio_nonce'], 'tg_portfolio_save_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save Preview Type
    if (isset($_POST['tg_portfolio_preview_type'])) {
        update_post_meta($post_id, 'tg_portfolio_preview_type', sanitize_text_field($_POST['tg_portfolio_preview_type']));
    }

    // Save Custom URL
    if (isset($_POST['tg_portfolio_custom_url'])) {
        update_post_meta($post_id, 'tg_portfolio_custom_url', esc_url_raw($_POST['tg_portfolio_custom_url']));
    } else {
        delete_post_meta($post_id, 'tg_portfolio_custom_url');
    }
}
add_action('save_post', 'tg_portfolio_save_preview_type_meta_box');