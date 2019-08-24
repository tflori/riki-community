import ActivateDialog from "@src/Vue/ActivateDialog";
import App from "@src/Vue/App";
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

    describe('activate', () => {
        beforeEach(() => {
            moxios.install();
        });

        afterEach(() => {
            moxios.uninstall();
        });

        it('posts an activation', (done) => {
            let app = new App();
            let activateDialog = new ActivateDialog({
                parent: app,
            });
            activateDialog['code'] = 'fooBar';

            spyOn(app, 'getCsrfToken').and.returnValue(Promise.resolve('foo123'));

            moxios.wait(() => {
                let request = moxios.requests.mostRecent();

                expect(request.config.method).toBe('post');
                expect(request.url).toBe('/user/activate');
                expect(JSON.parse(request.config.data)).toEqual({
                    token: 'fooBar',
                    csrf_token: 'foo123',
                });

                done();
            });

            activateDialog.activate();
        });

        describe('with success', () => {
            let app: App, activateDialog: ActivateDialog, user: any;

            beforeEach(() => {
                app = new App();
                activateDialog = new ActivateDialog({
                    parent: app,
                });
                activateDialog.$mount();
                activateDialog['code'] = 'fooBar';

                spyOn(app, 'getCsrfToken').and.returnValue(Promise.resolve('foo123'));

                user = {
                    id: 23,
                    name: 'John Doe',
                    displayName: 'john',
                    email: 'john.doe@example.com',
                    accountStatus: 'active',
                    created: (new Date()).toISOString(),
                    updated: (new Date()).toISOString(),
                };
            });

            it('closes the dialog on success', (done) => {
                spyOn(activateDialog, 'close');
                moxios.stubRequest('/user/activate', {
                    status: 200,
                    response: user,
                });

                activateDialog.activate();

                setTimeout(() => {
                    expect(activateDialog.close).toHaveBeenCalled();
                    done();
                });
            });

            it('updates the user in $root.$data', (done) => {
                moxios.stubRequest('/user/activate', {
                    status: 200,
                    response: user,
                });

                activateDialog.activate();

                setTimeout(() => {
                    expect(app.$data.user).toEqual(user);
                    done();
                });
            });

            it('shows a toast message', (done) => {
                spyOn(M, 'toast');
                moxios.stubRequest('/user/activate', {
                    status: 200,
                    response: user,
                });

                activateDialog.activate();

                setTimeout(() => {
                    expect(M.toast).toHaveBeenCalledWith({html: 'Account activated!'});
                    done();
                });
            });
        });

        describe('with error', () => {
            let app: App, activateDialog: ActivateDialog;

            beforeEach(() => {
                app = new App();
                activateDialog = new ActivateDialog({
                    parent: app,
                });
                activateDialog.$mount();
                activateDialog['code'] = 'fooBar';

                spyOn(app, 'getCsrfToken').and.returnValue(Promise.resolve('foo123'));
            });

            it('stores the error message', (done) => {
                moxios.stubRequest('/user/activate', {
                    status: 400,
                    response: {
                        message: 'Invalid activation code',
                    }
                });

                activateDialog.activate();

                setTimeout(() => {
                    expect(activateDialog['errorMessage']).toBe('Invalid activation code');
                    done();
                });
            });

            it('focuses the code', (done) => {
                spyOn(<HTMLElement>activateDialog.$refs.code, 'focus');
                moxios.stubRequest('/user/activate', {
                    status: 400,
                    response: {
                        message: 'Invalid activation code',
                    }
                });

                activateDialog.activate();

                setTimeout(() => {
                    expect((<HTMLElement>activateDialog.$refs.code).focus).toHaveBeenCalled();
                    done();
                });
            });

            it('logs unexpected errors', (done) => {
                spyOn(console, 'warn');
                moxios.stubRequest('/user/activate', {
                    status: 500,
                    responseText: ''
                });

                activateDialog.activate();

                setTimeout(() => {
                    expect(console.warn).toHaveBeenCalledWith(
                        'Activation failed for unknown reason',
                        jasmine.anything()
                    );
                    done();
                });
            });
        });
    });
});
