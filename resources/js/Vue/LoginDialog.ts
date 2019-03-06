import Vue from 'vue';
import Component from 'vue-class-component';
import SignupDialog from './SignupDialog';
import Modal = M.Modal;

@Component({
    template: `
        <div class="modal">
            <button class="btn-small btn-flat right modal-close"><i class="material-icons">close</i></button>
            <div class="modal-content row">
                <div class="col s12">
                    <h4>Login</h4>
                </div>
                <form class="col s12 l8 offset-l2" @submit.prevent="authenticate" action="/any_url">
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="login-email" name="email" ref="email" type="email" v-model="email" />
                            <label for="login-email">eMail Address</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="login-password" name="password" type="password" v-model="password" />
                            <label for="login-password">Password</label>
                        </div>
                    </div>
                    <button class="btn waves-effect waves-light" type="submit">
                        Login <i class="material-icons right">arrow_forward_ios</i>
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn-small light-blue waves-effect waves-light" @click="showSignup">
                    No Account? Signup Now!
                </a>
            </div>
        </div>
    `,
})
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
