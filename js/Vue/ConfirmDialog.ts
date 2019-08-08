import AbstractDialog from "@src/Vue/AbstractDialog";
import WithRender from '@view/ConfirmDialog.html';
import Component from 'vue-class-component';

@WithRender
@Component
export default class ConfirmDialog extends AbstractDialog {
    protected title: string = '';
    protected text: string = '';
    protected classes: string[] = [];
    protected buttons: {
        text: string,
        action: () => void,
    }[] = [];
    protected modalOptions = {
        onOpenEnd: this.opened,
        dismissible: false,
    };

    protected opened() {
        /* istanbul ignore next */
        (<HTMLElement[]>this.$refs.buttons)[0].focus();
    }
}
