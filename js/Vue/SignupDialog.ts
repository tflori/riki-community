/* istanbul ignore file */
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
        this.$nextTick(M.updateTextFields);
    }

    public showLogin() {
        this.close();
        (<LoginDialog>this.$root.$refs.loginDialog).open();
    }

    public register() {
        console.log(this.email, this.password, this.passwordConfirmation);
    }
}
