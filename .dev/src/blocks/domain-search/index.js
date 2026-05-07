import { registerBlockType } from '@wordpress/blocks';
import { Fragment } from '@wordpress/element';
import icon from './icon';
import Inspector from './inspector';
import Edit from './edit';
import metadata from './block.json';
import './editor.scss';

registerBlockType(metadata.name, {
	title: metadata.title,
	description: metadata.description,
	category: metadata.category,
	keywords: metadata.keywords,
	attributes: metadata.attributes,
	icon: { src: icon },
	edit: (props) => (
		<Fragment>
			<Inspector {...props} />
			<Edit {...props} />
		</Fragment>
	),
	save: () => null
});
