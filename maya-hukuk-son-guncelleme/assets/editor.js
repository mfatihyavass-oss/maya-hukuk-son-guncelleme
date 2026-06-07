(function (blocks, element, blockEditor, data) {
    var createElement = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var useSelect = data && data.useSelect;
    var settings = window.mhSgSettings || {};
    var fallbackDate = settings.fallbackDate || settings.todayDate || '';
    var authorName = settings.authorName || 'Av. Arb. M. Fatih Yavaş';
    var blockStyle = {
        '--mh-sg-text-color': settings.textColor || '#FFFFFF',
        '--mh-sg-gradient-start': settings.gradientStart || '#0B1530',
        '--mh-sg-gradient-end': settings.gradientEnd || '#122A57'
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
                createElement('p', { className: 'mh-sg-date' }, 'Son Güncelleme ' + modifiedDate),
                createElement('p', { className: 'mh-sg-author' }, authorName)
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.data);
