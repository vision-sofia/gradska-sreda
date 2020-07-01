$(document).on('click', '[data-toggle-open]', (e) => {
    const targetToggleEl = document.getElementById(e.currentTarget.getAttribute('data-toggle-for'));

    $(`[data-toggle-type="${targetToggleEl.getAttribute('data-toggle-type')}"]`).removeClass('active');

    targetToggleEl.classList.add('active');
});

$(document).on('click', '[data-toggle-close]', (e) => {
    const targetToggleEl = document.getElementById(e.currentTarget.getAttribute('data-toggle-for'));
    targetToggleEl.classList.remove('active');
});
