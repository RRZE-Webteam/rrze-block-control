document.addEventListener('DOMContentLoaded', function () {

    /*
     * -----------------------------------------
     * Helper: Convert comma list to clean array
     * -----------------------------------------
     */
    function parseList(value) {
        if (!value) return [];
        return value
            .split(',')
            .map(function (v) {
                return v.trim();
            })
            .filter(Boolean);
    }

    /*
     * -----------------------------------------
     * Get label element by block slug
     * -----------------------------------------
     */
    function getLabel(slug) {
        if (!slug) return null;

        try {
            return document.querySelector(
                '[data-block="' + CSS.escape(slug) + '"]'
            );
        } catch (e) {
            return null;
        }
    }

    /*
     * -----------------------------------------
     * Get checkbox inside label by slug
     * -----------------------------------------
     */
    function getCheckbox(slug) {
        const label = getLabel(slug);
        if (!label) return null;

        return label.querySelector('input[type="checkbox"]');
    }

    /*
     * -----------------------------------------
     * Activate parents recursively (UPWARD)
     * -----------------------------------------
     */
    function activateParents(slug) {

        const label = getLabel(slug);
        if (!label) return;

        const parents = parseList(label.dataset.parents);

        parents.forEach(function (parentSlug) {

            const parentCheckbox = getCheckbox(parentSlug);
            if (!parentCheckbox) return;

            if (!parentCheckbox.checked) {
                parentCheckbox.checked = true;
            }

            activateParents(parentSlug);
        });
    }

    /*
     * -----------------------------------------
     * Toggle children recursively (DOWNWARD)
     * -----------------------------------------
     */
    function toggleChildren(slug, state) {

        const label = getLabel(slug);
        if (!label) return;

        const children = parseList(label.dataset.children);

        children.forEach(function (childSlug) {

            const childCheckbox = getCheckbox(childSlug);
            if (!childCheckbox) return;

            childCheckbox.checked = state;

            toggleChildren(childSlug, state);
        });
    }

    /*
     * -----------------------------------------
     * Deactivate parents recursively (UPWARD)
     * Only if no child is still checked
     * -----------------------------------------
     */
    function deactivateParents(slug) {

        const label = getLabel(slug);
        if (!label) return;

        const parents = parseList(label.dataset.parents);

        parents.forEach(function (parentSlug) {

            const parentLabel = getLabel(parentSlug);
            if (!parentLabel) return;

            // Find all blocks that list this parent
            const allLabels = document.querySelectorAll('[data-block]');
            let anyChecked = false;

            allLabels.forEach(function (lbl) {

                const parentList = parseList(lbl.dataset.parents);

                if (parentList.includes(parentSlug)) {

                    const cb = lbl.querySelector('input[type="checkbox"]');
                    if (cb && cb.checked) {
                        anyChecked = true;
                    }
                }
            });

            // If no child is checked → deactivate parent
            if (!anyChecked) {

                const parentCheckbox = getCheckbox(parentSlug);
                if (!parentCheckbox) return;

                parentCheckbox.checked = false;

                deactivateParents(parentSlug);
            }
        });
    }

    /*
     * -----------------------------------------
     * Checkbox change handler
     * -----------------------------------------
     */
    document.querySelectorAll('.bc-block-grid input[type="checkbox"]').forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {

            const label = checkbox.closest('label');
            if (!label) return;

            const slug = label.dataset.block;
            if (!slug) return;

            if (checkbox.checked) {

                activateParents(slug);
                toggleChildren(slug, true);

            } else {

                toggleChildren(slug, false);
                deactivateParents(slug);
            }
        });
    });

});
