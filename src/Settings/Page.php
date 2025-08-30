<?php

/**
 * Settings page component.
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
 * Implements the settings page for the plugin in the WordPress backend.
 */
class Page implements Bootable
{
	/**
	 * Stores the admin page name. Note that this is set to the result of
	 * `add_submenu_page()`, which will return a string if the page was
	 * added but `false` if it was not (in cases where the user doesn't have
	 * permission to view it).
	 */
	private string|false $page = false;

	/**
	 * Sets up the default object state.
	 */
	public function __construct(protected Store $store)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function boot(): void
	{
		add_action('admin_menu', [$this, 'register']);
		add_action('admin_init', [$this, 'addSectionsAndFields']);
	}

	/**
	 * Registers the admin menu page with WordPress.
	 */
	public function register(): void
	{
		$this->page = add_submenu_page(
			'edit.php?post_type=' . Definitions::PROJECT_POST_TYPE,
			esc_html__('Portfolio Settings', 'x3p0-portfolio'),
			esc_html__('Settings', 'x3p0-portfolio'),
			'manage_options',
			'x3p0-portfolio-settings',
			[$this, 'display']
		);
	}

	public function addSectionsAndFields(): void
	{
		// Bail if the menu page wasn't added.
		if (! $this->page) {
			return;
		}

		// Register page sections.
		add_settings_section(
			'general',
			esc_html__('General', 'x3p0-portfolio'),
			[$this, 'displayGeneralSection'],
			$this->page
		);

		add_settings_section(
			'permalinks',
			esc_html__('Permalinks', 'x3p0-portfolio'),
			[$this, 'displayPermalinksSection'],
			$this->page
		);

		// Register settings fields for each section.
		$sections = [
			'general' => [
				'portfolio_title'       => esc_html__('Title', 'x3p0-portfolio'),
				'portfolio_description' => esc_html__('Description', 'x3p0-portfolio'),
			],
			'permalinks' => [
				'portfolio_rewrite_base' => esc_html__('Portfolio Base', 'x3p0-portfolio'),
				'project_rewrite_base'   => esc_html__('Project Slug', 'x3p0-portfolio'),
				'category_rewrite_base'  => esc_html__('Category Slug', 'x3p0-portfolio'),
				'tag_rewrite_base'       => esc_html__('Tag Slug', 'x3p0-portfolio'),
				'author_rewrite_base'    => esc_html__('Author Slug', 'x3p0-portfolio')
			]
		];

		foreach ($sections as $section => $fields) {
			foreach ($fields as $field => $title) {
				add_settings_field(
					$field,
					$title,
					$this->getFieldCallback($field),
					$this->page,
					$section,
					$this->getFieldArgs($field)
				);
			}
		}
	}

	/**
	 * Displays the settings page.
	 */
	public function display(): void
	{
		// Flush the rewrite rules if the settings were updated.
		if (isset($_GET['settings-updated'])) {
			flush_rewrite_rules();
		} ?>

		<div class="wrap">
			<h1><?php esc_html_e('Portfolio Settings', 'x3p0-portfolio'); ?></h1>

			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php settings_fields(Definitions::DATABASE_OPTION); ?>
				<?php do_settings_sections($this->page); ?>
				<?php submit_button(esc_attr__('Update Settings', 'x3p0-portfolio')); ?>
			</form>
		</div>
	<?php }

	/**
	 * Displays the General section.
	 */
	public function displayGeneralSection(): void
	{
		printf(
			'<p class="description">%s</p>',
			esc_html__('General portfolio settings for your site.', 'x3p0-portfolio')
		);
	}

	/**
	 * Displays the Permalinks section.
	 */
	public function displayPermalinksSection(): void
	{
		printf(
			'<p class="description">%s</p>',
			esc_html__('Set up custom permalinks for the portfolio section on your site.', 'x3p0-portfolio')
		);
	}

	/**
	 * Portfolio title field callback.
	 */
	public function displayPortfolioTitleField(): void
	{
		printf(
			'<input type="text" id="%s" name="%s" value="%s" /><br /><span class="description">%s</span>',
			esc_attr($this->getFieldId('portfolio_title')),
			esc_attr($this->getFieldName('portfolio_title')),
			esc_attr($this->getFieldValue('portfolio_title')),
			esc_html__('The name of your portfolio. May be used for the portfolio page title and other places, depending on your theme.', 'x3p0-portfolio')
		);
	}

	/**
	 * Render portfolio description field.
	 */
	public function displayPortfolioDescriptionField(): void
	{
		wp_editor(
			$this->getFieldValue('portfolio_description'),
			$this->getFieldId('portfolio_description'),
			[
				'textarea_name'    => $this->getFieldName('portfolio_description'),
				'drag_drop_upload' => true,
				'editor_height'    => 150
			]
		);

		printf(
			'<p><span class="description">%s</span></p>',
			esc_html__('Your portfolio description. This may be shown by your theme on the portfolio page.', 'x3p0-portfolio')
		);
	}

	public function displayPortfolioRewriteBaseField(): void
	{
		$this->displayRewriteBaseInputField('portfolio_rewrite_base');
	}

	public function displayProjectRewriteBaseField(): void
	{
		$this->displayRewriteBaseInputField('project_rewrite_base');
	}

	public function displayCategoryRewriteBaseField(): void
	{
		$this->displayRewriteBaseInputField('category_rewrite_base');
	}

	public function displayTagRewriteBaseField(): void
	{
		$this->displayRewriteBaseInputField('tag_rewrite_base');
	}

	public function displayAuthorRewriteBaseField(): void
	{
		$this->displayRewriteBaseInputField('author_rewrite_base');
	}

	private function getFieldCallback(string $field): callable
	{
		return [
			$this,
			lcfirst(str_replace('_', '', ucwords("display_{$field}_field", '_')))
		];
	}

	private function getFieldArgs(string $field): array
	{
		return [
			'label_for' => $this->getFieldId($field)
		];
	}

	private function getFieldId(string $field): string
	{
		return Definitions::DATABASE_OPTION . "_{$field}";
	}

	private function getFieldName(string $field): string
	{
		return Definitions::DATABASE_OPTION . "[{$field}]";
	}

	private function getFieldValue(string $field): string
	{
		return $this->store->get($field);
	}

	private function displayRewriteBaseInputField(string $field): void
	{
		$url = 'portfolio_rewrite_base' === $field
			? trailingslashit(home_url('/'))
			: trailingslashit(home_url($this->store->get('portfolio_rewrite_base')));

		printf(
			'<code>%s</code></code><input type="text" id="%s" name="%s" value="%s" />',
			esc_url($url),
			esc_attr($this->getFieldId($field)),
			esc_attr($this->getFieldName($field)),
			esc_attr($this->getFieldValue($field))
		);
	}
}
