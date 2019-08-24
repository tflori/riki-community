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
            axios({
                method: 'post',
                url: '/user/activate',
                data,
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
        });
    }
}
