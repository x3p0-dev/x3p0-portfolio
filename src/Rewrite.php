<?php

/**
 * Rewrite component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio;

use X3P0\Portfolio\Contracts\Bootable;
use X3P0\Portfolio\Settings\Store;

class Rewrite implements Bootable
{
	public function __construct(protected Store $settings)
	{}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		add_action('init', [$this, 'register'], 5);
	}

	/**
	 * Adds custom rewrite rules for the plugin.
	 */
	public function register(): void
	{
		$project_type = PostType::NAME;
		$author_slug  = $this->getAuthorSlug();

		// Where to place the rewrite rules. If no rewrite base, put
		// them at the bottom.
		$where = $this->settings->get('author_rewrite_base') ? 'top' : 'bottom';

		add_rewrite_rule($author_slug . '/([^/]+)/page/?([0-9]{1,})/?$', 'index.php?post_type=' . $project_type . '&author_name=$matches[1]&paged=$matches[2]', $where);
		add_rewrite_rule($author_slug . '/([^/]+)/?$',                   'index.php?post_type=' . $project_type . '&author_name=$matches[1]',                   $where);
	}

	/**
	 * Returns the portfolio rewrite slug, typically used for the primary
	 * portfolio project archive.
	 */
	public function getPortfolioSlug(): string
	{
		return $this->settings->get('portfolio_rewrite_base');
	}

	/**
	 * Returns the project rewrite slug used for single projects.
	 */
	public function getProjectSlug(): string
	{
		$portfolio_base = $this->getPortfolioSlug();
		$project_base   = $this->settings->get('project_rewrite_base');

		return $project_base ? trailingslashit($portfolio_base) . $project_base : $portfolio_base;
	}

	/**
	 * Returns the author rewrite slug used for author archives.
	 */
	public function getAuthorSlug(): string
	{
		$portfolio_base = $this->getPortfolioSlug();
		$author_base    = $this->settings->get('author_rewrite_base');

		return $author_base ? trailingslashit($portfolio_base) . $author_base : $portfolio_base;
	}

	/**
	 * Returns the category rewrite slug used for category archives.
	 */
	public function getCategorySlug(): string
	{
		$portfolio_base = $this->getPortfolioSlug();
		$category_base  = $this->settings->get('category_rewrite_base');

		return $category_base ? trailingslashit($portfolio_base) . $category_base : $portfolio_base;
	}

	/**
	 * Returns the tag rewrite slug used for tag archives.
	 */
	public function getTagSlug(): string
	{
		$portfolio_base = $this->getPortfolioSlug();
		$tag_base       = $this->settings->get('tag_rewrite_base');

		return $tag_base ? trailingslashit($portfolio_base) . $tag_base : $portfolio_base;
	}
}
