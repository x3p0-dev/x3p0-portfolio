<?php

/**
 * Settings store.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio\Settings;

use X3P0\Portfolio\Support\Definitions;

/**
 * Provides easy access to the plugin settings.
 */
class Store
{
	/**
	 * Stores a copy of the settings from the database option.
	 */
	protected array $settings;

	/**
	 * Returns the default settings.
	 */
	public function getDefaults(): array
	{
		return [
			'portfolio_title'        => __('Portfolio', 'x3p0-portfolio'),
			'portfolio_description'  => '',
			'portfolio_rewrite_base' => 'portfolio',
			'project_rewrite_base'   => '',
			'author_rewrite_base'    => 'authors',
			'category_rewrite_base'  => 'categories',
			'tag_rewrite_base'       => 'tags'
		];
	}

	/**
	 * Returns a setting or `null`.
	 */
	public function get(string $setting): mixed
	{
		return $this->has($setting) ? $this->settings[$setting] : null;
	}

	/**
	 * Determines whether a setting exists.
	 */
	public function has(string $setting): bool
	{
		return array_key_exists($setting, $this->all());
	}

	/**
	 * Returns all settings.
	 */
	public function all(): array
	{
		if (! isset($this->settings)) {
			$this->settings = wp_parse_args(
				get_option(Definitions::DATABASE_OPTION),
				$this->getDefaults()
			);
		}

		return $this->settings;
	}
}
