<?php

/**
 * Post meta component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio;

use X3P0\Portfolio\Contracts\Bootable;
use X3P0\Portfolio\Support\Definitions;

/**
 * Registers post meta for the plugin.
 */
class PostMeta implements Bootable
{
	/**
	 * {@inheritDoc}
	 */
	public function boot(): void
	{
		add_action('init', [$this, 'register']);
	}

	/**
	 * Registers post meta.
	 */
	public function register(): void
	{
		$type = Definitions::PROJECT_POST_TYPE;

		register_post_meta($type, Definitions::CLIENT_POST_META, [
			'label'             => __('Client', 'portfolio'),
			'sanitize_callback' => 'wp_strip_all_tags',
			'single'            => true,
			'show_in_rest'      => true,
			'type'              => 'string'
		]);

		register_post_meta($type, Definitions::LOCATION_POST_META, [
			'label'             => __('Location', 'x3p0-portfolio'),
			'sanitize_callback' => 'wp_strip_all_tags',
			'single'            => true,
			'show_in_rest'      => true,
			'type'              => 'string'
		]);

		register_post_meta($type, Definitions::URL_POST_META, [
			'label'             => __('URL', 'x3p-portfolio'),
			'sanitize_callback' => 'wp_strip_all_tags',
			'single'            => true,
			'show_in_rest'      => true,
			'type'              => 'string'
		]);
	}
}
