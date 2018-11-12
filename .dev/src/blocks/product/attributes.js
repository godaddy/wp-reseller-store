const { __ } = wp.i18n;

const attributes = {
	post_id: {
		type: 'number',
	},
	image_size: {
		type: 'string',
	},
	show_title: {
		type: 'boolean',
	},
	show_content: {
		type: 'boolean',
	},
	show_price: {
		type: 'boolean',
	},
	button_label: {
		type: 'string',
		default: __( 'Add to cart', 'reseller-store' )
	},
	content_height: {
		type: 'number',
		default: 250
	},
	text_more: {
		type: 'string',
		default: __( 'More info', 'reseller-store' )
	}
}

export default attributes;
