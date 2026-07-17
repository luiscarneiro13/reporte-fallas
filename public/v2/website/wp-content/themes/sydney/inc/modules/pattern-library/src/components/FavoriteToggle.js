import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { STORE_NAME } from '../store/index.js';

export function FavoriteToggle( { patternName } ) {
	const isFavorited = useSelect(
		( s ) => s( STORE_NAME ).isFavorite( patternName ),
		[ patternName ]
	);
	const { toggleFavorite } = useDispatch( STORE_NAME );

	const label = isFavorited
		? __( 'Remove from favorites', 'sydney' )
		: __( 'Add to favorites', 'sydney' );

	function handleClick() {
		toggleFavorite( patternName );
	}

	return (
		<button
			type="button"
			className={ `sydney-pattern-library-favorite-toggle${ isFavorited ? ' is-favorited' : '' }` }
			aria-label={ label }
			aria-pressed={ isFavorited }
			onClick={ handleClick }
		>
			<svg
				width="14"
				height="14"
				viewBox="0 0 24 24"
				fill={ isFavorited ? 'currentColor' : 'none' }
				stroke="currentColor"
				strokeWidth="2"
				strokeLinecap="round"
				strokeLinejoin="round"
				aria-hidden="true"
				focusable="false"
			>
				<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
			</svg>
		</button>
	);
}
