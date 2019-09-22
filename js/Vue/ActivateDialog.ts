import AbstractDialog from "@src/Vue/AbstractDialog";
import App from "@src/Vue/App";
import WithRender from '@view/ActivateDialog.html';
import axios from 'axios';
import M from "materialize-css";
import Component from 'vue-class-component';

@WithRender
@Component
export default class ActivateDialog extends AbstractDialog {
    protected code: string = '';
    protected errorMessage: string = '';

    protected opened() {
        /* istanbul ignore next */
        (<HTMLElement>this.$refs.code).focus();
    }

    public activate() {
        (<App>this.$root).getCsrfToken().then((csrfToken) => {
            let data = {
                csrf_token: csrfToken,
                token: this.code,
            };

            this.errorMessage = '';
            return axios.post('/user/activate', data);
        }).then((response) => {
            this.close();
            this.$root.$data.user = response.data;
            M.toast({html: "Account activated!"})
        }).catch((error) => {
            if (error.response && error.response.data && error.response.data.message) {
                this.errorMessage = error.response.data.message;
                (<HTMLElement>this.$refs.code).focus();
            } else {
                console.warn('Activation failed for unknown reason', error);
            }
        });
    }

    public resendActivation() {
        (<App>this.$root).getCsrfToken().then((csrfToken) => {
            return axios.get('/user/resendActivation', {params: {csrf_token: csrfToken}});
        }).then(() => {
            M.toast({html: 'New activation code sent!'});
        }).catch((error) => {
            console.warn('Resend activation code failed for unknown reason', error);
        });
    }
}
