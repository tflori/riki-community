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

    public reset() {
        this.email = '';
        this.password = '';
        this.passwordConfirmation = '';
        this.displayName = '';
        this.name = '';
        this.errors = {};
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
        };

        axios({
            method: 'post',
            url: '/registration',
            data: user,
        }).then((response) => {
            this.close();
            this.$root.$data.user = response.data;
        }).catch((error) => {
            M.toast({html: error.response.data.message, classes: 'red darken-2 white-text'});
            if (error.response.status === 400 && error.response.data.message === 'Invalid user data') {
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
        });
    }
}
