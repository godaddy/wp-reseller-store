const {
	Button,
	TextControl,
} = wp.components;

const Edit = ( { attributes } ) => {
	return (
		<div className="widget rstore-domain widget_search">
			<div style={ { float: 'left' } }>
				<TextControl type="text" className="search-field" placeholder={ attributes.text_placeholder } />
			</div>
			<Button className="search-submit" isDefault={ true }>{ attributes.text_search }</Button>
		</div>
	);
};

export default Edit;
