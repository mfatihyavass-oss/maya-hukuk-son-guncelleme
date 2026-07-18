(function () {
    var settings = window.mhSgDashboardAudit || {};
    var messages = settings.messages || {};

    function getMessage(key, fallback) {
        return messages[key] || fallback;
    }

    function postAjax(action) {
        var body = new URLSearchParams();

        body.append('action', action);
        body.append('nonce', settings.nonce || '');

        return fetch(settings.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: body.toString()
        }).then(function (response) {
            return response.json();
        });
    }

    function clearElement(element) {
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }
    }

    function setMessage(element, text, type) {
        element.className = 'mh-sg-date-audit-message';

        if (type) {
            element.className += ' is-' + type;
        }

        element.textContent = text || '';
    }

    function createCell(text) {
        var cell = document.createElement('td');
        cell.textContent = text || '';
        return cell;
    }

    function createTitleCell(item) {
        var cell = document.createElement('td');
        var link = document.createElement('a');

        link.href = item.editUrl || '#';
        link.textContent = item.title || ('#' + item.id);
        link.className = 'mh-sg-date-audit-title';
        cell.appendChild(link);

        return cell;
    }

    function createOutdatedTable(items) {
        var table = document.createElement('table');
        var thead = document.createElement('thead');
        var tbody = document.createElement('tbody');
        var headerRow = document.createElement('tr');

        table.className = 'widefat striped mh-sg-date-audit-table';

        ['Yazı/Sayfa', 'Tür', 'Yayın tarihi', 'Son güncelleme', 'İşlem'].forEach(function (label) {
            var th = document.createElement('th');
            th.scope = 'col';
            th.textContent = label;
            headerRow.appendChild(th);
        });

        thead.appendChild(headerRow);
        table.appendChild(thead);

        items.forEach(function (item) {
            var row = document.createElement('tr');
            var actionCell = document.createElement('td');
            var editLink = document.createElement('a');

            row.appendChild(createTitleCell(item));
            row.appendChild(createCell(item.postType));
            row.appendChild(createCell(item.publishDate));
            row.appendChild(createCell(item.modifiedDate));

            editLink.href = item.editUrl || '#';
            editLink.className = 'button button-small';
            editLink.textContent = 'Yazıyı güncelle';
            actionCell.appendChild(editLink);
            row.appendChild(actionCell);
            tbody.appendChild(row);
        });

        table.appendChild(tbody);
        return table;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var checkButton = document.getElementById('mh-sg-check-outdated-posts');
        var clearButton = document.getElementById('mh-sg-clear-outdated-posts');
        var spinner = document.getElementById('mh-sg-outdated-spinner');
        var messageElement = document.getElementById('mh-sg-outdated-message');
        var resultsElement = document.getElementById('mh-sg-outdated-results');

        if (!checkButton || !clearButton || !messageElement || !resultsElement) {
            return;
        }

        clearButton.addEventListener('click', function () {
            clearElement(resultsElement);
            setMessage(messageElement, '', '');
        });

        checkButton.addEventListener('click', function () {
            checkButton.disabled = true;

            if (spinner) {
                spinner.classList.add('is-active');
            }

            setMessage(messageElement, getMessage('checking', 'Kontrol ediliyor...'), 'info');
            clearElement(resultsElement);

            postAjax(settings.outdatedAction).then(function (result) {
                var items;

                checkButton.disabled = false;

                if (spinner) {
                    spinner.classList.remove('is-active');
                }

                if (!result || !result.success) {
                    setMessage(
                        messageElement,
                        result && result.data && result.data.message
                            ? result.data.message
                            : getMessage('error', 'İşlem tamamlanamadı.'),
                        'error'
                    );
                    return;
                }

                items = result.data.items || [];

                if (items.length === 0) {
                    setMessage(
                        messageElement,
                        getMessage('outdatedEmpty', 'Güncel olmayan yazı/sayfa bulunamadı.'),
                        'success'
                    );
                    return;
                }

                setMessage(messageElement, items.length + ' eski tarihli kayıt bulundu.', 'warning');
                resultsElement.appendChild(createOutdatedTable(items));
            }).catch(function () {
                checkButton.disabled = false;

                if (spinner) {
                    spinner.classList.remove('is-active');
                }

                setMessage(messageElement, getMessage('error', 'İşlem tamamlanamadı.'), 'error');
            });
        });
    });
})();
