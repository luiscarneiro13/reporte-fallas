import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export function SpinnerFallback() {
	return (
		<div className="sydney-pattern-library-loading" aria-live="polite" aria-busy="true">
			<Spinner />
			<span className="screen-reader-text">
				{ __( 'Loading patterns...', 'sydney' ) }
			</span>
		</div>
	);
}
