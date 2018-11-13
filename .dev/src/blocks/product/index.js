import icon from './icon';
import { mediaSelector, productSelector } from './selectors';
import attributes from './attributes';
import edit from './components/edit';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

registerBlockType(
	'reseller-store/product',
	{
		title: __( 'Product', 'reseller-store' ),
		description: __( 'Display a product post', 'reseller-store' ),
		icon: {
			src: icon,
		},
		category: 'reseller-store',
		keywords: [ 'product', 'reseller' ],
		attributes,
		edit: productSelector( mediaSelector( edit ) ),
		save: () => {
			// Rendering in PHP
			return null;
		},
	} );
