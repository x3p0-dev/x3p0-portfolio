<?php

/**
 * Editor component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio;

use X3P0\Portfolio\Contracts\Bootable;

/**
 * Implements actions and filters needed for the block editor.
 */
class Editor implements Bootable
{
	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		add_action('enqueue_block_editor_assets', [$this, 'enqueueAssets']);
	}

	/**
	 * Enqueues assets for the editor.
	 */
	public function enqueueAssets(): void
	{
		$dir = untrailingslashit(plugin_dir_path(__DIR__));
		$url = untrailingslashit(plugin_dir_url(__DIR__));

		$asset = include "{$dir}/public/js/editor.asset.php";

		wp_enqueue_script(
			'x3p0-portfolio-editor',
			"{$url}/public/js/editor.js",
			$asset['dependencies'],
			$asset['version'],
			true
		);
	}
}
