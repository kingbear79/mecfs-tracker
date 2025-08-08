jQuery(function ($) {
    function addRangeListeners(context) {
        $('input[type="range"]', context).each(function () {
            const $range = $(this);
            const $value = $range.next('.range-value');
            $value.text($range.val());
            $range.on('input', function () {
                $value.text($range.val());
            });
        });
    }

    addRangeListeners(document);

    let symptomIndex = 0;
    $('#add-symptom').on('click', function () {
        const row = $('<tr class="symptom-field"><td><input type="text" class="mecfs-form-control" name="new_symptoms[' + symptomIndex + '][label]" placeholder="Symptom" /></td><td><input type="range" class="mecfs-form-range" name="new_symptoms[' + symptomIndex + '][severity]" min="0" max="100" value="0" /><span class="range-value">0</span><div class="slider-scale"><span>0</span><span>100</span></div></td></tr>');
        $('#symptom-table-body').append(row);
        addRangeListeners(row);
        symptomIndex++;
    });

    $('#mecfs-tracker-form').on('submit', function (e) {
        e.preventDefault();
        let sum = 0;
        const questions = 4;
        for (let i = 1; i <= questions; i++) {
            const val = parseInt($('select[name="bell_q' + i + '"]').val(), 10);
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

        const emotionQuestions = [
            { id: 'aengste', invert: true },
            { id: 'stimmung', invert: false },
            { id: 'antrieb', invert: false },
            { id: 'depressivität', invert: true }
        ];
        let emotionSum = 0;
        let invertCount = 0;
        let nonInvertCount = 0;
        emotionQuestions.forEach(q => {
            let val = parseInt($('input[name="emotion[' + q.id + ']"]').val(), 10);
            if (q.invert) {
                val = 10 - val;
                invertCount++;
            } else {
                nonInvertCount++;
            }
            emotionSum += val;
        });
        const minSum = nonInvertCount * 1;
        const maxSum = invertCount * 9 + nonInvertCount * 10;
        const emotion = Math.round(((emotionSum - minSum) / (maxSum - minSum)) * 100);
        $('input[name="emotion"]').val(emotion);

        const data = $(this).serialize();
        $.post(MECFSTracker.ajax, data + '&action=mecfs_save_entry&nonce=' + MECFSTracker.nonce)
            .done(() => alert('Gespeichert'))
            .fail(() => alert('Fehler'));
    });
});
