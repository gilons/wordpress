<?php

/**
 * Plugin Name: X-Submit Voting Plugin
 * Plugin URI: <?php echo $GLOBALS['x-submit.com'] ?>
 * Description: Run your votings right from your site.
 * Version: 1.0
 * Author: X-Submit
 * Author URI: https://www.x-submit.com
 */
/*
X-Submit Voting Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

X-Submit Voting Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

 */

add_action("init", "x_submit_voting_plugin_activated");
$GLOBALS['x_submit_base_url'] = "https://localhost/";
$GLOBALS['x-submit.com'] = "https://x-submit.com";
$GLOBALS['current_votes_url'] = get_bloginfo('url') . "/wp-admin/admin.php?page=current-votings";
$GLOBALS['x_submit_config_url'] = get_bloginfo('url') . "/wp-admin/options-general.php?page=x_submit_voting_main_menu";

function x_submit_voting_plugin_activated()
{
}
function my_plugin_settings_link($links)
{
    $settings_link = '<a href="options-general.php?page=x_submit_voting_main_menu">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'my_plugin_settings_link');

add_action("admin_menu", "x_submit_plugin_create_menu");

function x_submit_plugin_create_menu()
{
    $x_submit_voting_plugin_title = "X-Submit Voting Plugin";
    add_menu_page(
        $x_submit_voting_plugin_title,
        $x_submit_voting_plugin_title,
        "manage_options",
        "x_submit_voting_main_menu",
        "x_submit_voting_setting_page"
    );
    add_submenu_page(
        "x_submit_voting_main_menu",
        "X-Submit Voting Current Votings",
        "Current Votings",
        'manage_options',
        "current-votings",
        "render_x_submit_votes_list"
    );

    add_action("admin_init", "load_x_submit_voting_setting_form");
    add_action('admin_init', 'x_submit_votings_registration');
    add_action('admin_init', 'load_x_submit_voting_scripts');
    //add_action("in", 'add_x_submit_voting_list_link');
    add_action("post_edit_form_tag", "add_x_submit_voting_list_link");
}
add_action("wp_enqueue_scripts", 'load_x_submit_voting_scripts');

function my_admin_bar_link()
{
    global $wp_admin_bar;
    global $post;
    if (!is_super_admin() || !is_admin_bar_showing()) {
        return;
    }
    //die(json_encode(get_current_screen()));
    if (function_exists('get_current_screen')) {
        $current_page_type = get_current_screen()->post_type;
        $is_block_editor = get_current_screen()->is_block_editor;
        if ($current_page_type == "post" && $is_block_editor) {

            $wp_admin_bar->add_menu(array(
                'id' => 'x_submit_voting-link',
                'parent' => false,
                'title' => __('Add X-Submit Voting'),
                'href' => $GLOBALS['current_votes_url'],
            ));
        }
    }
}
add_action("admin_bar_menu", 'my_admin_bar_link', 35);

function add_x_submit_voting_list_link()
{
    echo "<a class='components-button editor-post-switch-to-draft is-tertiary' href='" . $GLOBALS['x-submit.com'] . "'>Add Votes </a>";
}

function load_x_submit_voting_setting_form()
{
    x_submit_voting_register_setting_page();
    add_settings_section(
        "x-submit-plugin-setting",
        "Access Token Configuration",
        "x_submit_voting_plugin_setting_page",
        __FILE__
    );
    add_settings_field(
        "x-submit-access-token",
        "Access Token: ",
        "write_x_submit_plugin_access_token_feild",
        __FILE__,
        "x-submit-plugin-setting"
    );
}

function write_x_submit_plugin_access_token_feild()
{
    $x_submit_voting_options = get_option("x_submit_voting_options");
    //die(json_encode($options));
    ?>
<input type="text" name='x_submit_voting_options[access_token]'
    value="<?php echo esc_attr($x_submit_voting_options['access_token']); ?>" />
<?php
}
function x_submit_voting_setting_page()
{
    ?>
<div>
    <h1>Welcome to X-Submit Voting Plugin</h1>
    <p> Please, Follow the steps to run your plugin </p>
    <ul>
        <li>
            Open <a href="<?php echo $GLOBALS['x-submit.com'] ?>">X-Sibmit.com</a> and create and account if not yet done
        </li>
        <li>
            Get to the dashboard and go to the intergration page on the side navigation and select wordpress
        </li>
        <li>
            From there, Copy the access token generated or generate a new one and copy it.
        </li>
        <li>
            Paste The token generated on in input below and save it token able to view your current votes from
            X-Submit.com
        </li>
    </ul>
</div>
<form method="post" action="options.php">
    <?php settings_fields("x-submit-voting-settings-group");?>
    <?php do_settings_sections(__FILE__);?>
    <p class="submit">
        <input type="submit" class="button-primary" value="Save Value" />
    </p>
    <?php
if (isset(get_option("x_submit_voting_options")["access_token"])) {
        echo "Click <a href='" . $GLOBALS['current_votes_url'] . "'>Here</a> to view your current votes";
    }
    ?>
</form>
<?php
}
?>
<?php
function x_submit_voting_register_setting_page()
{
    register_setting(
        "x-submit-voting-settings-group",
        "x_submit_voting_options",
        'x_submit_voting_sanitize_function'
    );
}
function x_submit_voting_sanitize_function($input)
{
    //die(json_encode($input));
    $input['access_token'] = sanitize_text_field($input['access_token']);
    return $input;
}
add_action("admin_menu", "add_x_submit_plugin_option_to_admin_menu");

function add_x_submit_plugin_option_to_admin_menu()
{
    $x_submit_voting_plugin_title = "X-Submit Voting Plugin";
    add_options_page(
        $x_submit_voting_plugin_title,
        $x_submit_voting_plugin_title,
        'manage_options',
        "x_submit_voting_plugin",
        'x_submit_voting_plugin_setting_page'
    );
}

function x_submit_voting_plugin_setting_page()
{
}
function render_x_submit_votes_list()
{
    ?>
<H3>Current Votes</H3>
<?php
$x_submit_voting_options = get_option("x_submit_voting_options");
    $url = $GLOBALS['x_submit_base_url'] . "voting-plugin/all-votings" . "?token=" .
        $x_submit_voting_options["access_token"];
    $x_submit_current_votes = get_option("x_submit_current_votes");
    //die(json_encode($x_submit_current_votes));
    $body = json_decode($x_submit_current_votes['current_votings'], true);
    //print("feching from remote");
    $x_submit_votings = wp_remote_get($url);
    $tem_body = wp_remote_retrieve_body($x_submit_votings);
    $decoded_body = json_decode($tem_body, true);
    //die("current".is_string($tem_body));
    if (isset($tem_body) &&
        strlen($tem_body) > 0 && (isset($decoded_body) && 
        sizeof($decoded_body) > 0)) {
        //print("setting remote values");
        $body = $tem_body;
        $x_submit_current_votes["current_votings"] = $body;
//print($x_submit_current_votes['current_votings']);
        $body = json_decode($body, true);
        ?>
        <div class="x-submit-voting-connection-container">
        <p class="x-submit-voting-connection-success">These votings are in synch with <a href="<?php echo $GLOBALS['x-submit.com'] ?>">X-Sibmit.com</a></p>
<button type="button" class="button-primary" onclick="reloadPage()">Reload Votings Table</button>
</div>
<?php
} else {
        if (isset($body) && sizeof($body) > 0) {
            ?>
<div class="x-submit-voting-connection-container">
<div>
<p class="x-submit-connection-error">Unable to synchronize with <a href="<?php echo $GLOBALS['x-submit.com'] ?>">X-Submit.com</a> .
</p>
<p class="x-submit-connection-error">Please check your internet connection or click <a href="<?php
echo $GLOBALS['x_submit_config_url'] ?>">here</a> to update your access token</p>
</div>
<button type="button" class="button-primary reload-x-submit-votings-button" onclick="reloadPage()">Reload Votings Table</button>
</div>
<?php
}
    }
    //die( "trying_".strlen($x_submit_voting_options['access_token']));
    if ((!isset($body) || sizeof($body) <= 0) &&
        (isset($x_submit_voting_options['access_token']) &&
            strlen($x_submit_voting_options['access_token']) > 0)) {
        ?>
<p><strong>Hello, It apears that your X-Submit Access token is no more valide<strong></p>
<p><strong>or you don't have any voting yet on <a href="<?php echo $GLOBALS['x-submit.com'] ?>">X-Submit.com</a></strong></p>
<p>Either Click <a href="<?php echo $GLOBALS['x_submit_config_url']; ?>">Here</a> to setup your token </p>
<p>Or Follow the steps below to Create a voting on <a href="<?php echo $GLOBALS['x-submit.com'] ?>">X-Submit.com</a></p>
<ul>
    <li>
        Open <a href="<?php echo $GLOBALS['x-submit.com'] ?>">X-Submit.com</a>
    </li>
    <li>
        Go to dashboard and create a new campaign
    </li>
    <li>
        From there, go to manage forms tab on the side navigation
        and create a new form.
    </li>
    <li>
        Next, Build your form according to the data type of your nominees
    </li>
    <li>
        If your nominees are found on an excel sheet of a CSV sheet, you can import
        them directly into the platform by
        <ul>
            <li>
                Clicking on the submissions tab on the top navigation.
            </li>
            <li>
                From there, click on the Blue Upload Submissions button and proceed from there.
            </li>
        </ul>
    </li>
    <li>
        Or, click on the share link tab to get the link of your form.
        <ul>
            <li>
                Share it on social media or to your nominees .
            </li>
            <li>
                And get people to come and submit to your form .
            </li>
        </ul>
    </li>
    <li>
        Next, Click on the Setup Add-ons tab on the top navigation bar and choose voting.
    </li>
    <li>
        From There, Setup your work and refresh this page to have that voting loaded here.
    </li>
</ul>
<?php
} else if ((!isset($body) ||
        sizeof($body) <= 0) &&
        (!isset($x_submit_voting_options['access_token'])
            || strlen($x_submit_voting_options['access_token']) <= 0)) {
        ?>
<p><strong>You don't yet have access token configured</strong></p>
<p>Either Click <a href="<?php echo $GLOBALS['x_submit_config_url']; ?>">Here</a> to setup your token </p>
<?php
} else if (isset($body) && sizeof($body) > 0) {

        ?>
<form method="post" action="options.php">
    <?php settings_fields("x-submit-voting-current-votes-group");?>
    <table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th id="item-number" class="manage-column column-columnnamenum" scope="col">#No</th>
                <th id="campaign_name" class="manage-column column-columnname" scope="col">Campaign Name</th>
                <th id="form_name" class="manage-column column-columnname" scope="col">Form Name</th>
                <th id="voting_name" class="manage-column column-columnname" scope="col">Voing Name</th>
                <th id="actions" class="manage-column column-columnname num" scope="col">Voting ShortCode</th>
                <th id="actions" class="manage-column column-columnname num" scope="col">Voting Results ShortCode</th>
            </tr>
        </thead>
        <?php
foreach ($body as $key => $value) {
            ?>
        <tr>
            <td id="<?php echo $key . '_item_number' ?>"><?php
$key = $key + 1;
            echo $key
            ?></td>
            <td id="<?php echo $key . '_campaign_title' ?>">
                <?php echo $value["campaign_title"] ?>
            </td>
            <td id="<?php echo $key . '_round_title' ?>">
                <?php echo $value['round_title'] ?>
            </td>
            <td id="<?php echo $key . '_voting_title' ?>">
                <?php echo $value["voting_name"] ?>
            </td>
            <td id="<?php echo $key . 'voting' ?>">
                <div class="x-submit-voting-short-code-container">
                    <input class="x-submit-voting-short-code" id="<?php echo $key . '_voting-input' ?>"
                        value="<?php write_voting_short_code($value['voting_id'], $value['campaign_id'])?>">
                    <button type="button" class="x-submit-voting-copy-button" onclick="copyCode('<?php write_voting_short_code(
                $value['voting_id'],
                $value['campaign_id']
            )?>')">copy</button>
                </div>
            </td>
            <td class="x-submit-voting-results-short-code-container" id="<?php echo $key . 'voting-results' ?>">
                <input class="x-submit-voting-short-code" id="<?php echo $key . '_voting-results-input' ?>"
                    value="<?php write_voting_results_short_code($value['voting_id'], $value['campaign_id'])?>">
                <button type="button" class="x-submit-voting-copy-button" onclick="copyCode('<?php write_voting_results_short_code(
                $value['voting_id'],
                $value['campaign_id']
            )?>')">copy</button>
            </td>
        </tr>
        <?php
}
        ?>
    </table>
    <input type="hidden" name='x_submit_current_votes[current_votings]'
        value="<?php echo esc_attr($x_submit_current_votes['current_votings']); ?>" />
    <p class="submit">
        <input type="submit" class="button-primary" value="Save Votings" />
    </p>
</form>
<?php
}?>
<?php

}

