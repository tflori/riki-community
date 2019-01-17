import { Easing, ScrollAnimation, ScrollAnimator } from './ScrollAnimator';

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
                {
                    from: 0,
                    start: headerIconPosition.left,
                    end: offset + logoIconPosition.left,
                    easing: Easing.easeOutQuart,
                }
            ]
        }),
        new ScrollAnimation($headerIcon, 'top', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                {
                    from: 0,
                    start: headerIconPosition.top,
                    end: logoIconPosition.top + headerAnimationEnd,
                    easing: Easing.easeInQuad,
                }
            ]
        }),
        new ScrollAnimation($headerIcon, 'width', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                {
                    from: 0,
                    start: headerIconWidth,
                    end: headerIconWidth * iconRatio,
                }
            ]
        }),
        new ScrollAnimation($headerIcon, 'height', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                {
                    from: 0,
                    start: headerIconHeight,
                    end: headerIconHeight * iconRatio,
                }
            ]
        }),

        // modules animation
        new ScrollAnimation($headerModules, 'left', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: scrollTop => {
                // bound to center of icon
                let center = $headerIcon.position().left + ($headerIcon.width() || 0) / 2;
                return center - ($headerModules.width() || 0) / 2;
            }
        }),
        new ScrollAnimation($headerModules, 'top', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: scrollTop => {
                // bound to center of icon
                let center = $headerIcon.position().top + ($headerIcon.height() || 0) / 2;
                return center - ($headerModules.height() || 0) / 2;
            }
        }),
        new ScrollAnimation($headerModules, 'width', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: scrollTop => {
                // bound to size of icon
                let iconRatio = ($headerIcon.width() || 0) / headerIconWidth;
                return headerModulesWidth * iconRatio;
            }
        }),
        new ScrollAnimation($headerModules, 'height', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: scrollTop => {
                // bound to size of icon
                let iconRatio = ($headerIcon.height() || 0) / headerIconHeight;
                return headerModulesHeight * iconRatio;
            }
        }),
        new ScrollAnimation($headerModules, 'opacity', {
            from: 0,
            to: headerAnimationEnd / 4 * 3,
            steps: [
                {
                    from: 0,
                    start: 1,
                    end: 0,
                    easing: Easing.easeOutQuart,
                }
            ]
        }),

        // name animation
        new ScrollAnimation($headerName, 'left', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                {
                    from: 0,
                    start: headerNamePosition.left,
                    end: offset + logoNamePosition.left,
                    easing: Easing.easeOutQuart,
                }
            ]
        }),
        new ScrollAnimation($headerName, 'top', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                {
                    from: 0,
                    start: headerNamePosition.top,
                    end: logoNamePosition.top + headerAnimationEnd,
                    easing: Easing.easeInQuad,
                }
            ]
        }),
        new ScrollAnimation($headerName, 'width', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                {
                    from: 0,
                    start: $headerName.width() || 0,
                    end: $logoName.width() || 0,
                }
            ]
        }),

        // subtitle animation
        new ScrollAnimation($headerSubtitle, 'left', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                {
                    from: 0,
                    start: headerSubtitlePosition.left,
                    end: offset + logoSubtitlePosition.left,
                    easing: Easing.easeInQuad,
                }
            ]
        }),
        new ScrollAnimation($headerSubtitle, 'top', {
            from: 0,
            to: headerAnimationEnd,
            suffix: 'px',
            steps: [
                {
                    from: 0,
                    start: headerSubtitlePosition.top,
                    end: logoSubtitlePosition.top + headerAnimationEnd,
                    easing: Easing.easeOutQuad,
                }
            ]
        }),
        new ScrollAnimation($headerSubtitle, 'opacity', {
            from: 0,
            to: headerAnimationEnd / 4 * 3,
            steps: [
                {
                    from: 0,
                    start: 1,
                    end: showSubtitle ? 1 : 0,
                    easing: Easing.easeOutQuart,
                }
            ]
        }),
    ])).start();
});
