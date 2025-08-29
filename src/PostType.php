<?php

/**
 * Post type component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-portfolio
 */

declare(strict_types=1);

namespace X3P0\Portfolio;

use WP_Post;
use X3P0\Portfolio\Contracts\Bootable;
use X3P0\Portfolio\Settings\Store;
use X3P0\Portfolio\Support\Rewrite;

class PostType implements Bootable
{
	/**
	 * Post type name.
	 */
	public const NAME = 'portfolio_project';

	/**
	 * Sets up the default object state.
	 */
	public function __construct(
		protected Store   $settings,
		protected Rewrite $rewrite
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function boot(): void
	{
		// Register post types.
		add_action('init', [$this, 'register']);

		// Filter the post type archive title.
		add_filter('post_type_archive_title', [$this, 'archiveTitle'], 10, 2 );

		// Filter the "enter title here" text.
		add_filter('enter_title_here', [$this, 'enterTitleHere'], 10, 2);

		// Filter the bulk and post updated messages.
		add_filter('bulk_post_updated_messages', [$this, 'bulkPostUpdatedMessages'], 5, 2);
		add_filter('post_updated_messages', [$this, 'postUpdatedMessages'], 5);
	}

	/**
	 * Registers the post type.
	 */
	public function register(): void
	{
		register_post_type(self::NAME, [
			'description'         => $this->settings->get('description'),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'exclude_from_search' => false,
			'show_in_rest'        => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-portfolio',
			'can_export'          => true,
			'delete_with_user'    => false,
			'hierarchical'        => false,
			'has_archive'         => $this->rewrite->getPortfolioSlug(),
			'query_var'           => self::NAME,
			'capability_type'     => self::NAME,
			'map_meta_cap'        => true,

			// Post type capabilities.
			'capabilities' => [
				// meta caps (don't assign these to roles)
				'edit_post'              => 'edit_portfolio_project',
				'read_post'              => 'read_portfolio_project',
				'delete_post'            => 'delete_portfolio_project',

				// primitive/meta caps
				'create_posts'           => 'create_portfolio_projects',

				// primitive caps used outside of map_meta_cap()
				'edit_posts'             => 'edit_portfolio_projects',
				'edit_others_posts'      => 'edit_others_portfolio_projects',
				'publish_posts'          => 'publish_portfolio_projects',
				'read_private_posts'     => 'read_private_portfolio_projects',

				// primitive caps used inside of map_meta_cap()
				'read'                   => 'read',
				'delete_posts'           => 'delete_portfolio_projects',
				'delete_private_posts'   => 'delete_private_portfolio_projects',
				'delete_published_posts' => 'delete_published_portfolio_projects',
				'delete_others_posts'    => 'delete_others_portfolio_projects',
				'edit_private_posts'     => 'edit_private_portfolio_projects',
				'edit_published_posts'   => 'edit_published_portfolio_projects'
			],

			// Post type labels
			'labels' => [
				'name'                  => __('Projects',                   'x3p0-portfolio'),
				'singular_name'         => __('Project',                    'x3p0-portfolio'),
				'menu_name'             => __('Portfolio',                  'x3p0-portfolio'),
				'name_admin_bar'        => __('Project',                    'x3p0-portfolio'),
				'add_new'               => __('New Project',                'x3p0-portfolio'),
				'add_new_item'          => __('Add New Project',            'x3p0-portfolio'),
				'edit_item'             => __('Edit Project',               'x3p0-portfolio'),
				'new_item'              => __('New Project',                'x3p0-portfolio'),
				'view_item'             => __('View Project',               'x3p0-portfolio'),
				'view_items'            => __('View Projects',              'x3p0-portfolio'),
				'search_items'          => __('Search Projects',            'x3p0-portfolio'),
				'not_found'             => __('No projects found',          'x3p0-portfolio'),
				'not_found_in_trash'    => __('No projects found in trash', 'x3p0-portfolio'),
				'all_items'             => __('Projects',                   'x3p0-portfolio'),
				'featured_image'        => __('Project Image',              'x3p0-portfolio'),
				'set_featured_image'    => __('Set project image',          'x3p0-portfolio'),
				'remove_featured_image' => __('Remove project image',       'x3p0-portfolio'),
				'use_featured_image'    => __('Use as project image',       'x3p0-portfolio'),
				'insert_into_item'      => __('Insert into project',        'x3p0-portfolio'),
				'uploaded_to_this_item' => __('Uploaded to this project',   'x3p0-portfolio'),
				'filter_items_list'     => __('Filter projects list',       'x3p0-portfolio'),
				'items_list_navigation' => __('Projects list navigation',   'x3p0-portfolio'),
				'items_list'            => __('Projects list',              'x3p0-portfolio'),

				// Custom labels b/c WordPress doesn't have anything to handle this.
				'archive_title'         => $this->settings->get('portfolio_title'),
			],

			// The rewrite handles the URL structure.
			'rewrite' => [
				'slug'       => $this->rewrite->getProjectSlug(),
				'with_front' => false,
				'pages'      => true,
				'feeds'      => true,
				'ep_mask'    => EP_PERMALINK,
			],
			// What features the post type supports.
			'supports' => [
				'title',
				'editor',
				'excerpt',
				'author',
				'custom-fields',
				'thumbnail',
				'post-formats'
			]
		]);
	}

	/**
	 * Filters the post type archive title with the user-defined title.
	 */
	public function archiveTitle(string $title, string $post_type): string
	{
		return self::NAME === $post_type
			? $this->settings->get('portfolio_title')
			: $title;
	}

	/**
	 * Custom "enter title here" text.
	 */
	public function enterTitleHere(string $title, WP_Post $post): string
	{
		return self::NAME === $post->post_type
			? esc_html__('Enter project title', 'x3p0-portfolio')
			: $title;
	}

	/**
	 * Adds custom bulk post updated messages on the manage projects screen.
	 */
	public function bulkPostUpdatedMessages(array $messages, array $counts): array
	{
		$type = self::NAME;

		$messages[$type]['updated']   = _n('%s project updated.',                             '%s projects updated.',                               $counts['updated'],   'x3p0-portfolio');
		$messages[$type]['locked']    = _n('%s project not updated, somebody is editing it.', '%s projects not updated, somebody is editing them.', $counts['locked'],    'x3p0-portfolio');
		$messages[$type]['deleted']   = _n('%s project permanently deleted.',                 '%s projects permanently deleted.',                   $counts['deleted'],   'x3p0-portfolio');
		$messages[$type]['trashed']   = _n('%s project moved to the Trash.',                  '%s projects moved to the trash.',                    $counts['trashed'],   'x3p0-portfolio');
		$messages[$type]['untrashed'] = _n('%s project restored from the Trash.',             '%s projects restored from the trash.',               $counts['untrashed'], 'x3p0-portfolio');

		return $messages;
	}

	/**
	 * Adds custom post updated messages on the edit post screen.
	 */
	public function postUpdatedMessages(array $messages): array
	{
		global $post, $post_ID;

		$project_type = self::NAME;

		if ($project_type !== $post->post_type) {
			return $messages;
		}

		// Get permalink and preview URLs.
		$permalink   = get_permalink($post_ID);
		$preview_url = get_preview_post_link($post);

		// Translators: Scheduled project date format. See http://php.net/date
		$scheduled_date = date_i18n(__('M j, Y @ H:i', 'x3p0-portfolio'), strtotime($post->post_date));

		// Set up view links.
		$preview_link   = sprintf(' <a target="_blank" href="%1$s">%2$s</a>', esc_url($preview_url), esc_html__('Preview project', 'x3p0-portfolio'));
		$scheduled_link = sprintf(' <a target="_blank" href="%1$s">%2$s</a>', esc_url($permalink),   esc_html__('Preview project', 'x3p0-portfolio'));
		$view_link      = sprintf(' <a href="%1$s">%2$s</a>',                 esc_url($permalink),   esc_html__('View project',    'x3p0-portfolio'));

		// Post updated messages.
		$messages[$project_type] = array(
			1 => esc_html__('Project updated.', 'x3p0-portfolio') . $view_link,
			4 => esc_html__('Project updated.', 'x3p0-portfolio'),
			// Translators: %s is the date and time of the revision.
			5 => isset($_GET['revision']) ? sprintf(esc_html__('Project restored to revision from %s.', 'x3p0-portfolio'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
			6 => esc_html__('Project published.', 'x3p0-portfolio') . $view_link,
			7 => esc_html__('Project saved.', 'x3p0-portfolio'),
			8 => esc_html__('Project submitted.', 'x3p0-portfolio') . $preview_link,
			// Translators: %s is the scheduled date for the project.
			9 => sprintf(esc_html__('Project scheduled for: %s.', 'x3p0-portfolio'), "<strong>{$scheduled_date}</strong>") . $scheduled_link,
			10 => esc_html__('Project draft updated.', 'x3p0-portfolio') . $preview_link,
		);

		return $messages;
	}
}
