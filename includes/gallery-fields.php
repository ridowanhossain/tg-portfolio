<?php
// Add the Project Gallery meta box
function tg_portfolio_add_gallery_meta_box() {
    add_meta_box(
        'tg_portfolio_project_gallery',
        'Project Gallery',
        'tg_portfolio_render_gallery_meta_box',
        'tg_portfolio',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'tg_portfolio_add_gallery_meta_box');

// Render Project Gallery meta box
function tg_portfolio_render_gallery_meta_box($post) {
    // Get saved gallery data or initialize with a default item
    $gallery_items = get_post_meta($post->ID, 'tg_portfolio_gallery', true) ?: array(array('media_type' => 'image', 'url' => ''));
    wp_nonce_field('tg_portfolio_save_meta_box', 'tg_portfolio_nonce');
?>
    <div id="tg-portfolio-gallery-wrapper">
        <button type="button" id="add-gallery-item" class="button">Add Item</button>
        <table class="tg-portfolio-gallery-table">
            <thead>
                <tr>
                    <th class="drag-handle-column"></th> <!-- Drag Handler -->
                    <th class="media-type-column">Media Type</th>
                    <th class="url-column">URL</th>
                    <th class="preview-column">Preview</th>
                    <th class="action-column">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gallery_items as $index => $item) : ?>
                    <tr class="gallery-item">
                        <td class="drag-handle-column">
                            <span class="drag-handle dashicons dashicons-move"></span>
                        </td>
                        <td class="media-type-column">
                            <select name="tg_portfolio_gallery[<?php echo $index; ?>][media_type]" class="media-type-select">
                                <option value="image" <?php selected($item['media_type'], 'image'); ?>>Image</option>
                                <option value="video" <?php selected($item['media_type'], 'video'); ?>>Video</option>
                            </select>
                        </td>
                        <td class="url-column">
                            <div class="url-input-wrapper">
                                <input type="text" name="tg_portfolio_gallery[<?php echo $index; ?>][url]" value="<?php echo esc_url($item['url']); ?>" class="url-input" />
                                <button type="button" class="button tg-portfolio-media-picker" data-target="tg_portfolio_gallery[<?php echo $index; ?>][url]">Select Media</button>
                            </div>
                        </td>
                        <td class="preview-column preview-img">
                            <?php if ($item['media_type'] === 'image' && $item['url']) : ?>
                                <img src="<?php echo esc_url($item['url']); ?>" class="preview-image" alt="Image Preview" />
                            <?php elseif ($item['media_type'] === 'video' && $item['url']) : ?>
                                <img src="" class="video-thumbnail" alt="Video Thumbnail" />
                            <?php endif; ?>
                        </td>
                        <td class="action-column">
                            <button type="button" class="remove-item button">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const table = document.querySelector('.tg-portfolio-gallery-table tbody');

    // Initialize SortableJS for drag-and-drop functionality
    const sortable = new Sortable(table, {
        animation: 150,
        handle: '.drag-handle', // Use a handle for dragging
        onEnd: function(evt) {
            // Update the input names after reordering
            Array.from(table.rows).forEach((row, index) => {
                row.querySelector('.media-type-select').name = `tg_portfolio_gallery[${index}][media_type]`;
                row.querySelector('.url-input').name = `tg_portfolio_gallery[${index}][url]`;
            });
        },
    });

    // Add new gallery item
    document.querySelector('#add-gallery-item').addEventListener('click', function() {
        const rowCount = table.rows.length;
        const newRow = `
            <tr class="gallery-item">
                <td class="drag-handle-column">
                    <span class="drag-handle dashicons dashicons-move"></span>
                </td>
                <td class="media-type-column">
                    <select name="tg_portfolio_gallery[${rowCount}][media_type]" class="media-type-select">
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                    </select>
                </td>
                <td class="url-column">
                    <div class="url-input-wrapper">
                        <input type="text" name="tg_portfolio_gallery[${rowCount}][url]" class="url-input" />
                        <button type="button" class="button tg-portfolio-media-picker" data-target="tg_portfolio_gallery[${rowCount}][url]">Select Media</button>
                    </div>
                </td>
                <td class="preview-column preview-img"></td>
                <td class="action-column">
                    <button type="button" class="remove-item button">Remove</button>
                </td>
            </tr>`;
        table.insertAdjacentHTML('beforeend', newRow);
    });

    // Remove gallery item
    table.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            const row = e.target.closest('tr');
            if (table.rows.length > 1) { // Ensure at least one row remains
                row.remove();
            } else {
                // Clear the inputs of the default row
                const inputs = row.querySelectorAll('.url-input, .media-type-select');
                inputs.forEach(input => {
                    if (input.tagName === 'INPUT') input.value = '';
                    if (input.tagName === 'SELECT') input.selectedIndex = 0;
                });
                row.querySelector('.preview-img').innerHTML = ''; // Clear preview
            }
        }
    });

    // Handle URL field change event for preview
    table.addEventListener('input', function(e) {
        if (e.target.classList.contains('url-input')) {
            const row = e.target.closest('tr');
            const url = e.target.value;
            const mediaType = row.querySelector('.media-type-select').value;
            const previewCell = row.querySelector('.preview-img');
            previewCell.innerHTML = '';

            if (mediaType === 'image' && url) {
                previewCell.innerHTML = `<img src="${url}" class="preview-image" alt="Image Preview" />`;
            } else if (mediaType === 'video' && url) {
                // Fetch the thumbnail for YouTube videos only
                if (url.includes('youtube.com') || url.includes('youtu.be')) {
                    const videoId = url.split('v=')[1].split('&')[0];
                    const thumbnailUrl = `https://img.youtube.com/vi/${videoId}/0.jpg`;
                    previewCell.innerHTML = `<img src="${thumbnailUrl}" class="video-thumbnail" alt="Video Thumbnail" />`;
                }
            }
        }
    });

    // Handle media type change event
    table.addEventListener('change', function(e) {
        if (e.target.classList.contains('media-type-select')) {
            const row = e.target.closest('tr');
            const urlInput = row.querySelector('.url-input');
            const previewCell = row.querySelector('.preview-img');

            // Clear the URL input and preview
            urlInput.value = '';
            previewCell.innerHTML = '';
        }
    });

    // Reload the video thumbnails after page load to cover existing data
    const rows = table.querySelectorAll('.gallery-item');
    rows.forEach(function(row) {
        const urlField = row.querySelector('.url-input');
        const url = urlField.value;
        const mediaType = row.querySelector('.media-type-select').value;
        const previewCell = row.querySelector('.preview-img');

        if (mediaType === 'video' && url) {
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                const videoId = url.split('v=')[1].split('&')[0];
                const thumbnailUrl = `https://img.youtube.com/vi/${videoId}/0.jpg`;
                previewCell.innerHTML = `<img src="${thumbnailUrl}" class="video-thumbnail" alt="Video Thumbnail" />`;
            }
        }
    });

    // Media picker functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('tg-portfolio-media-picker')) {
            const targetInput = e.target.getAttribute('data-target');
            const mediaPicker = wp.media({
                title: 'Select Media',
                multiple: false,
            });

            mediaPicker.on('select', function() {
                const attachment = mediaPicker.state().get('selection').first().toJSON();
                const urlInput = document.querySelector(`input[name="${targetInput}"]`);
                if (urlInput) {
                    urlInput.value = attachment.url;

                    // Manually trigger the input event to update the preview
                    const event = new Event('input', { bubbles: true });
                    urlInput.dispatchEvent(event);
                }
            });

            mediaPicker.open();
        }
    });
});
    </script>
    <style>
        /* Table styling */
        .tg-portfolio-gallery-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .tg-portfolio-gallery-table th,
        .tg-portfolio-gallery-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .tg-portfolio-gallery-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .tg-portfolio-gallery-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .tg-portfolio-gallery-table tr:hover {
            background-color: #f1f1f1;
        }

        /* Column widths */
        .drag-handle-column {
            width: 10%;
        }

        .media-type-column {
            width: 15%;
        }

        .url-column {
            width: 40%;
        }

        .preview-column {
            width: 20%;
        }

        .action-column {
            width: 15%;
        }

        /* Drag handle styling */
        .drag-handle {
            cursor: move;
            color: #555;
        }

        /* Preview image styling */
        .preview-img img {
            max-width: 100%;
            max-height: 150px;
            display: block;
            margin: 0 auto;
        }

        /* URL input wrapper */
        .url-input-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .url-input {
            width: calc(100% - 100px);
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .url-input:focus {
            border-color: #2271b1;
            outline: none;
        }

        /* Media type select styling */
        .media-type-select {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .media-type-select:focus {
            border-color: #2271b1;
            outline: none;
        }

        /* Button styling */
        .button.tg-portfolio-media-picker {
            background-color: #2271b1;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .button.tg-portfolio-media-picker:hover {
            background-color: #135e96;
        }

        .button.remove-item {
            background-color: #dc3232;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .button.remove-item:hover {
            background-color: #a00;
        }
    </style>
<?php
}

// Save gallery meta box data
function tg_portfolio_save_gallery_meta_box($post_id) {
    if (!isset($_POST['tg_portfolio_nonce']) || !wp_verify_nonce($_POST['tg_portfolio_nonce'], 'tg_portfolio_save_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save gallery data
    if (isset($_POST['tg_portfolio_gallery'])) {
        $gallery_items = array_map(function ($item) {
            return [
                'media_type' => sanitize_text_field($item['media_type']),
                'url' => esc_url_raw($item['url']),
            ];
        }, $_POST['tg_portfolio_gallery']);
        update_post_meta($post_id, 'tg_portfolio_gallery', $gallery_items);
    } else {
        delete_post_meta($post_id, 'tg_portfolio_gallery');
    }
}
add_action('save_post', 'tg_portfolio_save_gallery_meta_box');