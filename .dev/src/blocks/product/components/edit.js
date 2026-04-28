import Inspector from './inspector';
import Editor from './editor';

const { Fragment, useEffect } = wp.element;

const Edit = (props) => {
	const { posts, attributes, setAttributes } = props;

	// Auto-select the first available product when the block is inserted without
	// a manually chosen product, so saving without touching the inspector doesn't
	// result in an invalid post_id on the rendered frontend.
	useEffect(() => {
		if (posts && posts.length && !attributes.post_id) {
			setAttributes({ post_id: posts[0].id.toString() });
		}
	}, [posts]);

	return (
		<Fragment>
			<Inspector {...props} />
			<Editor {...props} />
		</Fragment>
	);
};

export default Edit;
