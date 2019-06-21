import Vue from 'vue';
import { clickOn } from '../helper';

import SignupDialog from '@src/Vue/SignupDialog';
import LoginDialog from '@src/Vue/LoginDialog';

describe('LoginDialog', () => {
    it('is a vue component', () => {
        let loginDialog = new LoginDialog();

        expect(loginDialog).toBeInstanceOf(Vue);
    });

    it('renders a form with email and password field', () => {
        let loginDialog = new LoginDialog();

        loginDialog.$mount();

        const form = loginDialog.$el.querySelector('form');
        expect(form).toBeInstanceOf(HTMLElement);
        if (form instanceof HTMLElement) {
            expect(form.querySelector('input[name=email]')).toBeInstanceOf(HTMLElement);
            expect(form.querySelector('input[name=password]')).toBeInstanceOf(HTMLElement);
        }
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
        let loginDialog = new LoginDialog();
        let signupDialog = loginDialog.$root.$refs.signupDialog = new SignupDialog();
        loginDialog.$mount();
        spyOn(loginDialog, 'close').and.stub();
        spyOn(signupDialog, 'open').and.stub();

        loginDialog.showSignup();

        expect(loginDialog.close).toHaveBeenCalled();
        expect(signupDialog.open).toHaveBeenCalled();
    });

    describe('authentication', () => {
        it('closes the dialog', () => {
            let loginDialog = new LoginDialog();
            loginDialog.$mount();
            spyOn(loginDialog, 'close').and.stub();

            // @ts-ignore
            loginDialog.authenticate();

            expect(loginDialog.close).toHaveBeenCalled();
        });
    });
});