function x_submit_votings_registration()
{
    register_setting(
        "x-submit-voting-current-votes-group",
        "x_submit_current_votes",
        "x_submit_votings_sanitizer"
    );
}

function x_submit_votings_sanitizer($input)
{
    //die(json_encode($input));
    return $input;
}

function call_write_voting_func($atts)
{
    extract(shortcode_atts(array(
        'vid' => null,
        'cid' => null,
    ), $atts));
    return write_x_submit_voting_frame($vid, $cid);
}
function call_write_voting_results_func($atts)
{
    extract(shortcode_atts(array(
        'vid' => null,
        'cid' => null,
    ), $atts));
    return write_x_submit_voting_result_frame($vid, $cid);
}

function write_x_submit_voting_frame($vid, $cid)
{
    $x_submit_current_votes = get_option('x_submit_current_votes');
    $current_votes = json_decode($x_submit_current_votes['current_votings'], true);
    if (isset($current_votes) && sizeof($current_votes) > 0) {
        $this_vote = x_submit_array_find($current_votes, function ($el) use ($vid, $cid) {
            if ($el['campaign_id'] == $cid && $el['voting_id'] == $vid) {
                return true;
            } else {
                return false;
            }
        });
        $campaign_slug = $this_vote['campaign_slug'];
        $voting_slug = $this_vote['voting_slug'];
        return "<iframe id='x-submit-voting-iframe' class='x-submit-voting-iframe' height=1153px frameBorder='0' width='100%' src='" .
            $GLOBALS['x_submit_base_url'] . "campaign/" .
            $campaign_slug . "/voting/" . $voting_slug . "'></iframe>";

    }
}
function x_submit_array_find($xs, $f)
{
    foreach ($xs as $x) {
        if (call_user_func($f, $x) === true) {
            return $x;
        }
    }
    return null;
}

