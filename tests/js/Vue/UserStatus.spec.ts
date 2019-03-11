import UserStatus from '@src/Vue/UserStatus';

import Vue from 'vue';

describe('UserStatus', () => {
    it('is a vue component', () => {
        let userStatus = new UserStatus();

        expect(userStatus instanceof Vue).toBeTruthy();
    });

    it('renders a li with class uer-status', () => {
        let userStatus = new UserStatus();

        userStatus.$mount();

        expect(userStatus.$el.nodeName).toBe('LI');
        expect(userStatus.$el.classList).toContain('user-status');
    });
});
