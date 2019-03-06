import Vue from 'vue';
import Component from 'vue-class-component';
import LoginDialog from './LoginDialog';
import Modal = M.Modal;

@Component({
    template: `
        <div class="modal">
            <button class="btn-small btn-flat right modal-close"><i class="material-icons">close</i></button>
            <div class="modal-content row">
                <div class="col s12">
                    <h4>Signup</h4>
                </div>
                <form class="col s12 l8 offset-l2" @submit.prevent="register" action="/any_url">
                    <div class="row">
                        <div class="input-field col s12">
                            <input class="validate" id="signup-email" ref="email" type="email" v-model="email" />
                            <label for="signup-email">eMail Address</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="signup-password" type="password" v-model="password" />
                            <label for="signup-password">Password</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="signup-password-confirmation" type="password" v-model="passwordConfirmation" />
                            <label for="signup-password-confirmation">Confirmation</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="signup-displayName" type="text" v-model="displayName" />
                            <label for="signup-displayName">Pseudonym</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="signup-name" type="text" v-model="name" />
                            <label for="signup-name">Name</label>
                        </div>
                    </div>
                    <button class="btn waves-effect waves-light" type="submit">
                        Signup <i class="material-icons right">send</i>
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <!--<span class="">{{ registered ? '' : '' }}</span>-->
                <a class="btn-small grey waves-effect waves-light" @click="showLogin">
                    back to login
                </a>
            </div>
        </div>
    `,
})
export default class SignupDialog extends Vue {
    protected email: string = '';
    protected password: string = '';
    protected passwordConfirmation: string = '';
    protected displayName: string = '';
    protected name: string = '';

    protected _modalInstance: Modal|undefined;

    protected get dialog(): Modal {
        if (!this._modalInstance) {
            this._modalInstance = Modal.init(this.$el, {
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
