import {ScrollAnimation} from "@src/Models/Scrolling/ScrollAnimation";

export class ScrollAnimator {
    protected scrollTop: number = 0;
    protected worker: number | undefined;
    protected executor: () => void;

    constructor(public scrollAnimations: ScrollAnimation[]) {
        this.executor = this.exec.bind(this);
    }

    public start() {
        jQuery(window).on('scroll', this.executor).triggerHandler('scroll');
    }

    public stop() {
        jQuery(window).off('scroll', this.executor);
        if (this.worker) {
            clearTimeout(this.worker);
            this.worker = undefined;
        }

        for (let animation of this.scrollAnimations) {
            animation.reset();
        }
    }

    protected exec() {
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
    }
}
