<?php

/**
 * Template Name Posts: Custom Course Grid Template
 */
if (!defined('ABSPATH')) exit;
echo lmscustomization_template_header();
ob_start();
?>
<main class="course_grid_container">
    <section class="course_grid_content">
        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                the_content();
            endwhile;
        endif;
        ?>
    </section>
</main>
<?php
echo ob_get_clean();
echo lmscustomization_template_footer();
?>

</body>

</html>