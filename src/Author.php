<?php

/**
 * Author component.
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
 * Handles actions and filters related to portfolio project authors.
 */
class Author implements Bootable
{
	/**
	 * Sets up the default object state.
	 */
	public function __construct(protected Rewrite $rewrite)
	{}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		add_filter('author_link', [$this, 'authorLinkFilter'], 10, 3);
		add_filter('document_title_parts', [$this, 'documentTitlePartsFilter']);
		add_filter('get_the_archive_title', [$this, 'archiveTitleFilter']);
		add_filter('get_the_archive_description', [$this, 'archiveDescriptionFilter']);
	}

	/**
	 * Filters the WordPress post author link to point it toward the
	 * author's portfolio archive page whenever the link is associated with
	 * a portfolio project.
	 */
	public function authorLinkFilter(
		string $link,
		int $author_id,
		string $author_nicename
	): string {
		if (PostType::NAME !== get_post_type()) {
			return $link;
		}

		if ($GLOBALS['wp_rewrite']->using_permalinks()) {
			$slug = $this->rewrite->getAuthorSlug();
			return home_url(user_trailingslashit("{$slug}/{$author_nicename}"));
		}

		return add_query_arg(
			[
				'post_type' => ccp_get_project_post_type(),
				'author_name' => $author_nicename
			],
			home_url('/')
		);
	}

	/**
	 * Helper to determine if viewing an author archive.
	 */
	protected function isAuthorArchive(): bool
	{
		return is_post_type_archive(PostType::NAME) && is_author();
	}

	/**
	 * Filters the document title to show the author's name when viewing an
	 * author portfolio archive.
	 */
	public function documentTitlePartsFilter(array $title): array
	{
		if ($this->isAuthorArchive()) {
			$title['title'] = get_the_author_meta('display_name', absint(get_query_var('author')));
		}

		return $title;
	}

	/**
	 * Filters the archive title to show the author's name when viewing an
	 * author portfolio archive.
	 */
	public function archiveTitleFilter(string $title): string
	{
		return $this->isAuthorArchive()
			? get_the_author_meta('display_name', absint(get_query_var('author')))
			: $title;
	}

	/**
	 * Filters the archive description to show the author's name when
	 * viewing an author portfolio archive.
	 */
	public function archiveDescriptionFilter(string $title): string
	{
		return $this->isAuthorArchive()
			? get_the_author_meta('description', absint(get_query_var('author')))
			: $title;
	}
}
