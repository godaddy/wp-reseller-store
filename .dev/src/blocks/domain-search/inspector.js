const { __ } = wp.i18n;
const { InspectorControls } = wp.blockEditor;
const { Fragment } = wp.element;
const {
	CheckboxControl,
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
} = wp.components;

const Inspector = ( { attributes, setAttributes } ) => {
	return (
		<InspectorControls>
			<p> { __( 'Domain Search', 'reseller-store' ) }</p>
			<PanelBody>
				<TextControl
					label={ __( 'Title', 'reseller-store' ) }
					value={ attributes.title }
					onChange={ ( title ) => setAttributes( { title: title } ) }
				/>
			</PanelBody>
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
				<SelectControl
					label={ __( 'Search Type', 'reseller-store' ) }
					onChange={ ( searchType ) => setAttributes( { search_type: searchType } ) }
					value={ attributes.search_type }
					options={ [
						{ value: 'standard', label: __( 'Standard Domain Search', 'reseller-store' ) },
						{ value: 'advanced', label: __( 'Advanced Domain Search', 'reseller-store' ) },
						{ value: 'transfer', label: __( 'Transfer Domain', 'reseller-store' ) },
					] }
				/>
				{ 'advanced' === attributes.search_type && (
					<Fragment>
						<RangeControl
							beforeIcon="arrow-left-alt2"
							afterIcon="arrow-right-alt2"
							label={ __( 'On page search result size', 'reseller-store' ) }
							value={ attributes.page_size }
							onChange={ ( pageSize ) => setAttributes( { page_size: pageSize } ) }
							min={ 1 }
							max={ 30 }
						/>
						<CheckboxControl
							label={ __( 'Display results in a modal', 'reseller-store' ) }
							checked={ attributes.modal }
							onChange={ ( modal ) => setAttributes( { modal } ) }
						/>						
					</Fragment>
				) }
				<Fragment>
					<CheckboxControl
						label={ __( 'Display results in a new tab', 'reseller-store' ) }
						checked={ attributes.new_tab }
						onChange={ ( new_tab ) => setAttributes( { new_tab } ) }
					/>
				</Fragment>
			</PanelBody>
		</InspectorControls>
	);
};

export default Inspector;
