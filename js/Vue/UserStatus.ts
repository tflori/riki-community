import App from "@src/Vue/App";
import axios, {AxiosResponse} from 'axios';
import Component from 'vue-class-component';
import LoginDialog from "@src/Vue/LoginDialog";
import Vue from 'vue';
import WithRender from '@view/UserStatus.html'

@WithRender
@Component
export default class UserStatus extends Vue{
    protected static authCheck: Promise<AxiosResponse>;

    get user() {
        return this.$root.$data.user;
    }

    // public created() {
    //     if (!UserStatus.authCheck) {
    //         UserStatus.authCheck = axios({
    //             method: 'get',
    //             url: '/auth'
    //         });
    //         UserStatus.authCheck.then((response) => {
    //             if (response.data) {
    //                 this.$root.$data.user = response.data;
    //             }
    //         });
    //     }
    // }

    public openLoginDialog(): void {
        (<App>this.$root).openDialog(LoginDialog);
    }

    /* istanbul ignore next */
    public showUserMenu (): void {
        alert(JSON.stringify(this.user));
    }
}
