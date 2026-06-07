(function () {
    var settings = window.mhSgDashboardAudit || {};
    var messages = settings.messages || {};

    function getMessage(key, fallback) {
        return messages[key] || fallback;
    }

    function postAjax(action, data) {
        var body = new URLSearchParams();

        body.append('action', action);
        body.append('nonce', settings.nonce || '');

        Object.keys(data || {}).forEach(function (key) {
            body.append(key, data[key]);
        });

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

    function setMessage(messageElement, text, type) {
        messageElement.className = 'mh-sg-date-audit-message';

        if (type) {
            messageElement.className += ' is-' + type;
        }

        messageElement.textContent = text || '';
    }

    function createCell(text) {
        var cell = document.createElement('td');
        cell.textContent = text || '';
        return cell;
    }

    function createResultsTable(items, messageElement) {
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
            var titleCell = document.createElement('td');
            var titleLink = document.createElement('a');
            var actionCell = document.createElement('td');
            var syncButton = document.createElement('button');

            titleLink.href = item.editUrl || '#';
            titleLink.textContent = item.title || ('#' + item.id);
            titleLink.className = 'mh-sg-date-audit-title';
            titleCell.appendChild(titleLink);
            row.appendChild(titleCell);
            row.appendChild(createCell(item.postType));
            row.appendChild(createCell(item.publishDate));
            row.appendChild(createCell(item.modifiedDate));

            syncButton.type = 'button';
            syncButton.className = 'button button-small mh-sg-sync-publish-date';
            syncButton.dataset.postId = item.id;
            syncButton.textContent = 'Tarihi eşitle';

            if (!item.canSync) {
                syncButton.disabled = true;
            }

            syncButton.addEventListener('click', function () {
                syncPublishDate(item.id, syncButton, row, messageElement);
            });

            actionCell.appendChild(syncButton);
            row.appendChild(actionCell);
            tbody.appendChild(row);
        });

        table.appendChild(tbody);
        return table;
    }

    function renderResults(items, resultsElement, messageElement) {
        clearElement(resultsElement);

        if (!items || items.length === 0) {
            setMessage(messageElement, getMessage('empty', 'Uyumsuz yayın tarihi bulunan yazı/sayfa yok.'), 'success');
            return;
        }

        setMessage(messageElement, items.length + ' uyumsuz kayıt bulundu.', 'warning');
        resultsElement.appendChild(createResultsTable(items, messageElement));
    }

    function syncPublishDate(postId, button, row, messageElement) {
        var oldText = button.textContent;

        button.disabled = true;
        button.textContent = getMessage('syncing', 'Eşitleniyor...');

        postAjax(settings.syncAction, {
            postId: postId
        }).then(function (result) {
            var tableBody = row.parentNode;

            if (!result || !result.success) {
                button.disabled = false;
                button.textContent = oldText;
                setMessage(messageElement, result && result.data && result.data.message ? result.data.message : getMessage('error', 'İşlem tamamlanamadı.'), 'error');
                return;
            }

            row.parentNode.removeChild(row);
            setMessage(messageElement, getMessage('synced', 'Yayın tarihi eşitlendi.'), 'success');

            if (tableBody && tableBody.children.length === 0) {
                var table = tableBody.closest('table');

                setMessage(messageElement, getMessage('empty', 'Uyumsuz yayın tarihi bulunan yazı/sayfa yok.'), 'success');

                if (table && table.parentNode) {
                    table.parentNode.removeChild(table);
                }
            }
        }).catch(function () {
            button.disabled = false;
            button.textContent = oldText;
            setMessage(messageElement, getMessage('error', 'İşlem tamamlanamadı.'), 'error');
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var checkButton = document.getElementById('mh-sg-check-date-mismatches');
        var spinner = document.getElementById('mh-sg-date-audit-spinner');
        var messageElement = document.getElementById('mh-sg-date-audit-message');
        var resultsElement = document.getElementById('mh-sg-date-audit-results');

        if (!checkButton || !messageElement || !resultsElement) {
            return;
        }

        checkButton.addEventListener('click', function () {
            checkButton.disabled = true;

            if (spinner) {
                spinner.classList.add('is-active');
            }

            setMessage(messageElement, getMessage('checking', 'Kontrol ediliyor...'), 'info');
            clearElement(resultsElement);

            postAjax(settings.checkAction, {}).then(function (result) {
                checkButton.disabled = false;

                if (spinner) {
                    spinner.classList.remove('is-active');
                }

                if (!result || !result.success) {
                    setMessage(messageElement, result && result.data && result.data.message ? result.data.message : getMessage('error', 'İşlem tamamlanamadı.'), 'error');
                    return;
                }

                renderResults(result.data.items || [], resultsElement, messageElement);
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
