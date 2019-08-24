import AbstractDialog from "@src/Vue/AbstractDialog";
import ActivateDialog from "@src/Vue/ActivateDialog";
import App from "@src/Vue/App";
import ConfirmDialog from "@src/Vue/ConfirmDialog";
import moxios from "moxios";
import Vue from 'vue';

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

    describe('acknowledge', () => {
        it('returns a promise', () => {
            let app = new App();
            app.$refs.overlayContainer = document.createElement('DIV');

            let response = app.acknowledge({text: 'hello world'});

            expect(response).toBeInstanceOf(Promise);
        });

        it('opens a ConfirmDialog with appropriate data', () => {
            let app = new App();
            spyOn(app, 'openDialog');

            app.acknowledge({text: 'This is just a test message', title: 'Hello World', button: 'Sure'});

            expect(app.openDialog).toHaveBeenCalledWith(ConfirmDialog, {
                data: {
                    title: 'Hello World',
                    text: 'This is just a test message',
                    buttons: [{
                        text: 'Sure',
                        action: jasmine.any(Function),
                    }],
                }
            });
        });

        it('resolves the promise and closes the dialog', (done) => {
            let app = new App();
            let dialog = new ConfirmDialog();
            let action: () => void = () => {};
            spyOn(app, 'openDialog').and.callFake((Dialog: { new(options: any): AbstractDialog }, options: any) => {
                action = options.data.buttons[0].action;
                return dialog;
            });
            spyOn(dialog, 'close');
            let promise = app.acknowledge({text: 'hello world'});

            action();

            promise.then(() => {
                expect(dialog.close).toHaveBeenCalled();
                done();
            });
        });
    });

    describe('confirm', () => {
        it('returns a promise', () => {
            let app = new App();
            spyOn(app, 'openDialog');

            let response = app.confirm({text: 'hello world'});

            expect(response).toBeInstanceOf(Promise);
        });

        it('opens a ConfirmDialog with appropriate data', () => {
            let app = new App();
            spyOn(app, 'openDialog');

            app.confirm({text: 'This is not sure', title: 'Do that?', confirm: 'Sure', cancel: 'Cancel'});

            expect(app.openDialog).toHaveBeenCalledWith(ConfirmDialog, {
                data: {
                    title: 'Do that?',
                    text: 'This is not sure',
                    buttons: [
                        {
                            text: 'Cancel',
                            action: jasmine.any(Function),
                        },
                        {
                            text: 'Sure',
                            action: jasmine.any(Function),
                        },
                    ],
                }
            });
        });

        it('resolves the promise with true and closes the dialog', (done) => {
            let app = new App();
            let dialog = new ConfirmDialog();
            let action: () => void = () => {};
            spyOn(app, 'openDialog').and.callFake((Dialog: { new(options: any): AbstractDialog }, options: any) => {
                action = options.data.buttons[1].action;
                return dialog;
            });
            spyOn(dialog, 'close');
            let promise = app.confirm({text: 'hello world'});

            action();

            promise.then((response) => {
                expect(dialog.close).toHaveBeenCalled();
                expect(response).toBe(true);
                done();
            });
        });

        it('resolves the promise with false and closes the dialog', (done) => {
            let app = new App();
            let dialog = new ConfirmDialog();
            let action: () => void = () => {};
            spyOn(app, 'openDialog').and.callFake((Dialog: { new(options: any): AbstractDialog }, options: any) => {
                action = options.data.buttons[0].action;
                return dialog;
            });
            spyOn(dialog, 'close');
            let promise = app.confirm({text: 'hello world'});

            action();

            promise.then((response) => {
                expect(dialog.close).toHaveBeenCalled();
                expect(response).toBe(false);
                done();
            });
        });
    });

    describe('getCsrfToken', () => {
        beforeEach(() => {
            moxios.install();
        });

        afterEach(() => {
            moxios.uninstall();
        });

        it('requests /auth/token', (done) => {
            let app = new App();

            moxios.wait(() => {
                let request = moxios.requests.mostRecent();

                expect(request.config.method).toBe('get');
                expect(request.url).toBe('/auth/token');

                done();
            });

            app.getCsrfToken();
        });

        it('returns the token', (done) => {
            let app = new App();

            moxios.stubRequest('/auth/token', {
                status: 200,
                response: 'foo',
            });

            app.getCsrfToken().then((response) => {
                expect(response).toBe('foo');
                done();
            });
        });
    });
});
