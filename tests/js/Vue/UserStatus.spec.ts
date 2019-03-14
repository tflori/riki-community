import Vue from 'vue';
import { clickOn } from '../helper';

import LoginDialog from '@src/Vue/LoginDialog';
import UserStatus from '@src/Vue/UserStatus';

describe('UserStatus', () => {
    it('is a vue component', () => {
        let userStatus = new UserStatus();

        expect(userStatus instanceof Vue).toBeTruthy();
    });

    describe('without login', () =>{
        it('renders a link to login dialog', () => {
            let userStatus = new UserStatus();

            userStatus.$mount();

            expect(userStatus.$el.innerHTML).toMatch(/<a[^>]*>.*Login.*<\/a>/);
        });

        it('opens the login dialog on click', () => {
            let userStatus = new UserStatus();
            userStatus.$mount();
            let loginDialog = userStatus.$root.$refs.loginDialog = new LoginDialog();
            spyOn(loginDialog, 'open').and.returnValue(undefined);

            clickOn(userStatus.$el.querySelector('a'));

            expect(loginDialog.open).toHaveBeenCalled();
        });
    });

    describe('with login', () => {
        it('renders a link to user menu', () => {
            let userStatus = new UserStatus();
            userStatus.$root.$data.user = {id: 23, name: "John Doe", displayName: "jdoe"};

            userStatus.$mount();

            expect(userStatus.$el.innerHTML).toMatch(/<a[^>]*>.*jdoe.*<\/a>/);
        });
    });
});
