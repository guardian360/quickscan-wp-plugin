(function() {
    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var useBlockProps = wp.blockEditor.useBlockProps;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var ToggleControl = wp.components.ToggleControl;
    var SelectControl = wp.components.SelectControl;
    var __ = wp.i18n.__;

    registerBlockType('quickscan/security-scanner', {
        apiVersion: 2,
        title: __('Security Scanner', 'quickscan-connector'),
        icon: 'shield',
        category: 'widgets',
        description: __('Add a security scanner form to scan websites for vulnerabilities', 'quickscan-connector'),
        attributes: {
            showResults: {
                type: 'boolean',
                default: true
            },
            placeholder: {
                type: 'string',
                default: 'Enter website URL to scan...'
            },
            buttonText: {
                type: 'string',
                default: 'Start Security Scan'
            },
            title: {
                type: 'string',
                default: 'Website Security Scanner'
            },
            showTitle: {
                type: 'boolean',
                default: true
            }
        },
        supports: {
            html: false,
            align: ['left', 'center', 'right', 'wide', 'full']
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var showResults = attributes.showResults;
            var placeholder = attributes.placeholder;
            var buttonText = attributes.buttonText;
            var title = attributes.title;
            var showTitle = attributes.showTitle;
            
            var blockProps = useBlockProps({
                className: 'quickscan-block-editor'
            });

            return el('div', blockProps, [
                el(InspectorControls, {},
                    el(PanelBody, {
                        title: __('Scanner Settings', 'quickscan-connector'),
                        initialOpen: true
                    }, [
                        el(ToggleControl, {
                            label: __('Show Title', 'quickscan-connector'),
                            checked: showTitle,
                            onChange: function(value) {
                                setAttributes({ showTitle: value });
                            }
                        }),
                        showTitle && el(TextControl, {
                            label: __('Title', 'quickscan-connector'),
                            value: title,
                            onChange: function(value) {
                                setAttributes({ title: value });
                            }
                        }),
                        el(TextControl, {
                            label: __('Placeholder Text', 'quickscan-connector'),
                            value: placeholder,
                            onChange: function(value) {
                                setAttributes({ placeholder: value });
                            }
                        }),
                        el(TextControl, {
                            label: __('Button Text', 'quickscan-connector'),
                            value: buttonText,
                            onChange: function(value) {
                                setAttributes({ buttonText: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: __('Show Results on Page', 'quickscan-connector'),
                            checked: showResults,
                            onChange: function(value) {
                                setAttributes({ showResults: value });
                            },
                            help: __('If disabled, results will be shown in a popup or redirect to results page', 'quickscan-connector')
                        })
                    ])
                ),
                
                el('div', { className: 'quickscan-block-preview' }, [
                    showTitle && el('h3', {}, title),
                    el('div', { className: 'quickscan-form-preview' }, [
                        el('input', {
                            type: 'text',
                            placeholder: placeholder,
                            className: 'quickscan-url-input',
                            disabled: true
                        }),
                        el('button', {
                            className: 'quickscan-button',
                            disabled: true
                        }, buttonText)
                    ]),
                    el('p', { className: 'quickscan-preview-note' }, [
                        '⚡ ',
                        __('Security Scanner Preview', 'quickscan-connector')
                    ])
                ])
            ]);
        },

        save: function(props) {
            var attributes = props.attributes;
            var blockProps = useBlockProps.save({
                className: 'wp-block-quickscan-security-scanner'
            });

            return el('div', blockProps,
                el('div', {
                    className: 'quickscan-frontend-block',
                    'data-show-results': attributes.showResults,
                    'data-placeholder': attributes.placeholder,
                    'data-button-text': attributes.buttonText,
                    'data-title': attributes.title,
                    'data-show-title': attributes.showTitle
                })
            );
        }
    });
})();