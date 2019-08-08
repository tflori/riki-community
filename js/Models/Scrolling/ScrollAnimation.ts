import {AnimationSpeed} from "@src/Models/Scrolling/AnimationSpeed";
import {Step} from "@src/Models/Scrolling/Step";

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

    public reset() {
        this.element.css(this.style, '');
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
