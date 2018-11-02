const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

registerBlockType(
	'rstore/domain-search',
	{
		title: __( 'Domain Search', 'reseller-store'),
		description: __( 'A search form for domain registrations.', 'reseller-store'),
		category: 'reseller-store',
		attributes: {
			post_id: {
				type: 'integer',
			}
		},
		edit: (props) => {
			return <p>{__('Domain Search', 'reseller-store')}</p>;
		},
		save: () => {
			// Rendering in PHP
			return null;
		},
	} );
