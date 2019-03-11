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
        console.log('something happened');
        this.$data.user = {
            id: Math.round(Math.random() * 1000000),
            name: "John Arthur Doe",
            email: "john.doe@example.com",
            avatar: "https://www.gravatar.com/avatar/3cbc553a0a4353f5986bf8e8d36fe64a?s=24",
            displayName: "arthur42",
        };
    }
}
