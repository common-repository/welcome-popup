<?php
/*
Plugin Name: Welcome Popup
Plugin URI: https://wordpress.org/plugins/welcome-popup/
Description: Increase user interactivity and create curiosity by welcoming your visitors with a personalized message via Popup message. This plugin will allow WordPress site admin to set a personalized message for every visitor, they visit the site first time.
Version: 1.0.9
Author: Weblineindia
Author URI: http://www.weblineindia.com
License: GPL
*/

/**
 * Define Constants
 */
define ( 'WELCOME_POPUP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define ( 'WELCOME_POPUP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

$plugin_data = get_plugin_data( __FILE__ );
$plugin_version = $plugin_data['Version'];

register_activation_hook(__FILE__, 'welcome_popup_activate');
register_uninstall_hook(__FILE__, 'welcome_popup_uninstall');
add_filter('pre_set_site_transient_update_plugins', 'update_welcome_popup');

/**
 * Register style sheet.
 */
add_action( 'wp_enqueue_scripts', 'register_plugin_styles' );
function register_plugin_styles() {

	//Enqueue scroll css
	wp_register_style( 'scroll_css', WELCOME_POPUP_PLUGIN_URL .'css/wli-scrollbar.css' );
	wp_enqueue_style( 'scroll_css' );
}

/*
 *  This function is called when the plugin is activated.
 *
 *  @return             void
 *  @var                No arguments passed
 *  @author             AC
 */
function welcome_popup_activate() {
	global $plugin_version;

	$default_value = array(
		'version' => $plugin_version,
		'title' => 'Title',
		'content' => 'This is the default content.',
		'first_visit' => '1',
		'time' => '0',
		'display_never' => '1',
		'exclude_fields' => '',
		'show_preference' => '',
		'exclude_post_fields' => '',
		'width' 	=> '',
		'display_position' => '',
		'bg_color' 	=> '',
		'bg_image_id' 	=> '',
		'border_style' 	=> '',
		'border_size' 	=> '',
		'border_color' 	=> '',
		'border_radius' => '',
		'title_color' => '',
		'description_color' => '',
		'ddagain_link_color' => '',
		'close_btn_inside_popup' => '',
		'closebtn_image_id' => '',
		'overlay_color' => '',
		'overlay_opacity' 	=> '',
		'custom_css' 	=> '',
	);

	add_option('welcome_popup_settings',$default_value);

	update_option('welcome_popup_activation_date', time());
}


/**
 *  This function is called when the plugin is uninstalled.
 *
 *  @return             void
 *  @var                No arguments passed
 *  @author             AC
 */
function welcome_popup_uninstall()
{
	delete_option('welcome_popup_settings',$default_value);
}

/* Check update hook Start */
function update_welcome_popup($transient)
{
    if (empty($transient->checked)) {
        return $transient;
    }
    $plugin_folder = plugin_basename(__FILE__);
    if (isset($transient->checked[$plugin_folder])) {
        update_option('welcome_popup_activation_date', time());
    }
    return $transient;
}   
/* Check update hook End */


/**
 *  This function is use to link the admin css file.
 *
 *  @return             void
 *  @var                No arguments passed
 *  @author             AC
 */
function welcome_popup_load_preview( $hook ) {

	//Check if admin page
	if( 'settings_page_welcome_popup_page' == $hook ) {

		// Enqueue color picker styles      
		wp_enqueue_style( 'wp-color-picker' );

		// Enqueue color picker scripts      
		wp_enqueue_script( 'wp-color-picker' );

		//Enqueue admin js
		wp_enqueue_script( 'welcome-popup-admin-script', WELCOME_POPUP_PLUGIN_URL .'js/admin.js', array( 'jquery' ), '', false );
		wp_localize_script( 'welcome-popup-admin-script', 'WelcomePopupScriptsData', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'choose_image_title' => __( 'Choose an image', 'welcome_popup' ),
			'use_image_btn_text' => __( 'Use image', 'welcome_popup' ),
			'placeholder_img_src' => WELCOME_POPUP_PLUGIN_URL .'images/default.png',
		));

		wp_enqueue_script( 'welcome-popup-jss', WELCOME_POPUP_PLUGIN_URL .'js/install-plugin-welcome-popup.js', array( 'jquery' ), '', false );
		
	}
	//Enqueue Admin Notices CSS
	wp_register_style( 'welcome-popup-admin-notices-css', WELCOME_POPUP_PLUGIN_URL .'css/welcome-popup-admin-notices.css' );
	wp_enqueue_style( 'welcome-popup-admin-notices-css' );

}
add_action( 'admin_enqueue_scripts','welcome_popup_load_preview' );

