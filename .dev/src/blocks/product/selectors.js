const { withSelect } = wp.data;

const mediaSelector = withSelect((select, { posts, attributes } ) => {

	if (!posts) return {};

	const post = posts.filter( post => {
		return post.id == attributes.post_id;
	}).pop();

	if ( !post ) return {};

	if ( !post.featured_media ) return  { post };

	const media = select('core').getEntityRecord('root', 'media', post.featured_media);

	return {
		post,
		media
	};
});

const productSelector = withSelect( (select) => {
	const posts = select('core').getEntityRecords('postType', 'reseller_product', {per_page: 100});
	return {
		posts
	};
});

export  {
	productSelector,
	mediaSelector
}