function write_x_submit_voting_result_frame($vid, $cid)
{
    $x_submit_current_votes = get_option('x_submit_current_votes');
    $current_votes = json_decode($x_submit_current_votes['current_votings'], true);
    if (isset($current_votes) && sizeof($current_votes) > 0) {
        $this_vote = x_submit_array_find($current_votes, function ($el) use ($vid, $cid) {
            if ($el['campaign_id'] == $cid && $el['voting_id'] == $vid) {
                return true;
            } else {
                return false;
            }
        });
        $campaign_slug = $this_vote['campaign_slug'];
        $voting_id = $this_vote['voting_id'];
        return "<iframe  id='x-submit-voting-results-iframe' class='x-submit-voting-iframe' frameBorder='0' height=1153px width='100%' src='" .
            $GLOBALS['x_submit_base_url'] . "campaign/" .
            $campaign_slug . "/voting/results/voting/" . $voting_id . "'></iframe>";

    }
}
add_shortcode('xsv', 'call_write_voting_func');
add_shortcode('xsvr', 'call_write_voting_results_func');

function write_voting_short_code($vid, $cid)
{
    echo "[xsv vid=" . $vid . " cid=" . $cid . " ]";
}
function write_voting_results_short_code($vid, $cid)
{
    echo "[xsvr vid=" . $vid . " cid=" . $cid . " ]";
}
function add_x_submit_voting_js()
{
    //die("enqueing script");
    wp_enqueue_script(
        'your-script', // name your script so that you can attach other scripts and de-register, etc.
        plugin_dir_url(__FILE__) . '/js/x-submit-voting.js', // this is the location of your script file
        array('jquery'), // this array lists the scripts upon which your script depends,
        '1.1.0'
    );
}

function load_x_submit_voting_scripts(){
    load_x_submit_voting_css();
    add_x_submit_voting_js();
}

function load_x_submit_voting_css()
{
    wp_enqueue_style('x-submit-voting-styles',
        plugin_dir_url(__FILE__) . '/css/x-submit-voting.css',
        [], '1.0.1');
}
