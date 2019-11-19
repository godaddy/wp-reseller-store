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

const productSelector = withSelect( ( select, { attributes } ) => {
	const posts = select( 'core' ).getEntityRecords( 'postType', 'reseller_product', { per_page: 100 } );

	if ( posts && posts.length ) {
		if ( attributes.post_id === undefined ) {
			return {
				posts,
				post: posts[ 0 ],
			};
		}

		const post = posts.find( ( p ) => {
			return p.id.toString() === attributes.post_id;
		} );

		if ( post === undefined ) {
			return {
				posts,
				post: posts[ 0 ],
			};
		}

		return {
			posts,
			post,
		};
	}
	return {};
} );

export {
	productSelector,
	mediaSelector,
};
