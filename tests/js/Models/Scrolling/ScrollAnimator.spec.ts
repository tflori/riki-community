import {Easing, EasingDirection, EasingFx} from '@src/Models/Scrolling/AnimationSpeed';
import {ScrollAnimation} from "@src/Models/Scrolling/ScrollAnimation";
import {ScrollAnimator} from '@src/Models/Scrolling/ScrollAnimator';
import $ from 'jquery';

describe('ScrollAnimator', () => {
    beforeEach(() => {
        // @ts-ignore
        window.jQuery = $;
    });

    describe('start', () => {
        it('registers an event handler', () => {
            spyOn($.fn, 'on').and.returnValue($.fn);
            spyOn($.fn, 'triggerHandler');
            let scrollAnimator = new ScrollAnimator([]);

            scrollAnimator.start();

            expect($.fn.on).toHaveBeenCalledWith('scroll', jasmine.any(Function));
            expect($.fn.triggerHandler).toHaveBeenCalledWith('scroll');
        });
    });

    describe('stop', () => {
        it('removes the event handler', () => {
            spyOn($.fn, 'off');
            let scrollAnimator = new ScrollAnimator([]);
            scrollAnimator.start();

            scrollAnimator.stop();

            expect($.fn.off).toHaveBeenCalledWith('scroll', jasmine.any(Function));
        });

        it('resets all scrollAnimations', () => {
            let scrollAnimation = new ScrollAnimation($('<div>'), 'top', {
                from: 0,
                to: 10,
                start: 10,
                end: 5,
                suffix: 'vh',
            });
            let scrollAnimator = new ScrollAnimator([scrollAnimation]);
            spyOn(scrollAnimation, 'reset');
            scrollAnimator.start();

            scrollAnimator.stop();

            expect(scrollAnimation.reset).toHaveBeenCalled();
        });
    });

    describe('scroll handler', () => {
        let scrollHandler: () => void | boolean;
        let scrollAnimator: ScrollAnimator;

        beforeEach(() => {
            spyOn($.fn, 'on').and.callFake((event: string, handler: () => void | boolean) => {
                if (event === 'scroll') {
                    scrollHandler = handler;
                }
                return $.fn;
            });
            scrollAnimator = new ScrollAnimator([]);
            scrollAnimator.start();
            jest.useFakeTimers();
        });

        afterEach(() => {
            jest.useRealTimers();
        });

        it('executes all animations for the current scrollTop', () => {
            let animation1: ScrollAnimation = new ScrollAnimation($('<div>'), 'left', {
                from: 0,
                to: 50,
                suffix: 'px',
                start: 0,
                end: 100,
                easing: new Easing(EasingFx.Quad, EasingDirection.Out)
            });
            let animation2: ScrollAnimation = new ScrollAnimation($('<div>'), 'top', {
                from: 0,
                to: 50,
                suffix: 'px',
                start: 0,
                end: 100,
                easing: new Easing(EasingFx.Quad, EasingDirection.In)
            });
            scrollAnimator.scrollAnimations.push(animation1, animation2);
            spyOn(animation1, 'execute');
            spyOn(animation2, 'execute');
            document.documentElement.scrollTop = 10;

            scrollHandler();

            expect(animation1.execute).not.toHaveBeenCalled();
            expect(animation2.execute).not.toHaveBeenCalled();

            jest.runAllTimers();

            expect(animation1.execute).toHaveBeenCalledWith(10);
            expect(animation2.execute).toHaveBeenCalledWith(10);
        });

        it('uses document.body.scrollTop as fallback', () => {
            let animation1: ScrollAnimation = new ScrollAnimation($('<div>'), 'left', {
                from: 0,
                to: 50,
                suffix: 'px',
                start: 0,
                end: 100,
                easing: new Easing(EasingFx.Quad, EasingDirection.Out)
            });
            scrollAnimator.scrollAnimations.push(animation1);
            spyOn(animation1, 'execute');
            // @ts-ignore
            document.documentElement.scrollTop = undefined;
            document.body.scrollTop = 10;

            scrollHandler();
            jest.runAllTimers();

            expect(animation1.execute).toHaveBeenCalledWith(10);
        });

        it('does not execute two times', () => {
            let animation1: ScrollAnimation = new ScrollAnimation($('<div>'), 'left', {
                from: 0,
                to: 50,
                suffix: 'px',
                start: 0,
                end: 100,
                easing: new Easing(EasingFx.Quad, EasingDirection.Out)
            });
            scrollAnimator.scrollAnimations.push(animation1);
            spyOn(animation1, 'execute');
            document.documentElement.scrollTop = 10;

            scrollHandler();
            scrollHandler();
            jest.runAllTimers();

            expect(animation1.execute).toHaveBeenCalledWith(10);
            expect(animation1.execute).toHaveBeenCalledTimes(1);
        });
    });
});
