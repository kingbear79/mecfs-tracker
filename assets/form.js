jQuery(function ($) {
    $('#mecfs-tracker-form').on('submit', function (e) {
        e.preventDefault();
        let sum = 0;
        const questions = 5;
        for (let i = 1; i <= questions; i++) {
            const val = parseInt($('input[name="bell_q' + i + '"]:checked').val(), 10);
            if (isNaN(val)) {
                alert('Bitte alle Fragen beantworten.');
                return;
            }
            sum += val;
        }
        const bell = Math.round((sum / (questions * 4)) * 100);
        $('input[name="bell_score"]').val(bell);
        const data = $(this).serialize();
        $.post(MECFSTracker.ajax, data + '&action=mecfs_save_entry&nonce=' + MECFSTracker.nonce)
            .done(() => alert('Gespeichert'))
            .fail(() => alert('Fehler'));
    });
});
