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

    function createTitleCell(item) {
        var titleCell = document.createElement('td');
        var titleLink = document.createElement('a');

        titleLink.href = item.editUrl || '#';
        titleLink.textContent = item.title || ('#' + item.id);
        titleLink.className = 'mh-sg-date-audit-title';
        titleCell.appendChild(titleLink);

        return titleCell;
    }

    function createGroupRow(label, columnCount) {
        var row = document.createElement('tr');
        var cell = document.createElement('td');

        row.className = 'mh-sg-date-audit-group';
        cell.colSpan = columnCount;
        cell.textContent = label;
        row.appendChild(cell);

        return row;
    }

    function createMismatchTable(items, messageElement) {
        var table = document.createElement('table');
        var thead = document.createElement('thead');
        var tbody = document.createElement('tbody');
        var headerRow = document.createElement('tr');
        var lastDifferenceLabel = '';
        var columnCount = 6;

        table.className = 'widefat striped mh-sg-date-audit-table';

        ['Yazı/Sayfa', 'Fark', 'Tür', 'Yayın tarihi', 'Son güncelleme', 'İşlem'].forEach(function (label) {
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
            var syncButton = document.createElement('button');
            var differenceLabel = item.differenceLabel || 'Tarih farkı';

            if (differenceLabel !== lastDifferenceLabel) {
                tbody.appendChild(createGroupRow(differenceLabel, columnCount));
                lastDifferenceLabel = differenceLabel;
            }

            row.appendChild(createTitleCell(item));
            row.appendChild(createCell(differenceLabel));
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

    function clearReport(report) {
        clearElement(report.resultsElement);
        setMessage(report.messageElement, '', '');
    }

    function renderResults(report, items) {
        clearElement(report.resultsElement);

        if (!items || items.length === 0) {
            setMessage(report.messageElement, report.emptyMessage, 'success');
            return;
        }

        setMessage(report.messageElement, report.foundMessage(items.length), 'warning');
        report.resultsElement.appendChild(report.createTable(items, report.messageElement));
    }

    function cleanupEmptyMismatchTable(row, messageElement) {
        var tableBody = row.parentNode;

        if (!tableBody) {
            return false;
        }

        tableBody.removeChild(row);

        Array.prototype.slice.call(tableBody.querySelectorAll('.mh-sg-date-audit-group')).forEach(function (groupRow) {
            var nextRow = groupRow.nextElementSibling;

            if (!nextRow || nextRow.classList.contains('mh-sg-date-audit-group')) {
                groupRow.parentNode.removeChild(groupRow);
            }
        });

        if (tableBody.querySelectorAll('tr:not(.mh-sg-date-audit-group)').length === 0) {
            var table = tableBody.closest('table');

            setMessage(messageElement, getMessage('empty', 'Uyumsuz yayın tarihi bulunan yazı/sayfa yok.'), 'success');

            if (table && table.parentNode) {
                table.parentNode.removeChild(table);
            }

            return true;
        }

        return false;
    }

    function syncPublishDate(postId, button, row, messageElement) {
        var oldText = button.textContent;

        button.disabled = true;
        button.textContent = getMessage('syncing', 'Eşitleniyor...');

        postAjax(settings.syncAction, {
            postId: postId
        }).then(function (result) {
            if (!result || !result.success) {
                button.disabled = false;
                button.textContent = oldText;
                setMessage(messageElement, result && result.data && result.data.message ? result.data.message : getMessage('error', 'İşlem tamamlanamadı.'), 'error');
                return;
            }

            if (!cleanupEmptyMismatchTable(row, messageElement)) {
                setMessage(messageElement, getMessage('synced', 'Yayın tarihi eşitlendi.'), 'success');
            }
        }).catch(function () {
            button.disabled = false;
            button.textContent = oldText;
            setMessage(messageElement, getMessage('error', 'İşlem tamamlanamadı.'), 'error');
        });
    }

    function bindReport(report) {
        if (!report.checkButton || !report.clearButton || !report.messageElement || !report.resultsElement) {
            return;
        }

        report.clearButton.addEventListener('click', function () {
            clearReport(report);
        });

        report.checkButton.addEventListener('click', function () {
            report.checkButton.disabled = true;

            if (report.spinner) {
                report.spinner.classList.add('is-active');
            }

            setMessage(report.messageElement, getMessage('checking', 'Kontrol ediliyor...'), 'info');
            clearElement(report.resultsElement);

            postAjax(report.action, {}).then(function (result) {
                report.checkButton.disabled = false;

                if (report.spinner) {
                    report.spinner.classList.remove('is-active');
                }

                if (!result || !result.success) {
                    setMessage(report.messageElement, result && result.data && result.data.message ? result.data.message : getMessage('error', 'İşlem tamamlanamadı.'), 'error');
                    return;
                }

                renderResults(report, result.data.items || []);
            }).catch(function () {
                report.checkButton.disabled = false;

                if (report.spinner) {
                    report.spinner.classList.remove('is-active');
                }

                setMessage(report.messageElement, getMessage('error', 'İşlem tamamlanamadı.'), 'error');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindReport({
            action: settings.checkAction,
            checkButton: document.getElementById('mh-sg-check-date-mismatches'),
            clearButton: document.getElementById('mh-sg-clear-date-mismatches'),
            spinner: document.getElementById('mh-sg-date-audit-spinner'),
            messageElement: document.getElementById('mh-sg-date-audit-message'),
            resultsElement: document.getElementById('mh-sg-date-audit-results'),
            emptyMessage: getMessage('empty', 'Uyumsuz yayın tarihi bulunan yazı/sayfa yok.'),
            foundMessage: function (count) {
                return count + ' uyumsuz kayıt bulundu.';
            },
            createTable: createMismatchTable
        });

        bindReport({
            action: settings.outdatedAction,
            checkButton: document.getElementById('mh-sg-check-outdated-posts'),
            clearButton: document.getElementById('mh-sg-clear-outdated-posts'),
            spinner: document.getElementById('mh-sg-outdated-spinner'),
            messageElement: document.getElementById('mh-sg-outdated-message'),
            resultsElement: document.getElementById('mh-sg-outdated-results'),
            emptyMessage: getMessage('outdatedEmpty', 'Güncel olmayan yazı/sayfa bulunamadı.'),
            foundMessage: function (count) {
                return count + ' eski tarihli kayıt bulundu.';
            },
            createTable: createOutdatedTable
        });
    });
})();
