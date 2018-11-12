import Media from "./media";

const { __ } = wp.i18n;
const {
	Button
} = wp.components;

const { withSelect } = wp.data;

const render = ( {media, post, attributes} ) => {

	if (!post) return null;

	const contentStyle = {
		overflow: 'hidden',
		height: attributes.content_height > 0 ? `${attributes.content_height}px`: undefined
	};

	return (
		<div>
			<Media media={ media } size={ attributes.image_size }/>
			<div className="rstore-product-header">
				<h4 className="widget-title">{ post.title.rendered }</h4>
				<div dangerouslySetInnerHTML= { { __html: post.price_html } }/>
				<Button className='rstore-add-to-cart button btn btn-primary' >{attributes.button_label}</Button>
				<div style={ contentStyle } className='rstore-product-summary' dangerouslySetInnerHTML= { { __html: post.content.rendered } }/>
				{ attributes.content_height > 0 && <Button className="link" isLink={true}>{attributes.text_more}</Button> }
			</div>
		</div>
	);
};

const Edit = withSelect((select, { posts, attributes } ) => {

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
})(render);

export default Edit;
