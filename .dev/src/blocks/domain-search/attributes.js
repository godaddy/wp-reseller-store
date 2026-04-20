const { __ } = wp.i18n;

const attributes = {
	title: {
		type: 'string',
	},
	text_placeholder: {
		type: 'string',
		default: __( 'Find your perfect domain name', 'reseller-store' ),
	},
	text_search: {
		type: 'string',
		default: __( 'Search', 'reseller-store' ),
	},
	search_type: {
		type: 'string',
		default: 'standard',
	},
	modal: {
		type: 'boolean',
		default: false,
	},
	new_tab: {
		type: 'boolean',
		default: false,
	},
	page_size: {
		type: 'number',
		default: 5,
	},
};

export default attributes;