//Action to add admin inline scripts
add_action( 'admin_print_footer_scripts', 'welcome_popup_admin_footer_js' );
function welcome_popup_admin_footer_js() {

	global $hook_suffix;

	//Check if not admin page
	if( 'settings_page_welcome_popup_page' !== $hook_suffix ) return;
	?>
	<script type="text/javascript">
		jQuery(document).ready(function(){

			// Initilize WP color picker
			jQuery('.welcome_popup_color_picker').wpColorPicker();
		});
	</script>
	<?php
}

/**
 *  This function is used to include js file for the popup.
 *
 *  @return             void
 *  @var                No arguments passed
 *  @author             AC
 */
function welcome_popup_jquery_enqueuescripts() {
	global $plugin_version;
	$popup_time = get_welcome_popup_setting( 'time' );

	if( !wp_script_is( 'jquery' ) )
	{
		wp_enqueue_script('jquery');
	}

	//Enqueue welcome model
	wp_enqueue_script('jquery_welcome_model', WELCOME_POPUP_PLUGIN_URL .'js/modal.js', array('jquery'), $plugin_version);

	//Get option and passed on javascript
	$close_on_esc = get_welcome_popup_setting( 'close_on_esc' );
	$translation_array = array( 'popup_time' =>  $popup_time, 'close_on_esc' => $close_on_esc );

	wp_localize_script( 'jquery_welcome_model', 'welcomePopup', $translation_array );


	$wli_scroll_min_js = WELCOME_POPUP_PLUGIN_URL .'js/wli-scrollbar.js';
	wp_register_script( 'wli_scroll_min_js', $wli_scroll_min_js );
	wp_enqueue_script( 'wli_scroll_min_js' );

	$wli_scroll = WELCOME_POPUP_PLUGIN_URL .'js/wli-scroll.js';
	wp_register_script( 'wli_scroll', $wli_scroll );
	wp_enqueue_script( 'wli_scroll' );
}
add_action('wp_enqueue_scripts', 'welcome_popup_jquery_enqueuescripts');

// wp_register_style( 'plugin_css', s

/**
 *  When the plugin is loaded this function is called to load the plugin's translated string.
 *
 *  @return             void
 *  @var                No arguments passed
 *  @author             AC
 */
