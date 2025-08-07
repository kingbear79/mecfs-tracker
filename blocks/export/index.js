( function( blocks, element, i18n ) {
    const { registerBlockType } = blocks;
    const { createElement } = element;
    const { __ } = i18n;

    registerBlockType( 'mecfs/export', {
        title: __( 'MECFS Export-Button', 'mecfs-tracker' ),
        icon: 'download',
        category: 'widgets',
        edit: function() {
            return createElement( 'p', {}, __( 'MECFS Export-Button', 'mecfs-tracker' ) );
        },
        save: function() {
            return null;
        }
    } );
} )( window.wp.blocks, window.wp.element, window.wp.i18n );
