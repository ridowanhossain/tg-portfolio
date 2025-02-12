<?php
// Add meta boxes
function tg_portfolio_add_meta_boxes() {
    add_meta_box(
        'tg_portfolio_information_list',
        'Information List',
        'tg_portfolio_render_information_meta_box',
        'tg_portfolio',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'tg_portfolio_add_meta_boxes');

// Render Information List meta box
function tg_portfolio_render_information_meta_box($post) {
    // Get saved information list or initialize with a default row
    $information_list = get_post_meta($post->ID, 'tg_portfolio_information_list', true) ?: array(array('label' => '', 'value' => ''));
    wp_nonce_field('tg_portfolio_save_meta_box', 'tg_portfolio_nonce');
?>
    <div id="tg-portfolio-information-list-wrapper">
        <button type="button" id="add-information-row" class="button">Add Row</button>
        <table class="tg-portfolio-information-list-table">
            <thead>
                <tr>
                    <th class="drag-handle-column"></th> <!-- Drag Handler -->
                    <th class="label-column">Label</th>
                    <th class="value-column">Value</th>
                    <th class="action-column">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($information_list as $index => $item) : ?>
                    <tr class="information-item">
                        <td class="drag-handle-column">
                            <span class="drag-handle dashicons dashicons-move"></span>
                        </td>
                        <td class="label-column">
                            <input type="text" name="tg_portfolio_information_list[<?php echo $index; ?>][label]" value="<?php echo esc_attr($item['label']); ?>" class="label-input" />
                        </td>
                        <td class="value-column">
                            <input type="text" name="tg_portfolio_information_list[<?php echo $index; ?>][value]" value="<?php echo esc_attr($item['value']); ?>" class="value-input" />
                        </td>
                        <td class="action-column">
                            <button type="button" class="remove-row button">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.querySelector('.tg-portfolio-information-list-table tbody');

            // Initialize SortableJS for drag-and-drop functionality
            const sortable = new Sortable(table, {
                animation: 150,
                handle: '.drag-handle', // Use a handle for dragging
                onEnd: function(evt) {
                    // Update the input names after reordering
                    Array.from(table.rows).forEach((row, index) => {
                        row.querySelector('.label-input').name = `tg_portfolio_information_list[${index}][label]`;
                        row.querySelector('.value-input').name = `tg_portfolio_information_list[${index}][value]`;
                    });
                },
            });

            // Add new row
            document.querySelector('#add-information-row').addEventListener('click', function() {
                const rowCount = table.rows.length;
                const newRow = `
                    <tr class="information-item">
                        <td class="drag-handle-column">
                            <span class="drag-handle dashicons dashicons-move"></span>
                        </td>
                        <td class="label-column">
                            <input type="text" name="tg_portfolio_information_list[${rowCount}][label]" class="label-input" />
                        </td>
                        <td class="value-column">
                            <input type="text" name="tg_portfolio_information_list[${rowCount}][value]" class="value-input" />
                        </td>
                        <td class="action-column">
                            <button type="button" class="remove-row button">Remove</button>
                        </td>
                    </tr>`;
                table.insertAdjacentHTML('beforeend', newRow);
            });

            // Remove row
            table.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row')) {
                    const row = e.target.closest('tr');
                    if (table.rows.length > 1) { // Ensure at least one row remains
                        row.remove();
                    } else {
                        // Clear the inputs of the default row
                        const inputs = row.querySelectorAll('.label-input, .value-input');
                        inputs.forEach(input => input.value = '');
                    }
                }
            });
        });
    </script>
    <style>
        /* Table styling */
        .tg-portfolio-information-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .tg-portfolio-information-list-table th,
        .tg-portfolio-information-list-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .tg-portfolio-information-list-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .tg-portfolio-information-list-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .tg-portfolio-information-list-table tr:hover {
            background-color: #f1f1f1;
        }

        /* Column widths */
        .drag-handle-column {
            width: 10%;
        }

        .label-column {
            width: 25%;
        }

        .value-column {
            width: 50%;
        }

        .action-column {
            width: 15%;
        }

        /* Drag handle styling */
        .drag-handle {
            cursor: move;
            color: #555;
        }

        /* Input styling */
        .label-input,
        .value-input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .label-input:focus,
        .value-input:focus {
            border-color: #2271b1;
            outline: none;
        }

        /* Button styling */
        .button.remove-row {
            background-color: #dc3232;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .button.remove-row:hover {
            background-color: #a00;
        }
    </style>
<?php
}

// Save meta box data
function tg_portfolio_save_meta_box($post_id) {
    if (!isset($_POST['tg_portfolio_nonce']) || !wp_verify_nonce($_POST['tg_portfolio_nonce'], 'tg_portfolio_save_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save information list
    if (isset($_POST['tg_portfolio_information_list'])) {
        $information_list = array_map(function($item) {
            return [
                'label' => sanitize_text_field($item['label']),
                'value' => sanitize_text_field($item['value']),
            ];
        }, $_POST['tg_portfolio_information_list']);
        update_post_meta($post_id, 'tg_portfolio_information_list', $information_list);
    } else {
        delete_post_meta($post_id, 'tg_portfolio_information_list');
    }
}
add_action('save_post', 'tg_portfolio_save_meta_box');