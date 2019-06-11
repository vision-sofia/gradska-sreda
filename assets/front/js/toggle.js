$(document).on('click', '[data-toggle-open]', (e) => {
    const taegetToggleEl = document.getElementById(e.currentTarget.getAttribute('data-toggle-for'));
    taegetToggleEl.classList.add('active'); 
});

$(document).on('click', '[data-toggle-close]', (e) => {
    const taegetToggleEl = document.getElementById(e.currentTarget.getAttribute('data-toggle-for'));
    taegetToggleEl.classList.remove('active');
});