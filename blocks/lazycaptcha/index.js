(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls } = wp.blockEditor;
    const { PanelBody, SelectControl } = wp.components;
    const { createElement: el, Fragment } = wp.element;
    const { __ } = wp.i18n;

    registerBlockType('lazycaptcha/widget', {
        edit({ attributes, setAttributes }) {
            const blockProps = useBlockProps({
                style: {
                    border: '2px dashed #6366f1',
                    borderRadius: '8px',
                    padding: '20px',
                    textAlign: 'center',
                    background: 'linear-gradient(135deg, #eef2ff, #f5f3ff)',
                },
            });

            return el(
                Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: __('LazyCaptcha Settings', 'lazycaptcha') },
                        el(SelectControl, {
                            label: __('Challenge type (overrides default)', 'lazycaptcha'),
                            value: attributes.type,
                            options: [
                                { label: __('Use site default', 'lazycaptcha'), value: '' },
                                { label: __('Auto', 'lazycaptcha'), value: 'auto' },
                                { label: __('Image puzzles', 'lazycaptcha'), value: 'image_puzzle' },
                                { label: __('Proof of Work', 'lazycaptcha'), value: 'pow' },
                                { label: __('Behavioral', 'lazycaptcha'), value: 'behavioral' },
                                { label: __('Text / Math', 'lazycaptcha'), value: 'text_math' },
                            ],
                            onChange: (value) => setAttributes({ type: value }),
                        }),
                        el(SelectControl, {
                            label: __('Theme', 'lazycaptcha'),
                            value: attributes.theme,
                            options: [
                                { label: __('Use site default', 'lazycaptcha'), value: '' },
                                { label: __('Light', 'lazycaptcha'), value: 'light' },
                                { label: __('Dark', 'lazycaptcha'), value: 'dark' },
                            ],
                            onChange: (value) => setAttributes({ theme: value }),
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el('strong', {}, '🛡️ LazyCaptcha'),
                    el('br'),
                    el('span', { style: { fontSize: '13px', color: '#6b7280' } },
                        __('The widget will render here on the front end.', 'lazycaptcha')
                    )
                )
            );
        },
        save: () => null, // Server-rendered via render_callback
    });
})(window.wp);
