import domReady from '@wordpress/dom-ready';
import {createRoot} from '@wordpress/element';
import {__} from '@wordpress/i18n';
import {Panel, PanelBody, PanelRow} from '@wordpress/components';

const SettingsPage = () => {
    return (
        <div className={"wrap"}>
            <Panel header={"Selections"}>
                <PanelBody>
                    <PanelRow>
                        <div>Placeholder for message control</div>
                    </PanelRow>
                    <PanelRow>
                        <div>Placeholder for display control</div>
                    </PanelRow>
                </PanelBody>
                <PanelBody
                    title={__('Appearance', 'rrze-block-control')}
                    initialOpen={false}
                >
                    <PanelRow>
                        <div>Placeholder for size control</div>
                    </PanelRow>
                </PanelBody>
            </Panel>
        </div>
    );
};

domReady(() => {
    const root = createRoot(
        document.getElementById('rrze-block-control-setting')
    );

    root.render(<SettingsPage/>);
});