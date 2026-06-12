import { Button } from '@wordpress/components';

const Edit = ({ attributes }) => {
	return (
		<div className="widget rstore-domain widget_search">
			{attributes.title && (
				<div className="widget rstore-domain">{attributes.title}</div>
			)}
			<div className="search-form rstore-domain-form">
				<input
					className="search-field"
					placeholder={attributes.text_placeholder}
				/>
				<Button
					className="search-submit button btn btn-primary"
					isDefault={true}
				>
					{attributes.text_search}
				</Button>
			</div>
		</div>
	);
};

export default Edit;
