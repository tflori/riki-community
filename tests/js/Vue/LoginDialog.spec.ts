import ActivateDialog from '@src/Vue/ActivateDialog';
import App from "@src/Vue/App";
import LoginDialog from '@src/Vue/LoginDialog';
import SignupDialog from '@src/Vue/SignupDialog';
import moxios from "moxios";
import Vue from 'vue';

describe('LoginDialog', () => {
    beforeAll(() => {
        Vue.config.productionTip = false;
        Vue.config.devtools = false;
    });

    it('is a vue component', () => {
        let loginDialog = new LoginDialog();

        expect(loginDialog).toBeInstanceOf(Vue);
    });

    it('renders a form with email and password field', () => {
        let loginDialog = new LoginDialog();

        loginDialog.$mount();

        const form = <HTMLElement>loginDialog.$el.querySelector('form');
        expect(form).toBeInstanceOf(HTMLElement);
        expect(form.querySelector('input[name=email]')).toBeInstanceOf(HTMLElement);
        expect(form.querySelector('input[name=password]')).toBeInstanceOf(HTMLElement);
    });

    it('opens a modal dialog', () => {
        let loginDialog = new LoginDialog();
        loginDialog.$mount();
        // @ts-ignore
        spyOn(loginDialog.dialog, 'open');

        loginDialog.open();

        // @ts-ignore
        expect(loginDialog.dialog.open).toHaveBeenCalled();
    });

    it('closes the dialog', () => {
        let loginDialog = new LoginDialog();
        loginDialog.$mount();
        // @ts-ignore
        spyOn(loginDialog.dialog, 'close');

        loginDialog.close();

        // @ts-ignore
        expect(loginDialog.dialog.close).toHaveBeenCalled();
    });

    it('opens the signup dialog', () => {
        let app = new App();
        let loginDialog = new LoginDialog({
            parent: app,
        });
        loginDialog.$mount();
        spyOn(loginDialog, 'close').and.stub();
        spyOn(app, 'openDialog');

        loginDialog.showSignup();

        expect(loginDialog.close).toHaveBeenCalled();
        expect(app.openDialog).toHaveBeenCalledWith(SignupDialog);
    });

    describe('authentication', () => {
        beforeEach(() => {
            moxios.install();
        });

        afterEach(() => {
            moxios.uninstall();
        });

        it('posts a new authentication', (done) => {
            let loginDialog = new LoginDialog();
            loginDialog['email'] = 'john.doe@example.com';
            loginDialog['password'] = '123456';

            moxios.wait(() => {
                let request = moxios.requests.mostRecent();

                expect(request.config.method).toBe('post');
                expect(request.url).toBe('/auth');
                expect(JSON.parse(request.config.data)).toEqual({
                    email: 'john.doe@example.com',
                    password: '123456',
                });

                done();
            });

            loginDialog.authenticate();
        });

        describe('with success', () => {
            function respondWithUser(user: { [key: string]: any } | null = null) {
                user = Object.assign({
                    id: 23,
                    name: 'John Doe',
                    displayName: 'john',
                    email: 'john.doe@example.com',
                    accountStatus: 'active',
                    created: (new Date()).toISOString(),
                    updated: (new Date()).toISOString(),
                }, user || {});

                moxios.stubRequest('/auth', {
                    status: 200,
                    response: user,
                });
            }

            it('closes the dialog', (done) => {
                let loginDialog = new LoginDialog();
                loginDialog.$mount();
                spyOn(loginDialog, 'close').and.stub();
                respondWithUser();

                loginDialog.authenticate();

                setTimeout(() => {
                    expect(loginDialog.close).toHaveBeenCalled();
                    done();
                });
            });

            it('stores the user in $root', (done) => {
                let loginDialog = new LoginDialog();
                loginDialog.$mount();

                respondWithUser({
                    id: 42,
                    name: 'Arthur Dent',
                });

                loginDialog.authenticate();

                setTimeout(() => {
                    expect(loginDialog.$root.$data.user).not.toBeNull();
                    expect(loginDialog.$root.$data.user.id).toBe(42);
                    expect(loginDialog.$root.$data.user.name).toBe('Arthur Dent');
                    done();
                });
            });

            it('opens the activate dialog', (done) => {
                let app = new App();
                let loginDialog = new LoginDialog({
                    parent: app,
                });
                loginDialog.$mount();
                spyOn(loginDialog, 'close');
                spyOn(app, 'openDialog');

                respondWithUser({
                    accountStatus: 'pending',
                });

                loginDialog.authenticate();

                setTimeout(() => {
                    expect(app.openDialog).toHaveBeenCalledWith(ActivateDialog);
                    done();
                });
            });
        });

        describe('with error', () => {
            let app: App, loginDialog: LoginDialog;

            beforeEach(() => {
                app = new App();
                loginDialog = new LoginDialog({
                    parent: app,
                });
                loginDialog.$mount();
                loginDialog['email'] = 'jdoe@example.com';
                loginDialog['password'] = '123456';
            });

            it('stores the error message', (done) => {
                moxios.stubRequest('/auth', {
                    status: 400,
                    response: {
                        message: 'Authentication failed',
                    }
                });

                loginDialog.authenticate();

                setTimeout(() => {
                    expect(loginDialog['errorMessage']).toBe('Authentication failed');
                    done();
                });
            });

            it('focuses the password', (done) => {
                spyOn(<HTMLElement>loginDialog.$refs.password, 'focus');

                moxios.stubRequest('/auth', {
                    status: 400,
                    response: {
                        message: 'Authentication failed',
                    }
                });

                loginDialog.authenticate();

                setTimeout(() => {
                    expect((<HTMLElement>loginDialog.$refs.password).focus).toHaveBeenCalled();
                    done();
                });
            });

            it('logs a warning when the request fails', (done) => {
                spyOn(console, 'warn');

                moxios.stubRequest('/auth', {
                    status: 500,
                    response: 'Key value store not available',
                });

                loginDialog.authenticate();

                setTimeout(() => {
                    expect(console.warn).toHaveBeenCalledWith(
                        'Authentication failed for unknown reason',
                        jasmine.anything()
                    );
                    done();
                });
            });
        });
    });
});
