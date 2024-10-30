<?php
/*
Plugin Name: Google Buzz Button
Plugin URI: http://buzzzy.com/button/wordpress
Description: The Buzz It button is the most powerful Google Buzz share button since it is built on the Search API from http://Buzzzy.com the first 3rd party search engine for Google Buzz.
Version: 0.9.1
Author: Buzzzy
Author URI: http://buzzzy.com/
*/

function buzzzy_options() {
	add_menu_page('Buzzzy Button', 'Buzzzy Button', 8, basename(__FILE__), 'buzzzy_options_page', WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__),"",plugin_basename(__FILE__)).'img/ico.png');
	add_submenu_page(basename(__FILE__), 'Settings', 'Settings', 8, basename(__FILE__), 'buzzzy_options_page');
}

function buzzzy_build_options() {
	global $post;
    if ($post->post_status == 'publish') {
        $url = get_permalink();
    }
    $button = '?url=' . urlencode($url);
	$button.= '&amp;title='.urlencode($post->post_title);

    if (get_option('buzzzy_style') == 'compact') 
        $button .= '&amp;style=compact';
	else 
		$button .= '&amp;style=box';

	return $button;
}

function buzzzy_generate_button() {

    $button = '<div class="buzzzy_button" style="' . get_option('buzzzy_css') . '">';
    $button .= '<iframe src="http://buzzzy.com/api/buzz.it' . buzzzy_build_options() . '" ';

    if (get_option('buzzzy_style') == 'compact') {
        $button .= 'height="18" width="82"';
    } else {
		$button .= 'height="56" width="50"';
	}

	$button .= ' frameborder="0" scrolling="no"></iframe></div>';
    return $button;
}

/*
function buzzzy_update_title($content)
{
	global $post;
	
	if (get_option('buzzzy_display_place') != 'title')
		return $content;
	
	if (get_option('buzzzy_display_front') == null && is_home()) {
        return $content;
    }
	
	if (get_option('buzzzy_display_page') == null && is_page()) {
        return $content;
    }
	
	if (!is_feed())
		$button = buzzzy_generate_button();
	else
		return $content;
		
	if (get_option('buzzzy_position') == 'shortcode') 
	{
		return str_replace('[buzzzy]', $button, $content);
	} 
	else 
	{
		if (get_option('buzzzy_position') == 'before') 
			return $button . $content;
		else 
			return $content . $button;
	}
}
*/

function buzzzy()
{
	if (get_option('buzzzy_position') == 'manual')
        return buzzzy_generate_button();
    else
        return null;
}

function buzzzy_update($content) {

    global $post;
	
	if (get_option('buzzzy_display_front') == null && is_home()) {
        return $content;
    }
	
	if (get_option('buzzzy_display_page') == null && is_page()) {
        return $content;
    }
	
	if (!is_feed())
		$button = buzzzy_generate_button();
	else
		return $content;
		
	if (get_option('buzzzy_position') == 'manual') return $content; 
		
	if (get_option('buzzzy_position') == 'shortcode') 
	{
		return str_replace('[buzzzy]', $button, $content);
	} 
	else 
	{
		if (get_option('buzzzy_position') == 'before') 
			return $button . $content;
		else 
			return $content . $button;
	}
}

function buzzzy_remove_filter($content) {
	if (!is_feed()) {
    	remove_action('the_content', 'buzzzy_update');
	}
    return $content;
}


