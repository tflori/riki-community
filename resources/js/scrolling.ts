import { CalculatedStep, Easing, ScrollAnimation, ScrollAnimator, StaticStep } from './ScrollAnimator';

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
            steps: [
                new StaticStep(
                    0,
                    headerIconPosition.left,
                    offset + logoIconPosition.left,
                    Easing.easeOutQuart,
                )
            ],
        }),
        new ScrollAnimation($headerIcon, 'top', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                new StaticStep(
                    0,
                    headerIconPosition.top,
                    logoIconPosition.top + headerAnimationEnd,
                    Easing.easeInQuad,
                )
            ],
        }),
        new ScrollAnimation($headerIcon, 'width', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                new StaticStep(
                    0,
                    headerIconWidth,
                    headerIconWidth * iconRatio,
                )
            ],
        }),
        new ScrollAnimation($headerIcon, 'height', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                new StaticStep(
                    0,
                    headerIconHeight,
                    headerIconHeight * iconRatio,
                )
            ],
        }),

        // modules animation
        new ScrollAnimation($headerModules, 'left', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                new CalculatedStep(0, () => {
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
                new CalculatedStep(0, () => {
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
            steps: [
                new StaticStep(0, headerModulesWidth, headerModulesWidth * iconRatio)
            ],
        }),
        new ScrollAnimation($headerModules, 'height', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                new StaticStep(0, headerModulesHeight, headerModulesHeight * iconRatio)
            ],
        }),
        new ScrollAnimation($headerModules, 'opacity', {
            from: 0,
            to: headerAnimationEnd / 4 * 3,
            steps: [
                new StaticStep(0, 1, 0, Easing.easeOutQuart)
            ]
        }),

        // name animation
        new ScrollAnimation($headerName, 'left', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                new StaticStep(0, headerNamePosition.left, offset + logoNamePosition.left, Easing.easeOutQuart)
            ]
        }),
        new ScrollAnimation($headerName, 'top', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                new StaticStep(0, headerNamePosition.top, logoNamePosition.top + headerAnimationEnd, Easing.easeInQuad)
            ]
        }),
        new ScrollAnimation($headerName, 'width', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                new StaticStep(0, $headerName.width() || 0, $logoName.width() || 0)
            ]
        }),

        // subtitle animation
        new ScrollAnimation($headerSubtitle, 'left', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                new StaticStep(0, headerSubtitlePosition.left, offset + logoSubtitlePosition.left, Easing.easeInQuad)
            ]
        }),
        new ScrollAnimation($headerSubtitle, 'top', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                new StaticStep(0, headerSubtitlePosition.top, logoSubtitlePosition.top + headerAnimationEnd, Easing.easeOutQuad)
            ]
        }),
        new ScrollAnimation($headerSubtitle, 'opacity', {
            from: 0,
            to: headerAnimationEnd / 4 * 3,
            steps: [
                new StaticStep(0, 1, showSubtitle ? 1 : 0, Easing.easeOutQuart)
            ]
        }),
    ])).start();
});
