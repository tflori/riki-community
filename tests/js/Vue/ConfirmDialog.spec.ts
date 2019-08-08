import ConfirmDialog from "@src/Vue/ConfirmDialog";
import Vue from 'vue';

describe('ConfirmDialog', () => {
    it('is a vue component', () => {
        let confirmDialog = new ConfirmDialog();

        expect(confirmDialog).toBeInstanceOf(Vue)
    });
});
