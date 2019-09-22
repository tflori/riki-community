import ActivateDialog from "@src/Vue/ActivateDialog";
import App from "@src/Vue/App";
import LoginDialog from "@src/Vue/LoginDialog";
import WithRender from '@view/UserStatus.html'
import axios, {AxiosResponse} from 'axios';
import Vue from 'vue';
import Component from 'vue-class-component';

@WithRender
@Component
export default class UserStatus extends Vue {
    protected static authCheck: Promise<AxiosResponse>;
    private _userMenu: M.Dropdown | undefined;

    get user() {
        return this.$root.$data.user;
    }

    public created() {
        if (!UserStatus.authCheck) {
            UserStatus.authCheck = axios.get('/auth');
            UserStatus.authCheck.then((response) => {
                if (response.data) {
                    this.$root.$data.user = response.data;
                }
            }).catch((error) => {
                console.warn('Could not receive user status', error);
            });
        }
    }

    /* istanbul ignore next */
    public openLoginDialog(): void {
        (<App>this.$root).openDialog(LoginDialog);
    }

    /* istanbul ignore next */
    public openActivateDialog(): void {
        (<App>this.$root).openDialog(ActivateDialog);
    }

    public logout(): void {
        (<App>this.$root).getCsrfToken().then((csrfToken) => {
            return axios.delete('/auth', {
                params: {
                    csrf_token: csrfToken,
                }
            });
        }).then(() => {
            if (this._userMenu) {
                this._userMenu.destroy();
                this._userMenu = undefined;
            }
            this.$root.$data.user = null;
            M.toast({html: 'Successfully logged out!'});
        }).catch((error)  => {
            console.warn('Logout failed for unknown reason', error);
        });
    }

    protected get userMenu(): M.Dropdown {
        if (!this._userMenu) {
            this._userMenu = M.Dropdown.init(<HTMLElement>this.$refs.userMenuButton, {
                alignment: 'right',
                constrainWidth: false,
                coverTrigger: false,
                container: <HTMLElement>this.$root.$refs.overlayContainer,
            });
        }
        return <M.Dropdown>this._userMenu;
    }
}
