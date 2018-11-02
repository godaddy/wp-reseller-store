import icon from './icon';
import productSelector from './productSelector';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { withSelect } = wp.data;

registerBlockType(
	'rstore/product',
	{
		title: __( 'Product', 'reseller-store'),
		description: __( 'Display a product post', 'reseller-store'),
		icon: {
			src: icon,
		},
		category: 'reseller-store',
		attributes: {
			post_id: {
				type: 'integer',
			}
		},
		edit:
			withSelect( (select, props) => {
				const posts = select('core').getEntityRecords('postType', 'reseller_product', {per_page: 100});
				return {
					posts
				};
			})(productSelector),
		save: () => {
			// Rendering in PHP
			return null;
		},
	} );
