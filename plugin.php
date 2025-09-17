<?php

/**
 * Plugin Name:       X3P0: Portfolio
 * Plugin URI:        https://github.com/x3p0-dev/x3p0-portfolio
 * Description:       Manage, edit, and create portfolio projects.
 * Version:           1.0.0
 * Requires at least: 6.8
 * Requires PHP:      8.0
 * Author:            Justin Tadlock
 * Author URI:        https://justintadlock.com
 * Text Domain:       x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio;

# Prevent direct access.
defined('ABSPATH') || exit;

# Register autoloader for classes.
require_once 'src/Autoload.php';
Autoload::register();

# Load functions files.
require_once 'src/functions-helpers.php';

# Register activation hook.
register_activation_hook(__FILE__, [Plugin::class, 'activate']);

# Register uninstall hook.
register_uninstall_hook(__FILE__, [Plugin::class, 'uninstall']);

# Bootstrap the plugin.
add_action('plugins_loaded', [Plugin::class, 'boot'], PHP_INT_MIN);
