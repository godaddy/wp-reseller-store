const { withSelect } = wp.data;

const mediaSelector = withSelect( ( select, { post } ) => {
	if ( ! post || ! post.featured_media ) {
		return {};
	}

	const media = select( 'core' ).getEntityRecord( 'root', 'media', post.featured_media );

	return {
		media,
	};
} );

const productSelector = withSelect( ( select, { attributes, setAttributes } ) => {
	const posts = select( 'core' ).getEntityRecords( 'postType', 'reseller_product', { per_page: 100 } );

	if ( posts && posts.length && attributes.post_id === undefined ) {
		setAttributes( { post_id: posts[ 0 ].id } );
	}

	const post = posts && posts.length ? posts.filter( ( p ) => {
		return p.id === attributes.post_id;
	} ).pop() : undefined;

	return {
		posts,
		post,
	};
} );

export {
	productSelector,
	mediaSelector,
};
