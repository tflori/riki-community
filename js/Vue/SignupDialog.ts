import axios from 'axios';
import Component from 'vue-class-component';
import M from 'materialize-css';
import Vue from 'vue';

import LoginDialog from './LoginDialog';
import WithRender from '../../resources/views/components/SignupDialog.html';

@WithRender
@Component
export default class SignupDialog extends Vue {
    protected email: string = '';
    protected password: string = '';
    protected passwordConfirmation: string = '';
    protected displayName: string = '';
    protected name: string = '';
    protected errors: any = {};

    protected _modalInstance: M.Modal|undefined;

    protected get dialog(): M.Modal {
        if (!this._modalInstance) {
            this._modalInstance = M.Modal.init(this.$el, {
                onOpenStart: this.reset,
                onOpenEnd: () => {
                    /* istanbul ignore next */
                    (<HTMLElement>this.$refs.email).focus();
                },
            });
        }

        return this._modalInstance;
    }

    public close() {
        this.dialog.close();
    }

    public open() {
        this.dialog.open();
    }

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
        (<LoginDialog>this.$root.$refs.loginDialog).open();
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
