(function () {
    var config = window.mhSgSettingsPage || {};

    function bindRepeater(repeater, onChange) {
        var items = repeater.querySelector('[data-mh-sg-items]');
        var template = repeater.querySelector('[data-mh-sg-template]');
        var addButton = repeater.querySelector('[data-mh-sg-add]');

        if (!items || !template || !addButton) {
            return;
        }

        addButton.addEventListener('click', function () {
            var nextIndex = parseInt(repeater.dataset.nextIndex || '0', 10);
            var wrapper = document.createElement('div');
            var row;

            repeater.dataset.nextIndex = String(nextIndex + 1);
            wrapper.innerHTML = template.innerHTML.replace(/__INDEX__/g, String(nextIndex)).trim();
            row = wrapper.firstElementChild;

            if (row) {
                items.appendChild(row);

                if (row.querySelector('input')) {
                    row.querySelector('input').focus();
                }
            }

            onChange();
        });

        repeater.addEventListener('click', function (event) {
            var removeButton = event.target.closest('[data-mh-sg-remove]');
            var moveButton = event.target.closest('[data-mh-sg-move]');
            var row = event.target.closest('[data-mh-sg-row]');

            if (!row || !repeater.contains(row)) {
                return;
            }

            if (removeButton) {
                row.parentNode.removeChild(row);
                onChange();
                return;
            }

            if (moveButton) {
                if (moveButton.dataset.mhSgMove === 'up' && row.previousElementSibling) {
                    row.parentNode.insertBefore(row, row.previousElementSibling);
                } else if (moveButton.dataset.mhSgMove === 'down' && row.nextElementSibling) {
                    row.parentNode.insertBefore(row.nextElementSibling, row);
                }

                moveButton.focus();
                onChange();
            }
        });
    }

    function bindAvatar(picker, onChange) {
        var input = picker.querySelector('[data-mh-sg-avatar-input]');
        var thumb = picker.querySelector('[data-mh-sg-avatar-thumb]');
        var selectButton = picker.querySelector('[data-mh-sg-avatar-select]');
        var removeButton = picker.querySelector('[data-mh-sg-avatar-remove]');
        var frame = null;

        if (!input || !selectButton || !window.wp || !window.wp.media) {
            return;
        }

        function setThumb(url) {
            thumb.innerHTML = '';

            if (url) {
                var image = document.createElement('img');
                image.src = url;
                image.alt = '';
                thumb.appendChild(image);
            } else {
                var placeholder = document.createElement('span');
                placeholder.textContent = '?';
                thumb.appendChild(placeholder);
            }
        }

        selectButton.addEventListener('click', function () {
            if (!frame) {
                frame = window.wp.media({
                    title: 'Yazar fotoğrafı seç',
                    button: { text: 'Bu fotoğrafı kullan' },
                    library: { type: 'image' },
                    multiple: false
                });

                frame.on('select', function () {
                    var attachment = frame.state().get('selection').first().toJSON();
                    var sizes = attachment.sizes || {};
                    var url = sizes.thumbnail ? sizes.thumbnail.url : attachment.url;

                    input.value = String(attachment.id);
                    setThumb(url);
                    removeButton.hidden = false;
                    onChange();
                });
            }

            frame.open();
        });

        removeButton.addEventListener('click', function () {
            input.value = '0';
            setThumb('');
            removeButton.hidden = true;
            onChange();
        });
    }

    function bindPreview(form) {
        var preview = document.querySelector('[data-mh-sg-preview]');
        var spinner = document.querySelector('[data-mh-sg-preview-spinner]');
        var timer = null;
        var requestId = 0;

        if (!preview || !config.ajaxUrl) {
            return function () {};
        }

        function refresh() {
            var body = new FormData(form);
            var currentRequest = ++requestId;

            body.set('action', config.previewAction);
            body.set('nonce', config.nonce);

            if (spinner) {
                spinner.classList.add('is-active');
            }

            fetch(config.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: body
            }).then(function (response) {
                return response.json();
            }).then(function (result) {
                if (currentRequest !== requestId || !result || !result.success) {
                    return;
                }

                preview.innerHTML = result.data.html;

                if (window.mhSgInit) {
                    window.mhSgInit(preview);
                }
            }).catch(function () {
                // Önizleme hatası kaydetmeyi engellemez.
            }).then(function () {
                if (currentRequest === requestId && spinner) {
                    spinner.classList.remove('is-active');
                }
            });
        }

        return function schedule() {
            window.clearTimeout(timer);
            timer = window.setTimeout(refresh, 350);
        };
    }

    function bindUnsavedWarning(form) {
        var dirty = false;

        form.addEventListener('input', function () {
            dirty = true;
        });

        form.addEventListener('change', function () {
            dirty = true;
        });

        form.addEventListener('submit', function () {
            dirty = false;
        });

        window.addEventListener('beforeunload', function (event) {
            if (dirty) {
                event.preventDefault();
                event.returnValue = '';
            }
        });

        return function markDirty() {
            dirty = true;
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('mh-sg-settings-form');

        if (!form) {
            return;
        }

        var schedulePreview = bindPreview(form);
        var markDirty = bindUnsavedWarning(form);

        function onChange() {
            markDirty();
            schedulePreview();
        }

        // Özel buton rengi seçilince "Özel renk kullan" kutusunu otomatik işaretle.
        form.addEventListener('input', function (event) {
            var field = event.target.closest('.mh-sg-color-field');
            var toggle = field && event.target.type === 'color' ? field.querySelector('[data-mh-sg-custom-color]') : null;

            if (toggle) {
                toggle.checked = true;
            }
        });

        form.addEventListener('input', schedulePreview);
        form.addEventListener('change', schedulePreview);

        Array.prototype.forEach.call(form.querySelectorAll('[data-mh-sg-repeater]'), function (repeater) {
            bindRepeater(repeater, onChange);
        });

        Array.prototype.forEach.call(form.querySelectorAll('[data-mh-sg-avatar]'), function (picker) {
            bindAvatar(picker, onChange);
        });
    });
})();
