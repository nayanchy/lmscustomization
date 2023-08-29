<?php

/**
 * Plugin Name: LMS Customization
 * Description: Customizing current course in UNK U site
 * Version: 1.0.0
 * License: GPLV2 or Later
 * Author: Nayan Chowdhury
 */

if (!defined('ABSPATH')) {
    exit;
}

// Adding the template header file
require_once plugin_dir_path(__FILE__) . 'inc/lmscustomization_template_header.php';

// Adding the template footer file
require_once plugin_dir_path(__FILE__) . 'inc/lmscustomization_template_footer.php';

function add_mark_complete_button($content)
{
    global $post;
    $current_user = wp_get_current_user();

    if (current_user_can('manage_options') || $current_user->user_email === 'windless.storm@gmail.com') {
        $user_id = get_current_user_id();
        $completed_courses = get_user_meta($user_id, 'marked_completed_courses', true);
        if (empty($completed_courses)) {
            $completed_courses = array();
        }

        $is_complete = in_array($post->ID, $completed_courses);

        if ($is_complete) {
            $button_text = 'Completed';
        } else {
            $button_text = 'Mark as Complete';
        }

        $modified_content = '<button id="mark-complete-button" data-post-id="' . $post->ID . '">' . esc_html($button_text) . '</button>' . $content;
        return $modified_content;
    } else {
        return $content;
    }
}
add_filter('the_content', 'add_mark_complete_button');

function lmscustomization_enqueue_scripts()
{
    global $post;
    if ($post) {
        $template_name = get_post_meta(get_the_ID(), '_lmscustomization_selected_template', true);
        if ($template_name === 'single-custom-course-grid-template.php') {
            wp_enqueue_style('course-grid-style', plugin_dir_url(__FILE__) . 'css/course-grid-template.css');
        }
    }

    if (is_page() && has_shortcode(get_post()->post_content, 'course_card')) {
        wp_enqueue_style('custom-card-style', plugin_dir_url(__FILE__) . 'css/custom_card_style.css');
    }

    wp_enqueue_script('jquery');
    wp_enqueue_script('lmscustomization-script', plugin_dir_url(__FILE__) . 'js/lmscustomization.js', array('jquery'), '1.0.0', true);
    wp_localize_script('lmscustomization-script', 'lmscustomization_ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'lmscustomization_enqueue_scripts');

function handle_mark_complete_ajax()
{
    $user_id = get_current_user_id();
    $current_user = wp_get_current_user();

    if (isset($_POST['post_id'])) {
        $post_id = intval($_POST['post_id']);

        if (current_user_can('edit_post', $post_id) || $current_user->user_email === 'windless.storm@gmail.com') {
            $completed_courses = get_user_meta($user_id, 'marked_completed_courses', true);

            if (in_array($post_id, $completed_courses)) {
                // Remove the post ID from the array
                $completed_courses = array_diff($completed_courses, array($post_id));
                update_user_meta($user_id, 'marked_completed_courses', $completed_courses);
                wp_send_json_success('Incomplete');
            } else {
                // Add the post ID to the array
                $completed_courses[] = $post_id;
                update_user_meta($user_id, 'marked_completed_courses', $completed_courses);
                wp_send_json_success('Completed');
            }
        }
    }

    wp_send_json_error('Invalid request');
}
add_action('wp_ajax_mark_complete', 'handle_mark_complete_ajax');
add_action('wp_ajax_nopriv_mark_complete', 'handle_mark_complete_ajax');


/**
 * Adding the card shortcode
 */

function lmscustomization_card_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'title' => 'Session 01',
        'session-link' => '/',
        'session-image' => './course_image_test.png',
        'course-name' => 'How to Test Course',
    ), $atts);

    $title = $atts['title'];
    $sess_link = $atts['session-link'];
    $sess_img = $atts['session-image'];
    $course_name = $atts['course-name'];
    $percentage_completed = '';

    ob_start();
?>

    <div class="card-container">
        <a href="<?php echo esc_attr($sess_link); ?>" class="hero-image-container">
            <img class="hero-image" src="<?php echo esc_attr($sess_img); ?>" alt="Course Image" />
        </a>
        <main class="main-content">
            <h1><a href="<?php echo esc_attr($sess_link); ?>"><?php echo esc_html($title); ?></a></h1>
            <p class="card_excerpt">
                Our Equilibrium collection promotes balance and calm.
            </p>
            <div class="flex-row">
                <div class="card-logo">
                    <img src="<?php echo plugin_dir_url(__FILE__) . 'img/unku-logo.png' ?>" alt="avatar" class="small-avatar" />
                </div>
                <div class="percentage-completed">
                    <img src="https://i.postimg.cc/prpyV4mH/clock-selection-no-bg.png" alt="clock" class="small-image" />
                    <p class="percentage-number"><?php echo $percentage_completed ?></p>
                </div>
            </div>
        </main>
        <div class="card-attribute">
            <p>
                <a href="<?php echo esc_attr($atts['session-link']); ?>"><?php echo esc_html($course_name); ?></a>
            </p>
        </div>
    </div>

<?php
    return ob_get_clean();
}
function lmscustomization_card_wrapper_shortcode($atts, $content = null)
{
    return '<div class="card-flex">' . do_shortcode($content) . '</div>';
}
add_shortcode('course_card', 'lmscustomization_card_shortcode');
add_shortcode('course_card_wrapper', 'lmscustomization_card_wrapper_shortcode');

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

function debug(){
    global $post;
    echo "<pre>";
    print_r($post);
    echo "</pre>";
}

add_action('init', 'debug');
