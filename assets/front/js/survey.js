(() => {

    function questions() {

        $.ajax({
            url: '/geo/' + geoObject + '/q',

            success: function (result) {
                let html = ``;
                let survey = result.survey;
                let progress = result.progress;
                let isSelectedParent = false;
                Object.keys(survey).forEach(function (item) {
                    let answers = survey[item].answers;
                    let question = survey[item];
                    let indicatorStyle;

                    if (question.isAnswered && question.isCompleted) {
                        indicatorStyle = 'text-success';
                    } else if (question.isAnswered && !question.isCompleted) {
                        indicatorStyle = 'text-warning';
                    } else {
                        indicatorStyle = 'text-black-50';
                    }

                    html += `<div class="mb-4 " >
                            <i class="fas fa-check ${indicatorStyle}"></i> <h5 class="d-inline">${question.title}</h5>`;

                    Object.keys(answers).forEach(function (answer) {
                        if (answers[answer].parent === null) {
                            isSelectedParent = question.answers[answer].isSelected;

                            html += `<p class="mb-0"><label id="${answers[answer].uuid}"><input class="answer" type="` + (question.hasMultipleAnswers ? 'checkbox' : 'radio') + `" name="answers[option][${question.uuid}][]"
                                    ` + (answers[answer].isSelected ? 'checked="checked"' : '') + ` value="${answers[answer].uuid}" /> ${answers[answer].title}</label></p>`;

                            if (question.answers[answer].isFreeAnswer) {
                                html += `<textarea></textarea>`;
                            }
                        } else {
                            if (isSelectedParent === true) {
                                html += `<div style="padding-left:32px;"><label class="" id="${answers[answer].uuid}"><input class="answer" type="checkbox"
									name="answers[option][${question.uuid}][]"
                                    ` + (answers[answer].isSelected ? 'checked="checked"' : '') + ` value="${answers[answer].uuid}" /> ${answers[answer].title}</label>`;

                                if (answers[answer].isSelected && question.answers[answer].isFreeAnswer) {
                                    html += `<textarea class="d-block" id="textarea-${question.answers[answer].uuid}]">${question.answers[answer].explanation}</textarea>`;
                                }

                                html += '</div>';
                            }
                        }
                    });

                    if (question.isAnswered) {
                        html += `<button type="button" class="rem btn btn-sm btn-danger rounded-0" name="answers[option][${question.uuid}][]" value="${question.uuid}"> Изчисти отговора</button>`;
                    }

                    html += `</div>`;
                });

                $("#survey").html(html);

                const article = document.querySelector('#surveyProgress');

                let progressHtml = `
                                Попълнена анкета ${progress.percentage}%<br/> 
                                <div class="progress mb-4 rounded-0">
                                    <div class="progress-bar rounded-0 pt-1 ` + (progress.percentage === 100 ? 'bg-success' : '') + `" role="progressbar" style="width: ${progress.percentage}%;"
                                         aria-valuenow="${progress.percentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>`;

                if (progress.percentage === 100) {
                    progressHtml += `<div class="mt-3">
                                        <a href="/geo/${article.dataset.objectId}/result" class="btn btn-primary rounded-0 pt-2">Виж рейтинга</a>
                                    </div>`;
                }

                $("#surveyProgress").html(progressHtml);
            }
        });
    }

    questions();

    function saveExplanation(id, value) {

        $.ajax({
            type: "POST",
            url: '/geo/' + geoObject + '/q',
            data: {
                'explanation': {
                    "answer": id,
                    "text": value,
                }
            },
            beforeSend: function () {
                if (document.getElementById(id)) {
                    document.getElementById(id).style.backgroundColor = "green";
                    setTimeout(function () {
                        document.getElementById(id).style.backgroundColor = "white";
                    }, 200);
                }
            },
            success: function (data) {

            },
        });
    }

    let timeoutId;

    $(document).on('input propertychange change', 'textarea', function () {
        let $this = $(this);

        clearTimeout(timeoutId);

        timeoutId = setTimeout(function () {
            saveExplanation($this.attr("id"), $this.val());
        }, 600);
    });


    $(document).on('click', '.answer', function () {
        let value = this.value;
        $.ajax({
            type: "POST",
            url: '/geo/' + geoObject + '/q',
            data: {
                'answer': value,
            },
            beforeSend: function () {
                if (document.getElementById(value)) {
                    document.getElementById(value).style.color = "green";
                }
            },
            success: function () {
                questions();
            }
        });
    });

    $(document).on('click', '.rem', function () {
        let value = this.value;
        $.ajax({
            type: "POST",
            url: '/geo/' + geoObject + '/clear/' + value,

            success: function () {
                questions();
            }
        });
    });

})();