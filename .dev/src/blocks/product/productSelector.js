const { Spinner } = wp.components;
const { __ } = wp.i18n;

const productSelector = ( props ) => {
	const { posts, className, attributes, setAttributes } = props;

	const handleSelectChange = (event) => {
		setAttributes( { post_id: event.target.value } );
	}

	if (!posts) {
		return (
			<p className={className}>
				<Spinner/>
				{__('Loading Posts', 'reseller-store')}
			</p>
		);
	}
	if (0 === posts.length) {
		return <p>{__('No Posts', 'reseller-store')}</p>;
	}
	return (
		<select className={ className } onChange={ handleSelectChange } defaultValue = { attributes.post_id }>
			{posts.map(post => {
				return (
					<option className={ className } key={ post.id } value={post.id}>
						{post.title.rendered}
					</option>
				);
			})}
		</select>
	);
};

export default productSelector;