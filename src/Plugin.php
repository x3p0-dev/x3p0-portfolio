<?php

/**
 * Plugin lifecycle helper.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio;

use X3P0\Portfolio\Support\Definitions;

/**
 * A static class that handles the various duties during the plugin's lifecycle.
 * This class includes static methods for activating, deactivating, uninstalling,
 * and bootstrapping the plugin.
 */
class Plugin
{
	/**
	 * Bootstraps the plugin and should be used as a callback on the
	 * `plugins_loaded` action hook.
	 */
	public static function boot(): void
	{
		app()->boot();
	}

	/**
	 * Runs when the plugin is activated and should be called via the
	 * `register_activation_hook()` function.
	 */
	public static function activate(): void
	{
		if ($role = get_role('administrator')) {
			// Taxonomy caps.
			$role->add_cap('manage_portfolio_categories');
			$role->add_cap('edit_portfolio_categories');
			$role->add_cap('delete_portfolio_categories');
			$role->add_cap('assign_portfolio_categories');

			$role->add_cap('manage_portfolio_tags');
			$role->add_cap('edit_portfolio_tags');
			$role->add_cap('delete_portfolio_tags');
			$role->add_cap('assign_portfolio_tags');

			// Post type caps.
			$role->add_cap('create_portfolio_projects');
			$role->add_cap('edit_portfolio_projects');
			$role->add_cap('edit_others_portfolio_projects');
			$role->add_cap('publish_portfolio_projects');
			$role->add_cap('read_private_portfolio_projects');
			$role->add_cap('delete_portfolio_projects');
			$role->add_cap('delete_private_portfolio_projects');
			$role->add_cap('delete_published_portfolio_projects');
			$role->add_cap('delete_others_portfolio_projects');
			$role->add_cap('edit_private_portfolio_projects');
			$role->add_cap('edit_published_portfolio_projects');
		}
	}

	/**
	 * Runs when the plugin is deactivated and should be called via the
	 * `register_deactivation_hook()` function.
	 */
	public static function deactivate(): void
	{}

	/**
	 * Runs when the plugin is uninstalled and should be called via the
	 * `register_uninstall_hook()` function.
	 */
	public static function uninstall(): void
	{
		if (! defined('WP_UNINSTALL_PLUGIN')) {
			wp_die(sprintf(
				__('%s should only be called when uninstalling the plugin.', 'x3p0-portfolio'),
				'<code>' . __METHOD__ . '</code>'
			));
		}

		// Delete plugin options.
		delete_option(Definitions::DATABASE_OPTION);

		// If the administrator role exists, remove added capabilities
		// that the plugin added.
		if ($role = get_role('administrator')) {

			// Portfolio category taxonomy caps.
			$role->remove_cap('manage_portfolio_categories');
			$role->remove_cap('edit_portfolio_categories');
			$role->remove_cap('delete_portfolio_categories');
			$role->remove_cap('assign_portfolio_categories');

			// Portfolio tag taxonomy caps.
			$role->remove_cap('manage_portfolio_tags');
			$role->remove_cap('edit_portfolio_tags');
			$role->remove_cap('delete_portfolio_tags');
			$role->remove_cap('assign_portfolio_tags');

			// Portfolio project post type caps.
			$role->remove_cap('create_portfolio_projects');
			$role->remove_cap('edit_portfolio_projects');
			$role->remove_cap('edit_others_portfolio_projects');
			$role->remove_cap('publish_portfolio_projects');
			$role->remove_cap('read_private_portfolio_projects');
			$role->remove_cap('delete_portfolio_projects');
			$role->remove_cap('delete_private_portfolio_projects');
			$role->remove_cap('delete_published_portfolio_projects');
			$role->remove_cap('delete_others_portfolio_projects');
			$role->remove_cap('edit_private_portfolio_projects');
			$role->remove_cap('edit_published_portfolio_projects');
		}
	}
}
