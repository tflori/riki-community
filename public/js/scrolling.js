(function($, window, document, undefined) {
    // $ = jQuery, w = window, d = document, u = undefined
    $(document).ready(function() {
        let heightOfLargeHeader = $('body > header').height();
        let $navbar = $('.navbar-fixed');
        let heightOfSmallHeader = $navbar.height();
        let headerAnimationEnd = heightOfLargeHeader - heightOfSmallHeader;

        let headerIcon = $('#header-background-icon');
        let headerModules = $('#header-background-modules');
        let headerName = $('#header-background-name');
        let headerSubtitle = $('#header-background-subtitle');
        let logoIcon = $('#logo-icon');
        let logoName = $('#logo-name');
        let logoSubtitle = $('#logo-subtitle');
        let iconRatio = logoIcon.width()/headerIcon.width();
        $navbar.show();
        let offset = $navbar.find('.sidenav-toggle').is(':visible') ? 64 : 0;
        let scrollAnimations = [
            {
                from: 0,
                to: headerAnimationEnd/2,
                before: [
                    {
                        element: headerIcon,
                        styles: {
                            left: '',
                            width: '',
                            height: '',
                            top: '',
                        }
                    },
                    {
                        element: headerModules,
                        styles: {
                            opacity: '1',
                            left: '',
                            width: '',
                            height: '',
                        }
                    },
                    {
                        element: headerName,
                        styles: {
                            left: '',
                            width: '',
                        }
                    },
                    {
                        element: headerSubtitle,
                        styles: {
                            left: '',
                        }
                    },
                ],
                during: [
                    {
                        element: headerIcon,
                        vars: {
                            left: [headerIcon.position().left, offset + logoIcon.position().left],
                            width: [headerIcon.width(), logoIcon.width()],
                            height: [headerIcon.height(), logoIcon.height()],
                            top: [headerIcon.position().top, headerModules.position().top + headerModules.height() - iconRatio*44 - iconRatio*headerIcon.height()], // 311
                        },
                        styles: {
                            left: '%left%px',
                            width: '%width%px',
                            height: '%height%px',
                            top: '%top%px',
                        }
                    },
                    {
                        element: headerModules,
                        vars: {
                            left: [headerModules.position().left, offset + logoIcon.position().left - iconRatio*52],
                            width: [headerModules.width(), iconRatio*headerModules.width()],
                            height: [headerModules.height(), iconRatio*headerModules.height()],
                        },
                        styles: {
                            opacity: '%i%',
                            left: '%left%px',
                            width: '%width%px',
                            height: '%height%px',
                        }
                    },
                    {
                        element: headerName,
                        vars: {
                            left: [headerName.position().left, offset + logoName.position().left],
                            width: [headerName.width(), logoName.width()],
                        },
                        styles: {
                            left: '%left%px',
                            width: '%width%px',
                        }
                    },
                    {
                        element: headerSubtitle,
                        vars: {
                            left: [headerSubtitle.position().left, logoSubtitle.is(':visible') ? offset + logoSubtitle.position().left : $('body > header').width()],
                            opacity: [1, logoSubtitle.is(':visible') ? 1 : 0]
                        },
                        styles: {
                            left: '%left%px',
                            opacity: '%opacity%'
                        }
                    },
                ],
                after: [
                    {
                        element: headerIcon,
                        styles: {
                            left: offset + logoIcon.position().left + 'px',
                            width: logoIcon.width() + 'px',
                            height: logoIcon.height() + 'px',
                        }
                    },
                    {
                        element: headerModules,
                        styles: {
                            opacity: '0',
                        }
                    },
                    {
                        element: headerName,
                        styles: {
                            left: offset + logoName.position().left + 'px',
                            width: logoName.width() + 'px',
                        }
                    },
                    {
                        element: headerSubtitle,
                        styles: {
                            left: logoSubtitle.is(':visible') ? offset + logoSubtitle.position().left + 'px' : $('body > header').width() + 'px',
                            opacity: logoSubtitle.is(':visible') ? '1' : '0',
                        }
                    },
                ],
            },
            {
                from: headerAnimationEnd/2,
                to: headerAnimationEnd,
                before: [
                    {
                        element: headerName,
                        styles: {
                            top: '',
                        }
                    },
                    {
                        element: headerSubtitle,
                        styles: {
                            top: '',
                        }
                    },
                    {
                        element: $('div.navbar-fixed'),
                        styles: {
                            display: 'none',
                        },
                    },
                    {
                        element: $('body header'),
                        styles: {
                            opacity: '1'
                        },
                    },
                    {
                        element: $('#left-col'),
                        styles: {
                            position: 'absolute',
                            top: '',
                        },
                    },
                ],
                during: [
                    {
                        element: headerIcon,
                        vars: {
                            top: [headerModules.position().top + headerModules.height() - iconRatio*44 - iconRatio*headerIcon.height(), logoIcon.position().top + headerAnimationEnd],
                        },
                        styles: {
                            top: '%top%px',
                        }
                    },
                    {
                        element: headerName,
                        vars: {
                            top: [headerName.position().top, logoName.position().top + headerAnimationEnd],
                        },
                        styles: {
                            top: '%top%px',
                        }
                    },
                    {
                        element: headerSubtitle,
                        vars: {
                            top: [headerSubtitle.position().top, logoSubtitle.position().top + headerAnimationEnd],
                        },
                        styles: {
                            top: '%top%px',
                        }
                    },
                    {
                        element: $('div.navbar-fixed'),
                        styles: {
                            display: 'none',
                        },
                    },
                    {
                        element: $('body header'),
                        styles: {
                            opacity: '1',
                        },
                    },
                    {
                        element: $('#left-col'),
                        styles: {
                            position: 'absolute',
                            top: '',
                        },
                    },
                ],
                after: [
                    {
                        element: $('div.navbar-fixed'),
                        styles: {
                            display: 'block',
                        },
                    },
                    {
                        element: $('body header'),
                        styles: {
                            opacity: '0',
                        },
                    },
                    {
                        element: $('#left-col'),
                        styles: {
                            position: 'fixed',
                            top: '88px',
                        },
                    },
                ],
            },
        ];
        $navbar.hide();
        // console.log(scrollAnimations);

        let applyStyles = function applyStyles(definition, p) {
            let v, vars = {
                p: p,
                i: Math.abs(p - 1),
            };

            if (definition.vars) {
                let start, end, diff, current;
                for (v in definition.vars) {
                    [start, end] = definition.vars[v];
                    // if (typeof start === 'function') {
                    //     start = start();
                    // }
                    // if (typeof end === 'function') {
                    //     end = end();
                    // }
                    diff = Math.abs(end - start);
                    current = p * diff;
                    vars[v] = start > end ? start - current : start + current;
                }
            }

            if (definition.element && definition.styles) {
                let style, value;
                for (style in definition.styles) {
                    value = definition.styles[style];
                    for (v in vars) {
                        value = value.split('%' + v + '%').join(vars[v]);
                    }
                    definition.element.css(style, value);
                }
            }
        };

        $(window).scroll(function() {
            let st = document.documentElement.scrollTop || document.body.scrollTop;

            let p, elements, j, i = scrollAnimations.length;
            while (i--) {
                elements = [];
                if (st <= scrollAnimations[i].from) {
                    p = 0;
                    elements.push.apply(elements, scrollAnimations[i].before || []);
                } else if (st >= scrollAnimations[i].to) {
                    p = 1;
                    elements.push.apply(elements, scrollAnimations[i].after || []);
                } else {
                    p = (st - scrollAnimations[i].from) / (scrollAnimations[i].to - scrollAnimations[i].from);
                    elements.push.apply(elements, scrollAnimations[i].during || []);
                }
                j = elements.length;
                while (j--) {
                    applyStyles(elements[j], p);
                }
            }
        }).scroll();

        window.setTimeout(function() {
            let st = document.documentElement.scrollTop || document.body.scrollTop;
            if (st < headerAnimationEnd) {
                $(window).scrollTo(headerAnimationEnd, 500);
            }
        }, window.location.href.match(/^https?:\/\/[a-zA-Z0-9:.-]+\/?(home)?$/) ? 10000 : 2000);

        $('#mobile-nav').sidenav({
            onOpenStart: function() {
                $('.sidenav-toggle .menu-icon').hide();
                $('.sidenav-toggle .close-icon').css('display', 'block');
            },
            onCloseStart: function() {
                $('.sidenav-toggle .menu-icon').css('display', 'block');
                $('.sidenav-toggle .close-icon').hide();
            },
        });
        $('.sidenav-toggle').click(function(e) {
            let st = document.documentElement.scrollTop || document.body.scrollTop;
            let sidenav = M.Sidenav.getInstance($('#mobile-nav'));

            if (!sidenav.isOpen && st < headerAnimationEnd) {
                $(window).scrollTo(headerAnimationEnd + 8, 500, function() {
                    sidenav.open();
                });
                // w.setTimeout(, 1000);
            } else if (sidenav.isOpen) {
                sidenav.close();
            } else {
                sidenav.open();
            }

            return false;
        });

        $('.card-content.search input').focus(function() {
            $('.card.search').addClass('active');
        }).blur(function() {
            if ($(this).val() === '') {
                $('.card.search').removeClass('active');
            }
        }).on('change', function(e) {
            $('.card-content.search input').not(e.target).val($(e.target).val());
        });


        let $leftSearch = $('#left-col .card.search');
        let $rightSearch = $('#right-col .card.search');
        let $close = $('#close-search');
        let $searchTabs = $rightSearch.find('.card-tabs');
        let $searchResults = $rightSearch.find('.search-results');
        let openTimer = undefined;
        let search = function search(term) {
            window.setTimeout(function() {
                $rightSearch.promise().done(function() {
                    $searchTabs.show().find('.tabs').tabs();
                    $searchResults.show();
                });
            }, 200 + Math.random() * 700);
            $close.show();
        };

        $leftSearch.find('input').keydown(function() {
            let $this = $(this);
            if (openTimer) {
                window.clearTimeout(openTimer);
            }

            openTimer = window.setTimeout(function() {
                if ($this.val() === '') {
                    return;
                }

                $(window).scrollTo(headerAnimationEnd, 500);

                // morph left to right search
                $rightSearch.show();
                $leftSearch.animate({
                    width: $rightSearch.width(),
                    left: $rightSearch.offset().left - $leftSearch.offset().left,
                    top: -8,
                }, 500, function() {
                    $leftSearch.css({
                        opacity: 0,
                        left: '',
                        top: '',
                        width: '',
                    });
                    $rightSearch.find('input').val($this.val()).focus();
                });
                $rightSearch.hide();
                $rightSearch.show(500);

                search($this.val());
                openTimer = undefined;
            }, 300);
        });

        $rightSearch.find('input').keydown(function() {
            let $this = $(this);
            if (openTimer) {
                window.clearTimeout(openTimer);
            }

            openTimer = window.setTimeout(function() {
                if ($this.val() === '') {
                    return;
                }

                $(window).scrollTo(headerAnimationEnd, 500);

                search($this.val());
                openTimer = undefined;
            }, 300);
        });

        $close.click(function() {
            $leftSearch.attr('style', '').find('input').val('');
            $rightSearch.attr('style', 'display: none;').find('input').val('').blur();
            $close.hide();
            $searchTabs.hide();
            $searchResults.hide();
            return false;
        });
    });
}(jQuery, window, document));
