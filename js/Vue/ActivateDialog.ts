import WithRender from '@view/ActivateDialog.html';
import AbstractDialog from "@src/Vue/AbstractDialog";
import Component from 'vue-class-component';

@WithRender
@Component
export default class ActivateDialog extends AbstractDialog {
    protected code: string = '';

    protected opened() {
        /* istanbul ignore next */
        (<HTMLElement>this.$refs.code).focus();
    }
}
