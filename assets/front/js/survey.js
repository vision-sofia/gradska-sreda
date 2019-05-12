export class Survey {
    surveayForm;
    geoObjectUUID = ''; 
    timeoutId;
    layer;

    constructor() {
        this.pathVoteSurveyContaineEl = document.getElementById('path-vote-suevey');
        if (!this.pathVoteSurveyContaineEl) {
            return;
        }
        this.surveayForm = this.pathVoteSurveyContaineEl.querySelector('#surveyForm');
        this.events();
    }

    events() {
        $(document).on('click', '[data-survey-open]', () => {
            this.open();
        });

        $(document).on('click', '[data-survey-close]', () => {
            this.close();
        });
        
        $(document).on('input propertychange', '.answer', (e) => {
            const target = e.target;
            let data = {};
            let debounceTime = 0;
    
            if (target.tagName === 'TEXTAREA') {
                data = {
                    'explanation': {
                        "answer": target.id,
                        "text": target.value,
                    }
                };
    
                debounceTime = 400;
            } else {
                data = {
                    'answer': target.value,
                };
            }
           
            clearTimeout(this.timeoutId); 
    
            this.timeoutId = setTimeout(() => {
                this.submitSurvay(data, target.value)
            }, debounceTime);
        });

        $(document).on('click', '.rem', () => {
            let value = this.value;
            $.ajax({
                type: "POST",
                url: '/geo/' + this.geoObjectUUID + '/clear/' + value,
    
                success: () => {
                    getQuestions();
                }
            });
        });
    }

   getQuestions() {
        $.ajax({
            url: '/geo/' + this.geoObjectUUID + '/q',
            success: (result) => {
               this.onSuccess(result);
            }
        });
    }

    submitSurvay(data, value) {
        $.ajax({
            type: 'POST',
            url: '/geo/' + this.geoObjectUUID + '/q',
            data: data,
            beforeSend: () => {
                if (document.getElementById(value)) {
                    document.getElementById(value).style.color = "green";
                }
            },
            success: (result) => {
                this.onSuccess(result);
            }
        });
    };

    onSuccess(result) {
        let html = ``;
        let survey = result.survey;
        let progress = result.progress;
        let isSelectedParent = false;

        Object.keys(survey).forEach((item) => {
            let answers = survey[item].answers;
            let question = survey[item];

            html += `<div class="survay-question mb-4">
                        <div class="survay-question-title  mb-1">
                            <i class="mr-1 fas ` + (question.isAnswered ? 'text-success fa-check' : 'fa-check text-black-50') + `"></i>
                            <h5 class="survay-question-title-text d-inline">` + question.title + `</h5>
                        </div>
                        <div class="survay-question pl-4">`;

            Object.keys(answers).forEach((answer) => {
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

        this.surveayForm.innerHTML = html;

        let progressHTML = `<div class="progress mb-4">
                                <div class="progress-bar ` + (progress.percentage === 100 ? 'bg-success' : '') + `" role="progressbar" style="width: ` + progress.percentage + `%;"
                                 aria-valuenow="` + progress.percentage + `" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>`;
        if (progress.percentage === 100) {
            progressHTML += `<div class="mt-3">
                                <a href="/geo/${ this.geoObjectUUID }/result" class="btn btn-primary rounded-0 pt-2">Виж рейтинга</a>
                            </div>`;
        }

        $('#surveyProgressBar').html(progressHTML);
    }

    open() {
        this.pathVoteSurveyContaineEl.classList.add('active');
        this.pathVoteSurveyContaineEl.querySelector('.geo-object-name').textContent = this.layer.feature.properties.name;
        this.pathVoteSurveyContaineEl.querySelector('.geo-object-type').textContent = this.layer.feature.properties.type;
    }

    close() {
        this.pathVoteSurveyContaineEl.classList.remove('active');
    }
};
