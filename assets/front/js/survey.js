import { defaultMapSize } from './map-config';

export class Survey {
    mapInstance;
    layer;
    geoObjectUUID = '';
    isOpen = false;
    mapMarker;

    timeoutId;
    progress = 0;
    questions = [];
    results = {
        rating: [{}],
        respondents: [{}]
    };

    mapMarkerIcon = L.icon({
        iconUrl: 'front/svg/icon--map-pin--edit.svg',
        iconSize:     [53.707, 53.707], // size of the icon
        iconAnchor:   [26.9, 53], // point of the icon which will correspond to marker's location
    });

    // UI
    event;
    elPathVoteSurveyContainer;
    elProgressBar;
    elSurveayForm;
    elSurveyCarouselNav;
    elSurveyPollBtn;
    refSurveyCarousel;

    constructor(mapInstance) {
        this.mapInstance = mapInstance;
        this.init();
    }

    init() {
        this.queryElements();
        this.events();
    }

    queryElements() {
        this.elPathVoteSurveyContainer = document.getElementById('path-vote-survey');
        if (!this.elPathVoteSurveyContainer) {
           throw 'Element "#path-vote-survey" not found';
        }
        this.elProgressBar = this.elPathVoteSurveyContainer.querySelector('.survey-progress-bar');
        this.elSurveayForm = this.elPathVoteSurveyContainer.querySelector('.survey-form');

        this.elSurveyCarouselNav = this.elPathVoteSurveyContainer.querySelectorAll('.side-panel-nav-btn');
        this.elSurveyPollBtn = this.elSurveyCarouselNav[1];
        this.refSurveyCarousel = this.elPathVoteSurveyContainer.querySelectorAll('#carousel-survey-pages');
    }

    events() {
        $(document).on('click', '#confirm-button', () => {
            let id = $('#confirm-button').data('geo-object');

            $.ajax({
                type: 'POST',
                url: `/front-end/geo-object/${id}/confirm`,
                success: (result) => {
                    if (result.confirmed) {
                        this.styleConfirmButtonDone($('#confirm-button'));
                    }
                }
            });
        });

        $(this.elSurveyCarouselNav).on('click', (e) => {
            if (e.currentTarget.classList.contains('active')) {
                return false;
            }
            this.elSurveyCarouselNav.forEach((navItem) => {
                navItem.classList.remove('active');
            })
            e.currentTarget.classList.add('active')
        });


        $(document).on('click', '[data-toggle-for="path-vote-survey"][data-toggle-open]', () => {
            this.open(this.layer, this.ev);
        });

        $(document).on('click', '[data-toggle-for="path-vote-survey"][data-toggle-close]', () => {
            this.close();
        });

        $(document).on('input propertychange', '.survey-question-input', (e) => {
            const target = e.target;
            let data = {};
            let debounceTime = 0;

            if (target.tagName === 'TEXTAREA') {
                data = {
                    'explanation': {
                        'answer': target.id,
                        'text': target.value,
                    }
                };

                debounceTime = 1000;
            } else {
                data = {
                    'answer': target.value,
                };
            }

            clearTimeout(this.timeoutId);

            this.timeoutId = setTimeout(() => {
                if (target.tagName === 'TEXTAREA') {
                    this.submitSurveyCustomText(data, target.value);
                } else {
                    this.submitSurvey(data, target.value);
                }
            }, debounceTime);
        });

        $(document).on('click', '.survey-question .remove', (e) => {
            const uuid = e.currentTarget.getAttribute('data-uuid');
            this.clearQuestion(uuid);
        });
    }

    styleConfirmButtonDone(button) {
        button.removeClass("btn-secondary").removeClass('btn-primary');
        button.attr("disabled", true);
        button.html('Анкетата е изпратена');
    }

    styleConfirmButton(result) {
        let button = $('#confirm-button');

        if(result.survey.is_confirmed) {
            this.styleConfirmButtonDone(button);
        } else {
            if (result.survey.progress.percentage === 100) {
                button.removeClass("btn-secondary").addClass('btn-primary');
                button.attr("disabled", false);
                button.html('Изпрати');
            } else {
                button.removeClass("btn-primary").addClass('btn-secondary');
                button.attr("disabled", true);
                button.html('Изпрати');
            }
        }
    }

    getQuestions() {
        $.ajax({
            url: 'front-end/geo/' + this.geoObjectUUID,
            success: (result) => {
               this.buildSurvey(result);
               this.styleConfirmButton(result);
            }
        });
    }

