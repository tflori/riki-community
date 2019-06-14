/* istanbul ignore file */
import axios from 'axios';
import Component from 'vue-class-component';
import M from 'materialize-css';
import Vue from 'vue';

import LoginDialog from './LoginDialog';
import WithRender from '../../resources/views/components/SignupDialog.html';

@WithRender
@Component
export default class SignupDialog extends Vue {
    static readonly ERROR_MESSAGES: any = {
        PASSWORD_TO_WEAK: 'This password is to weak.',
        NOT_EQUAL: 'Passwords do not match.',
        NO_MATCH: {
            displayName: 'Only characters from this character class are allowed [\w @._-].',
            name: 'Only letters, numbers, spaces, dots and dashes are allowed.',
        }
    };

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
        }).then(console.log).catch((error) => {
            if (error.response.status === 400 && error.response.data.message === 'Invalid user data') {
                let errors = error.response.data.errors;
                for (let field in errors) {
                    this.errors[field] = errors[field].map((error: {key: string, message: string}) => {
                        if (SignupDialog.ERROR_MESSAGES[error.key]) {
                            return typeof SignupDialog.ERROR_MESSAGES[error.key] === 'object' ?
                                SignupDialog.ERROR_MESSAGES[error.key][field] :
                                SignupDialog.ERROR_MESSAGES[error.key];
                        }

                        return error.message;
                    });
                }
                console.log(this.errors);
                // this.errors = errors;
            }
        });
    }
}
