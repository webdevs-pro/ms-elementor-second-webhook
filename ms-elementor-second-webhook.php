<?php
/**
 * Plugin Name: MS Elementor Form Second Webhook
 * Description: This plugin add second webhook action to Elementor form
 * Plugin URI:  https://magnificsoft.com/
 * Version:     1.0
 * Author:      Magnific Soft
 */


add_action( 'elementor_pro/init', function() {
	// Here its safe to include our action class file
	include_once( 'second-webhook.php' );

	// Instantiate the action class
	$second_webhook_action = new MS_Second_Webhook();

	// Register the action with form widget
	\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $second_webhook_action->get_name(), $second_webhook_action );
});