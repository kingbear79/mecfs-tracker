jQuery(function ($) {
    $('#mecfs-tracker-form').on('submit', function (e) {
        e.preventDefault();
        const data = $(this).serialize();
        $.post(MECFSTracker.ajax, data + '&action=mecfs_save_entry&nonce=' + MECFSTracker.nonce)
            .done(() => alert('Gespeichert'))
            .fail(() => alert('Fehler'));
    });
});
