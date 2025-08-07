import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
    return (
        <div {...useBlockProps()}>
            <p>{__( 'Formular wird im Frontend gerendert.', 'mecfs-tracker' )}</p>
        </div>
    );
}