    getResults() {
        $.ajax({
            url: 'front-end/geo/' + this.geoObjectUUID + '/result',
            success: (result) => {
               this.buildResults(result);
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
                    document.getElementById(value).classList.add('active');
                }
            },
            success: (result) => {
                this.buildSurvey(result);
                this.getResults();
                this.styleConfirmButton(result);
            }
        });
    };

    submitSurveyCustomText(data, value) {
        $.ajax({
            type: 'POST',
            url: '/geo/' + this.geoObjectUUID + '/q',
            data: data,
            beforeSend: () => {
                if (document.getElementById(value)) {
                    document.getElementById(value).classList.add('active');
                }
            }
        });
    };

    buildResults(result) {
        let ratingHTML = ``;
        let respondentsHTML = ``;
        let isSelectedParent = false;
        this.results.rating = result.rating;
        this.results.respondents = result.respondents;

       this.results.rating.forEach((ratingItem) => {
            ratingHTML += `
                <div class="row">
                    <div class="col-lg-6 text-right">${ratingItem.criterion}</div>
                    <div class="col-lg-6">
                        <div class="progress mb-4">
                            <div class="progress-bar pt-1" role="progressbar" style="width: ${ratingItem.percentage}%;"
                                aria-valuenow="${ratingItem.percentage}" aria-valuemin="0"
                                aria-valuemax="100">${ratingItem.rating} / ${ratingItem.max}</div>
                        </div>
                    </div>
                </div>`;
        });

        document.querySelector('.survey-ratings-rating').innerHTML = ratingHTML;

        Object.keys(this.results.respondents).forEach((respondentUser) => {
            respondentsHTML += `<strong>${respondentUser}</strong>`;

            this.results.respondents[respondentUser].forEach((respondentItem) => {
                respondentsHTML += `
                    <div class="row">
                        <div class="col-lg-6 text-right">${respondentItem.criterion}</div>
                        <div class="col-lg-6">
                            <div class="progress mb-4">
                                <div class="progress-bar pt-1" role="progressbar" style="width: ${respondentItem.percentage}%;"
                                     aria-valuenow="${respondentItem.percentage}" aria-valuemin="0"
                                     aria-valuemax="100">${respondentItem.rating} / ${respondentItem.max}</div>
                            </div>
                        </div>
                    </div>`;
            });
        });

        document.querySelector('.survey-ratings-respondents').innerHTML = ratingHTML;

    }

    buildSurvey(result) {
        let html = ``;
        let isSelectedParent = false;
        this.progress = result.survey.progress;
        this.questions = result.survey.questions;

        Object.keys(this.questions).forEach((item) => {
            const answers = this.questions[item].answers;
            this.question = this.questions[item];

            const isAnswered = this.question.isAnswered && this.question.isCompleted;
            const isUnComplete = this.question.isAnswered && !this.question.isCompleted;

            html += `<div class="survey-question mb-4 ${isAnswered ? 'is-answered' : isUnComplete ? 'is-unComplete' : null}">`;

            html += `
                <div class="survey-question-title mb-1">
                    <h6 class="survey-question-title-text d-inline">${this.question.title}</h6>
                    <div class="d-flex flex-grow-1 align-items-start justify-content-end">
                        <button type="button" class="remove btn btn-sm btn-danger" name="answers[option][${this.question.uuid}][]" data-uuid="${this.question.uuid}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;

            html += `
                <div class="survey-question pl-1">
            `;

            Object.keys(answers).forEach((answer) => {
                if (answers[answer].parent === null) {
                    isSelectedParent = this.question.answers[answer].isSelected;

                    html += `<div class="d-flex flex-column">
                                <label class="survey-question-option ` + (answers[answer].isSelected  ? 'is-selected' : '') + `" id="` + answers[answer].uuid + `">
                                    <input class="mr-1 survey-question-input" type="` + (this.question.hasMultipleAnswers ? 'checkbox' : 'radio') + `" name="answers[option][` + this.question.uuid + `][]"
                                    ` + (answers[answer].isSelected ? 'checked="checked"' : '') + ` value="` + answers[answer].uuid + `" /> ` + answers[answer].title + `
                                </label>
                            </div>`;

                    if (this.question.answers[answer].isFreeAnswer) {
                        html += `<textarea class="survey-question-input"></textarea>`;
                    }
                } else {
                    if (isSelectedParent === true) {
                        html += `<div class="pl-4 d-flex flex-column">
                                    <label class="survey-question-option ` + (answers[answer].isSelected ? 'is-selected' : '') + `" id="` + answers[answer].uuid + `">
                                        <input class="mr-1 survey-question-input" type="checkbox" name="answers[option][` + this.question.uuid + `][]" ${(answers[answer].isSelected ? 'checked="checked"' : '')} value="${answers[answer].uuid}" />
                                        ` + answers[answer].title +
                                    `</label>`;

                        if (answers[answer].isSelected && this.question.answers[answer].isFreeAnswer) {
                            html += `<label class="` + (answers[answer].isSelected ? 'is-selected' : '') + `">
                                        <textarea class="survey-question-input d-block" id="textarea-` + this.question.answers[answer].uuid + `]">` + this.question.answers[answer].explanation + `</textarea>
                                    </label>`;
                        }

                        html += '</div>';
                    }
                }
            });

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
        this.mapInstance.map.closePopup();
        this.mapInstance.setLayerActiveStyle(this.layer);
        this.mapInstance.toggleHeaderEl(false);
        this.addMarker();

        if (layer && ev) {
            this.setLayerData(layer, ev);
        }

        this.elPathVoteSurveyContainer.querySelector('.geo-object-name').textContent = this.layer.feature.properties.name;
        this.elPathVoteSurveyContainer.querySelector('.geo-object-type').textContent = this.layer.feature.properties.type;
        this.mapInstance.addToActiveAreaList(this.elPathVoteSurveyContainer);
    }

    close() {
        this.isOpen = false;
        this.removeMarker();
        this.mapInstance.toggleHeaderEl(true);
        this.mapInstance.removeFromActiveAreaList(this.elPathVoteSurveyContainer);
    }

    clearQuestion(uuid) {
        $.ajax({
            type: 'POST',
            url: '/geo/' + this.geoObjectUUID + '/clear/' + uuid,
            success: () => {
                this.getQuestions();
                this.getResults();
            }
        });
    }
}
