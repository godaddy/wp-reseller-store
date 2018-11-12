const { __ } = wp.i18n;
const { InspectorControls } = wp.editor;

const {
	CheckboxControl,
	PanelBody,
	RangeControl,
	TextControl,
} = wp.components;


const Inspector  = ({ attributes, setAttributes }) => {
	return (
		<InspectorControls>
			<p> {__('Domain Search', 'reseller-store' )}</p>
			<PanelBody>
				<TextControl
					label={ __( 'Placeholder', 'reseller-store' ) }
					value={ attributes.text_placeholder }
					onChange={ text_placeholder => setAttributes( { text_placeholder } ) }
				/>
			</PanelBody>
			<PanelBody>
				<TextControl
					label={ __( 'Search Button', 'reseller-store' ) }
					value={ attributes.text_search }
					onChange={ text_search => setAttributes( { text_search } ) }
				/>
			</PanelBody>
			<PanelBody>
				<CheckboxControl
					label={ __( 'Redirect to search results page', 'reseller-store' ) }
					checked={ attributes.redirect }
					onChange={ redirect => setAttributes( { redirect } ) }
				/>
				{ ! attributes.redirect && (
					<RangeControl
						beforeIcon="arrow-left-alt2"
						afterIcon="arrow-right-alt2"
						label={ __( 'On page search result size', 'reseller-store' ) }
						value={ attributes.page_size }
						onChange={ page_size => setAttributes( { page_size } ) }
						min={ 1 }
						max={ 30 }
					/>
				) }
			</PanelBody>
		</InspectorControls>
	);
};

export default Inspector;