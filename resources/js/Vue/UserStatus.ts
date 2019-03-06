import Vue from 'vue';
import Component from 'vue-class-component';

// The @Component decorator indicates the class is a Vue component
@Component({
    // All component options are allowed in here
    template: `
        <li class="icon user-status">
            <a v-if="!user" class="btnLogin" @click="$root.$refs.loginDialog.open()">
                <i class="material-icons left">account_circle</i>
                <span class="icon-text"> Login / Signup</span>
            </a>
            <a v-else @click="showUserMenu" class="btnLogin">
                <div class="avatar" :style="{background: 'url(' + user.avatar + ')'}"></div>
                <span class="icon-text"> {{ user.displayName }}</span>
            </a>
        </li>
    `,
})
export default class UserStatus extends Vue {
    get user() {
        return this.$root.$data.user;
    }

    showUserMenu (): void {
        alert(JSON.stringify(this.user));
    }
}
