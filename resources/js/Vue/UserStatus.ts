import Vue from 'vue';
import Component from 'vue-class-component';
import WithRender from '@view/UserStatus.html'

@WithRender
@Component
export default class UserStatus extends Vue{
    get user() {
        return this.$root.$data.user;
    }

    showUserMenu (): void {
        alert(JSON.stringify(this.user));
    }
}
