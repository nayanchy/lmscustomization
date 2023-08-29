<?php

/**
 * Declaring the template footer function
 */

function lmscustomization_template_footer()
{
    ob_start(); // Start output buffering
?>

    <?php woo_footer_before(); ?>

    <div id="footer_wrapper">
        <footer id="footer_lmscustomization" class="footer_lmscustomization">
            <img src="https://www.unk.com/u/wp-content/themes/function-child/images/uncommin-knowledge-logo.jpg" alt="Uncommon Knowledge">
            <p class="copyright">Uncommon U &copy; <?php echo date('Y'); ?> | <?php _e('All Rights Reserved.'); ?></p>
        </footer><!-- /#footer  -->
    </div><!-- /#footer-wrapper  -->

    <?php wp_footer(); ?>
    <?php woo_foot(); ?>

<?php
    return ob_get_clean(); // Return the buffered content
}
?>