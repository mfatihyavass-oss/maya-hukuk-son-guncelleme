(function (blocks, element, blockEditor, data) {
    var createElement = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var useSelect = data && data.useSelect;
    var settings = window.mhSgSettings || {};
    var fallbackDate = settings.fallbackDate || settings.todayDate || '';
    var authorName = settings.authorName || 'Av. Arb. M. Fatih Yavaş';
    var bioText = settings.bioText || '';
    var actionButtons = Array.isArray(settings.actionButtons) ? settings.actionButtons : [];
    var infoBoxes = Array.isArray(settings.infoBoxes) ? settings.infoBoxes : [];
    var blockStyle = {
        '--mh-sg-text-color': settings.textColor || '#FFFFFF',
        '--mh-sg-gradient-start': settings.gradientStart || '#07111F',
        '--mh-sg-gradient-end': settings.gradientEnd || '#1F3D63'
    };

    function formatDate(dateValue) {
        if (typeof dateValue !== 'string' || dateValue === '') {
            return fallbackDate;
        }

        var match = dateValue.match(/^(\d{4})-(\d{2})-(\d{2})/);

        if (!match) {
            return fallbackDate;
        }

        return match[3] + '.' + match[2] + '.' + match[1];
    }

    function useModifiedDate() {
        if (!useSelect) {
            return fallbackDate;
        }

        var modifiedDate = useSelect(function (select) {
            var editor = null;
            var currentPost = {};

            try {
                editor = select('core/editor');
                currentPost = editor && editor.getCurrentPost ? editor.getCurrentPost() : {};
            } catch (error) {
                return '';
            }

            if (editor && editor.getCurrentPostAttribute) {
                return editor.getCurrentPostAttribute('modified') || (currentPost && currentPost.modified) || '';
            }

            return currentPost && currentPost.modified ? currentPost.modified : '';
        }, []);

        return formatDate(modifiedDate);
    }

    function preventEditorNavigation(event) {
        event.preventDefault();
    }

    blocks.registerBlockType('maya-hukuk/son-guncelleme', {
        edit: function () {
            var modifiedDate = useModifiedDate();
            var blockProps = useBlockProps({
                className: 'mh-sg-block is-editor-preview',
                style: blockStyle
            });

            return createElement(
                'div',
                blockProps,
                createElement(
                    'div',
                    { className: 'mh-sg-content' },
                    createElement(
                        'div',
                        { className: 'mh-sg-meta' },
                        createElement('p', { className: 'mh-sg-date' }, 'Son Güncelleme ' + modifiedDate),
                        createElement('p', { className: 'mh-sg-author' }, authorName)
                    ),
                    bioText ? createElement('p', { className: 'mh-sg-bio' }, bioText) : null,
                    infoBoxes.length ? createElement(
                        'div',
                        { className: 'mh-sg-info-list', 'aria-label': 'Bilgi kutucuklari' },
                        infoBoxes.map(function (infoBox, index) {
                            return createElement(
                                'div',
                                { className: 'mh-sg-info-box', key: 'info-' + index },
                                infoBox.title ? createElement('span', { className: 'mh-sg-info-title' }, infoBox.title) : null,
                                infoBox.text ? createElement('span', { className: 'mh-sg-info-text' }, infoBox.text) : null
                            );
                        })
                    ) : null,
                    actionButtons.length ? createElement(
                        'div',
                        { className: 'mh-sg-actions', 'aria-label': 'Baglantilar' },
                        actionButtons.map(function (button, index) {
                            return createElement(
                                'a',
                                {
                                    className: 'mh-sg-action',
                                    href: button.url || '#',
                                    key: 'action-' + index,
                                    onClick: preventEditorNavigation
                                },
                                createElement('span', { className: 'mh-sg-action-label' }, button.label || ''),
                                createElement('span', { className: 'mh-sg-action-icon', 'aria-hidden': 'true' }, String.fromCharCode(8594))
                            );
                        })
                    ) : null
                )
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.data);
