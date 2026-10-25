(function () {
    var settings = window.mhSgDashboardAudit || {};
    var messages = settings.messages || {};

    function getMessage(key, fallback) {
        return messages[key] || fallback;
    }

    function postAjax(params) {
        var body = new URLSearchParams();

        body.append('action', settings.outdatedAction);
        body.append('nonce', settings.nonce || '');

        Object.keys(params).forEach(function (key) {
            body.append(key, params[key]);
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

    function setMessage(element, text, type) {
        element.className = 'mh-sg-date-audit-message';

        if (type) {
            element.className += ' is-' + type;
        }

        element.textContent = text || '';
    }

    function createElement(tag, className, text) {
        var element = document.createElement(tag);

        if (className) {
            element.className = className;
        }

        if (text !== undefined && text !== null) {
            element.textContent = text;
        }

        return element;
    }

    function renderSummary(element, summary) {
        clearElement(element);

        if (!summary || typeof summary.total === 'undefined') {
            element.hidden = true;
            return;
        }

        [
            { label: 'Yayında', value: summary.total, type: '' },
            { label: summary.staleMonths + ' aydan eski', value: summary.stale, type: summary.stale ? 'is-warning' : 'is-success' },
            {
                label: summary.autoInsert ? 'Kartı yok (otomatik ekleniyor)' : 'Kartı yok',
                value: summary.missing,
                type: summary.missing && !summary.autoInsert ? 'is-error' : 'is-success'
            }
        ].forEach(function (stat) {
            var item = createElement('div', 'mh-sg-summary-item ' + stat.type);

            item.appendChild(createElement('strong', '', String(stat.value)));
            item.appendChild(createElement('span', '', stat.label));
            element.appendChild(item);
        });

        element.hidden = false;
    }

    function createTable(items) {
        var table = createElement('table', 'widefat striped mh-sg-date-audit-table');
        var thead = document.createElement('thead');
        var tbody = document.createElement('tbody');
        var headerRow = document.createElement('tr');

        ['Yazı/Sayfa', 'Son güncelleme', 'İşlem'].forEach(function (label) {
            var th = createElement('th', '', label);
            th.scope = 'col';
            headerRow.appendChild(th);
        });

        thead.appendChild(headerRow);
        table.appendChild(thead);

        items.forEach(function (item) {
            var row = document.createElement('tr');
            var titleCell = document.createElement('td');
            var dateCell = document.createElement('td');
            var actionCell = createElement('td', 'mh-sg-date-audit-actions');
            var titleLink = createElement('a', 'mh-sg-date-audit-title', item.title || ('#' + item.id));
            var editLink = createElement('a', 'button button-small button-primary', 'Güncelle');
            var viewLink = createElement('a', 'button button-small', 'Gör');

            titleLink.href = item.editUrl || '#';
            titleCell.appendChild(titleLink);
            titleCell.appendChild(createElement('span', 'mh-sg-date-audit-sub', item.postType + ' · Yayın: ' + item.publishDate));

            dateCell.appendChild(createElement('span', 'mh-sg-date-audit-age', item.age));
            dateCell.appendChild(createElement('span', 'mh-sg-date-audit-sub', item.modifiedDate));

            editLink.href = item.editUrl || '#';
            viewLink.href = item.viewUrl || '#';
            viewLink.target = '_blank';
            viewLink.rel = 'noopener';
            actionCell.appendChild(editLink);
            actionCell.appendChild(viewLink);

            row.appendChild(titleCell);
            row.appendChild(dateCell);
            row.appendChild(actionCell);
            tbody.appendChild(row);
        });

        table.appendChild(tbody);
        return table;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var checkButton = document.getElementById('mh-sg-check-outdated-posts');
        var clearButton = document.getElementById('mh-sg-clear-outdated-posts');
        var typeSelect = document.getElementById('mh-sg-report-type');
        var thresholdSelect = document.getElementById('mh-sg-report-threshold');
        var thresholdWrap = document.querySelector('[data-mh-sg-threshold-wrap]');
        var spinner = document.getElementById('mh-sg-outdated-spinner');
        var summaryElement = document.getElementById('mh-sg-outdated-summary');
        var messageElement = document.getElementById('mh-sg-outdated-message');
        var resultsElement = document.getElementById('mh-sg-outdated-results');

        if (!checkButton || !clearButton || !messageElement || !resultsElement) {
            return;
        }

        function setBusy(isBusy) {
            checkButton.disabled = isBusy;

            if (spinner) {
                spinner.classList.toggle('is-active', isBusy);
            }
        }

        if (typeSelect && thresholdWrap) {
            typeSelect.addEventListener('change', function () {
                thresholdWrap.hidden = typeSelect.value === 'missing';
            });
        }

        clearButton.addEventListener('click', function () {
            clearElement(resultsElement);
            setMessage(messageElement, '', '');

            if (summaryElement) {
                clearElement(summaryElement);
                summaryElement.hidden = true;
            }
        });

        checkButton.addEventListener('click', function () {
            var report = typeSelect ? typeSelect.value : 'outdated';

            setBusy(true);
            setMessage(messageElement, getMessage('checking', 'Kontrol ediliyor...'), 'info');
            clearElement(resultsElement);

            postAjax({
                report: report,
                months: report === 'outdated' && thresholdSelect ? thresholdSelect.value : '0'
            }).then(function (result) {
                var data;
                var items;
                var text;

                setBusy(false);

                if (!result || !result.success) {
                    setMessage(
                        messageElement,
                        result && result.data && result.data.message ? result.data.message : getMessage('error', 'İşlem tamamlanamadı.'),
                        'error'
                    );
                    return;
                }

                data = result.data;
                items = data.items || [];

                if (summaryElement) {
                    renderSummary(summaryElement, data.summary);
                }

                if (items.length === 0) {
                    setMessage(
                        messageElement,
                        report === 'missing'
                            ? getMessage('missingEmpty', 'Tüm içeriklerde kart bulunuyor.')
                            : getMessage('outdatedEmpty', 'Güncel olmayan yazı/sayfa bulunamadı.'),
                        'success'
                    );
                    return;
                }

                text = data.total + ' kayıt bulundu.';

                if (data.total > items.length) {
                    text += ' En eski ' + items.length + ' kayıt gösteriliyor.';
                }

                setMessage(messageElement, text, 'warning');
                resultsElement.appendChild(createTable(items));
            }).catch(function () {
                setBusy(false);
                setMessage(messageElement, getMessage('error', 'İşlem tamamlanamadı.'), 'error');
            });
        });
    });
})();
