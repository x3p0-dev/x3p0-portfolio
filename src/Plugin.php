<?php

/**
 * Plugin container implementation.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio;

use X3P0\Portfolio\Contracts\{Bootable, Container};

/**
 * The plugin class is a simple container used to store and reference the
 * various Plugin components. It doesn't support automatic dependency injection
 * (manual only) because it would be overkill for this project.
 */
class Plugin implements Container
{
	/**
	 * Stored definitions of single instances.
	 */
	private array $instances = [];

	/**
	 * Registers the default container bindings.
	 */
	public function __construct()
	{
		$this->registerDefaultBindings();
	}

	/**
	 * {@inheritdoc}
	 */
	#[\Override]
	public function boot(): void
	{
		foreach ($this->instances as $binding) {
			$binding instanceof Bootable && $binding->boot();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function instance(string $abstract, mixed $instance): void
	{
		$this->instances[$abstract] = $instance;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(string $abstract): mixed
	{
		return $this->has($abstract) ? $this->instances[$abstract] : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function has(string $abstract): bool
	{
		return isset($this->instances[$abstract]);
	}

	/**
	 * Registers the default bindings we need to run the Plugin.
	 */
	private function registerDefaultBindings(): void
	{
		$this->instance('settings.store', new Settings\Store());

		$this->instance('settings.registrar', new Settings\Registrar(
			$this->get('settings.store')
		));

		$this->instance('support.rewrite', new Support\Rewrite(
			$this->get('settings.store')
		));

		$this->instance('author', new Author(
			$this->get('settings.store'),
			$this->get('support.rewrite')
		));

		$this->instance('post.type', new PostType(
			$this->get('settings.store'),
			$this->get('support.rewrite')
		));

		$this->instance('taxonomy',  new Taxonomy($this->get('support.rewrite')));
		$this->instance('post.meta', new PostMeta());
		$this->instance('editor',    new Editor());

		// Admin only.
		if (is_admin()) {
			$this->instance('settings.page', new Settings\Page(
				$this->get('settings.store'))
			);
		}
	}
}
