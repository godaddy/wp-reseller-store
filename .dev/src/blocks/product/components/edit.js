import Inspector from './inspector';
import Editor from './editor';

const Edit = ( props ) => {
	return [
		<Inspector { ...props } />,
		<Editor { ...props } />
	];
};

export default Edit;
