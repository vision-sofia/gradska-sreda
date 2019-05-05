(() => {
    const surveayForm = $('#survayForm');

    function getQuestions() {

        $.ajax({
            url: '/geo/' + geoObject + '/q',
            success: function (result) {
               onSuccess(result);
            }
        });
    }

    getQuestions();

    let timeoutId;

    $(document).on('input propertychange', '.answer', function () {
        let data = {};
        let debounceTime = 0;

        if (this.tagName === 'TEXTAREA') {
            data = {
                'explanation': {
                    "answer": this.id,
                    "text": this.value,
                }
            };

            debounceTime = 400;
        } else {
            data =  {
                'answer': this.value,
            };
        }
       
        clearTimeout(timeoutId);

        timeoutId = setTimeout(function () {
            submitSurvay(data, this.value)
        }, debounceTime);
    });

    function submitSurvay(data, value) {
        $.ajax({
            type: 'POST',
            url: '/geo/' + geoObject + '/q',
            data: data,
            beforeSend: function () {
                if (document.getElementById(value)) {
                    document.getElementById(value).style.color = "green";
                }
            },
            success: function (result) {
                onSuccess(result);
            }
        });
    }

    function onSuccess(result) {
        let html = ``;
        let survey = result.survey;
        let progress = result.progress;
        let isSelectedParent = false;

        Object.keys(survey).forEach(function (item) {
            let answers = survey[item].answers;
            let question = survey[item];

            html += `<div class="survay-question mb-4">
                        <div class="survay-question-title  mb-1">
                            <i class="mr-1 fas ` + (question.isAnswered ? 'text-success fa-check' : 'fa-check text-black-50') + `"></i>
                            <h5 class="survay-question-title-text d-inline">` + question.title + `</h5>
                        </div>
                        <div class="survay-question pl-4">`;

            Object.keys(answers).forEach(function (answer) {
                if (answers[answer].parent === null) {
                    isSelectedParent = question.answers[answer].isSelected;

                    html += `<div class="d-flex flex-column">
                                <label class="survay-question-option ` + (answers[answer].isSelected ? 'is-answered' : '') + `" id="` + answers[answer].uuid + `">
                                    <input class="mr-1 answer" type="` + (question.hasMultipleAnswers ? 'checkbox' : 'radio') + `" name="answers[option][` + question.uuid + `][]"
                                    ` + (answers[answer].isSelected ? 'checked="checked"' : '') + ` value="` + answers[answer].uuid + `" /> ` + answers[answer].title + `
                                </label>
                            </div>`;

                    if (question.answers[answer].isFreeAnswer) {
                        html += `<textarea class="answer"></textarea>`;
                    }
                } else {
                    if (isSelectedParent === true) {
                        html += `<div class="pl-4 d-flex flex-column">
                                    <label class="survay-question-option ` + (answers[answer].isSelected ? 'is-answered' : '') + `" id="` + answers[answer].uuid + `">
                                        <input class="mr-1  answer" type="checkbox" name="answers[option][` + question.uuid + `][]"` + (answers[answer].isSelected ? 'checked="checked"' : '') + ` value="` + answers[answer].uuid + `" />
                                        ` + answers[answer].title +
                                    `</label>`;

                        if (answers[answer].isSelected && question.answers[answer].isFreeAnswer) {
                            html += `<label class="` + (answers[answer].isSelected ? 'is-answered' : '') + `">
                                        <textarea class="answer d-block" id="textarea-` + question.answers[answer].uuid + `]">` + question.answers[answer].explanation + `</textarea>
                                    </label>`;
                        }

                        html += '</div>';
                    }
                }
            });

            if (question.isAnswered) {
                html += `<div class="d-flex justify-content-end">
                            <button type="button" class="rem btn btn-sm btn-danger" name="answers[option][` + question.uuid + `][]" value="` + question.uuid + `">Изчисти</button>
                        </div>`;
            }

            html += `
                        </div>
                    </div>
                    `;
        });

        surveayForm.html(html);

        let progressbar = `<div class="progress mb-4">
                            <div class="progress-bar ` + (progress.percentage === 100 ? 'bg-success' : '') + `" role="progressbar" style="width: ` + progress.percentage + `%;"
                                 aria-valuenow="` + progress.percentage + `" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>`;

        $("#surveyProgressBar").html(progressbar);
    }

    $(document).on('click', '.rem', function () {
        let value = this.value;
        $.ajax({
            type: "POST",
            url: '/geo/' + geoObject + '/clear/' + value,

            success: function () {
                getQuestions();
            }
        });
    });

})();