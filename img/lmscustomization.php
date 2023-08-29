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
    <div class="card-flex">
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
                        <img src="<?php plugin_dir_url(__FILE__) . 'img/unku-logo.png' ?>" alt="avatar" class="small-avatar" />
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
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('course_card', 'lmscustomization_card_shortcode');
