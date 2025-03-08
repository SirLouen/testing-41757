<?php
/**
 * Plugin Name: Testing 41757 Plugin
 * Description: Tests the added_new_user_to_blog custom hook
 * Version: 1.0
 * Author: SirLouen <sir.louen@gmail.com>
 */

if (!defined('ABSPATH')) {
    exit;
}

function test_added_new_user_to_blog($user_id, $password, $meta, $blog_id) {
    // Test 1: Simple file logging
    $log_file = WP_CONTENT_DIR . '/user-blog-additions.log';
    
    $log_message = sprintf(
        "[%s] User ID: %d was added to Blog ID: %d with role: %s\n",
        date('Y-m-d H:i:s'),
        $user_id,
        $password,
        $blog_id,
        $meta['new_role']
    );
    
    file_put_contents($log_file, $log_message, FILE_APPEND);
    
    // Test 2: We can update for example, user meta based on $blog_id
    update_user_meta($user_id, 'date_added_to_blog_' . $blog_id, current_time('mysql'));
}

add_action('added_new_user_to_blog', 'test_added_new_user_to_blog', 10, 4);

// Test 3: Adding a special capability caled "access_premium_content"
// based on the fact that the user has registered in this specific network site
function assign_special_capability($user_id, $password, $meta, $blog_id) {
    // For the test we are sticking with the second network site created with ID = 2
    if ($blog_id == 2) { 
        switch_to_blog($blog_id);
        $user = new WP_User($user_id);
        $user->add_cap('access_premium_content');
        restore_current_blog();
    }
}

add_action('added_new_user_to_blog', 'assign_special_capability', 20, 4);
