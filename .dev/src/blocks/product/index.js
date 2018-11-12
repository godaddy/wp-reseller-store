import icon from './icon';
import productSelector from './productSelector';
import attributes from './attributes';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

const { withSelect } = wp.data;

const getPosts = withSelect( (select) => {
		const posts = select('core').getEntityRecords('postType', 'reseller_product', {per_page: 100});
		return {
			posts
		};
	})(productSelector);

registerBlockType(
	'reseller-store/product',
	{
		title: __( 'Product', 'reseller-store'),
		description: __( 'Display a product post', 'reseller-store'),
		icon: {
			src: icon,
		},
		category: 'reseller-store',
		keywords:['product', 'reseller'],
		attributes,
		edit: getPosts,
		save: () => {
			// Rendering in PHP
			return null;
		},
	} );
