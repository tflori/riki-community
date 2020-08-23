import {CalculatedStep} from "@src/Models/Scrolling/CalculatedStep";
import {ScrollAnimation} from "@src/Models/Scrolling/ScrollAnimation";
import {Easing, EasingDirection, EasingFx} from './Models/Scrolling/AnimationSpeed';
import {ScrollAnimator} from './Models/Scrolling/ScrollAnimator';

jQuery(function ($) {
    function createScrollAnimator(): ScrollAnimator {
        // get sizes
        $navBar.show();
        let heightOfLargeHeader = $header.height() || 0;
        let heightOfSmallHeader = $navBar.height() || 0;
        let headerAnimationEnd = heightOfLargeHeader - heightOfSmallHeader;
        let iconRatio = ($logoIcon.width() || 1) / ($headerIcon.width() || 1);
        let offset = $navBar.find('.sidenav-toggle').is(':visible') ? 64 : 0;
        let headerModulesHeight = $headerModules.height() || 0;
        let headerModulesWidth = $headerModules.width() || 0;
        let logoIconPosition = $logoIcon.position();
        let headerIconPosition = $headerIcon.position();
        let headerIconWidth = $headerIcon.width() || 0;
        let headerIconHeight = $headerIcon.height() || 0;
        let headerNamePosition = $headerName.position();
        let logoNamePosition = $logoName.position();
        let headerSubtitlePosition = $headerSubtitle.position();
        let logoSubtitlePosition = $logoSubtitle.position();
        let showSubtitle = $logoSubtitle.is(':visible');
        $navBar.hide();

        return new ScrollAnimator([
            // toggles for header, fixed nav and left navigation
            new ScrollAnimation($fixedNav, 'display', {
                from: headerAnimationEnd-1,
                to: headerAnimationEnd-1,
                before: 'none',
                after: 'block',
            }),
            new ScrollAnimation($header, 'opacity', {
                from: headerAnimationEnd-1,
                to: headerAnimationEnd-1,
                before: '1',
                after: '0',
            }),
            new ScrollAnimation($leftNav, 'position', {
                from: headerAnimationEnd-1,
                to: headerAnimationEnd-1,
                before: 'absolute',
                after: 'fixed'
            }),
            new ScrollAnimation($leftNav, 'top', {
                from: headerAnimationEnd-1,
                to: headerAnimationEnd-1,
                before: '',
                suffix: 'px',
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
                from: 0,
                to: 0,
                before: '',
                suffix: 'px',
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

            // icon animation
            new ScrollAnimation($headerIcon, 'left', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                start: headerIconPosition.left,
                end: offset + logoIconPosition.left,
                easing: new Easing(EasingFx.Quad, EasingDirection.Out),
            }),
            new ScrollAnimation($headerIcon, 'top', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                start: headerIconPosition.top,
                end: logoIconPosition.top + headerAnimationEnd,
                easing: new Easing(EasingFx.Sine, EasingDirection.In),
            }),
            new ScrollAnimation($headerIcon, 'width', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                start: headerIconWidth,
                end: headerIconWidth * iconRatio,
            }),
            new ScrollAnimation($headerIcon, 'height', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                start: headerIconHeight,
                end: headerIconHeight * iconRatio,
            }),

            // modules animation
            new ScrollAnimation($headerModules, 'left', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                steps: [
                    new CalculatedStep(() => {
                        // bound to center of icon
                        let center = $headerIcon.position().left + ($headerIcon.width() || 0) / 2;
                        return center - ($headerModules.width() || 0) / 2;
                    }, true),
                ],
            }),
            new ScrollAnimation($headerModules, 'top', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                steps: [
                    new CalculatedStep(() => {
                        // bound to center of icon
                        let center = $headerIcon.position().top + ($headerIcon.height() || 0) / 2;
                        return center - ($headerModules.height() || 0) / 2 + 1;
                    }, true),
                ],
            }),
            new ScrollAnimation($headerModules, 'width', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                start: headerModulesWidth,
                end: headerModulesWidth * iconRatio,
            }),
            new ScrollAnimation($headerModules, 'height', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                start: headerModulesHeight,
                end: headerModulesHeight * iconRatio,
            }),
            new ScrollAnimation($headerModules, 'opacity', {
                from: 0,
                to: headerAnimationEnd / 4 * 3,
                start: 1,
                end: 0,
                easing: new Easing(EasingFx.Quad, EasingDirection.Out),
            }),

            // name animation
            new ScrollAnimation($headerName, 'left', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                start: headerNamePosition.left,
                end: offset + logoNamePosition.left,
                easing: new Easing(EasingFx.Quad, EasingDirection.Out),
            }),
            new ScrollAnimation($headerName, 'top', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                start: headerNamePosition.top,
                end: logoNamePosition.top + headerAnimationEnd,
                easing: new Easing(EasingFx.Sine, EasingDirection.In),
            }),
            new ScrollAnimation($headerName, 'width', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                start: $headerName.width(),
                end: $logoName.width(),
            }),

            // subtitle animation
            new ScrollAnimation($headerSubtitle, 'left', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                start: headerSubtitlePosition.left,
                end: showSubtitle ? offset + logoSubtitlePosition.left : $(window).width(),
                easing: new Easing(EasingFx.Sine, EasingDirection.Out),
            }),
            new ScrollAnimation($headerSubtitle, 'top', {
                from: 0,
                to: headerAnimationEnd,
                suffix: 'px',
                start: headerSubtitlePosition.top,
                end: logoSubtitlePosition.top + headerAnimationEnd,
                // easing: new Easing(EasingFx.Sine, EasingDirection.In),
            }),
            new ScrollAnimation($headerSubtitle, 'opacity', {
                from: 0,
                to: headerAnimationEnd / 4 * 3,
                start: 1,
                end: showSubtitle ? 1 : 0,
                easing: new Easing(EasingFx.Quad, EasingDirection.Out),
            }),
        ]);
    }

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

    let scrollAnimator: ScrollAnimator | undefined;
    $(window).on('resize', () => {
        if (scrollAnimator) {
            scrollAnimator.stop();
        }
        scrollAnimator = createScrollAnimator();
        scrollAnimator.start();
    }).trigger('resize');
});
