import $ from 'jquery';
import { Easing, EasingDirection, EasingFx } from '../../resources/js/AnimationSpeed';
import { CalculatedStep, ScrollAnimation, ScrollAnimator, StaticStep } from '../../resources/js/ScrollAnimator';

describe('ScrollAnimation', () => {
    describe('constructor', () => {
        it('sorts the steps', () => {
            let step1 = new StaticStep(1, 0);
            let step2 = new StaticStep(0, 1, undefined, 10);
            let animation = {
                from: 0,
                to: 20,
                steps: [
                    step2,
                    step1,
                ],
            };

            new ScrollAnimation($('<div>'), 'opacity', animation);

            expect(animation.steps[0]).toBe(step1);
            expect(animation.steps[1]).toBe(step2);
        });

        it('forces the first step to start from scroll animation', () => {
            let step1 = new StaticStep(1, 0, undefined, 10);
            let animation = {
                from: 0,
                to: 20,
                steps: [ step1 ],
            };

            new ScrollAnimation($('<div>'), 'opacity', animation);

            expect(animation.steps[0].from).toBe(0);
        });

        it('forces steps with same from to start 1px later', () => {
            let animation = {
                from: 0,
                to: 20,
                steps: [
                    new StaticStep(1, 0),
                    new StaticStep(0, 1),
                ],
            };

            new ScrollAnimation($('<div>'), 'opacity', animation);

            expect(animation.steps[0].from).toBe(0);
            expect(animation.steps[1].from).toBe(1);
        });

        it('sets to of all steps', () => {
            let animation = {
                from: 0,
                to: 20,
                steps: [
                    new StaticStep(1, 0),
                    new StaticStep(0, 1, undefined, 10),
                ],
            };

            new ScrollAnimation($('<div>'), 'opacity', animation);

            expect(animation.steps[0].to).toBe(10);
            expect(animation.steps[1].to).toBe(20);
        });

        it('keeps existing to', () => {
            let animation = {
                from: 0,
                to: 20,
                steps: [
                    new StaticStep(1, 0, undefined, 0, 2),
                    new StaticStep(0, 1, undefined, 10),
                ],
            };

            new ScrollAnimation($('<div>'), 'opacity', animation);

            expect(animation.steps[0].to).toBe(2);
            expect(animation.steps[1].to).toBe(20);
        });

        it('forces to to a maximum of from -1', () => {
            let animation = {
                from: 0,
                to: 20,
                steps: [
                    new StaticStep(1, 0, undefined, 0, 12),
                    new StaticStep(0, 1, undefined, 10),
                ],
            };

            new ScrollAnimation($('<div>'), 'opacity', animation);

            expect(animation.steps[0].to).toBe(10);
            expect(animation.steps[1].to).toBe(20);
        });

        it('does not require steps', () => {
            expect(() => {
                new ScrollAnimation($('<div>'), 'opacity', {
                    from: 0,
                    to: 20,
                });
            }).not.toThrow();
        });

        it('creates a step from start and end', () => {
            let easing = new Easing(EasingFx.Quad, EasingDirection.In);

            let scrollAnimation = new ScrollAnimation($('<div>'), 'opacity', {
                from: 0,
                to: 20,
                steps: [],
                start: 0,
                end: 1,
                easing,
            });

            // @ts-ignore
            expect(scrollAnimation.steps[0]).toBeInstanceOf(StaticStep);
            // @ts-ignore
            expect(scrollAnimation.steps[0].start).toBe(0);
            // @ts-ignore
            expect(scrollAnimation.steps[0].end).toBe(1);
            // @ts-ignore
            expect(scrollAnimation.steps[0].easing).toBe(easing);
        });
    });

    describe('execute', () => {
        beforeEach(() => {
            jest.useFakeTimers();
        });

        afterEach(() => {
            jest.useRealTimers();
        });

        it('applies before when scrollTop < animation.from', () => {
            let element = $('<div>');
            spyOn(element, 'css');
            let scrollAnimation = new ScrollAnimation(element, 'opacity', {
                from: 10,
                to: 20,
                before: '1',
            });

            scrollAnimation.execute(0);

            expect(element.css).toHaveBeenCalledWith('opacity', '1');
        });

        it('applies after when scrollTop > animation.to', () => {
            let element = $('<div>');
            spyOn(element, 'css');
            let scrollAnimation = new ScrollAnimation(element, 'opacity', {
                from: 0,
                to: 20,
                before: '0',
                after: '1',
            });

            scrollAnimation.execute(25);

            expect(element.css).toHaveBeenCalledWith('opacity', '1');
        });

        it('calculates the ratio of the scroll position and applies it', () => {
            let element = $('<div>');
            spyOn(element, 'css');
            let scrollAnimation = new ScrollAnimation(element, 'left', {
                from: 0,
                to: 20,
                steps: [
                    new StaticStep(0, 10),
                ],
                suffix: 'px'
            });

            scrollAnimation.execute(15);

            expect(element.css).toHaveBeenCalledWith('left', '7.5px');
        });

        it('applies the minimum for scrollTop < animation.from', () => {
            let element = $('<div>');
            spyOn(element, 'css');
            let scrollAnimation = new ScrollAnimation(element, 'left', {
                from: 10,
                to: 20,
                suffix: 'px',
                steps: [
                    new StaticStep(0, 10),
                ]
            });

            scrollAnimation.execute(5);

            expect(element.css).toHaveBeenCalledWith('left', '0px');
        });

        it('applies the maximum for scrollTop > animation.from', () => {
            let element = $('<div>');
            spyOn(element, 'css');
            let scrollAnimation = new ScrollAnimation(element, 'left', {
                from: 10,
                to: 20,
                suffix: 'px',
                steps: [
                    new StaticStep(0, 10),
                ]
            });

            scrollAnimation.execute(25);

            expect(element.css).toHaveBeenCalledWith('left', '10px');
        });

        it('applies easing if defined', () => {
            let element = $('<div>');
            spyOn(element, 'css');
            let scrollAnimation = new ScrollAnimation(element, 'left', {
                from: 10,
                to: 20,
                suffix: 'px',
                steps: [
                    new StaticStep(0, 10, new Easing(EasingFx.Quad, EasingDirection.In)),
                ],
            });

            scrollAnimation.execute(15);

            expect(element.css).toHaveBeenCalledWith('left', '2.5px');
        });

        it('finds the current step', () => {
            let element = $('<div>');
            spyOn(element, 'css');
            let scrollAnimation = new ScrollAnimation(element, 'left', {
                from: 0,
                to: 30,
                suffix: 'px',
                steps: [
                    new StaticStep(0, 20, undefined,  0),
                    new StaticStep(20, 0, undefined, 10), // from scrollTop 10 to scrollTop 20
                    new StaticStep(0, 40, undefined, 20), // unused step in this test
                ],
            });

            scrollAnimation.execute(15); // it has to use the second step

            expect(element.css).toHaveBeenCalledWith('left', '10px'); // 20 + (-20*0.5) = 10
        });

        it('calls calc from a calculated step', () => {
            let step = new CalculatedStep(function () {
                return 23;
            });
            spyOn(step, 'calc').and.callThrough();
            let element = $('<div>');
            spyOn(element, 'css');
            let scrollAnimation = new ScrollAnimation(element, 'left', {
                from: 0,
                to: 20,
                suffix: 'px',
                steps: [step],
            });

            scrollAnimation.execute(15);

            expect(step.calc).toHaveBeenCalledWith(15);
            expect(element.css).toHaveBeenCalledWith('left', '23px');
        });

        it('defers calculation to next execution loop', () => {
            let step = new CalculatedStep(function () {
                return 23;
            }, true);
            let element = $('<div>');
            spyOn(element, 'css');
            let scrollAnimation = new ScrollAnimation(element, 'left', {
                from: 0,
                to: 20,
                suffix: 'px',
                steps: [step],
            });

            scrollAnimation.execute(15);

            expect(element.css).not.toHaveBeenCalled();

            jest.runAllTimers();

            expect(element.css).toHaveBeenCalledWith('left', '23px');
        });

        it('does not execute anything if element does not exist', () => {
            let element = $('#i-may-not-exist');
            spyOn(element, 'css');
            let scrollAnimation = new ScrollAnimation(element, 'left', {
                from: 0,
                to: 20,
                suffix: 'px',
                start: 0,
                end: 10,
            });

            scrollAnimation.execute(10);

            expect(element.css).not.toHaveBeenCalled();
        });

        it('does not execute anything when no step is defined', () => {
            let element = $('<div>');
            spyOn(element, 'css');
            let scrollAnimation = new ScrollAnimation(element, 'left', {
                from: 0,
                to: 20,
            });

            scrollAnimation.execute(10);

            expect(element.css).not.toHaveBeenCalled();
        });
    });
});

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

    describe('scroll handler', () => {
        let scrollHandler: () => void|boolean;
        let scrollAnimator: ScrollAnimator;

        beforeEach(() => {
            spyOn($.fn, 'on').and.callFake((event: string, handler: () => void|boolean) => {
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
