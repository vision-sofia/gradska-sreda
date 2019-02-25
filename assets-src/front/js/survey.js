(() => {
    if (!document.getElementById('surveyForm')) {
        return;
    }

    $(document).on('change', '[data-parent]', function () {
        let parentId = $(this).data('parent');

        $(`#${parentId}`).prop('checked', true);

        deselectIrrelevant(parentId);
    });

    $(document).on('blur keyup', 'textarea[data-parent]', function () {
        if (!$(this).val()) { return; }

        let parentId = $(this).data('parent');

        $(`#${parentId}`).prop('checked', true);

        deselectIrrelevant(parentId);
    });

    $(document).on('change', '.custom-file-input', function () {
        let fileName = $(this).val().replace('C:\\fakepath\\', '');
        $(this).next('.custom-file-label').html(fileName);
    });

    function deselectIrrelevant(parentId) {
        [...document.querySelectorAll(`[data-parent`)].forEach((e) => {
            if (e.dataset.parent !== parentId) {
                e.checked = false;
                e.value = '';

                if (e.getAttribute('type') === 'file') {
                    $(e).next('.custom-file-label').html('Качете снимка');
                }
            }
        });
    }

    $(document).on('submit', '#surveyForm', function (e) {
        e.preventDefault();
        $('.loading').removeClass('d-none');
        postForm();
    });

    const objectId = $('#surveyForm').data('objectId');
    const surveyFormHolder = $('#surveyFormContent');

    getNewQuestion();

    function getNewQuestion() {
        $('.loading').removeClass('d-none');
        $.ajax({
            url: `/front-end/geo-object/${objectId}/survey`,
            success: function (results) {
                $('.loading').addClass('d-none');
                if (Object.keys(results).length > 0) {
                    if (results.status && results.status === 'no_question') {
                        printMessage(results.message || 'Няма повече въпроси');
                    } else {
                        questionPrinter(results);
                    }
                }
            }
        });
    }

    function postForm() {
        let form = new FormData();

        [...document.querySelectorAll('#surveyForm [name]')].forEach(el => {
            let name = el.getAttribute('name');
            let type = el.getAttribute('type');
            let isFile = type === 'file';
            let value = isFile ? (el.files[0] || null) : el.value;

            if ((type === 'checkbox' || type === 'radio') && !el.checked) {
                return;
            }

            form.append(name, value);
        });

        $.ajax({
            method: 'POST',
            data: form,
            cache: false,
            contentType: false,
            processData: false,
            url: `/front-end/geo-object/${objectId}/survey`,
            success: function () {
                getNewQuestion();
            }
        });
    }

    function printMessage(msg) {
        let formParent = $('#surveyForm').parent();
        let html = `<div class="row">
                        <div class="col">
                            <h3>${msg}</h3>
                        </div>
                    </div>`;
        formParent.html(html);
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
                                <input type="radio" id="${answer.id}" data-parent="${answer.id}" name="answers[][id]" value="${answer.id}" class="custom-control-input">
                                <label class="custom-control-label" for="${answer.id}">${answer.title}</label>
                            </div>
                        </div>`;

                if (answer.is_photo_enabled) {
                    html += `<div class="form-group pl-4">
                                <div class="custom-file">
                                    <input type="file" name="answers[][${answer.id }][photo]" data-parent="${answer.id}" class="custom-file-input" id="file_${answer.id }">
                                    <label class="custom-file-label" for="file_${answer.id }">Качете снимка</label>
                                </div>
                             </div>`;
                }
            } else {
                if (answer.is_free_answer) {
                    html += `<div class="form-group pl-4">
                                <label for="${answer.id }">${answer.title}</label>
                                <textarea name="answers[][${answer.id }][explanation]" id="${answer.id}" data-parent="${answer.parent}" rows="3" class="form-control w-100"></textarea>
                            </div>`;
                }
                else {
                    html += `<div class="form-group pl-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="${answer.id}" data-parent="${answer.parent}" name="answers[][id]" value="${answer.id}" class="custom-control-input">
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
