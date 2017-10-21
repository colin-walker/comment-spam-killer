<?php

	if(!defined('ABSPATH')) exit; //Don't run if accessed directly


	/**
	 * Comment Spam Killer
	 *
	 * @package Comment Spam Killer
	 *
   	 * Plugin Name: Comment Spam Killer
   	 *
   	 * Description: Prevent posting of comments containing entries in a specified list
   	 *
   	 * Version: 0.1.0
   	 *
   	 * Author: Colin Walker
	*/


	add_action( 'plugins_loaded', 'spam_plugin' );

	function spam_plugin() {
		register_activation_hook( __FILE__, 'spam_activate' );
		register_deactivation_hook(__FILE__, 'spam_deactivate');
	}


	// add actions	

	add_action( 'admin_init', 'spam_settings' );
	add_action( 'admin_menu', 'spam_menu' );



	function spam_activate() {
		add_option('spamphrases', '');
	}

	function spam_deactivate() {
		delete_option('spamphrases');
	}

    // register settings

	function spam_settings() {
		register_setting( 'comment-spam-settings-group', 'spamphrases' );
	}



	// create menu/settings page

	function spam_menu() {
		add_menu_page('Comment Spam Settings', 'Comment Spam', 'administrator', 'comment-spam', 'comment_spam_settings_page', 'dashicons-welcome-comments', 4 );
	}

	function comment_spam_settings_page() { ?>
		<div class="wrap">
		<h2>Comment spam</h2>
		<p>The spam terms added below will prevent comments from being saved that contain theme.</p>
		<p>Add multiple terms each on separate lines</p>

		<form method="post" action="options.php">
			<?php settings_fields( 'comment-spam-settings-group' ); ?>
			<p>Terms to forbid:</p>
			<input type="text" name="spamphrases" value="<?php echo esc_attr( get_option('spamphrases') ); ?>" size="50" />
			<br />
			<?php submit_button(); ?>
		</form>

<?php } 


function kill_spam_comments() {
    if (!empty($_POST['comment'])) {
        $post_comment_content = $_POST['comment'];
        $lower_case_comment = strtolower($_POST['comment']);

        // List of banned words in comments.
        // Comments with these words will be auto-deleted.
		$phrases_str = get_option('spamphrases');
		$phrases_str = preg_replace( '/[, ]/', ',', $phrases_str );
		$phrases = explode( ',', $phrases_str );

		$offset=0;
		
		foreach($phrases as $phrase) {
			if(!empty($phrase)) {
    			if(strpos($lower_case_comment, $phrase, $offset) !== false) {
					wp_die( __('Sorry, I will not tolerate comment spam. Banned term: ' . $phrase, 'No comment spam!') );
    			}
			}
		}
	}
}


add_action('init', 'kill_spam_comments');

?>