import Easings = JQuery.Easings;
import { isArray } from 'util';

export class ScrollAnimator {
    protected steps: number[] = [];

    constructor(protected scrollAnimations: ScrollAnimation[]) {}

    public start() {
        jQuery(window).on('scroll', () => {
            let scrollTop = document.documentElement.scrollTop || document.body.scrollTop;

            // remove steps that have not started yet
            if (this.steps.length > 1) {
                this.steps.pop();
            }

            this.steps.push(scrollTop);
            this.execute();
        }).triggerHandler('scroll');
    }

    protected execute(): void {
        // when there is more than one scroll top in the stack it means another execution is still running
        if (this.steps.length > 1) {
            return;
        }

        while (this.steps.length > 0) {
            this.scrollAnimations.map(animation => animation.execute(this.steps[0]));
            this.steps.shift();
        }
    }
}

export enum Easing {
    easeInQuad,
    easeOutQuad,
    easeInOutQuad,
    easeInCubic,
    easeOutCubic,
    easeInOutCubic,
    easeInQuart,
    easeOutQuart,
    easeInOutQuart,
    easeInQuint,
    easeOutQuint,
    easeInOutQuint
}

class EasingFunctions {
    static easeInQuad (t: number): number {
        return t*t;
    }
    static easeOutQuad (t: number): number {
        return t*(2-t);
    }
    static easeInOutQuad (t: number): number {
        return t<.5 ? 2*t*t : -1+(4-2*t)*t;
    }
    static easeInCubic (t: number): number {
        return t*t*t;
    }
    static easeOutCubic (t: number): number {
        return (--t)*t*t+1;
    }
    static easeInOutCubic (t: number): number {
        return t<.5 ? 4*t*t*t : (t-1)*(2*t-2)*(2*t-2)+1;
    }
    static easeInQuart (t: number): number {
        return t*t*t*t;
    }
    static easeOutQuart (t: number): number {
        return 1-(--t)*t*t*t;
    }
    static easeInOutQuart (t: number): number {
        return t<.5 ? 8*t*t*t*t : 1-8*(--t)*t*t*t;
    }
    static easeInQuint (t: number): number {
        return t*t*t*t*t;
    }
    static easeOutQuint (t: number): number {
        return 1+(--t)*t*t*t*t;
    }
    static easeInOutQuint (t: number): number {
        return t<.5 ? 16*t*t*t*t*t : 1+16*(--t)*t*t*t*t;
    }
}

export class ScrollAnimation {
    constructor(protected element: JQuery, protected style: string, protected animation: {
        from: number,
        to: number,
        steps?: {
            from: number,
            to?: number,
            start: number,
            end: number,
            easing?: Easing
        }[]|((scrollTop: number) => number),
        suffix?: string,
        before?: string,
        after?: string,
    }) {
        if (this.animation.steps instanceof Array) {
            this.animation.steps.sort((a, b) => {
                return a.from - b.from;
            });

            // force from and to for the first and last step
            this.animation.steps[0].from = this.animation.from;
            this.animation.steps[this.animation.steps.length - 1].to = this.animation.to;

            // set to for all steps
            if (this.animation.steps.length > 1) {
                for (let i = 1; i < this.animation.steps.length; i++) {
                    this.animation.steps[i].from = Math.max(
                        this.animation.steps[i-1].from + 1,
                        this.animation.steps[i].from
                    );
                    this.animation.steps[i-1].to = this.animation.steps[i].from;
                }
            }
        }
    }

    public execute(scrollTop: number) {
        if (this.element.length === 0) {
            return;
        }

        if (this.animation.before !== undefined && this.animation.from > scrollTop) {
            this.element.css(this.style, this.animation.before);
            return;
        } else if (this.animation.after !== undefined && this.animation.to < scrollTop) {
            this.element.css(this.style, this.animation.after);
            return;
        }

        if (this.animation.steps !== undefined) {
            let value = '';
            if (typeof this.animation.steps === 'function') {
                // calculate the value with steps function
                value = this.animation.steps(scrollTop).toString(10) + this.animation.suffix;
            } else {
                // find the correct step
                let step: {
                    from: number,
                    to?: number,
                    start: number,
                    end: number,
                    easing?: Easing
                } = this.animation.steps[0];
                if (this.animation.steps.length > 1) {
                    for (let i = 1; i < this.animation.steps.length; i++) {
                        if (this.animation.steps[i].from > scrollTop) {
                            break;
                        }
                        step = this.animation.steps[i];
                    }
                }
                step.to = step.to || 0; // we need a to value

                // calculate the value for this step
                let t: number = (scrollTop - step.from) / (step.to - step.from);
                t = Math.max(0, Math.min(1, t)); // limit to 0 - 1
                if (step.easing) {
                    t = (EasingFunctions as any as {[method: string]: (t: number) => number})[Easing[step.easing]](t);
                }
                value = (step.start + (step.end - step.start) * t) + (this.animation.suffix || '');
            }
            this.element.css(this.style, value);
        }
    }
}
