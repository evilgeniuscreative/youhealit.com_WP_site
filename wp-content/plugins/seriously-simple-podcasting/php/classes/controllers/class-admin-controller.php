<?php

namespace SeriouslySimplePodcasting\Controllers;

use SeriouslySimplePodcasting\Handlers\Castos_Handler;
use SeriouslySimplePodcasting\Renderers\Renderer;
use SeriouslySimplePodcasting\Traits\URL_Helper;
use SeriouslySimplePodcasting\Traits\Useful_Variables;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Controller
 *
 * @category    Class
 */
class Admin_Controller {

	use Useful_Variables;

	use URL_Helper;

	/**
	 * @var Renderer
	 */
	public $renderer;

	/**
	 * @var Castos_Handler
	 */
	private $castos_handler;

	/**
	 * Admin_Controller constructor.
	 *
	 * @param Renderer       $renderer       Renderer instance for rendering views.
	 * @param Castos_Handler $castos_handler Handler for Castos API interactions.
	 */
	public function __construct( $renderer, $castos_handler ) {
		$this->renderer       = $renderer;
		$this->castos_handler = $castos_handler;

		$this->init_useful_variables();
		$this->register_hooks();
	}

	/**
	 * Register all relevant front end hooks and filters
	 */
	public function register_hooks() {
		add_action( 'in_admin_header', [ $this, 'render_ssp_info_section' ] );
		add_action( 'current_screen', [ $this, 'disable_notices' ], 99 );
	}

	/**
	 * Disables redundant notices on SSP pages.
	 *
	 * @since %ver%
	 *
	 * @return void
	 */
	public function disable_notices() {
		if ( ! $this->is_ssp_admin_page() || ! $this->is_ssp_podcast_page() ) {
			return;
		}

		add_action(
            'admin_enqueue_scripts',
            function () {
				$this->remove_notice_actions();
			}
        );
	}

	/**
	 * Remove all admin notices except the priority 12 that is used by SSP.
	 *
	 * @param int $except_priority Priority to exclude from removal. Default is 12.
	 *
	 * @return void
	 */
	protected function remove_notice_actions( $except_priority = 12 ) {
		// Remove all admin notices except SSP that uses 12 priority level.
		$priorities = range( 1, 99 );
		foreach ( $priorities as $priority ) {
			if ( $except_priority == $priority ) {
				continue;
			}
			remove_all_actions( 'admin_notices', $priority );
		}
	}


	/**
	 * Renders the SSP info section on the admin page.
	 *
	 * @return void
	 */
	public function render_ssp_info_section(): void {
		if ( ! $this->is_ssp_admin_page() || $this->is_any_post_page() ) {
			return;
		}

		$is_connected = ssp_is_connected_to_castos();

		if ( $is_connected ) {
			$me   = $this->castos_handler->me();
			$plan = $me['plan'] ?? '';
		} else {
			$plan = '';
		}

		$this->renderer->render( 'admin/ssp-info-section', compact( 'plan', 'is_connected' ) );
	}
}
