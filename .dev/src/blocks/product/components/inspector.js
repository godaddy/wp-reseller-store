const { __ } = wp.i18n;
const { InspectorControls } = wp.editor;

const {
	CheckboxControl,
	PanelBody,
	RangeControl,
	SelectControl,
	Spinner,
	TextControl,
} = wp.components;

const Inspector = ( { posts, media, attributes, setAttributes } ) => {
	if ( ! posts ) {
		return (
			<InspectorControls>
				<Spinner />
				{ __( 'Loading Posts', 'reseller-store' ) }
			</InspectorControls>
		);
	}

	if ( 0 === posts.length ) {
		return <p>{ __( 'No products found', 'reseller-store' ) }</p>;
	}

	const products = posts.map( ( post ) => {
		return { value: post.id, label: post.title.rendered };
	} );

	let mediaOptions = [];

	if ( media ) {
		const keys = Object.keys( media.media_details.sizes );

		mediaOptions = keys.map( ( size ) => {
			return { value: size, label: size };
		} );

		mediaOptions.push( { value: 'none', label: __( 'Hide image', 'reseller-store' ) } );
	}

	return (
		<InspectorControls>
			<PanelBody>
				<SelectControl
					label={ __( 'Select Product', 'reseller-store' ) }
					onChange={ ( postId ) => setAttributes( { post_id: postId.toString() } ) }
					value={ attributes.post_id }
					options={ products }
				/>
			</PanelBody>
			<PanelBody>
				<SelectControl
					label={ __( 'Image Size', 'reseller-store' ) }
					onChange={ ( imageSize ) => setAttributes( { image_size: imageSize } ) }
					value={ attributes.image_size }
					options={ mediaOptions }
				/>
			</PanelBody>
			<PanelBody>
				<TextControl
					label={ __( 'Button', 'reseller-store' ) }
					value={ attributes.button_label }
					onChange={ ( buttonLabel ) => setAttributes( { button_label: buttonLabel } ) }
				/>
			</PanelBody>
			<PanelBody>
				<CheckboxControl
					label={ __( 'Set content height', 'reseller-store' ) }
					checked={ attributes.content_height > 0 }
					onChange={ ( checked ) => {
						setAttributes( { content_height: checked ? 250 : 0 } );
					} }
				/>
				{ attributes.content_height > 0 &&
				<div>
					<RangeControl
						beforeIcon="arrow-left-alt2"
						afterIcon="arrow-right-alt2"
						label={ __( 'Product content height', 'reseller-store' ) }
						value={ attributes.content_height }
						onChange={ ( contentHeight ) => setAttributes( { content_height: contentHeight } ) }
						min={ 1 }
						max={ 500 }
					/>
					<TextControl
						label={ __( 'More info button text', 'reseller-store' ) }
						value={ attributes.text_more }
						onChange={ ( textMore ) => setAttributes( { text_more: textMore } ) }
					/>
				</div>
				}
			</PanelBody>
		</InspectorControls>
	);
};

export default Inspector;
