/* istanbul ignore file */
import Vue from 'vue';
import Component from 'vue-class-component';
import VueResource from 'vue-resource';

import LoginDialog from './LoginDialog';
import SignupDialog from './SignupDialog';
import UserStatus from './UserStatus';

Vue.use(VueResource);

@Component({
    components: {
        UserStatus,
        LoginDialog,
        SignupDialog,
    },
})
export default class App extends Vue {
    public data() {
        return {
            user: null,
        };
    }

    public created() {
        // @todo update user status
    }
}
