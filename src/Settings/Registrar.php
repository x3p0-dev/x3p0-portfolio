<?php

/**
 * Settings registrar component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio\Settings;

use X3P0\Portfolio\Contracts\Bootable;
use X3P0\Portfolio\Support\Definitions;

/**
 * Registers the plugin settings with WordPress.
 */
class Registrar implements Bootable
{
	/**
	 * Sets up the default object state.
	 */
	public function __construct(protected Store $store)
	{}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		add_action('init', [$this, 'register']);
	}

	/**
	 * Register the database option with WordPress.
	 */
	public function register(): void
	{
		register_setting(Definitions::DATABASE_OPTION, Definitions::DATABASE_OPTION, [
			'type'              => 'array',
			'label'             => __('Portfolio Settings', 'x3p0-portfolio'),
			'description'       => __('An array of settings for the X3P0: Portfolio plugin.', 'x3p0-portfolio'),
			'default'           => $this->store->getDefaults(),
			'show_in_rest'      => false,
			'sanitize_callback' => [$this, 'sanitize']
		]);
	}

	/**
	 * Sanitization callback for the registered database option.
	 */
	public function sanitize(array $settings): array
	{
		$defaults = $this->store->getDefaults();

		$base = static::sanitizeRewriteSlug($settings['portfolio_rewrite_base']);

		// Set rewrite slugs.
		$settings['portfolio_rewrite_base'] = $base ?: $defaults['portfolio_rewrite_base'];
		$settings['project_rewrite_base']   = static::sanitizeRewriteSlug($settings['project_rewrite_base']);
		$settings['category_rewrite_base']  = static::sanitizeRewriteSlug($settings['category_rewrite_base']);
		$settings['tag_rewrite_base']       = static::sanitizeRewriteSlug($settings['tag_rewrite_base']);
		$settings['author_rewrite_base']    = static::sanitizeRewriteSlug($settings['author_rewrite_base']);

		// Set the portfolio title.
		$settings['portfolio_title'] = $settings['portfolio_title']
			? strip_tags($settings['portfolio_title'])
			: esc_html__('Portfolio', 'x3p0-portfolio');

		// Kill evil scripts.
		$settings['portfolio_description'] = stripslashes(
			wp_filter_post_kses(addslashes($settings['portfolio_description']))
		);

		// -------------------------------------------------------------
		// The following handles permalink conflicts between the various
		// objects. The order of these should not be changed. Each
		// conflict is handled based on an object type that is higher in
		// the hierarchy. If neither have a rewrite base defined, the
		// object higher in the hierarchy wins out and the secondary
		// rewrite rule base is assigned its fallback.
		// -------------------------------------------------------------
		$priorities = [
			'project_rewrite_base',
			'category_rewrite_base',
			'author_rewrite_base',
			'tag_rewrite_base'
		];

		foreach ($priorities as $i => $primary) {
			if ($settings[$primary]) {
				continue;
			}

			foreach (array_slice($priorities, $i + 1) as $secondary) {
				if (! $settings[$secondary]) {
					$settings[$secondary] = $defaults[$secondary];
				}
			}
		}

		// Return the validated/sanitized settings.
		return $settings;
	}

	/**
	 * Helper function for sanitizing rewrite slugs.
	 */
	private static function sanitizeRewriteSlug(string $slug): string
	{
		return $slug ? trim(strip_tags($slug), '/') : '';
	}
}
