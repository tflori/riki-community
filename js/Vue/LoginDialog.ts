import AbstractDialog from "@src/Vue/AbstractDialog";
import ActivateDialog from "@src/Vue/ActivateDialog";
import App from "@src/Vue/App";
import axios from 'axios';
import Component from 'vue-class-component';
import M from 'materialize-css';
import SignupDialog from '@src/Vue/SignupDialog';
import WithRender from '@view/LoginDialog.html';

@WithRender
@Component
export default class LoginDialog extends AbstractDialog {
    protected email: string = '';
    protected password: string = '';

    public authenticate() {
        let authData = {
            email: this.email,
            password: this.password,
        };

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
            M.toast({html: error.response.data.message, classes: 'red darken-2 white-text'});
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
