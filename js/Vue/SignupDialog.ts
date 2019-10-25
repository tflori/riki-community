import AbstractDialog from "@src/Vue/AbstractDialog";
import App from "@src/Vue/App";
import LoginDialog from '@src/Vue/LoginDialog';
import WithRender from '@view/SignupDialog.html';
import axios from 'axios';
import M from 'materialize-css';
import Component from 'vue-class-component';

@WithRender
@Component
export default class SignupDialog extends AbstractDialog {
    protected email: string = '';
    protected password: string = '';
    protected passwordConfirmation: string = '';
    protected displayName: string = '';
    protected name: string = '';
    protected errors: any = {};
    protected errorMessage: string = '';

    public reset() {
        this.email = '';
        this.password = '';
        this.passwordConfirmation = '';
        this.displayName = '';
        this.name = '';
        this.errors = {};
        this.errorMessage = '';
        this.$nextTick(M.updateTextFields);
    }

    public showLogin() {
        this.close();
        (<App>this.$root).openDialog(LoginDialog);
    }

    public register() {
        let user = {
            email: this.email,
            password: this.password,
            passwordConfirmation: this.passwordConfirmation,
            displayName: this.displayName,
            name: this.name,
            recaptchaToken: ''
        };

        this.errors = {};
        this.errorMessage = '';
        (<App>this.$root).getRecaptchaToken('signup').then((token) => {
            user.recaptchaToken = token;
            return axios.post('/registration', user);
        }).then((response: any) => {
            this.close();
            this.$root.$data.user = response.data;
        }).catch((error: any) => {
            if (error.response && error.response.data && error.response.data.message) {
                this.errorMessage = error.response.data.message;
                if (error.response.data.message === 'Invalid user data') {
                    this.errors = error.response.data.errors;

                    // reset the password if it has errors
                    if (this.errors.password) {
                        this.password = '';
                        this.passwordConfirmation = '';
                        this.$nextTick(M.updateTextFields);
                    }

                    // focus the first field with errors
                    for (let field of ['email', 'password', 'displayName', 'name']) {
                        if (this.errors[field]) {
                            (<HTMLElement>this.$refs[field]).focus();
                            break;
                        }
                    }
                }
            } else {
                console.warn('Registration failed for unknown reason', error);
            }
        });
    }

    protected opened() {
        /* istanbul ignore next */
        (<App>this.$root).loadRecaptcha();
    }
}
