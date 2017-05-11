<?php
/*
 * Plugin Name: CW Open Graph Tags
 * Description: Easily manage meta tags by posting manually titles and description for each post, or the plugin automatically does this for you.
 * Version: 1.0.4
 * Author: Ivan Balkanov
 * Author URI: http://codewan.com
 * License: GPL2
 */

if (!class_exists('CW_Open_Graph')) {

    class CW_Open_Graph {

        public function __construct() {

            add_action('add_meta_boxes', array($this, 'meta_boxes'));
            add_action('save_post', array($this, 'open_graph_save'));
            add_action('wp_head', 'cw_open_graph');
        }

        // saving open graph meta to database
        public function open_graph_save($post_id) {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                return $post_id;
            }

            if (isset($_POST['open_graph_title'])) {
                update_post_meta($post_id, 'open_graph_title', $_POST['open_graph_title']);
                update_post_meta($post_id, 'open_graph_description', $_POST['open_graph_description']);
            }
            return $post_id;
        }

        //hooking meta boxes to every post and page 
        public function meta_boxes() {
            add_meta_box('test_meta', 'Facebook Open Graph', array($this, 'meta_box_cb'), 'post');
            add_meta_box('test_meta', 'Facebook Open Graph', array($this, 'meta_box_cb'), 'page');
        }
        
        //creating meta boxes
        public function meta_box_cb($post) {
            $og_title = '';
            $og_description = '';
            if (!empty($post)) {
                $og_title = get_post_meta($post->ID, 'open_graph_title', true);
                $og_description = get_post_meta($post->ID, 'open_graph_description', true);
            }
            ?>
            <p>Open Graph Title <i>(between 40 and 70 characters)</i></p>
            <input type="text" name="open_graph_title" maxlength="100" style="width: 100%" value="<?php echo $og_title; ?>"/>
            <p>Open Graph Description <i>(Придържайте се около 45 думи или 300 символа, но се уверете че първите 110 ще са достатъчно ясни ако се прекъснат след това.)</i></p>
            <textarea name="open_graph_description" maxlength="300" style="width: 100%; height: 75px;"><?php echo $og_description; ?></textarea>
            <?php
        }

    }
    //if no have title and description, generate automatically
    function cw_auto_generate_content() {
        ?>

        <meta property="og:title" content="<?php the_title() ?>" />
        <meta property="og:description" content="<?php
        $post = get_post($post);
        $content = substr($post->post_content, 0, 300);
        $content1 = strip_tags($content);
        echo htmlspecialchars($content1, ENT_COMPAT, 'ISO-8859-1', true);
        ?> "/>

        <?php
    }
    //If you have a title and description written, plugin use them
    function cw_manual_generate_content() {
        ?>
        <meta property="og:title" content="<?php
        $post = get_post($post);
        $title = $post->open_graph_title;
        $content = $post->open_graph_description;
        echo $title;
        ?>" />
        <meta property="og:description" content="<?php
        echo $content;
        ?> "/>
              <?php
          }
          //this function check if you have written title and description 
          //and adding automatically site name, url, type and image
          function cw_open_graph() {

              if (is_single() || is_page() || is_home()) {
                  ?>
            <meta property="og:site_name" content="<?php bloginfo('name') ?>" />
            <meta property="og:url" content="<?php
            if (is_front_page()) {
                echo get_home_url();
            } else {
                the_permalink();
            }
            ?>" />
            <meta property="og:type" content="<?php
            if (is_page() || is_home()) {
                echo 'website';
            } else {
                echo 'article';
            }
            ?>" /> 
                  <?php
                  if (has_post_thumbnail()) :
                      $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
                      ?>
                <meta property="og:image" content="<?php echo $image[0]; ?>"/>  
            <?php endif; ?>
            <?php
            $post = get_post($post);
            if (empty($post->open_graph_title)) {
                cw_auto_generate_content();
            } else {
                cw_manual_generate_content();
            }
        }
    }

    new CW_Open_Graph;
}
