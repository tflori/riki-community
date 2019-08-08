import AbstractDialog from "@src/Vue/AbstractDialog";
import ActivateDialog from "@src/Vue/ActivateDialog";
import ConfirmDialog from "@src/Vue/ConfirmDialog";
import LoginDialog from '@src/Vue/LoginDialog';
import SignupDialog from '@src/Vue/SignupDialog';
import UserStatus from '@src/Vue/UserStatus';
import axios from 'axios';
import Vue from 'vue';
import Component from 'vue-class-component';
import VueResource from 'vue-resource';

Vue.use(VueResource);

@Component({
    components: {
        UserStatus,
        LoginDialog,
        SignupDialog,
        ActivateDialog,
    },
})
export default class App extends Vue {
    public data() {
        return {
            user: null,
        };
    }

    public openDialog<T extends AbstractDialog>(Dialog: { new(options: any): T }, options: any = {}): T {
        let dialog = new Dialog(Object.assign(options, {
            parent: this
        }));
        dialog.$mount();
        (<HTMLElement>this.$refs.overlayContainer).appendChild(dialog.$el);
        dialog.open();
        return dialog;
    }

    /**
     * Request an acknowledgement from the user
     *
     * Returns a Promise that is resolved as soon the user clicks on the button.
     *
     * Title is optional and button has the default value 'OK'
     *
     * Example:
     * (<App>this.$root).acknowledge({text: 'Login failed'}).then(() => {
     *     this.password = '';
     *     (<HTMLElement>this.$refs.password).focus();
     * })
     *
     * @param {string} text
     * @param {string?} title
     * @param {string?} button
     * @return {Promise<void>}
     */
    public acknowledge({text, title, button = 'OK'}: {
        text: string,
        title?: string,
        button?: string,
    }): Promise<void> {
        return new Promise((resolve) => {
            let dialog = this.openDialog(ConfirmDialog, {
                data: {
                    text,
                    title,
                    buttons: [{
                        text: button,
                        action: () => {
                            dialog.close();
                            resolve();
                        },
                    }],
                },
            });
        });
    }


    /**
     * Request a confirmation from the user
     *
     * Returns a Promise that is resolved with true or false as soon the user clicks on one of the button.
     *
     * Title is optional and buttons have the default values 'Yes' and 'No'
     *
     * Example:
     * (<App>this.$root).confirm({text: 'Login failed', confirm: 'Retry', cancel: 'Cancel'}).then((response) => {
     *     if (response) {
     *         this.password = '';
     *         (<HTMLElement>this.$refs.password).focus();
     *     } else {
     *         this.close();
     *     }
     * });
     *
     * @param {string} text      Text to show in the body
     * @param {string?} title    Title to show
     * @param {string?} confirm  Text on the confirm button
     * @param {string?} cancel   Text on the cancel button
     * @return {Promise<void>}
     */
    public confirm({text, title, confirm = 'Yes', cancel = 'No'}: {
        text: string,
        title?: string,
        confirm?: string,
        cancel?: string,
    }): Promise<boolean> {
        return new Promise((resolve) => {
            let dialog = this.openDialog(ConfirmDialog, {
                data: {
                    text,
                    title,
                    buttons: [
                        {
                            text: cancel,
                            action: () => {
                                dialog.close();
                                resolve(false);
                            },
                        },
                        {
                            text: confirm,
                            action: () => {
                                dialog.close();
                                resolve(true);
                            },
                        },
                    ],
                },
            });
        });
    }

    public getCsrfToken(): Promise<string | void> {
        return axios({
            method: 'get',
            url: '/auth/token',
        }).then((response) => {
            return response.data;
        }).catch((response) => {
            /* istanbul ignore next */
            console.warn('Could not receive csrf token', response);
        });
    }
}
