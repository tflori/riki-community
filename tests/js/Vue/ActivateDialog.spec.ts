import ActivateDialog from "@src/Vue/ActivateDialog";
import moxios from "moxios";
import Vue from 'vue';

describe('ActivateDialog', () => {
    beforeAll(() => {
        Vue.config.productionTip = false;
        Vue.config.devtools = false;
    });

    it('is a vue component', () => {
        let activateDialog = new ActivateDialog();

        expect(activateDialog).toBeInstanceOf(Vue);
    });

    it('renders a form with activation code field', () => {
        let activateDialog = new ActivateDialog();

        activateDialog.$mount();

        const form = <HTMLElement>activateDialog.$el.querySelector('form');
        expect(form).toBeInstanceOf(HTMLElement);
        expect(form.querySelector('input[name=code]')).toBeInstanceOf(HTMLElement);
    });

    it('opens a modal dialog', () => {
        let activateDialog = new ActivateDialog();
        activateDialog.$mount();
        // @ts-ignore
        spyOn(activateDialog.dialog, 'open');

        activateDialog.open();

        // @ts-ignore
        expect(activateDialog.dialog.open).toHaveBeenCalled();
    });

    it('closes the dialog', () => {
        let activateDialog = new ActivateDialog();
        activateDialog.$mount();
        // @ts-ignore
        spyOn(activateDialog.dialog, 'close');

        activateDialog.close();

        // @ts-ignore
        expect(activateDialog.dialog.close).toHaveBeenCalled();
    });
});
