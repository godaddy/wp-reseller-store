const Media = ( { post, media, size } ) => {
	if ( 'icon' === size ) {
		return <div dangerouslySetInnerHTML={ { __html: post.icon_html } } />;
	}

	if ( ! media || 'none' === size ) {
		return null;
	}

	if ( size in media.media_details.sizes ) {
		return (
			<img title={ media.title.rendered } alt={ media.alt_text } src={ media.media_details.sizes[ size ].source_url }></img> );
	}

	return null;
};

export default Media;

