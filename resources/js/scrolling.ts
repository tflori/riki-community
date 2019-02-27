import { Easing, EasingDirection, EasingFx } from './AnimationSpeed';
import { CalculatedStep, ScrollAnimation, ScrollAnimator } from './ScrollAnimator';

jQuery(function($) {
    // get the elements
    let $body = $('body > header');
    let $navBar = $('.navbar-fixed');
    let $headerIcon = $('#header-background-icon');
    let $headerModules = $('#header-background-modules');
    let $headerName = $('#header-background-name');
    let $headerSubtitle = $('#header-background-subtitle');
    let $logoIcon = $('#logo-icon');
    let $logoName = $('#logo-name');
    let $logoSubtitle = $('#logo-subtitle');
    let $leftNav = $('#left-col');
    let $fixedNav = $('div.navbar-fixed');
    let $header = $('body header');

    // get sizes
    $navBar.show();
    let heightOfLargeHeader = $body.height() || 0;
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

    (new ScrollAnimator([
        // toggles for header, fixed nav and left navigation
        new ScrollAnimation($fixedNav, 'display', {
            from: headerAnimationEnd,
            to: headerAnimationEnd,
            before: 'none',
            after: 'block',
        }),
        new ScrollAnimation($header, 'opacity', {
            from: headerAnimationEnd,
            to: headerAnimationEnd,
            before: '1',
            after: '0',
        }),
        new ScrollAnimation($leftNav, 'position', {
            from: headerAnimationEnd,
            to: headerAnimationEnd,
            before: 'absolute',
            after: 'fixed'
        }),
        new ScrollAnimation($leftNav, 'top', {
            from: headerAnimationEnd,
            to: headerAnimationEnd,
            before: '',
            after: '88px'
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
    ])).start();
});
