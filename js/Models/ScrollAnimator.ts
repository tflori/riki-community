import {AnimationSpeed} from './AnimationSpeed';

export class ScrollAnimator {
    protected scrollTop: number = 0;
    protected worker: number | undefined;

    constructor(public scrollAnimations: ScrollAnimation[]) {
    }

    public start() {
        jQuery(window).on('scroll', () => {
            this.scrollTop = document.documentElement.scrollTop || document.body.scrollTop;

            if (this.worker) {
                clearTimeout(this.worker);
            }
            this.worker = window.setTimeout(() => {
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
    protected _suffix: string = '';
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
        this._suffix = animation.suffix || '';

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
                        this._steps[i - 1].from + 1,
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
        } else if (animation.start !== undefined && animation.end !== undefined) {
            this._steps = [
                new Step(animation.start, animation.end, animation.easing, animation.from, animation.to)
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

    public get suffix(): string {
        return this._suffix;
    }

    public get before(): undefined | string {
        return this._before;
    }

    public get after(): undefined | string {
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

            if (step.wait) {
                let calc = step.calc;
                setTimeout(() => {
                    let value = calc(scrollTop).toString(10);
                    this.element.css(this.style, value + this.suffix);
                });
            } else {
                let value = step.calc(scrollTop).toString(10);
                this.element.css(this.style, value + this.suffix);
            }
        }
    }
}

export class Step {
    public wait: boolean = false;

    constructor(
        public start: number,
        public end: number,
        public easing?: AnimationSpeed,
        public from: number = 0,
        public to: number = 0,
    ) {
    }

    public calc(scrollTop: number): number {
        let t: number = (scrollTop - this.from) / (this.to - this.from);
        t = Math.max(0, Math.min(1, t)); // limit to 0 - 1
        if (this.easing) {
            t = this.easing.calc(t);
        }
        return (this.start + (this.end - this.start) * t);
    }
}

export class CalculatedStep extends Step {
    constructor(
        public calc: (scrollTop: number) => number,
        public wait: boolean = false,
        public from: number = 0,
        public to: number = 0,
    ) {
        super(0, 0);
    }
}
