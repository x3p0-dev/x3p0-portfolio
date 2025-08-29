<?php

/**
 * Rewrite support class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio\Support;

use X3P0\Portfolio\Settings\Store;

/**
 * Custom methods for working with rewrite slugs for custom object types.
 */
class Rewrite
{
	/**
	 * Setups up the default object state.
	 */
	public function __construct(protected Store $settings)
	{}

	/**
	 * Returns the portfolio rewrite slug, typically used for the primary
	 * portfolio project archive.
	 */
	public function getPortfolioSlug(): string
	{
		return $this->settings->get('portfolio_rewrite_base');
	}

	/**
	 * Helper for appending a path to the portfolio slug.
	 */
	public function appendToSlug(string $path): string
	{
		$portfolio_slug = $this->getPortfolioSlug();

		return $path ? trailingslashit($portfolio_slug) . $path : $portfolio_slug;
	}

	/**
	 * Returns the project rewrite slug used for single projects.
	 */
	public function getProjectSlug(): string
	{
		return $this->appendToSlug($this->settings->get('project_rewrite_base'));
	}

	/**
	 * Returns the author rewrite slug used for author archives.
	 */
	public function getAuthorSlug(): string
	{
		return $this->appendToSlug($this->settings->get('author_rewrite_base'));
	}

	/**
	 * Returns the category rewrite slug used for category archives.
	 */
	public function getCategorySlug(): string
	{
		return $this->appendToSlug($this->settings->get('category_rewrite_base'));
	}

	/**
	 * Returns the tag rewrite slug used for tag archives.
	 */
	public function getTagSlug(): string
	{
		return $this->appendToSlug($this->settings->get('tag_rewrite_base'));
	}
}
