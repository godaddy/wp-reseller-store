import Inspector from './inspector';
import Edit from './edit';

const productSelector = ( props ) => {
	return [
		<Inspector { ...props } />,
		<Edit { ...props } />
	];
};

export default productSelector;