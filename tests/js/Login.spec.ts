import Login from '../../resources/js/Login';

import Vue from 'vue';

describe('Login dialog', () => {
    it('is a vue component', () => {
        let login = new Login();

        expect(login instanceof Vue).toBeTruthy();
    });
});
