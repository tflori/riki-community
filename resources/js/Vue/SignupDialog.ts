import Vue from 'vue';
import Component from 'vue-class-component';

@Component({
    template: `
        <div id="signup-dialog" class="modal">
            <button class="btn-small btn-flat right modal-close"><i class="material-icons">close</i></button>
            <div class="modal-content row">
                <div class="col s12">
                    <h4>Signup</h4>
                </div>
                <form class="col s12 l8 offset-l2" @submit.prevent="register" action="/any_url">
                    <div class="row">
                        <div class="input-field col s12">
                            <input class="validate" id="signup-email" name="email" type="email" v-model="email" />
                            <label for="signup-email">eMail Address</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="signup-password" name="password" type="password" v-model="password" />
                            <label for="signup-password">Password</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="signup-password-confirmation" name="password-confirmation" type="password" 
                                 v-model="passwordConfirmation" />
                            <label for="signup-password-confirmation">Confirmation</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="signup-displayName" name="displayName" type="text" v-model="displayName" />
                            <label for="signup-displayName">Pseudonym</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="signup-name" name="name" type="text" v-model="name" />
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

    public mounted() {
        this.$nextTick(() => {
            let $el = jQuery(this.$el);
            $el.modal({
                onOpenStart: this.reset,
                onOpenEnd: () => {
                    $el.find('#signup-email').focus();
                },
            });
        });
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
        jQuery('#login-dialog').modal('open');
    }

    public register() {
        console.log(this.email, this.password, this.passwordConfirmation);
    }

    public close() {
        jQuery(this.$el).modal('close');
    }
}
