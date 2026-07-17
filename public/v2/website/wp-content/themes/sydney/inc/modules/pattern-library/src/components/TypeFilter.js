import { useDispatch, useSelect } from '@wordpress/data';
import { Button, Flex } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { STORE_NAME } from '../store/index.js';
import { TYPE_ORDER } from '../constants.js';

export function TypeFilter( { patterns } ) {
	const selected = useSelect( ( select ) => select( STORE_NAME ).getSelectedType() );
	const { setType } = useDispatch( STORE_NAME );

	const types = [ ...new Set( patterns.flatMap( ( p ) => p.categories || [] ) ) ]
		.sort( ( a, b ) => {
			const ai = TYPE_ORDER.indexOf( a );
			const bi = TYPE_ORDER.indexOf( b );
			if ( ai === -1 && bi === -1 ) return a.localeCompare( b );
			if ( ai === -1 ) return 1;
			if ( bi === -1 ) return -1;
			return ai - bi;
		} );

	const allTypes = [
		{ slug: 'all', label: __( 'All', 'sydney' ) },
		...types.map( ( slug ) => {
			const raw = slug.replace( 'sydney-', '' );
			let label;
			if ( raw.toLowerCase() === 'cta' ) {
				label = 'CTA';
			} else {
				label = raw.replace( /-/g, ' ' ).replace( /^\w/, ( c ) => c.toUpperCase() );
			}
			return { slug, label };
		} ),
	];

	function handleClick( slug ) {
		setType( slug );
	}

	return (
		<Flex direction="column" gap={ 1 }>
			{ allTypes.map( ( { slug, label } ) => (
				<Button
					key={ slug }
					isPressed={ selected === slug }
					onClick={ () => handleClick( slug ) }
				>
					{ label }
				</Button>
			) ) }
		</Flex>
	);
}
