import App from "@src/Vue/App";
import LoginDialog from '@src/Vue/LoginDialog';
import moxios from 'moxios';
import SignupDialog from '@src/Vue/SignupDialog';
import Vue from 'vue';
import {respondWith} from '../helper';

describe('SignupDialog', () => {
    beforeAll(() => {
        Vue.config.productionTip = false;
        Vue.config.devtools = false;
    });

    it('is a vue component', () => {
        let signupDialog = new SignupDialog();

        expect(signupDialog).toBeInstanceOf(Vue);
    });

    it('renders the signup form', () => {
        let signupDialog = new SignupDialog();

        signupDialog.$mount();

        const form = signupDialog.$el.querySelector('form');
        expect(form).toBeInstanceOf(HTMLElement);
        if (form instanceof HTMLElement) {
            expect(form.querySelector('input#signup-email')).toBeInstanceOf(HTMLElement);
            expect(form.querySelector('input#signup-password')).toBeInstanceOf(HTMLElement);
            expect(form.querySelector('input#signup-password-confirmation')).toBeInstanceOf(HTMLElement);
            expect(form.querySelector('input#signup-display-name')).toBeInstanceOf(HTMLElement);
            expect(form.querySelector('input#signup-name')).toBeInstanceOf(HTMLElement);
        }
    });

    it('opens a modal dialog', () => {
        let signupDialog = new SignupDialog();
        signupDialog.$mount();
        // @ts-ignore
        spyOn(signupDialog.dialog, 'open');

        signupDialog.open();

        // @ts-ignore
        expect(signupDialog.dialog.open).toHaveBeenCalled();
    });

    it('closes the dialog', () => {
        let signupDialog = new SignupDialog();
        signupDialog.$mount();
        // @ts-ignore
        spyOn(signupDialog.dialog, 'close');

        signupDialog.close();

        // @ts-ignore
        expect(signupDialog.dialog.close).toHaveBeenCalled();
    });

    it('opens the login dialog', () => {
        let app = new App();
        let signupDialog = new SignupDialog({
            parent: app,
        });
        signupDialog.$mount();
        spyOn(signupDialog, 'close').and.stub();
        spyOn(app, 'openDialog');

        signupDialog.showLogin();

        expect(signupDialog.close).toHaveBeenCalled();
        expect(app.openDialog).toHaveBeenCalledWith(LoginDialog);
    });

    it('updates the text fields on reset', () => {
        let signupDialog = new SignupDialog();
        spyOn(signupDialog, '$nextTick');

        signupDialog.reset();

        expect(signupDialog.$nextTick).toHaveBeenCalledWith(M.updateTextFields);
    });

    describe('registration', () => {
        beforeEach(() => {
            moxios.install();
        });

        afterEach(() => {
            moxios.uninstall();
        });

        it('posts the current form data', (done) => {
            let signupDialog = new SignupDialog();
            const userData = {
                email: 'john.doe@example.com',
                password: 'asdf123',
                passwordConfirmation: 'asdf123',
                displayName: 'john',
                name: 'John Doe',
            };
            for (let field in userData) {
                // @ts-ignore
                signupDialog[field] = userData[field];
            }

            moxios.wait(() => {
                let request = moxios.requests.mostRecent();

                expect(request.config.method).toBe('post');
                expect(request.url).toBe('/registration');
                expect(JSON.parse(request.config.data)).toEqual(userData);

                done();
            });

            signupDialog.register();
        });

        describe('with errors', () => {
            function respondWithErrors(errors: {[key: string]: string[]}): Promise<any> {
                let response = {
                    reason: 'Bad Request',
                    message: 'Invalid user data',
                    errors: errors,
                };

                return respondWith(400, response);
            }

            it('shows a toast message on error', (done) => {
                let signupDialog = new SignupDialog();
                signupDialog.$mount();
                spyOn(M, 'toast');

                respondWith(400, {message: 'That failed'}).then(() => {
                    expect(M.toast).toHaveBeenCalledWith(jasmine.objectContaining({
                        html: 'That failed'
                    }));
                    done();
                });

                signupDialog.register();
            });

            it('stores the errors from response', (done) => {
                let signupDialog = new SignupDialog();
                signupDialog.$mount();
                const errors = {
                    password: ['Foo'],
                };

                respondWithErrors(errors).then(() => {
                    expect(signupDialog['errors']).toEqual(errors);
                    done();
                });

                signupDialog.register();
            });

            it('focuses the first element with error', (done) => {
                let signupDialog = new SignupDialog();
                signupDialog.$mount();
                spyOn(<HTMLElement>signupDialog.$refs.password, 'focus');

                respondWithErrors({
                    displayName: ['Foo'],
                    password: ['Foo'],
                }).then(() => {
                    expect((<HTMLElement>signupDialog.$refs.password).focus).toHaveBeenCalled();
                    done();
                });

                signupDialog.register();
            });

            it('resets the password elements when password has errors', (done) => {
                let signupDialog = new SignupDialog();
                signupDialog.$mount();
                signupDialog['password'] = 'asdf';
                signupDialog['passwordConfirmation'] = 'asdf';
                spyOn(signupDialog, '$nextTick');

                respondWithErrors({
                    password: ['Password to weak'],
                }).then(() => {
                    expect(signupDialog['password']).toBe('');
                    expect(signupDialog['passwordConfirmation']).toBe('');
                    expect(signupDialog.$nextTick).toHaveBeenCalledWith(M.updateTextFields);
                    done();
                });

                signupDialog.register();
            });
        });

        describe('with success', () => {
            function respondWithUser(user: {[key: string]: any}|null = null): Promise<any> {
                user = Object.assign({
                    id: 23,
                    name: 'John Doe',
                    displayName: 'john',
                    email: 'john.doe@example.com',
                    accountStatus: 'pending',
                    created: (new Date()).toISOString(),
                    updated: (new Date()).toISOString(),
                }, user || {});

                return respondWith(200, user);
            }

            it('closes the signup dialog', (done) => {
                let signupDialog = new SignupDialog();
                signupDialog.$mount();
                spyOn(signupDialog, 'close');

                respondWithUser().then(() => {
                    expect(signupDialog.close).toHaveBeenCalled();
                    done();
                });

                signupDialog.register();
            });

            it('stores the user in $root', (done) => {
                let signupDialog = new SignupDialog();
                signupDialog.$mount();

                respondWithUser({
                    id: 42,
                    name: 'Arthur Dent',
                }).then(() => {
                    expect(signupDialog.$root.$data.user).not.toBeNull();
                    expect(signupDialog.$root.$data.user.id).toBe(42);
                    expect(signupDialog.$root.$data.user.name).toBe('Arthur Dent');
                    done();
                });

                signupDialog.register();
            });
        });
    });
});
