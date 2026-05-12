import { Fragment, useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import Inspector from './inspector';
import Editor from './editor';

const Edit = ({ attributes, setAttributes }) => {
	const { posts, post } = useSelect(
		(select) => {
			const allPosts = select('core').getEntityRecords(
				'postType',
				'reseller_product',
				{ per_page: 100 }
			);

			if (!allPosts || !allPosts.length) {
				return { posts: allPosts, post: undefined };
			}

			if (attributes.post_id === undefined) {
				return { posts: allPosts, post: allPosts[0] };
			}

			const selectedPost = allPosts.find(
				(p) => p.id.toString() === attributes.post_id
			);

			return { posts: allPosts, post: selectedPost ?? allPosts[0] };
		},
		[attributes.post_id]
	);

	const media = useSelect(
		(select) => {
			if (!post || !post.featured_media) {
				return undefined;
			}
			return select('core').getEntityRecord(
				'root',
				'media',
				post.featured_media
			);
		},
		[post]
	);

	// Auto-select the first available product when the block is inserted without
	// a manually chosen product, so saving without touching the inspector doesn't
	// result in an invalid post_id on the rendered frontend.
	useEffect(() => {
		if (posts && posts.length && !attributes.post_id) {
			setAttributes({ post_id: posts[0].id.toString() });
		}
	}, [posts]); // eslint-disable-line react-hooks/exhaustive-deps

	return (
		<Fragment>
			<Inspector
				posts={posts}
				media={media}
				attributes={attributes}
				setAttributes={setAttributes}
			/>
			<Editor post={post} media={media} attributes={attributes} />
		</Fragment>
	);
};

export default Edit;
