jQuery(function ($) {
    let heightOfLargeHeader = $('#riki-community > header').height() || 0;
    let heightOfSmallHeader = $('.navbar-fixed').height() || 0;
    let headerAnimationEnd = heightOfLargeHeader - heightOfSmallHeader;

    window.setTimeout(function () {
        let st = document.documentElement.scrollTop || document.body.scrollTop;
        if (st < headerAnimationEnd) {
            $('html, body').animate({
                scrollTop: headerAnimationEnd
            }, 1000);
        }
    }, window.location.href.match(/^https?:\/\/[a-zA-Z0-9:.-]+\/?(home)?$/) ? 5000 : 2000);

    $('#mobile-nav').sidenav({
        onOpenStart: function () {
            $('.sidenav-toggle .menu-icon').hide();
            $('.sidenav-toggle .close-icon').css('display', 'block');
        },
        onCloseStart: function () {
            $('.sidenav-toggle .menu-icon').css('display', 'block');
            $('.sidenav-toggle .close-icon').hide();
        },
    });
    $('.sidenav-toggle').click(function (e) {
        let st = document.documentElement.scrollTop || document.body.scrollTop;
        let sidenav = M.Sidenav.getInstance($('#mobile-nav')[0]);

        if (!sidenav.isOpen && st < headerAnimationEnd) {
            $('html, body').animate({
                scrollTop: headerAnimationEnd
            }, 500, function () {
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

    $('.card-content.search input').focus(function () {
        $('.card.search').addClass('active');
    }).blur(function () {
        if ($(this).val() === '') {
            $('.card.search').removeClass('active');
        }
    }).on('change', function (e) {
        $('.card-content.search input').not(e.target).val(<string>$(e.target).val());
    });

    let $leftSearch = $('#left-col .card.search');
    let $rightSearch = $('#right-col .card.search');
    let $close = $('#close-search');
    let $searchTabs = $rightSearch.find('.card-tabs');
    let $searchResults = $rightSearch.find('.search-results');
    let openTimer: number;
    let search = function search(term: string) {
        window.setTimeout(function () {
            $rightSearch.promise().done(function () {
                $searchTabs.show().find('.tabs').tabs();
                $searchResults.show();
            });
        }, 200 + Math.random() * 700);
        $close.show();
    };

    $leftSearch.find('input').on('keydown', function () {
        let $this = $(this);
        if (openTimer) {
            window.clearTimeout(openTimer);
            openTimer = 0;
        }

        openTimer = window.setTimeout(function () {
            if ($this.val() === '') {
                return;
            }

            $('html, body').animate({
                scrollTop: headerAnimationEnd
            }, 500);

            // morph left to right search
            $rightSearch.show();

            let offsetRight = $rightSearch.offset() || {left: 0, top: 0};
            let offsetLeft = $leftSearch.offset() || {left: 0, top: 0};
            $leftSearch.animate({
                width: $rightSearch.width(),
                left: offsetRight.left - offsetLeft.left,
                top: -8,
            }, 500, function () {
                $leftSearch.css({
                    opacity: 0,
                    left: '',
                    top: '',
                    width: '',
                });
                $rightSearch.find('input').val(<string>$this.val()).focus();
            });
            $rightSearch.hide();
            $rightSearch.show(500);

            search(<string>$this.val());
            openTimer = 0;
        }, 300);
    });

    $rightSearch.find('input').on('keydown', function () {
        let $this = $(this);
        if (openTimer) {
            window.clearTimeout(openTimer);
        }

        openTimer = window.setTimeout(function () {
            if ($this.val() === '') {
                return;
            }

            $('html, body').animate({
                scrollTop: headerAnimationEnd
            }, 500);

            search(<string>$this.val());
            openTimer = 0;
        }, 300);
    });

    $close.on('click', function () {
        $leftSearch.attr('style', '').find('input').val('');
        $rightSearch.attr('style', 'display: none;').find('input').val('').blur();
        $close.hide();
        $searchTabs.hide();
        $searchResults.hide();
        return false;
    });
});
