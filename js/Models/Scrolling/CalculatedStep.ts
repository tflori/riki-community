import {Step} from "@src/Models/Scrolling/Step";

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
