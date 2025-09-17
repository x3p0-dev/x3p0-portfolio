<?php

/**
 * The helpers functions file houses any necessary PHP functions for the plugin.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio;

/**
 * Stores a globally accessible single instance of the plugin in the static
 * `$app` variable. Devs can access any class/component by passing in a binding
 * reference via `app()->get($abstract)`.
 */
function app(): App
{
	static $app;

	if (! $app instanceof App) {
		do_action('x3p0/portfolio/init', $app = new App());
	}

	return $app;
}
