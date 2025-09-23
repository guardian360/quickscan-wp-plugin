import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

registerBlockType('quickscan/security-scanner', {
    edit: (props) => {
        const { attributes, setAttributes } = props;
        const { showResults, placeholder, buttonText, title, showTitle } = attributes;
        
        const blockProps = useBlockProps({
            className: 'quickscan-block-editor'
        });

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Scanner Settings', 'quickscan-connector')} initialOpen={true}>
                        <ToggleControl
                            label={__('Show Title', 'quickscan-connector')}
                            checked={showTitle}
                            onChange={(value) => setAttributes({ showTitle: value })}
                        />
                        {showTitle && (
                            <TextControl
                                label={__('Title', 'quickscan-connector')}
                                value={title}
                                onChange={(value) => setAttributes({ title: value })}
                            />
                        )}
                        <TextControl
                            label={__('Placeholder Text', 'quickscan-connector')}
                            value={placeholder}
                            onChange={(value) => setAttributes({ placeholder: value })}
                        />
                        <TextControl
                            label={__('Button Text', 'quickscan-connector')}
                            value={buttonText}
                            onChange={(value) => setAttributes({ buttonText: value })}
                        />
                        <ToggleControl
                            label={__('Show Results on Page', 'quickscan-connector')}
                            checked={showResults}
                            onChange={(value) => setAttributes({ showResults: value })}
                            help={__('If disabled, results will be shown in a popup or redirect to results page', 'quickscan-connector')}
                        />
                    </PanelBody>
                </InspectorControls>
                
                <div {...blockProps}>
                    <div className="quickscan-block-preview">
                        {showTitle && <h3>{title}</h3>}
                        <div className="quickscan-form-preview">
                            <input 
                                type="text" 
                                placeholder={placeholder}
                                className="quickscan-url-input"
                                disabled
                            />
                            <button className="quickscan-button" disabled>
                                {buttonText}
                            </button>
                        </div>
                        <p className="quickscan-preview-note">
                            ⚡ {__('Security Scanner Preview', 'quickscan-connector')}
                        </p>
                    </div>
                </div>
            </>
        );
    },

    save: (props) => {
        const { attributes } = props;
        const blockProps = useBlockProps.save({
            className: 'wp-block-quickscan-security-scanner'
        });

        return (
            <div {...blockProps}>
                <div
                    className="quickscan-frontend-block"
                    data-show-results={attributes.showResults}
                    data-placeholder={attributes.placeholder}
                    data-button-text={attributes.buttonText}
                    data-title={attributes.title}
                    data-show-title={attributes.showTitle}
                >
                    {/* Content will be rendered by frontend JavaScript */}
                </div>
            </div>
        );
    }
});