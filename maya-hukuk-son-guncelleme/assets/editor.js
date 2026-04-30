(function (blocks, element, blockEditor) {
    var createElement = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var settings = window.mhSgSettings || {};
    var todayDate = settings.todayDate || '';
    var authorName = settings.authorName || 'Av. Arb. M. Fatih Yavaş';
    var blockStyle = {
        '--mh-sg-text-color': settings.textColor || '#FFFFFF',
        '--mh-sg-gradient-start': settings.gradientStart || '#0B1530',
        '--mh-sg-gradient-end': settings.gradientEnd || '#122A57'
    };

    blocks.registerBlockType('maya-hukuk/son-guncelleme', {
        edit: function () {
            var blockProps = useBlockProps({
                className: 'mh-sg-block is-editor-preview',
                style: blockStyle
            });

            return createElement(
                'div',
                blockProps,
                createElement('p', { className: 'mh-sg-date' }, 'Son Güncelleme ' + todayDate),
                createElement('p', { className: 'mh-sg-author' }, authorName)
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor);
