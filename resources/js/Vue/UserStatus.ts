import Component from 'vue-class-component';
import Vue from 'vue';

import WithRender from '@view/UserStatus.html'

@WithRender
@Component
export default class UserStatus extends Vue{
    get user() {
        return this.$root.$data.user;
    }

    /* istanbul ignore next */
    showUserMenu (): void {
        alert(JSON.stringify(this.user));
    }
}