function welcome_popup_init() {
	load_plugin_textdomain( 'welcome_popup', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'welcome_popup_init');


/**
 *  This function is used to get the options value from the database
 *
 *  @return             It will return the current value for the specified key
 *  @var                The key is passed to retrieve its value
 *  @author             AC
 */
function get_welcome_popup_setting($key= '')
{
	if($key == '')
		return '';
	else
	{
		$current_option = get_option('welcome_popup_settings');
		if(isset($current_option[$key])) {
			return $current_option[$key];
		}
		else
			return '';
	}
}


/**
 *  This function is used to update the option value pair to the database.
 *
 *  @return             The function returns true if the value is updated else false
 *  @var                The key to update and the new value for this key is passed
 *  @author             AC
 */
function update_all_settings($key= '', $value = '')
{
	$msg = 0;
	if($key == '')
		return true;
	else
	{
		update_option('welcome_popup_settings',$value);
		$msg = 1;
	}
	return $msg;
}

/**
 * Display footer text that graciously asks them to rate us.
 *
 * @since 1.0.4
 *
 * @param string $text
 *
 * @return string
 */
add_filter( 'admin_footer_text', 'welcome_popup_admin_footer_text', 1, 2 );
function welcome_popup_admin_footer_text( $text ) {

	global $current_screen;

	//Check of relatd screen match
	if ( ! empty( $current_screen->id ) && strpos( $current_screen->id, 'settings_page_welcome_popup_page' ) !== false ) {

		$url  = 'https://wordpress.org/support/plugin/welcome-popup/reviews/?filter=5#new-post';
		$wpdev_url  = 'https://www.weblineindia.com/wordpress-development.html?utm_source=WP-Plugin&utm_medium=Welcome%20Popup&utm_campaign=Footer%20CTA';
		$text = sprintf(
			wp_kses(
				'Please rate our plugin %1$s <a href="%2$s" target="_blank" rel="noopener noreferrer">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%3$s" target="_blank" rel="noopener">WordPress.org</a> to help us spread the word. Thank you from the <a href="%4$s" target="_blank" rel="noopener noreferrer">WordPress development</a> team at WeblineIndia.',
				array(
					'a' => array(
						'href'   => array(),
						'target' => array(),
						'rel'    => array(),
					),
				)
			),
			'<strong>"Welcome Popup"</strong>',
			$url,
			$url,
			$wpdev_url
		);
	}

	return $text;
}

/**
 * CTA section callback function.
 *
 * @since    1.0.4
 */
function welcome_popup_admin_CTA_callback() {
	?>
	<div class="welcome-popup-plugin-cta">
		<h2 class="welcome-popup-heading">Thank you for downloading our plugin - Welcome Popup.</h2>
		<h2 class="welcome-popup-heading">We're here to help !</h2>
		<p>Our plugin comes with free, basic support for all users. We also provide plugin customization in case you want to customize our plugin to suit your needs.</p>
		<a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=Welcome%20Popup&utm_campaign=Free%20Support" target="_blank" class="button">Need help?</a>
		<a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=Welcome%20Popup&utm_campaign=Plugin%20Customization" target="_blank" class="button">Want to customize plugin?</a>
	</div>
	<?php
		$all_plugins = get_plugins();
		if (!(isset($all_plugins['xml-sitemap-for-google/xml-sitemap-for-google.php']))) {
			?>
				<div class="welcome-popup-plugin-cta show-other-plugin" id="xml-plugin-banner">
					<h2 class="welcome-popup-heading">Want to Rank Higher on Google?</h2>
					<h3 class="welcome-popup-heading">Install <span>XML Sitemap for Google</span> Plugin</h3>
					<hr>
					<p>Our plugin comes with free, basic support for all users.</p>
					<ul class="custom-bullet">
						<li>Easy Setup and Effortless Integration</li>	
						<li>Automatic Updates</li>	
						<li>Improve Search Rankings</li>	
						<li>SEO Best Practices</li>
						<li>Optimized for Performance</li>
					</ul>						
					<br>
					<button id="open-install-welcome-popup" class="button-install">Install Plugin</button>
				</div>
			<?php 
		}	
}

/**
 *  This function is use to add the submenu page to the setting menu.
 *
 *  @return             String
 *  @var                No arguments passed
 *  @author             AC
 */
function welcome_popup_admin_add_page() {

	//Add option page
	add_options_page( __('Welcome Popup Settings Page', 'welcome_popup' ), __( 'Welcome Popup', 'welcome_popup' ), 'manage_options', 'welcome_popup_page', 'welcome_popup_options_page');
}
add_action('admin_menu', 'welcome_popup_admin_add_page');


/**
 *  This function is use to make the html page for the settings page.
 *
 *  @return             form
 *  @var                No arguments passed
 *  @author             AC
 */
function welcome_popup_options_page() {
	global $msg_box;
	if(isset($_POST['welcome_popup_submit'])) {

		if(isset($_POST['display_never'])){
			$dis_never = $_POST['display_never'];
		}
		else {
			$dis_never = 0;
		}

		if(isset($_POST['first_visit'])){
			$fir_visit = $_POST['first_visit'];
		}
		else {
			$fir_visit = 0;
		}

		if(isset($_POST['close_on_esc'])){
			$close_on_esc = $_POST['close_on_esc'];
		}
		else {
			$close_on_esc = 0;
		}

		if(isset($_POST['exclude_fields'])){
			$exclude_page = $_POST['exclude_fields'];
		}
		else{
			$exclude_page = '';
		}

		if(isset($_POST['exclude_post_fields'])){
			$exclude_post = $_POST['exclude_post_fields'];
		}
		else{
			$exclude_post = '';
		}

		$show_preference = !empty( $_POST['show_preference'] ) ? $_POST['show_preference'] : '';

		$changed_value = array(
			'title' => $_POST['title'],
			'content' => $_POST['content'],
			'first_visit' => $fir_visit,
			'close_on_esc' => $close_on_esc,
			'time' => $_POST['time'],
			'display_never' => $dis_never,
			'show_preference' => $show_preference,
			'exclude_fields' => $exclude_page,
			'exclude_post_fields' => $exclude_post,
			'width' 	=> !empty( $_POST['width'] ) ? $_POST['width'] : '',
			'display_position' => !empty( $_POST['display_position'] ) ? $_POST['display_position'] : '',
			'bg_color' 	=> !empty( $_POST['bg_color'] ) ? $_POST['bg_color'] : '',
			'bg_image_id' 	=> !empty( $_POST['bg_image_id'] ) ? $_POST['bg_image_id'] : '',
			'border_style' 	=> !empty( $_POST['border_style'] ) ? $_POST['border_style'] : '',
			'border_size' 	=> !empty( $_POST['border_size'] ) ? $_POST['border_size'] : '',
			'border_color' 	=> !empty( $_POST['border_color'] ) ? $_POST['border_color'] : '',
			'border_radius' => !empty( $_POST['border_radius'] ) ? $_POST['border_radius'] : '',
			'title_color' => !empty( $_POST['title_color'] ) ? $_POST['title_color'] : '',
			'description_color'  => !empty( $_POST['description_color'] ) ? $_POST['description_color'] : '',
			'ddagain_link_color' => !empty( $_POST['ddagain_link_color'] ) ? $_POST['ddagain_link_color'] : '',
			'close_btn_inside_popup' => !empty( $_POST['close_btn_inside_popup'] ) ? $_POST['close_btn_inside_popup'] : '',
			'closebtn_image_id' => !empty( $_POST['closebtn_image_id'] ) ? $_POST['closebtn_image_id'] : '',
			'overlay_color' 	=> !empty( $_POST['overlay_color'] ) ? $_POST['overlay_color'] : '',
			'overlay_opacity' 	=> !empty( $_POST['overlay_opacity'] ) ? $_POST['overlay_opacity'] : '',
			'custom_css' 	=> !empty( $_POST['custom_css'] ) ? $_POST['custom_css'] : '',
		);

		$msg_box = update_all_settings('welcome_popup_settings', $changed_value);
	}
	?>
	<div class="wrap-welcome-popup">
			<div class="inner-welcome-popup">
				<div class="left-box-welcome-popup">
					<h2><?php _e('Welcome Popup Settings','welcome_popup');?></h2>
					<?php
					if($msg_box) {?>
						<div class="updated notice is-dismissible">
							<p><strong><?php _e('Settings Saved','welcome_popup');?></strong></p>
						</div>
					<?php }?>
					<form method="post">
						<h3><?php _e( 'General Options', 'welcome_popup' );?></h3>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row"><?php _e('Title:','welcome_popup');?></th>
									<td><input name="title" type="text" id="title" class="regular-text" value="<?php echo esc_attr(get_welcome_popup_setting( 'title' ));?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e('Content:','welcome_popup');?></th>
									<td>
										<?php
										wp_editor(
											stripslashes(get_welcome_popup_setting( 'content' )),
											'popup_content',
											apply_filters( 'welcome_popup_content_editor_args', array(
												'quicktags'     => array("buttons"=>"strong,em,link,b-quote,del,ins,img,ul,ol,li,code,close"),
												'textarea_name' => 'content',
												'textarea_rows' => 4,
											))
										);
										?>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e('Only on first visit:','welcome_popup');?></th>
									<td>
										<input name="first_visit" type="checkbox" value="1" <?php  checked( '1', get_welcome_popup_setting( 'first_visit' ) ); ?> />
										<p class="description"><?php _e( 'Enable this if you want to display popup only on first visit.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e('Close On ESC:','welcome_popup');?></th>
									<td>
										<input name="close_on_esc" type="checkbox" value="1" <?php  checked( '1', get_welcome_popup_setting( 'close_on_esc' ) ); ?> />
										<p class="description"><?php _e( 'Enable this if you want to close popup on ESC key.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e('Popup delay (in seconds):','welcome_popup');?></th>
									<td>
										<input name="time" type="number" id="time" class="small-text" value="<?php echo esc_attr(get_welcome_popup_setting( 'time' ));?>" />
										<p class="description"><?php _e( 'Enter popup delay time ( seconds ), Leave it blank to use default.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e('Show Display never link:','welcome_popup');?></th>
									<td>
										<input type="checkbox" name="display_never" value="1" <?php checked( '1', get_welcome_popup_setting( 'display_never' ) ); ?> />
										<p class="description"><?php _e( 'Enable this if you want to display link for Dont Display Again.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e('Show Preference:','welcome_popup');?></th>
									<?php $show_preference = get_welcome_popup_setting('show_preference'); ?>
									<td>
										<select name="show_preference" tabindex="1" id="show_preference">
											<option value="" <?php selected($show_preference,'');?>><?php _e( 'All Users', 'welcome_popup' );?></option>
											<option value="login" <?php selected($show_preference,'login');?>><?php _e( 'Logged In User Only', 'welcome_popup' );?></option>
											<option value="guest" <?php selected($show_preference,'guest');?>><?php _e( 'Guest User Only', 'welcome_popup' );?></option>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e('Exclude Pages:','welcome_popup');?></th>
									<?php $exclude_fields = get_welcome_popup_setting('exclude_fields'); ?>
									<td>
										<select name="exclude_fields[]" size="3" multiple="multiple" tabindex="1" id="exclude_pages">
											<?php
											$pages = get_pages();
											foreach ( $pages as $page ) { ?>
												<option
									<?php if (is_array($exclude_fields) && in_array($page->ID,$exclude_fields)) {echo "selected=selected";}?>
										value="<?php echo esc_attr($page->ID); ?>">
													<?php echo __("$page->post_title",'welcome_popup'); ?>
												</option>
											<?php
											}?>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e('Exclude Posts:','welcome_popup');?></th>
									<?php $exclude_post_fields = get_welcome_popup_setting('exclude_post_fields'); ?>
									<td>
										<select name="exclude_post_fields[]" size="3" multiple="multiple" tabindex="1" id="exclude_pages">
											<?php
											$posts_fields_args = array(
												'posts_per_page' => -1,
												'post_type'      => 'post',
											);
											$posts_fields = get_posts($posts_fields_args);
											foreach ( $posts_fields as $post ) { ?>
												<option
									<?php if (is_array($exclude_post_fields) && in_array($post->ID,$exclude_post_fields)) {echo "selected=selected";}?>
													value="<?php echo esc_attr($post->ID); ?>">
													<?php echo __("$post->post_title",'welcome_popup'); ?>
												</option>
											<?php
											}?>
										</select>
									</td>
								</tr>
							</tbody>
						</table>
						<h3><?php _e( 'Popup Layout Options', 'welcome_popup' );?></h3>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row"><?php _e( 'Width:','welcome_popup' );?></th>
									<td>
										<input name="width" type="number" id="width" class="small-text" value="<?php echo esc_attr(get_welcome_popup_setting( 'width' ));?>" min="0"/>
										<p class="description"><?php _e( 'Enter popup width ( pixels ), Leave it blank to use default.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Display Position:','welcome_popup' );?></th>
									<td>
										<?php $display_position = esc_attr(get_welcome_popup_setting( 'display_position' ));?>
										<select id="display_position" name="display_position">
											<option value=""><?php _e( 'Center Popup (Default)','welcome_popup' );?></option>
											<option value="left-bottom" <?php selected( $display_position, 'left-bottom' );?>><?php _e( 'Left Bottom','welcome_popup' );?></option>
											<option value="right-bottom" <?php selected( $display_position, 'right-bottom' );?>><?php _e( 'Right Bottom','welcome_popup' );?></option>
											<option value="top-bar" <?php selected( $display_position, 'top-bar' );?>><?php _e( 'Top Bar','welcome_popup' );?></option>
										</select>
										<p class="description"><?php _e( 'Choose display popup position.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Background Color:','welcome_popup' );?></th>
									<td>
										<input name="bg_color" type="text" id="bg_color" value="<?php echo esc_attr(get_welcome_popup_setting( 'bg_color' ));?>" class="regular-text welcome_popup_color_picker" />
										<p class="description"><?php _e( 'Choose background color of popup, Leave it blank to use default.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Background Image:','welcome_popup' );?></th>
									<td>
										<?php
										$bg_image_id = get_welcome_popup_setting( 'bg_image_id' );
										$bg_image = !empty( $bg_image_id ) ? wp_get_attachment_thumb_url($bg_image_id) : WELCOME_POPUP_PLUGIN_URL .'images/default.png';
										?>
										<div id="bg_image" class="welcome_popup_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($bg_image); ?>" width="150px" height="150px" /></div>
										<div style="line-height: 150px;">
											<input type="hidden" id="bg_image_id" class="welcome_popup_upload_img_id" name="bg_image_id" value="<?php echo absint( $bg_image_id ); ?>" />
											<button type="button" class="welcome_popup_upload_image_button button"><?php _e( 'Upload/Add image', 'welcome_popup' ); ?></button>
											<button type="button" class="welcome_popup_remove_image_button button" <?php echo empty( $bg_image_id ) ? 'style="display:none;"' : '';?>><?php _e( 'Remove image', 'welcome_popup' ); ?></button>
										</div>
										<p class="description"><?php _e( 'Choose background image of popup.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Title Color:','welcome_popup' );?></th>
									<td>
										<input name="title_color" type="text" id="title_color" value="<?php echo esc_attr(get_welcome_popup_setting( 'title_color' ));?>" class="welcome_popup_color_picker" />
										<p class="description"><?php _e( 'Choose title color of popup, Leave it blank to use default.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Description Color:','welcome_popup' );?></th>
									<td>
										<input name="description_color" type="text" id="description_color" value="<?php echo esc_attr(get_welcome_popup_setting( 'description_color' ));?>" class="welcome_popup_color_picker" />
										<p class="description"><?php _e( 'Choose description color of popup, Leave it blank to use default.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Don\'t Display Again Link Color:','welcome_popup' );?></th>
									<td>
										<input name="ddagain_link_color" type="text" id="ddagain_link_color" value="<?php echo esc_attr(get_welcome_popup_setting( 'ddagain_link_color' ));?>" class="welcome_popup_color_picker" />
										<p class="description"><?php _e( 'Choose Don\'t Display Again Link color of popup, Leave it blank to use default.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Border Style:','welcome_popup' );?></th>
									<td>
										<?php $border_style = esc_attr(get_welcome_popup_setting( 'border_style' ));?>
										<select id="border_style" name="border_style">
											<option value=""><?php _e( 'None','welcome_popup' );?></option>
											<option value="solid" <?php selected( $border_style, 'solid' );?>><?php _e( 'Solid','welcome_popup' );?></option>
											<option value="dotted" <?php selected( $border_style, 'dotted' );?>><?php _e( 'Dotted','welcome_popup' );?></option>
											<option value="dashed" <?php selected( $border_style, 'dashed' );?>><?php _e( 'Dashed','welcome_popup' );?></option>
											<option value="double" <?php selected( $border_style, 'double' );?>><?php _e( 'Double','welcome_popup' );?></option>
											<option value="groove" <?php selected( $border_style, 'groove' );?>><?php _e( 'Groove','welcome_popup' );?></option>
											<option value="inset" <?php selected( $border_style, 'inset' );?>><?php _e( 'Inset (inner shadow)','welcome_popup' );?></option>
											<option value="outset" <?php selected( $border_style, 'outset' );?>><?php _e( 'Outset','welcome_popup' );?></option>
											<option value="ridge" <?php selected( $border_style, 'ridge' );?>><?php _e( 'Ridge','welcome_popup' );?></option>
										</select>
										<p class="description"><?php _e( 'Choose border style of popup, Leave it blank to use default.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Border Size:','welcome_popup' );?></th>
									<td>
										<input name="border_size" type="number" id="border_size" class="small-text" value="<?php echo esc_attr(get_welcome_popup_setting( 'border_size' ));?>" />
										<p class="description"><?php _e( 'Enter popup border size ( pixels ), Leave it blank to use default.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Border Color:','welcome_popup' );?></th>
									<td>
										<input name="border_color" type="text" id="border_color" value="<?php echo esc_attr(get_welcome_popup_setting( 'border_color' ));?>" class="welcome_popup_color_picker" />
										<p class="description"><?php _e( 'Choose border color of popup, Leave it blank to use default.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Border Radius:','welcome_popup' );?></th>
									<td>
										<input name="border_radius" type="number" id="border_radius" class="small-text" value="<?php echo esc_attr(get_welcome_popup_setting( 'border_radius' ));?>" />
										<p class="description"><?php _e( 'Enter popup border radius ( pixels ), Leave it blank to use default.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Close Custom Icon:','welcome_popup' );?></th>
									<td>
										<?php
										$closebtn_image_id = get_welcome_popup_setting( 'closebtn_image_id' );
										$closebtn_image = !empty( $closebtn_image_id ) ? wp_get_attachment_thumb_url($closebtn_image_id) : WELCOME_POPUP_PLUGIN_URL .'images/default.png';
										?>
										<div id="closebtn_image" class="welcome_popup_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($closebtn_image); ?>" width="40px" height="40px" /></div>
										<div style="line-height: 40px;">
											<input type="hidden" id="closebtn_image_id" class="welcome_popup_upload_img_id" name="closebtn_image_id" value="<?php echo absint( $closebtn_image_id ); ?>" />
											<button type="button" class="welcome_popup_upload_image_button button"><?php _e( 'Upload/Add image', 'welcome_popup' ); ?></button>
											<button type="button" class="welcome_popup_remove_image_button button" <?php echo empty( $closebtn_image_id ) ? 'style="display:none;"' : '';?>><?php _e( 'Remove image', 'welcome_popup' ); ?></button>
										</div>
										<p class="description"><?php _e( 'Choose custom image of popup close button.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Move Close Button Inside Container:','welcome_popup' );?></th>
									<td>
										<input name="close_btn_inside_popup" type="checkbox" value="1" <?php  checked( '1', get_welcome_popup_setting( 'close_btn_inside_popup' ) ); ?> />
										<p class="description"><?php _e( 'Moves the position of the close button inside the popup.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Overlay Color:','welcome_popup' );?></th>
									<td>
										<input name="overlay_color" type="text" id="overlay_color" value="<?php echo esc_attr(get_welcome_popup_setting( 'overlay_color' ));?>" class="welcome_popup_color_picker" />
										<p class="description"><?php _e( 'Choose popup overlay color, Leave it blank to use default.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Overlay Opacity:','welcome_popup' );?></th>
									<td>
										<input name="overlay_opacity" type="number" id="overlay_opacity" class="small-text" value="<?php echo esc_attr(get_welcome_popup_setting( 'overlay_opacity' ));?>" min="0" max="100" />
										<p class="description"><?php _e( 'Enter popup overlay opacity ( % ), Leave it blank to use default.', 'welcome_popup' );?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Custom CSS:','welcome_popup' );?></th>
									<td>
										<textarea id="custom_css" name="custom_css" class="large-text" rows="4"><?php echo esc_attr(get_welcome_popup_setting( 'custom_css' ));?></textarea>
										<p class="description"><?php _e( 'Enter Custom CSS which you want to apply on popup.', 'welcome_popup' );?></p>
									</td>
								</tr>
							</tbody>
						</table>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row"></th>
									<td>
										<p class="submit">
											<input type="submit" name="welcome_popup_submit" id="welcome_popup_submit" class="button button-primary" value="<?php _e('Save Changes','welcome_popup');?>">
										</p>
									</td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
				<div class="right-box-welcome-popup">
					<?php echo welcome_popup_admin_CTA_callback(); ?>
				</div>
			</div>						
	</div>
<?php
}

/**
 *  This function is use to set the cookie on certain conditions.
 *
 *  @return             Cookie on  UI side
 *  @var                No arguments passed
 *  @author             AC
 */
function welcome_popup_set_cookie() {
	global $post, $flag;

	//Check if show preferece not match then skip
	$show_preference = get_welcome_popup_setting('show_preference');
	if( $show_preference == 'login' && ! is_user_logged_in() ) {
		return false;
	} elseif( $show_preference == 'guest' && is_user_logged_in() ) {
		return false;
	}

	//Check if empty then return
	if( empty( $post->ID ) ) return;

	$flag = 0;
	$post->ID;
	$exclude_fields = get_welcome_popup_setting('exclude_fields');
	$exclude_post_fields = get_welcome_popup_setting('exclude_post_fields');

	//Enqueue plugin css
	wp_register_style( 'plugin_css', WELCOME_POPUP_PLUGIN_URL .'css/mystyle.css' );
	wp_enqueue_style( 'plugin_css' );

	//Get exclude post ids
	$exclude_ids = !empty( $exclude_fields ) ? $exclude_fields : array();
	$exclude_ids = !empty( $exclude_post_fields ) ? array_merge($exclude_ids,$exclude_post_fields) : $exclude_ids;

	//Allow third party to add post in exclusion
	$exclude_extra_ids = apply_filters( 'welcome_popup_exclude_post_ids', array() );
	if( !empty( $exclude_extra_ids ) && is_array( $exclude_extra_ids ) ) {
		$exclude_ids = array_merge( $exclude_ids, $exclude_extra_ids );
	}

	//Check if exclude posts and prepare for display
	if( empty( $exclude_ids ) || ( !empty( $exclude_ids ) && ! in_array( $post->ID, $exclude_ids ) ) ) {

		$flag = 1;
		if (!isset($_COOKIE['visit'])) {
			ob_start();
			setcookie('visit', 'set', time()+60,COOKIEPATH, COOKIE_DOMAIN, false);
			ob_flush();
		}
	}
}
add_action( 'wp', 'welcome_popup_set_cookie' );

//Action to add popup html in header
add_action( 'wp_head', 'get_welcome_popup_html' );
function get_welcome_popup_html() {

	global $flag;

	//Check if show preferece not match then skip
	$show_preference = get_welcome_popup_setting('show_preference');
	if( $show_preference == 'login' && ! is_user_logged_in() ) {
		return false;
	} elseif( $show_preference == 'guest' && is_user_logged_in() ) {
		return false;
	}

	//Check if allow to display or not
	if( $flag != 1 ) { $flag = 0; return false; }

	//Get require options
	$show_hide = get_welcome_popup_setting('display_never');
	$fir_visit = get_welcome_popup_setting( 'first_visit' );

	//Check if popup allowed
	if( ( $fir_visit == '1' && ! isset($_COOKIE['visit']) && ! isset($_COOKIE['popup']) ) ||
		( $fir_visit != '1' && ! isset($_COOKIE['popup']) ) ) {

		//Get popup all needed options
		$popup_title 	= get_welcome_popup_setting( 'title' );
		$content 		= stripslashes(get_welcome_popup_setting( 'content' ));
		$popup_content 	= apply_filters( 'welcome_popup_content', $content );
		$display_position 	= get_welcome_popup_setting( 'display_position' );
		$width 	= get_welcome_popup_setting( 'width' );
		$bg_color 	= get_welcome_popup_setting( 'bg_color' );
		$title_color 	= get_welcome_popup_setting( 'title_color' );
		$desc_color 	= get_welcome_popup_setting( 'description_color' );
		$ddagain_color 	= get_welcome_popup_setting( 'ddagain_link_color' );
		$border_style 	= get_welcome_popup_setting( 'border_style' );
		$border_size 	= get_welcome_popup_setting( 'border_size' );
		$border_color 	= get_welcome_popup_setting( 'border_color' );
		$border_radius 	= get_welcome_popup_setting( 'border_radius' );
		$bg_image_id 	= get_welcome_popup_setting( 'bg_image_id' );
		$closebtn_inside_popup 	= get_welcome_popup_setting( 'close_btn_inside_popup' );
		$closebtn_image_id 		= get_welcome_popup_setting( 'closebtn_image_id' );
		$overlay_color 	= get_welcome_popup_setting( 'overlay_color' );
		$overlay_opacity 	= get_welcome_popup_setting( 'overlay_opacity' );
		$custom_css 	= get_welcome_popup_setting( 'custom_css' );

		//Prepare require class
		$display_class 	= 'welcome-popup-';
		$display_class 	.= !empty( $display_position ) ? $display_position : 'middle';
		$closebtn_class = $closebtn_inside_popup ? 'btn_inside_close' : '';

		//Build popup block style
		$popup_block_style = '';
		if( !empty( $width ) ) {
			$popup_block_style .= 'width:'. $width .'px;';
		}
		if( !empty( $bg_color ) ) {
			$popup_block_style .= 'background-color:'. $bg_color .';';
		}
		if( !empty( $border_style ) && !empty( $border_size ) ) {
			$popup_block_style .= 'border-style:'. $border_style .';';
			$popup_block_style .= 'border-width:'. $border_size .'px;';
			$popup_block_style .= 'border-color:'. $border_color .';';
		}
		if( !empty( $border_radius ) ) {
			$popup_block_style .= 'border-radius:'. $border_radius .'px;';
		}
		if( !empty( $bg_image_id ) ) {
			$bg_image_url = wp_get_attachment_thumb_url($bg_image_id);
			$popup_block_style .= 'background-image:url('. $bg_image_url .');';
		}

		//Get close btn style
		$closebtn_style = '';
		if( !empty( $closebtn_image_id ) ) {
			$closebtn_image_url = wp_get_attachment_thumb_url($closebtn_image_id);
			$closebtn_style = 'background-image:url('. $closebtn_image_url .');';
		}

		//Make title and description style
		$title_style = !empty( $title_color ) ? 'style="color: '. $title_color .';"' : '';
		$title_border_style = !empty( $title_color ) ? 'style="border-bottom-color: '. $title_color .';"' : '';
		$desc_style = !empty( $desc_color ) ? 'style="color: '. $desc_color .';"' : '';

		//Build common style
		$common_style = '';
		if( !empty( $ddagain_color ) ) {
			$common_style .= '.content_box .display, .content_box .display a, .content_box .display a:hover{color: '. $ddagain_color .'}';
		}

		//Build overlay styles
		$overlay_style = '';
		if( !empty( $overlay_color ) ) {
			$overlay_style .= 'background-color:'. $overlay_color .';';
		}
		if( !empty( $overlay_opacity ) ) {
			$overlay_opacity = $overlay_opacity / 100;
			$overlay_style .= 'opacity:'. $overlay_opacity .';';
		}
		if( !empty( $overlay_style ) ) {
			$common_style .= '.welcome-popup-middle + #overlay{'. $overlay_style .'}';
		}

		//Add custom style
		if( !empty( $custom_css ) ) {
			$common_style .= $custom_css;
		}?>
		<div class="popup_bg <?php echo $display_class;?>" style="display: none;">
			<div class="popup_block" <?php echo !empty( $popup_block_style ) ? 'style="'. $popup_block_style .'"' : '';?>>
				<div class="inner">
					<a href="javascript:void(0);" class="btn_close <?php if($closebtn_inside_popup) { echo 'btn_inside_close';};?>" title="<?php _e("Close","welcome_popup");?>" <?php if($closebtn_style) { echo 'style="'. $closebtn_style .'"';};?>><?php _e("Close","welcome_popup");?></a>

					<?php if( $popup_title != '' ) {?>
						<div class="heading_block" <?php echo $title_border_style;?>>
							<div class="heading01" <?php echo $title_style;?>><?php echo $popup_title;?></div>
							</div>
						<?php }?>

					<div class="content_box <?php echo ($popup_title == '') ? 'blank' : '';?>">
						<div class="content_desc">
							<div class="content_wrap" <?php echo $desc_style;?>>
								<?php echo wpautop($popup_content);?>
							</div>
						</div>
						<?php
						//Display link for hide popup
						if( $show_hide == 1 ) {
							echo '<p class="display"><a href="javascript:void(0);">'.__("Dont Display Again","welcome_popup").'</a></p>';
						}?>
					</div>
				</div>
			</div>
		</div>
		<div id="overlay" style="display: none;"></div>
		<?php
		//Display style
		if( !empty( $common_style ) ) {
			echo '<style type="text/css">'. $common_style .'</style>';
		}

	} else {
		/* echo "cookie set for display never so will not appear and first visit not checked"; */
	}
}

//Filter to add shortcode support in popup content
add_filter( 'welcome_popup_content', 'welcome_popup_content_filter' );
function welcome_popup_content_filter( $content ) {
	return do_shortcode( $content );
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'welcome_popup_add_action_links');
// Function for add action link
function welcome_popup_add_action_links($links_array)
{
    array_unshift($links_array, '<a href="options-general.php?page=welcome_popup_page">Settings</a>');
    return $links_array;
}

?>
