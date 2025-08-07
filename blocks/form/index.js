( function( blocks, element, i18n ) {
    const { registerBlockType } = blocks;
    const { createElement } = element;
    const { __ } = i18n;

    registerBlockType( 'mecfs/form', {
        title: __( 'MECFS Tracker Formular', 'mecfs-tracker' ),
        icon: 'clipboard',
        category: 'widgets',
        edit: function() {
            return createElement( 'p', {}, __( 'MECFS Tracker Formular', 'mecfs-tracker' ) );
        },
        save: function() {
            return null;
        }
    } );
} )( window.wp.blocks, window.wp.element, window.wp.i18n );
