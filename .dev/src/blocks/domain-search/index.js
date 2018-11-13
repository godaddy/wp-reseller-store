import attributes from './attributes';
import Inspector from './inspector';
import Edit from './edit';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;

registerBlockType(
	'reseller-store/domain-search',
	{
		title: __( 'Domain Search', 'reseller-store' ),
		description: __( 'A search form for domain registrations.', 'reseller-store' ),
		category: 'reseller-store',
		keywords: [ 'reseller', 'domain', 'search' ],
		attributes,
		edit: ( props ) => {
			return (
				<Fragment>
					<Inspector { ...props } />
					<Edit { ...props } />
				</Fragment>
			);
		},
		save: () => {
			// Rendering in PHP
			return null;
		},
	} );
