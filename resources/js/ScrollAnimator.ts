import Timeout = NodeJS.Timeout;
import { AnimationSpeed } from './AnimationSpeed';

export class ScrollAnimator {
    protected scrollTop: number = 0;
    protected worker: Timeout|undefined;

    constructor(public scrollAnimations: ScrollAnimation[]) {}

    public start() {
        jQuery(window).on('scroll', () => {
            this.scrollTop = document.documentElement.scrollTop || document.body.scrollTop;

            if (this.worker) {
                clearTimeout(this.worker);
            }
            this.worker = setTimeout(() => {
                for (let animation of this.scrollAnimations) {
                    animation.execute(this.scrollTop);
                }
                this.worker = undefined;
            }, 0);
        }).triggerHandler('scroll');
    }
}

export class ScrollAnimation {
    protected _from: number;
    protected _to: number;
    protected _steps: Step[] = [];
    protected _suffix?: string;
    protected _before?: string;
    protected _after?: string;

    constructor(protected _element: JQuery, protected _style: string, animation: {
        from: number,
        to: number,
        steps?: Step[],
        suffix?: string,
        before?: string,
        after?: string,
        start?: number,
        end?: number,
        easing?: AnimationSpeed,
    }) {
        this._from = animation.from;
        this._to = animation.to;
        this._before = animation.before;
        this._after = animation.after;
        this._suffix = animation.suffix;

        if (animation.steps !== undefined && animation.steps.length > 0) {
            this._steps = animation.steps;
            this._steps.sort((a, b) => {
                return a.from - b.from;
            });

            // force from and to for the first and last step
            this._steps[0].from = animation.from;
            this._steps[this._steps.length - 1].to = animation.to;

            // set to for all steps
            if (this._steps.length > 1) {
                for (let i = 1; i < this._steps.length; i++) {
                    this._steps[i].from = Math.max(
                        this._steps[i-1].from + 1,
                        this._steps[i].from
                    );
                    this._steps[i - 1].to = !this._steps[i - 1].to ?
                                            this._steps[i].from :
                                            Math.min(
                                                this._steps[i - 1].to,
                                                this._steps[i].from,
                                            );
                }
            }
        } else if (animation.start !==  undefined && animation.end !== undefined) {
            this._steps = [
                new StaticStep(animation.start, animation.end, animation.easing, animation.from, animation.to)
            ];
        }
    }

    public get from(): number {
        return this._from;
    }

    public get to(): number {
        return this._to;
    }

    public get steps(): Step[] {
        return this._steps;
    }

    public get suffix(): undefined|string {
        return this._suffix;
    }

    public get before(): undefined|string {
        return this._before;
    }

    public get after(): undefined|string {
        return this._after;
    }

    get element(): JQuery {
        return this._element;
    }

    get style(): string {
        return this._style;
    }

    public execute(scrollTop: number) {
        if (this.element.length === 0) {
            return;
        }

        if (this.before !== undefined && this.from > scrollTop) {
            this.element.css(this.style, this.before);
            return;
        } else if (this.after !== undefined && this.to < scrollTop) {
            this.element.css(this.style, this.after);
            return;
        }

        if (this.steps.length > 0) {
            // find the correct step
            let step: Step = this.steps[0];
            if (this.steps.length > 1) {
                for (let i = 1; i < this.steps.length; i++) {
                    if (this.steps[i].from > scrollTop) {
                        break;
                    }
                    step = this.steps[i];
                }
            }

            if (step instanceof StaticStep) {
                // calculate the value for this step
                let t: number = (scrollTop - step.from) / (step.to - step.from);
                t = Math.max(0, Math.min(1, t)); // limit to 0 - 1
                if (step.easing) {
                    t = step.easing.calc(t);
                }
                let value = (step.start + (step.end - step.start) * t) + (this.suffix || '');
                this.element.css(this.style, value);
            } else if (step instanceof CalculatedStep) {
                // calculate the value from this steps calc function
                if (step.wait) {
                    // wait for this execution loop to finish before calculating with calc
                    let calc = step.calc;
                    setTimeout(() => {
                        let value = calc(scrollTop).toString(10);
                        this.element.css(this.style, value + this.suffix)
                    }, 0);
                } else {
                    let value = step.calc(scrollTop).toString(10);
                    this.element.css(this.style, value + this.suffix)
                }
            }
        }
    }
}

export abstract class Step {
    public from: number = 0;
    public to: number = 0;
}

export class StaticStep extends Step {
    constructor(
        public start: number,
        public end: number,
        public easing?: AnimationSpeed,
        public from: number = 0,
        public to: number = 0,
    ) {
        super();
    }
}

export class CalculatedStep extends Step {
    constructor(
        public calc: (scrollTop: number) => number,
        public wait: boolean = false,
        public from: number = 0,
        public to: number = 0,
    ) {
        super();
    }
}
