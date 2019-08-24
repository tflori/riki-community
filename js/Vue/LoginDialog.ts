import AbstractDialog from "@src/Vue/AbstractDialog";
import ActivateDialog from "@src/Vue/ActivateDialog";
import App from "@src/Vue/App";
import SignupDialog from '@src/Vue/SignupDialog';
import WithRender from '@view/LoginDialog.html';
import axios from 'axios';
import M from 'materialize-css';
import Component from 'vue-class-component';

@WithRender
@Component
export default class LoginDialog extends AbstractDialog {
    protected email: string = '';
    protected password: string = '';
    protected showPassword: boolean = false;
    protected errorMessage: string = '';

    public authenticate() {
        let authData = {
            email: this.email,
            password: this.password,
        };

        this.errorMessage = '';
        axios({
            method: 'post',
            url: '/auth',
            data: authData,
        }).then((response) => {
            this.close();
            this.$root.$data.user = response.data;
            if (response.data.accountStatus === 'pending') {
                (<App>this.$root).openDialog(ActivateDialog);
            }
        }).catch((error) => {
            if (error.response && error.response.data && error.response.data.message) {
                this.errorMessage = error.response.data.message;
                (<HTMLElement>this.$refs.password).focus();
            } else {
                console.warn('Authentication failed for unknown reason', error);
            }
        });
    }

    public showSignup() {
        this.close();
        (<App>this.$root).openDialog(SignupDialog);
    }

    protected opened() {
        /* istanbul ignore next */
        (<HTMLElement>this.$refs.email).focus();
    }
}
