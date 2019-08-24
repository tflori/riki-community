import App from "@src/Vue/App";
import LoginDialog from '@src/Vue/LoginDialog';
import UserStatus from '@src/Vue/UserStatus';
import moxios from "moxios";
import Vue from 'vue';
import {clickOn, containing, prepareComponent} from '../helper';

describe('UserStatus', () => {
    beforeAll(() => {
        Vue.config.productionTip = false;
        Vue.config.devtools = false;
    });

    beforeEach(() => {
        // @ts-ignore
        UserStatus.authCheck = undefined;
        moxios.install();
    });

    afterEach(() => {
        moxios.uninstall();
    });

    it('is a vue component', () => {
        let userStatus = new UserStatus();

        expect(userStatus).toBeInstanceOf(Vue);
    });

    describe('created', () => {
        it('requests the auth status', (done) => {
            moxios.wait(() => {
                let request = moxios.requests.mostRecent();

                expect(request).toBeDefined();
                expect(request.config.method).toBe('get');
                expect(request.url).toBe('/auth');
                done();
            }, 0);

            new UserStatus();
        });

        it('stores the response', (done) => {
            let user = {
                id: 23,
                displayName: 'john',
            };
            moxios.stubRequest('/auth', {
                status: 200,
                response: user
            });

            let userStatus = new UserStatus();

            setTimeout(() => {
                expect(userStatus.$root.$data.user).toEqual(user);
                done();
            })
        });

        it('shows a warning when the request fails', (done) => {
            spyOn(console, 'warn');
            moxios.stubRequest('/auth', {
                status: 500,
                response: 'Key value store not available',
            });

            new UserStatus();

            setTimeout(() => {
                expect(console.warn).toHaveBeenCalled();
                done();
            })
        });
    });

    describe('without login', () => {
        it('renders a link to login dialog', () => {
            let userStatus = new UserStatus();

            userStatus.$mount();

            expect(containing(userStatus.$el.querySelectorAll('a'), 'Login').length).toBe(1);
        });

        it('opens the login dialog on click', () => {
            let app = new App();
            let userStatus = new UserStatus({
                parent: app,
            });
            userStatus.$mount();
            spyOn(app, 'openDialog');

            clickOn(userStatus.$el.querySelector('a'));

            expect(app.openDialog).toHaveBeenCalledWith(LoginDialog);
        });
    });

    describe('with login', () => {
        it('renders a link to user menu', () => {
            let userStatus = new UserStatus();
            userStatus.$root.$data.user = {id: 23, name: "John Doe", displayName: "jdoe"};

            userStatus.$mount();

            expect(containing(userStatus.$el.querySelectorAll('a'), 'jdoe').length).toBe(1);
        });

        it('opens the userMenu on click', (done) => {
            let userStatus = prepareComponent(UserStatus);
            userStatus.$root.$data.user = {id: 23, name: "John Doe", displayName: "jdoe"};

            userStatus.$nextTick(() => {
                clickOn(<HTMLElement>userStatus.$refs.userMenuButton);

                expect((<HTMLElement>userStatus.$refs.userMenu).style.display).toBe('block');
                done();
            });
        });
    });
});
