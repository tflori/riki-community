import App from "@src/Vue/App";
import Vue from 'vue';
import ActivateDialog from "@src/Vue/ActivateDialog";

describe('App', () => {
    beforeAll(() => {
        Vue.config.productionTip = false;
        Vue.config.devtools = false;
    });

    it('is a vue component', () => {
        let app = new App();

        expect(app).toBeInstanceOf(Vue);
    });

    describe('openDialog', () => {
        it('appends the child to overlayContainer', () => {
            let app = new App();
            app.$refs.overlayContainer = document.createElement('DIV');
            spyOn(app.$refs.overlayContainer, 'appendChild');

            app.openDialog(ActivateDialog);

            expect(app.$refs.overlayContainer.appendChild).toHaveBeenCalled();
        });
    });
});
