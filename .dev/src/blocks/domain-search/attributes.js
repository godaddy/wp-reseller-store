const { __ } = wp.i18n;

const attributes = {
	redirect: {
		type: 'boolean',
		default: true,
	},
	text_placeholder: {
		type: 'string',
		default: __( 'Find your perfect domain name', 'reseller-store' ),
	},
	text_search: {
		type: 'string',
		default: __( 'Search', 'reseller-store' ),
	},
	page_size: {
		type: 'number',
		default: 5,
	},
};

export default attributes;
