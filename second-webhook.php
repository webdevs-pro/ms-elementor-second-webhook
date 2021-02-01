<?php
/**
 * Class MS_Second_Webhook
 * @see https://developers.elementor.com/custom-form-action/
 * Custom elementor form action after submit to add a subsciber to
 * Sendy list via API 
 */
class MS_Second_Webhook extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	public function get_name() {
		return 'webhook_2';
	}

	public function get_label() {
		return __( 'Webhook 2', 'elementor-pro' );
	}

	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_webhook_2',
			[
				'label' => __( 'Webhook 2', 'elementor-pro' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'webhooks_2',
			[
				'label' => __( 'Webhook URL', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'https://your-webhook-url.com', 'elementor-pro' ),
				'label_block' => true,
				'separator' => 'before',
				'description' => __( 'Enter the integration URL (like Zapier) that will receive the form\'s submitted data.', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		$widget->add_control(
			'webhooks_advanced_data_2',
			[
				'label' => __( 'Advanced Data', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
				'render_type' => 'none',
			]
		);

		$widget->end_controls_section();
	}

	public function on_export( $element ) {}

	public function run( $record, $ajax_handler ) {
		$settings = $record->get( 'form_settings' );

		if ( empty( $settings['webhooks_2'] ) ) {
			return;
		}

		if ( isset( $settings['webhooks_advanced_data_2'] ) && 'yes' === $settings['webhooks_advanced_data_2'] ) {
			$body['form'] = [
				'id' => $settings['id'],
				'name' => $settings['form_name'],
			];

			$body['fields'] = $record->get( 'fields' );
			$body['meta'] = $record->get( 'meta' );
		} else {
			$body = $record->get_formatted_data( true );
			$body['form_id'] = $settings['id'];
			$body['form_name'] = $settings['form_name'];
		}

		$args = [
			'body' => $body,
		];

		/**
		 * Forms webhook request arguments.
		 *
		 * Filters the request arguments delivered by the form webhook when executing
		 * an ajax request.
		 *
		 * @since 1.0.0
		 *
		 * @param array       $args   Webhook request arguments.
		 * @param Form_Record $record An instance of the form record.
		 */
		$args = apply_filters( 'elementor_pro/forms/webhooks/request_args', $args, $record );

		$response = wp_remote_post( $settings['webhooks_2'], $args );

		/**
		 * Elementor form webhook response.
		 *
		 * Fires when the webhook response is retrieved.
		 *
		 * @since 1.0.0
		 *
		 * @param \WP_Error|array $response The response or WP_Error on failure.
		 * @param Form_Record     $record   An instance of the form record.
		 */
		do_action( 'elementor_pro/forms/webhooks/response', $response, $record );

		if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			$ajax_handler->add_admin_error_message( 'Webhook Error' );
		}
	}
}
