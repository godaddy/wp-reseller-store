const { __ } = wp.i18n;
const { InspectorControls } = wp.editor;

const {
	CheckboxControl,
	PanelBody,
	RangeControl,
	TextControl,
} = wp.components;

const Inspector = ( { attributes, setAttributes } ) => {
	return (
		<InspectorControls>
			<p> { __( 'Domain Search', 'reseller-store' ) }</p>
			<PanelBody>
				<TextControl
					label={ __( 'Placeholder', 'reseller-store' ) }
					value={ attributes.text_placeholder }
					onChange={ ( textPlaceholder ) => setAttributes( { text_placeholder: textPlaceholder } ) }
				/>
			</PanelBody>
			<PanelBody>
				<TextControl
					label={ __( 'Search Button', 'reseller-store' ) }
					value={ attributes.text_search }
					onChange={ ( textSearch ) => setAttributes( { text_search: textSearch } ) }
				/>
			</PanelBody>
			<PanelBody>
				<CheckboxControl
					label={ __( 'Redirect to search results page', 'reseller-store' ) }
					checked={ attributes.redirect }
					onChange={ ( redirect ) => setAttributes( { redirect } ) }
				/>
				{ ! attributes.redirect && (
					<RangeControl
						beforeIcon="arrow-left-alt2"
						afterIcon="arrow-right-alt2"
						label={ __( 'On page search result size', 'reseller-store' ) }
						value={ attributes.page_size }
						onChange={ ( pageSize ) => setAttributes( { page_size: pageSize } ) }
						min={ 1 }
						max={ 30 }
					/>
				) }
			</PanelBody>
		</InspectorControls>
	);
};

export default Inspector;
