<?php

/**
 * Taxonomy component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio;

use X3P0\Portfolio\Contracts\Bootable;

class Taxonomy implements Bootable
{
	/**
	 * Stores the category taxonomy name.
	 */
	public const CATEGORY_NAME = 'portfolio_category';

	/**
	 * Stores the tag taxonomy name.
	 */
	public const TAG_NAME = 'portfolio_tag';

	/**
	 * Sets up the default object state.
	 */
	public function __construct(protected Rewrite $rewrite)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function boot(): void
	{
		add_action('init', [$this, 'register'], 9);

		add_filter('term_updated_messages', [$this, 'termUpdatedMessages'], 5);
	}

	/**
	 * Registers the taxonomies.
	 */
	public function register(): void
	{
		register_taxonomy(self::CATEGORY_NAME, PostType::NAME, [
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'show_tagcloud'     => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'query_var'         => self::CATEGORY_NAME,
			'capabilities'      => [
				'manage_terms' => 'manage_portfolio_categories',
				'edit_terms'   => 'edit_portfolio_categories',
				'delete_terms' => 'delete_portfolio_categories',
				'assign_terms' => 'assign_portfolio_categories'
			],
			'labels' => [
				'name'                  => __('Project Categories',         'x3p0-portfolio'),
				'singular_name'         => __('Project Category',           'x3p0-portfolio'),
				'menu_name'             => __('Categories',                 'x3p0-portfolio'),
				'name_admin_bar'        => __('Category',                   'x3p0-portfolio'),
				'search_items'          => __('Search Categories',          'x3p0-portfolio'),
				'popular_items'         => __('Popular Categories',         'x3p0-portfolio'),
				'all_items'             => __('All Categories',             'x3p0-portfolio'),
				'edit_item'             => __('Edit Category',              'x3p0-portfolio'),
				'view_item'             => __('View Category',              'x3p0-portfolio'),
				'update_item'           => __('Update Category',            'x3p0-portfolio'),
				'add_new_item'          => __('Add New Category',           'x3p0-portfolio'),
				'new_item_name'         => __('New Category Name',          'x3p0-portfolio'),
				'not_found'             => __('No categories found.',       'x3p0-portfolio'),
				'no_terms'              => __('No categories',              'x3p0-portfolio'),
				'items_list_navigation' => __('Categories list navigation', 'x3p0-portfolio'),
				'items_list'            => __('Categories list',            'x3p0-portfolio'),

				// Hierarchical only.
				'select_name'       => __('Select Category',  'x3p0-portfolio'),
				'parent_item'       => __('Parent Category',  'x3p0-portfolio'),
				'parent_item_colon' => __('Parent Category:', 'x3p0-portfolio'),
			],

			// The rewrite handles the URL structure.
			'rewrite' => [
				'slug'         => $this->rewrite->getCategorySlug(),
				'with_front'   => false,
				'hierarchical' => false,
				'ep_mask'      => EP_NONE
			]
		]);

		// Set up the arguments for the portfolio tag taxonomy.
		register_taxonomy(self::TAG_NAME, PostType::NAME, [
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'show_tagcloud'     => true,
			'show_admin_column' => true,
			'hierarchical'      => false,
			'query_var'         => self::TAG_NAME,
			'capabilities'      => [
				'manage_terms' => 'manage_portfolio_tags',
				'edit_terms'   => 'edit_portfolio_tags',
				'delete_terms' => 'delete_portfolio_tags',
				'assign_terms' => 'assign_portfolio_tags'
			],
			'labels' => [
				'name'                  => __('Project Tags',         'x3p0-portfolio'),
				'singular_name'         => __('Project Tag',          'x3p0-portfolio'),
				'menu_name'             => __('Tags',                 'x3p0-portfolio'),
				'name_admin_bar'        => __('Tag',                  'x3p0-portfolio'),
				'search_items'          => __('Search Tags',          'x3p0-portfolio'),
				'popular_items'         => __('Popular Tags',         'x3p0-portfolio'),
				'all_items'             => __('All Tags',             'x3p0-portfolio'),
				'edit_item'             => __('Edit Tag',             'x3p0-portfolio'),
				'view_item'             => __('View Tag',             'x3p0-portfolio'),
				'update_item'           => __('Update Tag',           'x3p0-portfolio'),
				'add_new_item'          => __('Add New Tag',          'x3p0-portfolio'),
				'new_item_name'         => __('New Tag Name',         'x3p0-portfolio'),
				'not_found'             => __('No tags found.',       'x3p0-portfolio'),
				'no_terms'              => __('No tags',              'x3p0-portfolio'),
				'items_list_navigation' => __('Tags list navigation', 'x3p0-portfolio'),
				'items_list'            => __('Tags list',            'x3p0-portfolio'),

				// Non-hierarchical only.
				'separate_items_with_commas' => __('Separate tags with commas',      'x3p0-portfolio'),
				'add_or_remove_items'        => __('Add or remove tags',             'x3p0-portfolio'),
				'choose_from_most_used'      => __('Choose from the most used tags', 'x3p0-portfolio')
			],

			// The rewrite handles the URL structure.
			'rewrite' => [
				'slug'         => $this->rewrite->getTagSlug(),
				'with_front'   => false,
				'hierarchical' => false,
				'ep_mask'      => EP_NONE
			]
		]);
	}

	/**
	 * Filters the term updated messages in the admin.
	 */
	public function termUpdatedMessages(array $messages): array
	{
		// Add the portfolio category messages.
		$messages[self::CATEGORY_NAME] = [
			0 => '',
			1 => __('Category added.',       'x3p0-portfolio'),
			2 => __('Category deleted.',     'x3p0-portfolio'),
			3 => __('Category updated.',     'x3p0-portfolio'),
			4 => __('Category not added.',   'x3p0-portfolio'),
			5 => __('Category not updated.', 'x3p0-portfolio'),
			6 => __('Categories deleted.',   'x3p0-portfolio'),
		];

		// Add the portfolio tag messages.
		$messages[self::TAG_NAME] = [
			0 => '',
			1 => __('Tag added.',       'x3p0-portfolio'),
			2 => __('Tag deleted.',     'x3p0-portfolio'),
			3 => __('Tag updated.',     'x3p0-portfolio'),
			4 => __('Tag not added.',   'x3p0-portfolio'),
			5 => __('Tag not updated.', 'x3p0-portfolio'),
			6 => __('Tags deleted.',    'x3p0-portfolio'),
		];

		return $messages;
	}
}
