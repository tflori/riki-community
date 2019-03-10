import Vue from 'vue';
import Component from 'vue-class-component';
import SignupDialog from './SignupDialog';
import Modal = M.Modal;
import WithRender from '@view/LoginDialog.html';

@WithRender
@Component
export default class LoginDialog extends Vue {
    protected email: string = '';
    protected password: string = '';

    protected _modalInstance: Modal|undefined;

    protected get dialog(): Modal {
        if (!this._modalInstance) {
            this._modalInstance = Modal.init(this.$el, {
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

    public authenticate() {
        // for now lets assume it works - so we store a user object in $root.$data.user
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
