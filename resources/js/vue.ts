import Vue from 'vue';
import LoginDialog from './Vue/LoginDialog';
import SignupDialog from './Vue/SignupDialog';
import UserStatus from './Vue/UserStatus';

// @ts-ignore
export const vm = new Vue({
    el: '#riki-community',
    components: {
        UserStatus,
        LoginDialog,
        SignupDialog,
    },
    data: {
        user: null,
    },
});
