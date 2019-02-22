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
})();
