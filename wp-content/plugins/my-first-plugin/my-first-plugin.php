<?php
/**
 * Plugin Name: My First Plugin
 * Plugin URI: http://localhost:8080/wordpress/my-first-plugin
 * Description: The very first plugin that I have ever created.
 * Version: 1.0
 * Author: Fokam Giles
 * Author URI: http://www.mywebsite.com
 */
/*
My First Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

My First Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with My First Plugin. If not, see http: //localhost:8080/wordpress/my-first-plugin.
 */

 add_action('the_content', 'my_thank_you_text');

function my_thank_you_text($content)
{
    return $content .= '<p>Thank you for reading!</p>';
}

//add_action('init','pluginprefix_setup_post_type');


function pluginprefix_setup_post_type(){
    register_post_type('book',['public' => true]);
}


register_activation_hook(__FILE__,'init_plugin');

function init_plugin(){
    //pluginprefix_setup_post_type();
    //die('activating plugin');
   // flush_rewrite_rules(true);
}

register_deactivation_hook(__FILE__,'purge_plugin');

function purge_plugin(){
   // unregister_post_type('book');
   // flush_rewrite_rules(true);
    //die('cleaning plugin');
}
function wporg_options_page_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
// output security fields for the registered setting "wporg_options"
    settings_fields('wporg_options');
    // output setting sections and their fields
    // (sections are registered for "wporg", each field is registered to a specific section)
    do_settings_sections('wporg');
    // output save settings button
    submit_button(__('Save Settings', 'textdomain'));
    ?>
        </form>
    </div>
    <?php
}
function wporg_options_page()
{
    add_plugins_page(
        'tools.php',
        'WPOrg Options',
        'WPOrg Options',
        'manage_options',
        'wporg',
        'wporg_options_page_html'
    );
}
//add_action('admin_menu', 'wporg_options_page');

function tutsplus_register_post_type()
{

    // movies
    $labels = array(
        'name' => __('Movies', 'tutsplus'),
        'singular_name' => __('Movie', 'tutsplus'),
        'add_new' => __('New Movie', 'tutsplus'),
        'add_new_item' => __('Add New Movie', 'tutsplus'),
        'edit_item' => __('Edit Movie', 'tutsplus'),
        'new_item' => __('New Movie', 'tutsplus'),
        'view_item' => __('View Movie', 'tutsplus'),
        'search_items' => __('Search Movies', 'tutsplus'),
        'not_found' => __('No Movies Found', 'tutsplus'),
        'not_found_in_trash' => __('No Movies found in Trash', 'tutsplus'),
    );
    $args = array(
        'labels' => $labels,
        'has_archive' => true,
        'public' => true,
        'hierarchical' => false,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'custom-fields',
            'thumbnail',
            'page-attributes',
        ),
        'rewrite' => array('slug' => 'movies'),
        'show_in_rest' => true,

    );
    register_post_type('tutsplus_movie', $args);

}
//add_action('init', 'tutsplus_register_taxonomy');

//plugins_url()
//register_uninstall_hook(__FILE__,'plugin_uninstall_function')

