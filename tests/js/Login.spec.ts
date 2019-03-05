import UserStatus from '../../resources/js/Vue/UserStatus';

import Vue from 'vue';

describe('User Status', () => {
    it('is a vue component', () => {
        let login = new UserStatus();

        expect(login instanceof Vue).toBeTruthy();
    });
});
