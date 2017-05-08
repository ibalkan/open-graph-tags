<?php
/**
 * Plugin Name: My Open Graph Tags
 * Plugin URI: http://codewan.com
 * Description: This plugin adds some Facebook Open Graph tags to our single posts or page.
 * Version: 1.0.3
 * Author: Ivan Balkanov
 * Author URI: http://codewan.com
 * License: GPL2
 */
add_action('wp_head', 'cw_facebook_tags');

function cw_facebook_tags() {
    
    if (is_single() || is_page() || is_home()) {
        ?>
        <meta property="og:title" content="<?php the_title() ?>" />
        <meta property="og:site_name" content="<?php bloginfo('name') ?>" />
        <meta property="og:url" content="<?php the_permalink() ?>" />

        <meta property="og:description" content="<?php
        $post = get_post($post);
        $content = substr($post->post_content, 0, 300);
        echo htmlspecialchars($content, ENT_COMPAT, 'ISO-8859-1', true);

        ?>" />

        <meta property="og:type" content="<?php
        if (is_front_page()) {
            echo 'front page';
        } elseif (is_page()) {
            echo 'page';
        } elseif (is_single()) {
            echo 'artical';
        }
        ?>" />

              <?php
              if (has_post_thumbnail()) :
                  $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
                  ?>
            <meta property="og:image" content="<?php echo $image[0]; ?>"/>  
        <?php endif; ?>

        <?php
    }
}
