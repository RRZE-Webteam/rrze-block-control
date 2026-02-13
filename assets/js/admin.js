document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.bc-select-all-category').forEach(function (button) {

        button.addEventListener('click', function () {

            const fieldset = button.closest('.bc-block-category');
            if (!fieldset) return;

            const checkboxes = fieldset.querySelectorAll('input[type="checkbox"]');

            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(function (checkbox) {
                checkbox.checked = !allChecked;
            });

            // Toggle visual state
            button.classList.toggle('is-active', !allChecked);


        });

    });

});
