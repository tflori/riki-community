import Timeout = NodeJS.Timeout;

export class ScrollAnimator {
    protected scrollTop: number = 0;
    protected worker: Timeout|undefined;

    constructor(protected scrollAnimations: ScrollAnimation[]) {}

    public start() {
        jQuery(window).on('scroll', () => {
            this.scrollTop = document.documentElement.scrollTop || document.body.scrollTop;

            if (this.worker) {
                clearTimeout(this.worker);
            }
            this.worker = setTimeout(() => {
                console.log(this.scrollTop);
                for (let animation of this.scrollAnimations) {
                    animation.execute(this.scrollTop);
                }
                this.worker = undefined;
            }, 0);
        }).triggerHandler('scroll');
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
        steps?: Array<StaticStep|CalculatedStep>,
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
            // find the correct step
            let step: StaticStep|CalculatedStep = this.animation.steps[0];
            if (this.animation.steps.length > 1) {
                for (let i = 1; i < this.animation.steps.length; i++) {
                    if (this.animation.steps[i].from > scrollTop) {
                        break;
                    }
                    step = this.animation.steps[i];
                }
            }
            step.to = step.to || 0; // we need a to value

            if (step instanceof StaticStep) {
                // calculate the value for this step
                let t: number = (scrollTop - step.from) / (step.to - step.from);
                t = Math.max(0, Math.min(1, t)); // limit to 0 - 1
                if (step.easing) {
                    t = (EasingFunctions as any as {[method: string]: (t: number) => number})[Easing[step.easing]](t);
                }
                let value = (step.start + (step.end - step.start) * t) + (this.animation.suffix || '');
                this.element.css(this.style, value);
            } else if (step.wait) {
                // wait for this execution loop to finish before calculating with calc
                let calc = step.calc;
                setTimeout(() => {
                    let value = calc(scrollTop).toString(10);
                    this.element.css(this.style, value + this.animation.suffix)
                }, 0);
            } else {
                // calculate the value from this steps calc function
                let value = step.calc(scrollTop).toString(10);
                this.element.css(this.style, value + this.animation.suffix)
            }
        }
    }
}

export class StaticStep {
    public to: number = 0;
    constructor(
        public from: number,
        public start: number,
        public end: number,
        public easing?: Easing
    ) {}
}

export class CalculatedStep {
    public to: number = 0;
    constructor(
        public from: number,
        public calc: (scrollTop: number) => number,
        public wait?: boolean,
    ) {}
}
