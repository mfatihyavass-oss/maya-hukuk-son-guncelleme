(function () {
    function bindRepeater(repeater) {
        var items = repeater.querySelector('[data-mh-sg-items]');
        var template = repeater.querySelector('[data-mh-sg-template]');
        var addButton = repeater.querySelector('[data-mh-sg-add]');

        if (!items || !template || !addButton) {
            return;
        }

        addButton.addEventListener('click', function () {
            var nextIndex = parseInt(repeater.dataset.nextIndex || '0', 10);
            var wrapper = document.createElement('div');

            repeater.dataset.nextIndex = String(nextIndex + 1);
            wrapper.innerHTML = template.innerHTML.replace(/__INDEX__/g, String(nextIndex)).trim();

            if (wrapper.firstElementChild) {
                items.appendChild(wrapper.firstElementChild);
            }
        });

        repeater.addEventListener('click', function (event) {
            var removeButton = event.target.closest('[data-mh-sg-remove]');
            var row = removeButton ? removeButton.closest('[data-mh-sg-row]') : null;

            if (row && repeater.contains(row)) {
                row.parentNode.removeChild(row);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        Array.prototype.forEach.call(document.querySelectorAll('[data-mh-sg-repeater]'), bindRepeater);
    });
})();
