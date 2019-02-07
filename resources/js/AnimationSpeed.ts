/**
 * These classes describe a function from time where time is the progress of the animation (0 = not started; 1 =
 * ended) and the function return value is how much the original value has to be changed.
 *
 * Example:
 * An element is at 50px left and you want to move it to 100px left. You want to change it by 50px. Then 0 means: move
 * by 0px; and 1 means: move by 50px. If half of your animation is over you calculate for time = 0.5.
 *
 * Linear is the default and means a constant speed.
 * Linear is always equal time - so left = 50px + 0.5 * 50px = 75px.
 *
 * In* means that the animation should start slow and end fast.
 * In quad is 0.5 * 0.5 = 0.25 - so left = 50px + 0.25 * 50px = 63px.
 *
 * Out* means that the animation should start fast and end slow.
 * Out quad is 1- -0.5 * -0.5 = 0.75 - so left = 50px + 0.75 * 50px = 88px.
 *
 * InOut* means that it should start and end slow.
 * In the center they meet at 0.5 - so left = 50px + 0.5 * 50px = 88px.
 */
export abstract class AnimationSpeed {
    public abstract calc(x: number): number;
}

export class Linear extends AnimationSpeed {
    public calc(x: number): number {
        return x;
    }
}

export enum EasingFx {
    Quad,
    Cubic,
    Quart,
    Quint,
    Sine,
    Expo,
    Elastic,
    Back,
}

export enum EasingDirection {
    In,
    Out,
    InOut
}

export class Easing extends AnimationSpeed {
    constructor(protected fx: EasingFx, protected direction: EasingDirection, protected strength: number = 1.70158) {
        super()
    }

    public calc(x: number): number {
        if (x == 0 || x == 1) {
            return x;
        }

        switch (this.direction) {
            case EasingDirection.In:
                return this.calcIn(x);
            case EasingDirection.Out:
                return this.calcOut(x);
            case EasingDirection.InOut:
                return this.calcInOut(x);
        }
    }

    protected calcIn(x: number): number {
        return this.execFx(x);
    }

    protected calcOut(x: number): number {
        return Math.abs(this.execFx(Math.abs(x-1))-1)
    }

    protected calcInOut(x: number): number {
        return x < 0.5 ? this.execFx(x*2)/2 : Math.abs(this.execFx(Math.abs(x-1) * 2) / 2 - 1);

    }

    protected execFx(x: number): number {
        switch (this.fx) {
            case EasingFx.Quad:
                return Math.pow(x, 2);
            case EasingFx.Cubic:
                return Math.pow(x, 3);
            case EasingFx.Quart:
                return Math.pow(x, 4);
            case EasingFx.Quint:
                return Math.pow(x, 5);
            case EasingFx.Sine:
                return Math.sin((x - 1) * Math.PI / 2) + 1;
            case EasingFx.Expo:
                return Math.pow(2, 10 * (x - 1));
            case EasingFx.Elastic:
                return -(Math.pow(2, 10 * (x - 1)) * Math.sin((x - 1.075) * (2 * Math.PI) / 0.3));
            case EasingFx.Back:
                return Math.pow(x, 2) * ((this.strength + 1) * x - this.strength);
        }
    }
}
