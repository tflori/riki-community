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

    protected authenticate() {
        // for now lets assume it works - so we store a user object in $root.$data.user
        /* istanbul ignore next */
        this.$root.$data.user = {
            id: Math.round(Math.random() * 1000000),
            name: "John Arthur Doe",
            email: "john.doe@example.com",
            avatar: "https://www.gravatar.com/avatar/3cbc553a0a4353f5986bf8e8d36fe64a?s=24",
            displayName: "arthur42",
        };
        this.close();
    }

    public showSignup() {
        this.close();
        (<SignupDialog>this.$root.$refs.signupDialog).open();
    }
}
