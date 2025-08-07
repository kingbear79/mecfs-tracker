jQuery(function ($) {
    let symptomIndex = 0;
    $('#add-symptom').on('click', function () {
        $('#custom-symptoms').append('<div class="symptom-field"><input type="text" name="new_symptoms[' + symptomIndex + '][label]" placeholder="Symptom" /> <input type="range" name="new_symptoms[' + symptomIndex + '][severity]" min="0" max="100" /></div>');
        symptomIndex++;
    });
    $('#mecfs-tracker-form').on('submit', function (e) {
        e.preventDefault();
        let sum = 0;
        const questions = 4;
        for (let i = 1; i <= questions; i++) {
            const val = parseInt($('input[name="bell_q' + i + '"]:checked').val(), 10);
            if (isNaN(val)) {
                alert('Bitte alle Fragen beantworten.');
                return;
            }
            sum += val;
        }
        const total = 30 + 40 + 40 + 40;
        const max = 100;
        const bell = Math.round((sum / total) * max);
        $('input[name="bell_score"]').val(bell);
        const data = $(this).serialize();
        $.post(MECFSTracker.ajax, data + '&action=mecfs_save_entry&nonce=' + MECFSTracker.nonce)
            .done(() => alert('Gespeichert'))
            .fail(() => alert('Fehler'));
    });
});
