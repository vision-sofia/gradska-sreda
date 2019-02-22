(() => {
    if (!document.getElementById('surveyForm')) {
        return;
    }

    $(document).on('change', '[data-parent]', function () {
        let parentId = $(this).data('parent');

        $(`#${parentId}`).prop('checked', true);

        deselectIrrelevant(parentId);
    });

    $(document).on('blur keyup', '[data-txt-parent]', function () {
        if (!$(this).val()) { return; }

        let parentId = $(this).data('txtParent');

        $(`#${parentId}`).prop('checked', true);

        deselectIrrelevant(parentId);
    });

    function deselectIrrelevant(parentId) {
        [...document.querySelectorAll(`[data-parent`)].forEach((e) => {
            if ($(e).data('parent') !== parentId) {
                $(e).prop('checked', false);
            }
        });
        [...document.querySelectorAll(`[data-txt-parent`)].forEach((e) => {
            if ($(e).data('txtParent') !== parentId) {
                $(e).val('');
            }
        });
    }

    $(document).on('submit', '#surveyForm', function (e) {
        e.preventDefault();
        getNewQuestion();
    });

    const objectId = $('#surveyForm').data('objectId');
    const surveyFormHolder = $('#surveyFormContent');

    $.ajax({
        url: `/front-end/geo-object/${objectId}/survey`,
        success: function (results) {
            console.log(results);
            if (Object.keys(results).length !== 0) {
                questionPrinter(results);
            }
        }
    });

    function getNewQuestion() {
        $.ajax({
            method: 'POST',
            data: $('#surveyForm').serialize(),
            url: `/front-end/geo-object/${objectId}/survey`,
            success: function (results) {
                console.log(results);
                if (Object.keys(results).length !== 0) {
                    questionPrinter(results);
                }
            }
        });
    }

    function questionPrinter(results) {
        let html = `<div class="form-group">
                        <h5 class="font-weight-bold m-0">${results.question}</h5>
                    </div>`;
        html += `<div class="py-3">`;
        results.answers.forEach((answer) =>{
            if (!answer.parent) {
                html += `<div class="form-group">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="${answer.id}" data-parent="${answer.id}" name="answers[]" value="${answer.id}" class="custom-control-input">
                                <label class="custom-control-label" for="${answer.id}">${answer.title}</label>
                            </div>
                        </div>`;
            } else {
                if (answer.is_free_answer) {
                    html += `<div class="form-group pl-4">
                                <label for="${answer.id }}">${answer.title}</label>
                                <textarea name="answers[]" id="${answer.id}" data-txt-parent="${answer.parent}" rows="3" class="form-control w-100"></textarea>
                            </div>`;
                } else {
                    html += `<div class="form-group pl-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="${answer.id}" data-parent="${answer.parent}" name="answers[]" value="${answer.id}" class="custom-control-input">
                                    <label class="custom-control-label" for="${answer.id}">${answer.title}</label>
                                </div>
                            </div>`;
                }
            }
        });
        html += `</div>`;

        surveyFormHolder.html(html);
    }
})();
