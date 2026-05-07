import { registerBlockType } from '@wordpress/blocks';
import icon from './icon';
import Edit from './components/edit';
import metadata from './block.json';
import './editor.scss';

registerBlockType(metadata.name, {
	title: metadata.title,
	description: metadata.description,
	category: metadata.category,
	keywords: metadata.keywords,
	attributes: metadata.attributes,
	icon: { src: icon },
	edit: Edit,
	save: () => null
});