function buzzzy_options_page() {
?>
    <div class="wrap">
    <div class="icon32" id="icon-options-general"><br/></div>
	<h2>Settings for Buzzzy Button</h2>
    <p>
		This plugin will install the Buzzzy Button for each of your blog posts that will enable your website visitors to share your posts on Google Buzz.
		<br />It is referenced by the class name <code>buzzzy_button</code>.
    </p>
    <form method="post" action="options.php">
    <?php
        if(function_exists('settings_fields'))
		{
            settings_fields('buzzzy-options');
        } else 
		{
            wp_nonce_field('update-options');
            ?>
			<input type="hidden" name="page_options" value="buzzzy_display_place,buzzzy_position,buzzzy_css,buzzzy_display_page,buzzzy_display_front" />
            <input type="hidden" name="action" value="update" />
            <?php
        }
    ?>
        <table class="form-table">
            <tr>
	            <th scope="row" valign="top">
	                Display
	            </th>
	            <td>
	                    <input type="checkbox" value="1" <?php if (get_option('buzzzy_display_page') == '1') echo 'checked="checked"'; ?> name="buzzzy_display_page" id="buzzzy_display_page" group="buzzzy_display"/>
	                    <label for="buzzzy_display_page">Display the button on pages</label>
	                    <br/>
	                    <input type="checkbox" value="1" <?php if (get_option('buzzzy_display_front') == '1') echo 'checked="checked"'; ?> name="buzzzy_display_front" id="buzzzy_display_front" group="buzzzy_display"/>
	                    <label for="buzzzy_display_front">Display the button on the front page (home)</label>
	            </td>
	        </tr>
			<!--
			<tr>
                <th scope="row" valign="top">
                    Located in
                </th>
                <td>
                	<select name="buzzzy_display_place">
                		<option <?php if (get_option('buzzzy_display_place') == 'content') echo 'selected="selected"'; ?> value="content">Content</option>
                		<option <?php if (get_option('buzzzy_display_place') == 'title') echo 'selected="selected"'; ?> value="title">Title</option>
                	</select>
                </td>
            </tr>
			-->
			<tr>
                <th scope="row" valign="top">
                    Position
                </th>
                <td>
                	<select name="buzzzy_position">
                		<option <?php if (get_option('buzzzy_position') == 'before') echo 'selected="selected"'; ?> value="before">Before</option>
                		<option <?php if (get_option('buzzzy_position') == 'after') echo 'selected="selected"'; ?> value="after">After</option>
                		<option <?php if (get_option('buzzzy_position') == 'shortcode') echo 'selected="selected"'; ?> value="shortcode">Shortcode [buzzzy]</option>
						<option <?php if (get_option('buzzzy_position') == 'manual') echo 'selected="selected"'; ?> value="manual">Manual</option>
					</select>
					<span class="description">If <b>Manual</b> use: <code>if(function_exists('buzzzy')) echo buzzzy();</code></span>
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top"><label for="buzzzy_style">Styling</label></th>
                <td>
                    <input type="text" value="<?php echo htmlspecialchars(get_option('buzzzy_css')); ?>" name="buzzzy_css" id="buzzzy_css" />
                    <span class="description">Add style to the div that surrounds the button E.g. <code>float: right; margin-right: 5px;</code></span>
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top">
                    Type
                </th>
                <td>
                    <input type="radio" value="box" <?php if (get_option('buzzzy_style') == 'box') echo 'checked="checked"'; ?> name="buzzzy_style" id="buzzzy_style_box" group="buzzzy_style"/>
                    <label for="buzzzy_style_box">The box style widget</label>
                    <br/>
                    <input type="radio" value="compact" <?php if (get_option('buzzzy_style') == 'compact') echo 'checked="checked"'; ?> name="buzzzy_style" id="buzzzy_style_compact" group="buzzzy_style" />
                    <label for="buzzzy_style_compact">The compact style widget</label>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
    </div>
<?php
}

function buzzzy_init(){
    if(function_exists('register_setting')){
        register_setting('buzzzy-options', 'buzzzy_display_page');
        register_setting('buzzzy-options', 'buzzzy_display_front');
        register_setting('buzzzy-options', 'buzzzy_style');
        register_setting('buzzzy-options', 'buzzzy_css');
        register_setting('buzzzy-options', 'buzzzy_position');
		//register_setting('buzzzy-options', 'buzzzy_display_place');
    }
}

if(is_admin()){
    add_action('admin_menu', 'buzzzy_options');
    add_action('admin_init', 'buzzzy_init');
}

function buzzzy_activate(){
    add_option('buzzzy_position', 'before');
    add_option('buzzzy_css', 'margin-left:5px;float: right;');
    add_option('buzzzy_style', 'compact');
    add_option('buzzzy_display_page', '1');
    add_option('buzzzy_display_front', '1');
	//add_option('buzzzy_display_place', 'content');
}

//add_filter('the_title', 'buzzzy_update_title', 7);
add_filter('the_content', 'buzzzy_update', 8);
add_filter('get_the_excerpt', 'buzzzy_remove_filter', 9);

register_activation_hook( __FILE__, 'buzzzy_activate');
