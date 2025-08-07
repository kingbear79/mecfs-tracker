( function( blocks, element, i18n ) {
    const { registerBlockType } = blocks;
    const { createElement } = element;
    const { __ } = i18n;

    registerBlockType( 'mecfs/chart', {
        title: __( 'MECFS Tracker Diagramm', 'mecfs-tracker' ),
        icon: 'chart-bar',
        category: 'widgets',
        edit: function() {
            return createElement( 'p', {}, __( 'MECFS Tracker Diagramm', 'mecfs-tracker' ) );
        },
        save: function() {
            return null;
        }
    } );
} )( window.wp.blocks, window.wp.element, window.wp.i18n );
