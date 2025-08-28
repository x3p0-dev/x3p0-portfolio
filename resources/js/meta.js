import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

import {
	RangeControl,
	__experimentalInputControl as InputControl,
	__experimentalNumberControl as NumberControl,
	__experimentalVStack as VStack
} from '@wordpress/components';

registerPlugin('x3p0-portfolio', {
	render: () => {
		const postType = useSelect(
			(select) => select('core/editor').getCurrentPostType(),
			[]
		);

		const [meta, setMeta] = useEntityProp('postType', postType, 'meta');

		if ('portfolio_project' !== postType) {
			return null;
		}

		return (
			<PluginDocumentSettingPanel
				title={ __('Project Data', 'themeslug') }
			>
				<VStack>
					<InputControl
						label={ __('URL', 'themeslug') }
						value={ meta?.portfolio_project_url }
						onChange={ (value) => setMeta({
							...meta,
							portfolio_project_url: value || null
						}) }
					/>
					<InputControl
						label={ __('Client', 'themeslug') }
						value={ meta?.portfolio_project_client }
						onChange={ (value) => setMeta({
							...meta,
							portfolio_project_client: value || null
						}) }
					/>
					<InputControl
						label={ __('Location', 'themeslug') }
						value={ meta?.portfolio_project_location }
						onChange={ (value) => setMeta({
							...meta,
							portfolio_project_location: value || null
						}) }
					/>
				</VStack>
			</PluginDocumentSettingPanel>
		);
	}
});
