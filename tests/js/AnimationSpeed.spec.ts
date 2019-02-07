import {
    AnimationSpeed,
    Easing,
    EasingDirection,
    EasingFx,
    Linear,
} from '../../resources/js/AnimationSpeed';

// easing is currently forced to start from 0 and end at 1 with a force of π/2 in duration 1
// the idea is that this is just simple math around
describe('AnimationSpeed', () => {
    describe('Linear', () => {
        let easing: AnimationSpeed = new Linear();

        it('is always exactly the same', () => {
            for (let x = 0; x <= 1; x += 0.05) {
                expect(easing.calc(x)).toBe(x);
            }
        });
    });

    describe('Easing Effects', () => {
        const checkpoints = {
            [EasingFx.Quad]: {
                0.25: 0.0625,
                0.5: 0.25,
                0.75: 0.5625,
            },
            [EasingFx.Cubic]: {
                0.25: 0.015625,
                0.5: 0.125,
                0.75: 0.421875,
            },
            [EasingFx.Quart]: {
                0.25: 0.00390625,
                0.5: 0.0625,
                0.75: 0.31640625,
            },
            [EasingFx.Quint]: {
                0.25: 0.0009765625,
                0.5: 0.03125,
                0.75: 0.2373046875,
            },
            [EasingFx.Sine]: {
                0.25: 0.076120467488713,
                0.5: 0.292893218813453,
                0.75: 0.61731656763491,
            },
            [EasingFx.Expo]: {
                0.25: 0.00552427172802,
                0.5: 0.03125,
                0.75: 0.176776695296637,
            },
            [EasingFx.Elastic]: {
                0.115: 0.00206106108729,
                0.265: -0.005829561085049,
                0.415: 0.016488488698317,
                0.565: -0.046636488680391,
                0.715: 0.131907909586537,
                0.865: -0.37309190944313,
            },
            [EasingFx.Back]: {
                0.115: -0.0183946300175,
                0.265: -0.0692180647925,
                0.415: -0.0999635750675,
                0.565: -0.0559241658425,
                0.715: 0.1176071578825,
                0.865: 0.4753373911075,
            },
        };

        for (let effect in checkpoints) {
            ((effect: EasingFx) => {
                describe(EasingFx[effect], () => {
                    let easing = new Easing(effect, EasingDirection.In);

                    it('starts at 0', () => {
                        expect(easing.calc(0)).toBe(0);
                    });

                    it('ends at 1', () => {
                        expect(easing.calc(1)).toBe(1);
                    });

                    it('calculates the expected values', () => {
                        for (let checkpoint in checkpoints[effect]) {
                            let calculated = easing.calc(parseFloat(checkpoint));

                            // @ts-ignore
                            expect(calculated).toBeCloseTo(checkpoints[effect][checkpoint], 8);
                        }
                    });
                });
            })(parseInt(effect, 10));
        }
    });

    describe('Easing Directions', () => {
        const checkpoints = {
            0.115: 0.00206106108729,
            0.265: -0.005829561085049,
            0.415: 0.016488488698317,
            0.565: -0.046636488680391,
            0.715: 0.131907909586537,
            0.865: -0.37309190944313,
        };

        it('Out reverses the effect', () => {
            let easing = new Easing(EasingFx.Elastic, EasingDirection.Out);
            for (let checkpoint in checkpoints) {
                // reverse the input
                let x = 1 - parseFloat(checkpoint);

                // reverse expected
                // @ts-ignore
                let expected = 1 - checkpoints[checkpoint];

                let calculated = easing.calc(x);

                expect(calculated).toBeCloseTo(expected, 8);
            }
        });

        it('InOut works like in till 0.5', () => {
            let easing = new Easing(EasingFx.Elastic, EasingDirection.InOut);
            for (let checkpoint in checkpoints) {
                let x = parseFloat(checkpoint) / 2;
                // @ts-ignore
                let expected = checkpoints[checkpoint] / 2;

                let calculated = easing.calc(x);

                expect(calculated).toBeCloseTo(expected, 8);
            }
        });

        it('InOut works like out from 0.5', () => {
            let easing = new Easing(EasingFx.Elastic, EasingDirection.InOut);
            for (let checkpoint in checkpoints) {
                let x = 1 - (parseFloat(checkpoint) / 2);
                // @ts-ignore
                let expected = 1 - (checkpoints[checkpoint] / 2);

                let calculated = easing.calc(x);

                expect(calculated).toBeCloseTo(expected, 8);
            }
        });
    });
});
