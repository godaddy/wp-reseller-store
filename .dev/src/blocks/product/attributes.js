const { __ } = wp.i18n;

const attributes = {
	post_id: {
		type: 'string',
	},
	image_size: {
		type: 'string',
		default: 'icon',
	},
	show_title: {
		type: 'boolean',
		default: true,
	},
	show_content: {
		type: 'boolean',
		default: true,
	},
	show_price: {
		type: 'boolean',
		default: true,
	},
	button_label: {
		type: 'string',
		default: __( 'Add to cart', 'reseller-store' ),
	},
	content_height: {
		type: 'number',
		default: 250,
	},
	text_more: {
		type: 'string',
		default: __( 'More info', 'reseller-store' ),
	},
	redirect: {
		type: 'boolean',
		default: true,
	},
	layout_type: {
		type: 'string',
		default: 'default',
	},
};

export default attributes;
