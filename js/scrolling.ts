import {CalculatedStep} from "@src/Models/Scrolling/CalculatedStep";
import {ScrollAnimation} from "@src/Models/Scrolling/ScrollAnimation";
import {Easing, EasingDirection, EasingFx} from './Models/Scrolling/AnimationSpeed';
import {ScrollAnimator} from './Models/Scrolling/ScrollAnimator';

jQuery(function ($) {
    // get the elements
    let $header = $('#riki-community > header');
    let $navBar = $('.navbar-fixed');
    let $headerIcon = $('#header-background-icon');
    let $headerModules = $('#header-background-modules');
    let $headerName = $('#header-background-name');
    let $headerSubtitle = $('#header-background-subtitle');
    let $logoIcon = $('#logo-icon');
    let $logoName = $('#logo-name');
    let $logoSubtitle = $('#logo-subtitle');
    let $leftNav = $('#left-col');
    let $toggles = $leftNav.find('.toggles');
    let $fixedNav = $('div.navbar-fixed');
    let $footer = $('footer.page-footer');

    function createToggles(sizes: any): ScrollAnimation[] {
        return [
            new ScrollAnimation($fixedNav, 'display', {
                from: sizes.headerAnimationEnd-1, to: sizes.headerAnimationEnd-1,
                before: 'none', after: 'block',
            }),
            new ScrollAnimation($header, 'opacity', {
                from: sizes.headerAnimationEnd-1, to: sizes.headerAnimationEnd-1,
                before: '1', after: '0',
            }),
            new ScrollAnimation($leftNav, 'position', {
                from: sizes.headerAnimationEnd-1, to: sizes.headerAnimationEnd-1,
                before: 'absolute', after: 'fixed'
            }),
            new ScrollAnimation($leftNav, 'top', {
                from: sizes.headerAnimationEnd-1, to: sizes.headerAnimationEnd-1,
                before: '', suffix: 'px',
                steps: [
                    new CalculatedStep(() => {
                        // prevent floating over footer
                        let $parent = $leftNav.parent();

                        $leftNav.css('top', '88px');
                        // @ts-ignore
                        let space = $parent.offset().top + $parent.height() - $leftNav.offset().top - $leftNav.outerHeight();
                        return space < 0 ? 88 + space : 88;
                    }, true),
                ],
            }),
            new ScrollAnimation($toggles, 'bottom', {
                from: 0, to: 0, before: '', suffix: 'px',
                steps: [
                    new CalculatedStep((scrollTop) => {
                        let leftNavEnd: number = (<{top: number}>$leftNav.offset()).top +
                            <number>$leftNav.outerHeight();
                        let bottomFromBody: number = window.innerHeight + scrollTop;
                        let posUnderMenu: number = bottomFromBody - leftNavEnd;
                        if (posUnderMenu < 0) {
                            return posUnderMenu;
                        }

                        let topOfFooter: number = bottomFromBody - (<{top: number}>$footer.offset()).top;
                        return Math.max(topOfFooter + 40, 8);
                    }, true),
                ],
            }),
        ];
    }

    function createIconAnimation(sizes: any): ScrollAnimation[] {
        return [
            new ScrollAnimation($headerIcon, 'left', {
                from: 0, to: sizes.headerAnimationEnd,
                suffix: 'px', start: sizes.headerIconPosition.left,
                end: sizes.offset + sizes.logoIconPosition.left,
                easing: new Easing(EasingFx.Quad, EasingDirection.Out),
            }),
            new ScrollAnimation($headerIcon, 'top', {
                from: 0, to: sizes.headerAnimationEnd,
                suffix: 'px', start: sizes.headerIconPosition.top,
                end: sizes.logoIconPosition.top + sizes.headerAnimationEnd,
                easing: new Easing(EasingFx.Sine, EasingDirection.In),
            }),
            new ScrollAnimation($headerIcon, 'width', {
                from: 0, to: sizes.headerAnimationEnd,
                suffix: 'px', start: sizes.headerIconWidth,
                end: sizes.headerIconWidth * sizes.iconRatio,
            }),
            new ScrollAnimation($headerIcon, 'height', {
                from: 0, to: sizes.headerAnimationEnd,
                suffix: 'px', start: sizes.headerIconHeight,
                end: sizes.headerIconHeight * sizes.iconRatio,
            }),
        ];
    }

    function createModulesAnimation(sizes: any): ScrollAnimation[] {
        return [// modules animation
            new ScrollAnimation($headerModules, 'left', {
                suffix: 'px', from: 0, to: sizes.headerAnimationEnd,
                steps: [
                    new CalculatedStep(() => {
                        // bound to center of icon
                        let center = $headerIcon.position().left + ($headerIcon.width() || 0) / 2;
                        return center - ($headerModules.width() || 0) / 2;
                    }, true),
                ],
            }),
            new ScrollAnimation($headerModules, 'top', {
                suffix: 'px', from: 0, to: sizes.headerAnimationEnd,
                steps: [
                    new CalculatedStep(() => {
                        // bound to center of icon
                        let center = $headerIcon.position().top + ($headerIcon.height() || 0) / 2;
                        return center - ($headerModules.height() || 0) / 2 + 1;
                    }, true),
                ],
            }),
            new ScrollAnimation($headerModules, 'width', {
                suffix: 'px', from: 0, to: sizes.headerAnimationEnd,
                start: sizes.headerModulesWidth,
                end: sizes.headerModulesWidth * sizes.iconRatio,
            }),
            new ScrollAnimation($headerModules, 'height', {
                suffix: 'px', from: 0, to: sizes.headerAnimationEnd,
                start: sizes.headerModulesHeight,
                end: sizes.headerModulesHeight * sizes.iconRatio,
            }),
            new ScrollAnimation($headerModules, 'opacity', {
                from: 0, to: sizes.headerAnimationEnd / 4 * 3,
                start: 1, end: 0,
                easing: new Easing(EasingFx.Quad, EasingDirection.Out),
            }),
        ];
    }

    function createNameAnimation(sizes: any): ScrollAnimation[] {
        return [// name animation
            new ScrollAnimation($headerName, 'left', {
                suffix: 'px', from: 0, to: sizes.headerAnimationEnd,
                start: sizes.headerNamePosition.left,
                end: sizes.offset + sizes.logoNamePosition.left,
                easing: new Easing(EasingFx.Quad, EasingDirection.Out),
            }),
            new ScrollAnimation($headerName, 'top', {
                suffix: 'px', from: 0, to: sizes.headerAnimationEnd,
                start: sizes.headerNamePosition.top,
                end: sizes.logoNamePosition.top + sizes.headerAnimationEnd,
                easing: new Easing(EasingFx.Sine, EasingDirection.In),
            }),
            new ScrollAnimation($headerName, 'width', {
                suffix: 'px', from: 0, to: sizes.headerAnimationEnd,
                start: $headerName.width(), end: $logoName.width(),
            }),
        ];
    }

    function createSubtitleAnimation(sizes: any): ScrollAnimation[] {
        return [// subtitle animation
            new ScrollAnimation($headerSubtitle, 'left', {
                from: 0, to: sizes.headerAnimationEnd,
                suffix: 'px', start: sizes.headerSubtitlePosition.left,
                end: sizes.showSubtitle ? sizes.offset + sizes.logoSubtitlePosition.left : $(window).width(),
                easing: new Easing(EasingFx.Sine, EasingDirection.Out),
            }),
            new ScrollAnimation($headerSubtitle, 'top', {
                from: 0, to: sizes.headerAnimationEnd,
                suffix: 'px', start: sizes.headerSubtitlePosition.top,
                end: sizes.logoSubtitlePosition.top + sizes.headerAnimationEnd,
                // easing: new Easing(EasingFx.Sine, EasingDirection.In),
            }),
            new ScrollAnimation($headerSubtitle, 'opacity', {
                from: 0, to: sizes.headerAnimationEnd / 4 * 3,
                start: 1, end: sizes.showSubtitle ? 1 : 0,
                easing: new Easing(EasingFx.Quad, EasingDirection.Out),
            }),
        ];
    }

    function createScrollAnimator(): ScrollAnimator {
        // get sizes
        $navBar.show();
        const sizes = {
            heightOfLargeHeader: $header.height() || 0,
            heightOfSmallHeader: $navBar.height() || 0,
            headerAnimationEnd: 0,
            iconRatio: ($logoIcon.width() || 1) / ($headerIcon.width() || 1),
            offset: $navBar.find('.sidenav-toggle').is(':visible') ? 64 : 0,
            headerModulesHeight: $headerModules.height() || 0,
            headerModulesWidth: $headerModules.width() || 0,
            logoIconPosition: $logoIcon.position(),
            headerIconPosition: $headerIcon.position(),
            headerIconWidth: $headerIcon.width() || 0,
            headerIconHeight: $headerIcon.height() || 0,
            headerNamePosition: $headerName.position(),
            logoNamePosition: $logoName.position(),
            headerSubtitlePosition: $headerSubtitle.position(),
            logoSubtitlePosition: $logoSubtitle.position(),
            showSubtitle: $logoSubtitle.is(':visible'),
        };
        $navBar.hide();
        sizes.headerAnimationEnd = sizes.heightOfLargeHeader - sizes.heightOfSmallHeader;

        const scrollAnimations: ScrollAnimation[] = [
            ...createToggles(sizes),
            ...createIconAnimation(sizes),
            ...createModulesAnimation(sizes),
            ...createNameAnimation(sizes),
            ...createSubtitleAnimation(sizes),
        ];

        return new ScrollAnimator(scrollAnimations);
    }

    let scrollAnimator: ScrollAnimator | undefined;
    $(window).on('resize', () => {
        if (scrollAnimator) {
            scrollAnimator.stop();
        }
        scrollAnimator = createScrollAnimator();
        scrollAnimator.start();
    }).trigger('resize');
});
