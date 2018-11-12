const { __ } = wp.i18n;
const { InspectorControls } = wp.editor;

const {
	CheckboxControl,
	PanelBody,
	RangeControl,
	Spinner,
	SelectControl,
	TextControl
} = wp.components;

const Inspector  = ( {posts, attributes, setAttributes} ) => {

	if (!posts) {
		return (
			<p>
				<Spinner/>
				{__('Loading Posts', 'reseller-store')}
			</p>
		);
	}

	if (0 === posts.length) {
		return <p>{__('No products found', 'reseller-store')}</p>;
	}

	const options = posts.map(post => {
		return {value: post.id, label: post.title.rendered}
	});

	if (attributes.post_id === undefined) {

		setAttributes({post_id: options[0].value})

	}
	return (
		<InspectorControls>
			<PanelBody>
				<SelectControl
					label={__('Select Product', 'reseller-store')}
					onChange={post_id => setAttributes({post_id})}
					value={attributes.post_id}
					options={options}
				/>
			</PanelBody>

			<PanelBody>
				<SelectControl
					label={__('Image Size', 'reseller-store')}
					onChange={image_size => setAttributes({image_size})}
					value={attributes.image_size}
					options={[
						{value: 'thumbnail', label: __('Thumbnail', 'reseller-store')},
						{value: 'medium', label: __('Medium Resolution', 'reseller-store')},
						{value: 'large', label: __('Large Resolution', 'reseller-store')},
						{value: 'full', label: __('Original Resolution', 'reseller-store')},
						{value: 'none', label: __('Hide image', 'reseller-store')},
					]}
				/>
			</PanelBody>
			<PanelBody>
				<TextControl
					label={__('Button', 'reseller-store')}
					value={attributes.button_label}
					onChange={button_label => setAttributes({button_label})}
				/>
			</PanelBody>
			<PanelBody>
				<CheckboxControl
					label={__('Set content height', 'reseller-store')}
					checked={attributes.content_height > 0}
					onChange={checked => {
						setAttributes({content_height: checked ? 250 : 0})
					}}
				/>
				{attributes.content_height > 0 &&
				<div>
					<RangeControl
						beforeIcon="arrow-left-alt2"
						afterIcon="arrow-right-alt2"
						label={__('Product content height', 'reseller-store')}
						value={attributes.content_height}
						onChange={content_height => setAttributes({content_height})}
						min={1}
						max={500}
					/>
					<TextControl
						label={__('More info button text', 'reseller-store')}
						value={attributes.text_more}
						onChange={text_more => setAttributes({text_more})}
					/>
				</div>
				}
			</PanelBody>
		</InspectorControls>
	);
};

export default Inspector;
