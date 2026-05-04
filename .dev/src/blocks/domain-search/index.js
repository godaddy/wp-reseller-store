import { registerBlockType } from '@wordpress/blocks';
import { Fragment } from '@wordpress/element';
import icon from './icon';
import Inspector from './inspector';
import Edit from './edit';
import metadata from './block.json';
import './editor.scss';

registerBlockType(metadata.name, {
	...metadata,
	icon: { src: icon },
	edit: (props) => (
		<Fragment>
			<Inspector {...props} />
			<Edit {...props} />
		</Fragment>
	),
	save: () => null
});
