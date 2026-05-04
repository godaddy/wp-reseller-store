import { Fragment } from '@wordpress/element';
import { Button, Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import Media from './media';

const Editor = ({ media, post, attributes }) => {
	if (!post) {
		return (
			<p>
				<Spinner />
				{__('Loading Product Info', 'reseller-store')}
			</p>
		);
	}

	const contentStyle = {
		overflow: 'hidden',
		height:
			attributes.content_height > 0
				? `${attributes.content_height}px`
				: undefined
	};

	return (
		<Fragment>
			<Media post={post} media={media} size={attributes.image_size} />
			<div className="rstore-product-header">
				{attributes.show_title && (
					<h4 className="widget-title">{post.title.rendered}</h4>
				)}
				{attributes.layout_type === 'default' && attributes.show_price && (
					<div
						// post.price_html is escaped server-side via WordPress REST API
						dangerouslySetInnerHTML={{
							__html: post.price_html
						}}
					/>
				)}
				{attributes.layout_type === 'default' &&
					attributes.button_label.length > 0 && (
						<Button className="rstore-add-to-cart button btn btn-primary">
							{attributes.button_label}
						</Button>
					)}
				{attributes.show_content && (
					<div
						style={contentStyle}
						className="rstore-product-summary"
						// post.content.rendered is escaped server-side via WordPress REST API
						dangerouslySetInnerHTML={{
							__html: post.content.rendered
						}}
					/>
				)}
				{attributes.show_content && attributes.content_height > 0 && (
					<Button className="link" isLink={true}>
						{attributes.text_more}
					</Button>
				)}
				{attributes.layout_type === 'classic' && attributes.show_price && (
					<div
						// post.price_html is escaped server-side via WordPress REST API
						dangerouslySetInnerHTML={{
							__html: post.price_html
						}}
					/>
				)}
				{attributes.layout_type === 'classic' &&
					attributes.button_label.length > 0 && (
						<Button className="rstore-add-to-cart button btn btn-primary">
							{attributes.button_label}
						</Button>
					)}
			</div>
		</Fragment>
	);
};

export default Editor;
