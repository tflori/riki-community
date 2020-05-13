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
    private recaptchaLoaded!: Promise<void>;

    public data() {
        return {
            user: null,
            darkmodeEnabled: true,
        };
    }

    /**
     * Open a Dialog with options
     *
     * Creates an instance of Dialog, appends it to the overlayContainer and opens it.
     *
     * Returns the created instance
     *
     * @param {T extends AbstractDialog} Dialog
     * @param {any} options
     * @return {T extends AbstractDialog}
     */
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

    /**
     * Get an CSRF token for the current session
     *
     * @return {Promise<string>}
     */
    public getCsrfToken(): Promise<string> {
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

    /* istanbul ignore next: can't be tested in unit tests */
    /**
     * Load Googles recaptcha v3
     *
     * @return {Promise<void>}
     */
    public loadRecaptcha(): Promise<void> {
        if (!this.recaptchaLoaded) {
            this.recaptchaLoaded = new Promise((resolve) => {
                jQuery.getScript('https://www.google.com/recaptcha/api.js?render=' + AppConfig.recaptchaKey, () => {
                    grecaptcha.ready(resolve);
                });
            });
        }

        return this.recaptchaLoaded;
    }

    /* istanbul ignore next: can't be tested in unit tests */
    /**
     * Get recaptcha token
     *
     * @param {string} action
     * @return {Promise<string>}
     */
    public getRecaptchaToken(action: string): Promise<string> {
        return this.loadRecaptcha().then(() => {
            return grecaptcha.execute(AppConfig.recaptchaKey, {action});
        });
    }
}
