import Inspector from './inspector';
import Editor from './editor';

const { Fragment } = wp.element;

const Edit = ( props ) => {
	return (
		<Fragment>
			<Inspector { ...props } />,
			<Editor { ...props } />,
		</Fragment>
	);
};

export default Edit;
