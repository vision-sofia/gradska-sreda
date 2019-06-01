export class Survey {
    layer;
    event;
    geoObjectUUID = ''; 
    isOpen = false;

    timeoutId;
    mapInstance;
    lastCenterPoint;
    elPathVoteSurveyContainer;
    elProgressBar;
    elSurveyPollBtn;
    elSurveayForm;
    mapAreaHeight = 100; // Precents 
    mapMarkerIcon = L.icon({
        iconUrl: 'front/svg/icon--map-pin--edit.svg',
        iconSize:     [53.707, 53.707], // size of the icon
        iconAnchor:   [26.9, 53], // point of the icon which will correspond to marker's location
    });
    mapMarker;
    progress = 0;

    constructor(mapInstance) {
        this.mapInstance = mapInstance;
        this.elPathVoteSurveyContainer = document.getElementById('path-vote-suevey');
        if (!this.elPathVoteSurveyContainer) {
            return;
        }
        this.elProgressBar = this.elPathVoteSurveyContainer.querySelector('.survey-progress-bar');
        this.elSurveyPollBtn = this.elPathVoteSurveyContainer.querySelector('.survey-btn-poll');
        this.elSurveayForm = this.elPathVoteSurveyContainer.querySelector('.survey-form');
        this.events();
    }

    events() {
        $(document).on('click', '[data-survey-open]', () => {
            this.mapInstance.map.closePopup();

            this.open(this.layer, this.ev);
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
                this.submitSurvey(data, target.value)
            }, debounceTime);
        });

        $(document).on('click', '.rem', () => {
            let value = this.value;
            $.ajax({
                type: "POST",
                url: '/geo/' + this.geoObjectUUID + '/clear/' + value,
                success: () => {
                    this.getQuestions();
                    this.getResults();
                }
            });
        });
    }

   getQuestions() {
        $.ajax({
            url: 'front-end/geo/' + this.geoObjectUUID,
            success: (result) => {
               this.onGetQuestionsSuccess(result);
            }
        });
    }

    getResults() {
        $.ajax({
            url: 'front-end/geo/' + this.geoObjectUUID + '/result',
            success: (result) => {
            //    this.onGetResultsSuccess(result);
            console.log(result);
            
            }
        });
    }

    submitSurvey(data, value) {
        $.ajax({
            type: 'POST',
            url: '/geo/' + this.geoObjectUUID + '/q',
            data: data,
            beforeSend: () => {
                if (document.getElementById(value)) {
                    document.getElementById(value).style.color = 'green';
                }
            },
            success: (result) => {
                this.onGetQuestionsSuccess(result);
            }
        });
    };

    onGetQuestionsSuccess(result) {
        let html = ``;
        let questions = result.survey.questions;
        let isSelectedParent = false;
        this.progress = result.survey.progress;

        Object.keys(questions).forEach((item) => {
            let answers = questions[item].answers;
            let question = questions[item];

            html += `<div class="survey-question mb-4">
                        <div class="survey-question-title  mb-1">
                            <i class="mr-1 fas ` + (question.isAnswered ? 'text-success fa-check' : 'fa-check text-black-50') + `"></i>
                            <h5 class="survey-question-title-text d-inline">` + question.title + `</h5>
                        </div>
                        <div class="survey-question pl-4">`;

            Object.keys(answers).forEach((answer) => {
                if (answers[answer].parent === null) {
                    isSelectedParent = question.answers[answer].isSelected;

                    html += `<div class="d-flex flex-column">
                                <label class="survey-question-option ` + (answers[answer].isSelected ? 'is-answered' : '') + `" id="` + answers[answer].uuid + `">
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
                                    <label class="survey-question-option ` + (answers[answer].isSelected ? 'is-answered' : '') + `" id="` + answers[answer].uuid + `">
                                        <input class="mr-1  answer" type="checkbox" name="answers[option][` + question.uuid + `][]" ${(answers[answer].isSelected ? 'checked="checked"' : '')} value="${answers[answer].uuid}" />
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

        this.elSurveayForm.innerHTML = html;

        this.elProgressBar.style.width = this.progress.percentage + '%';

        if (this.progress.percentage === 100) {
            this.elSurveyPollBtn.classList.remove('disabled');
            $(this.elSurveyPollBtn).parent().tooltip('disable');
        } else {
            this.elSurveyPollBtn.classList.add('disabled');
            $(this.elSurveyPollBtn).parent().tooltip('enable');
        }
    }

    setLayerData(layer, ev) {
        this.layer = layer;
        this.event = ev;
        this.geoObjectUUID = this.layer.feature.properties.id;
        this.getQuestions();
        this.getResults();
    }

    addMarker() {
        this.removeMarker();
        this.mapMarker = L.marker(this.layer.getCenter(), {
            icon: this.mapMarkerIcon
        });
        this.mapMarker.addTo(this.mapInstance.map);
    }

    removeMarker() {
        if (this.mapMarker) {
            this.mapMarker.remove();
        }
    }

    open(layer, ev) {
        this.isOpen = true;
        this.mapInstance.setLayerActiveStyle(this.layer);
        this.addMarker();

        if (layer && ev) {
            this.setLayerData(layer, ev);
        }

        this.elPathVoteSurveyContainer.querySelector('.geo-object-name').textContent = this.layer.feature.properties.name;
        this.elPathVoteSurveyContainer.querySelector('.geo-object-type').textContent = this.layer.feature.properties.type;
        this.elPathVoteSurveyContainer.classList.add('active');

        this.lastCenterPoint = this.event.latlng;
        const surveyHeight = parseFloat(getComputedStyle(this.elPathVoteSurveyContainer).getPropertyValue('--suevey-height'));
        const activeAreaHeight = this.mapAreaHeight - surveyHeight;

        this.mapInstance.toggleHeaderEl(false);
        this.mapInstance.map.setActiveArea({
            height: activeAreaHeight + '%',
            top: 0,
            bottom: 0,
        });

        this.mapInstance.zoomToLayer(this.layer, this.event, this.layer.getCenter());

    }

    toggleHeader() {

    }

    close() {
        this.isOpen = false;
        this.removeMarker();
        this.elPathVoteSurveyContainer.classList.remove('active');

        this.mapInstance.toggleHeaderEl(true);

        this.mapInstance.map.setActiveArea({
            height: this.mapAreaHeight + '%',
        });
        this.mapInstance.map.panTo(this.lastCenterPoint);
    }
};
