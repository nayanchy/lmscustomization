<?php
// Registering custom template
function lmscustomization_add_page_template($templates)
{
    $templates[plugin_dir_path(__FILE__) . 'templates/single-custom-course-grid-template.php'] = 'Custom Course Grid Template';
    return $templates;
}
// add_filter('theme_page_templates', 'lmscustomization_add_page_template');


function lmscustomization_add_meta_box()
{
    add_meta_box(
        'lmscustomization_page_template_meta_box',
        'LMS Custom Template',
        'lmscustomization_render_meta_box',
        'page',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'lmscustomization_add_meta_box');

function lmscustomization_render_meta_box($post)
{
    $selected_template = get_post_meta($post->ID, '_lmscustomization_selected_template', true);
    $use_custom_template = get_post_meta($post->ID, '_lmscustomization_use_custom_template', true);
    $template_options = array(
        'default' => 'Default Template',
        'single-custom-course-grid-template.php' => 'Custom Course Grid Template',
    );
?>
    <label>
        <select name="lmscustomization_selected_template">
            <?php
            foreach ($template_options as $value => $label) {
                printf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($value),
                    selected($value, $selected_template, false),
                    esc_html($label)
                );
            }
            ?>
        </select>
        <br>
        <input type="checkbox" name="lmscustomization_use_custom_template" value="1" <?php checked($use_custom_template, 1); ?> />
        Use LMS Custom Template
    </label>
<?php
}


function lmscustomization_save_meta_box($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['lmscustomization_selected_template'])) {
        update_post_meta($post_id, '_lmscustomization_selected_template', sanitize_text_field($_POST['lmscustomization_selected_template']));
    }

    if (isset($_POST['lmscustomization_use_custom_template'])) {
        update_post_meta($post_id, '_lmscustomization_use_custom_template', 1);
    } else {
        delete_post_meta($post_id, '_lmscustomization_use_custom_template');
    }
}
add_action('save_post', 'lmscustomization_save_meta_box');


function lmscustomization_use_custom_template($template)
{
    if (is_page()) {
        $use_custom_template = get_post_meta(get_the_ID(), '_lmscustomization_use_custom_template', true);
        if ($use_custom_template) {
            $selected_template = get_post_meta(get_the_ID(), '_lmscustomization_selected_template', true);
            if (!empty($selected_template)) {
                $template = plugin_dir_path(__FILE__) . 'templates/' . $selected_template;
            }
        }
    }
    return $template;
}
add_filter('page_template', 'lmscustomization_use_custom_template');
