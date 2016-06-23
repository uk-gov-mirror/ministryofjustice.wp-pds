<?php

/**
 * Clean up the_excerpt()
 */
function roots_excerpt_more($more) {
    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'roots') . '</a>';
}

add_filter('excerpt_more', 'roots_excerpt_more');

/**
 * Manage output of wp_title()
 */
function roots_wp_title($title) {
    if (is_feed()) {
        return $title;
    }

    $title .= get_bloginfo('name');

    return $title;
}

add_filter('wp_title', 'roots_wp_title', 10);

// Is page or child of page
function is_tree($pid) {
    global $post;

    if ($post) {
        $ancestors = get_post_ancestors($post->$pid);
        $root = count($ancestors) - 1;

        if (is_page() && (is_page($pid) || $post->post_parent == $pid || in_array($pid, $ancestors))) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/* Setup option tree */
add_filter('ot_theme_mode', '__return_true');
add_filter('ot_show_pages', '__return_false');
add_filter('ot_show_new_layout', '__return_false');
add_filter('ot_use_theme_options', '__return_false');

add_filter('ot_header_version_text', '__return_null');

//load_template( trailingslashit( get_template_directory() ) . 'inc/theme-options.php' );
require_once (trailingslashit(get_template_directory()) . 'option-tree/ot-loader.php');

/**
 * Meta Boxes
 */
load_template(trailingslashit(get_template_directory()) . 'lib/meta-boxes.php');

// Set image size
add_image_size('advocate_slide', 242, 190, true);
add_image_size('advocate_slide_thumb', 160, 190, true);

// Prevent default fields in optiontree listitem
add_filter('ot_list_item_settings', 'filter_ot_list_item_settings', 10, 2);

function filter_ot_list_item_settings($settings, $id) {

    // Only remove settings from a specific option
    if (in_array($id, array('solicitor-advocates', 'duty-solicitors', 'police-reps')))
        return array();

    return $settings;
}

// Overwrites WPEngine's option to turn RAND ordering off
update_option('wpe-rand-enabled', 1);

// Replaces menu_order with two meta_value for sorting by multiple custom fields
function orderbyreplace($orderby) {
    return str_replace('wp_posts.menu_order', 'wp_postmeta.meta_value, mt1.meta_value', $orderby);
}

/**
 * Get the 'site section' for the current page.
 * Returns either 'advocates' or 'solicitors',
 * or false if on an unknown page.
 *
 * @return string|false
 */
function get_site_section() {
  $breadcrumbs = new Roots_Breadcrumbs();
  $trail = $breadcrumbs->get_trail();

  if (count($trail) < 2) {
    return false;
  }

  $page = $trail[1];
  if ($page->post_name == 'advocates') {
    return 'advocates';
  }
  else if ($page->post_name == 'solicitors') {
    return 'solicitors';
  }
  else {
    return false;
  }
}
