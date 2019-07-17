import axios from 'axios';
import Component from 'vue-class-component';
import M from 'materialize-css';
import Vue from 'vue';

import SignupDialog from './SignupDialog';
import WithRender from '../../resources/views/components/LoginDialog.html';

@WithRender
@Component
export default class LoginDialog extends Vue {
    protected email: string = '';
    protected password: string = '';
    protected _modalInstance: M.Modal|undefined;

    public close() {
        this.dialog.close();
    }

    public open() {
        this.dialog.open();
    }

    protected get dialog(): M.Modal {
        if (!this._modalInstance) {
            this._modalInstance = M.Modal.init(this.$el, {
                onOpenEnd: () => {
                    /* istanbul ignore next */
                    (<HTMLElement>this.$refs.email).focus();
                },
            });
        }

        return this._modalInstance;
    }

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
        }).catch((error) => {
            M.toast({html: error.response.data.message, classes: 'red darken-2 white-text'});
        });
    }

    public showSignup() {
        this.close();
        (<SignupDialog>this.$root.$refs.signupDialog).open();
    }
}
