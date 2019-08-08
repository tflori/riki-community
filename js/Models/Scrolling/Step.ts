import {AnimationSpeed} from "@src/Models/Scrolling/AnimationSpeed";

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
