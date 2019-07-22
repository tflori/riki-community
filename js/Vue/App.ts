import AbstractDialog from "@src/Vue/AbstractDialog";
import ActivateDialog from "@src/Vue/ActivateDialog";
import Component from 'vue-class-component';
import LoginDialog from '@src/Vue/LoginDialog';
import SignupDialog from '@src/Vue/SignupDialog';
import UserStatus from '@src/Vue/UserStatus';
import Vue from 'vue';
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

    public created() {
        // @todo update user status
    }

    public openDialog(Dialog: { new(options: any): AbstractDialog }) {
        let dialog = new Dialog({
            parent: this
        });
        dialog.$mount();
        (<HTMLElement>this.$refs.overlayContainer).appendChild(dialog.$el);
        dialog.open();
    }
}
