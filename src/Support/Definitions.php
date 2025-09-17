<?php

/**
 * Plugin supporting constants.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio\Support;

/**
 * Stores constants used throughout the plugin.
 */
class Definitions
{
	/**
	 * Database option name.
	 */
	public const DATABASE_OPTION = 'x3p0_portfolio';

	/**
	 * $portfolio_slug portfolio project post type.
	 */
	public const POST_TYPE_PROJECT = 'portfolio_project';

	/**
	 * The portfolio project client post meta key.
	 */
	public const POST_META_CLIENT = 'portfolio_project_client';

	/**
	 * The portfolio project location post meta key.
	 */
	public const POST_META_LOCATION = 'portfolio_project_location';

	/**
	 * The portfolio project URL post meta key.
	 */
	public const POST_META_URL = 'portfolio_project_url';

	/**
	 * The portfolio category taxonomy.
	 */
	public const TAXONOMY_CATEGORY = 'portfolio_category';

	/**
	 * The portfolio tag taxonomy.
	 */
	public const TAXONOMY_TAG = 'portfolio_tag';
}
