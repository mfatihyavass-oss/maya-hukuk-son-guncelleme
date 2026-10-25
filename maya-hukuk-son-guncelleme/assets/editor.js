(function (blocks, element, blockEditor, components, data, ServerSideRender) {
    var createElement = element.createElement;
    var Fragment = element.Fragment;
    var useBlockProps = blockEditor.useBlockProps;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var CheckboxControl = components.CheckboxControl;
    var Disabled = components.Disabled;
    var ExternalLink = components.ExternalLink;
    var useSelect = data.useSelect;
    var settings = window.mhSgEditor || {};
    var themes = settings.themes || {};
    var sections = settings.sections || {};

    function useEditedPost(context) {
        return useSelect(function (select) {
            var editor = select('core/editor');
            var postId = context && context.postId ? context.postId : (editor && editor.getCurrentPostId ? editor.getCurrentPostId() : 0);
            var modified = '';

            if (editor && editor.getCurrentPostId && editor.getCurrentPostId() === postId && editor.getCurrentPostAttribute) {
                modified = editor.getCurrentPostAttribute('modified') || '';
            }

            return { postId: postId, modified: modified };
        }, [context && context.postId]);
    }

    function themeOptions() {
        var options = [{ label: 'Genel ayarı kullan (' + (themes[settings.globalTheme] || '') + ')', value: '' }];

        Object.keys(themes).forEach(function (key) {
            options.push({ label: themes[key], value: key });
        });

        return options;
    }

    function Inspector(props) {
        var hidden = Array.isArray(props.attributes.hiddenSections) ? props.attributes.hiddenSections : [];

        function toggleSection(key, isVisible) {
            var next = hidden.filter(function (item) {
                return item !== key;
            });

            if (!isVisible) {
                next.push(key);
            }

            props.setAttributes({ hiddenSections: next });
        }

        return createElement(
            InspectorControls,
            null,
            createElement(
                PanelBody,
                { title: 'Bu karttaki görünüm', initialOpen: true },
                createElement(SelectControl, {
                    label: 'Tema',
                    value: props.attributes.theme || '',
                    options: themeOptions(),
                    onChange: function (value) {
                        props.setAttributes({ theme: value });
                    },
                    __nextHasNoMarginBottom: true
                }),
                createElement('p', { style: { margin: '16px 0 8px', fontWeight: 600 } }, 'Gösterilen bölümler'),
                Object.keys(sections).map(function (key) {
                    return createElement(CheckboxControl, {
                        key: key,
                        label: sections[key],
                        checked: hidden.indexOf(key) === -1,
                        onChange: function (isVisible) {
                            toggleSection(key, isVisible);
                        },
                        __nextHasNoMarginBottom: true
                    });
                }),
                createElement(
                    'p',
                    { style: { marginTop: '16px', color: '#757575' } },
                    'Yazar, butonlar, renkler ve yasal uyarı tüm sitede ortaktır. '
                ),
                settings.settingsUrl ? createElement(ExternalLink, { href: settings.settingsUrl }, 'Genel ayarları düzenle') : null
            )
        );
    }

    blocks.registerBlockType('maya-hukuk/son-guncelleme', {
        edit: function (props) {
            var post = useEditedPost(props.context);
            var blockProps = useBlockProps({ className: 'mh-sg-editor-wrapper' });

            return createElement(
                Fragment,
                null,
                createElement(Inspector, props),
                createElement(
                    'div',
                    blockProps,
                    createElement(
                        Disabled,
                        null,
                        createElement(ServerSideRender, {
                            block: 'maya-hukuk/son-guncelleme',
                            attributes: props.attributes,
                            skipBlockSupportAttributes: true,
                            urlQueryArgs: {
                                post_id: post.postId || undefined,
                                mh_sg_rev: post.modified || undefined
                            }
                        })
                    )
                )
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.data, window.wp.serverSideRender);
