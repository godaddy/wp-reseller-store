const Media  = ( { media, size } ) => {

	if ( !media ) return null;

	if ( size in media.media_details.sizes ) {
	  return (<img title={media.title.rendered} src={media.media_details.sizes[size].source_url}></img>)
	}

	return null;

}

export default Media;

