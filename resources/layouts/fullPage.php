<?php /** @var callable $e */ /** @var Syna\View $v */ ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="/favicon.ico">
        <link rel="icon" href="/images/favicon.png" sizes="48x48">
        <link rel="apple-touch-icon" sizes="256x256" href="/images/favicon-2x.png">
        <title>Ríki:Welcome!</title>
        <link rel="stylesheet" href="/bundle.css">
    </head>
    <body class="grey lighten-4">

        <!-- Header -->
        <header>
            <nav class="teal darken-1">
                <div class="nav-wrapper">
                    <div class="logo-wrapper">
                        <div id="header-background-icon"></div>
                        <div id="header-background-modules"></div>
                        <div id="header-background-name"></div>
                        <div id="header-background-subtitle"></div>
                    </div>
                    <a href="#" class="sidenav-toggle left hide-on-large-only"><i class="material-icons menu-icon">menu</i><i class="material-icons close-icon">close</i></a>
                    <ul class="right">
                        <li class="icon"><a class="btnLogin"><i class="material-icons left">account_circle</i><span class="icon-text"> Login / Signup</span></a></li>
                    </ul>
                    <div class="account">

                    </div>
                </div>
            </nav>
        </header>

        <!-- Toolbar -->
        <div class="navbar-fixed">
            <nav class="teal darken-1">
                <div class="nav-wrapper">
                    <a href="#" class="sidenav-toggle left hide-on-large-only"><i class="material-icons menu-icon">menu</i><i class="material-icons close-icon">close</i></a>
                    <a href="/home" class="brand-logo left"><div id="logo-icon"></div><div id="logo-name"></div><div id="logo-subtitle"></div></a>
                    <ul class="right">
                        <li class="icon"><a class="btnLogin"><i class="material-icons left">account_circle</i><span class="icon-text"> Login / Signup</span></a></li>
                    </ul>
                    <div class="account">

                    </div>
                </div>
            </nav>
        </div>

        <!-- Mobile Navigation -->
        <ul class="sidenav" id="mobile-nav">
            <li><a href="/home"><i class="material-icons">home</i> Home</a></li>
            <li><a href="/blog"><i class="material-icons">rss_feed</i> Blog</a></li>
            <li><a href="/guide"><i class="material-icons">toc</i> Guide</a></li>
            <li><a href="/docs"><i class="material-icons">library_books</i> Documentation</a></li>
            <li><a href="/exchange"><i class="material-icons">question_answer</i> Exchange</a></li>
        </ul>

        <!-- Content -->
        <div class="main container" style="min-height: 480px">
            <div class="row">

                <div id="left-col" class="col l3 hide-on-med-and-down">
                    <div id="left-col-wrapper">
                        <div class="card search">
                            <div class="card-content search">
                                <div class="input-field">
                                    <input type="text" placeholder="Search">
                                    <label class="label-icon right"><i class="material-icons">search</i></label>
                                </div>
                            </div>
                        </div>
                        <ul class="nav">
                            <li><a href="/home"><i class="material-icons">home</i> Home</a></li>
                            <li><a href="/blog"><i class="material-icons">rss_feed</i> Blog</a></li>
                            <li><a href="/guide"><i class="material-icons">toc</i> Guide</a></li>
                            <li><a href="/docs"><i class="material-icons">library_books</i> Documentation</a></li>
                            <li><a href="/exchange"><i class="material-icons">question_answer</i> Exchange</a></li>
                        </ul>
                    </div>
                </div>

                <div id="right-col" class="col s12 l9 offset-l3">

                    <!-- search result -->
                    <div class="card search show-on-medium-and-down" style="display: none;">
                        <div class="card-content search">
                            <div class="input-field">
                                <input name="riki-search" id="search" type="text" placeholder="Search">
                                <label class="label-icon right"><i class="material-icons">search</i></label>
                            </div>
                            <a id="close-search" href="#"><i class="material-icons">close</i></a>
                        </div>
                        <div class="card-tabs" style="display: none">
                            <ul class="tabs tabs-fixed-width">
                                <li class="tab"><a class="teal-text active" href="#search-everywhere">Everywhere</a></li>
                                <li class="tab"><a class="teal-text" href="#search-documentation">documentation</a></li>
                                <li class="tab"><a class="teal-text" href="#search-exchange">exchange</a></li>
                                <li class="tab"><a class="teal-text" href="#search-guide">guide</a></li>
                            </ul>
                        </div>
                        <div class="card-content search-results grey lighten-5" style="display: none">
                            <div id="search-everywhere">
                                <div class="card search-result">
                                    <div class="search-area"><i class="material-icons">library_books</i></div>
                                    <div class="search-text grey-text text-darken-2">
                                        <h6 class="teal-text">Documentation - Bootstrap</h6>
                                        <p>... sodales sagittis magna. Sed consequat, leo <b>eget</b> bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero ...</p>
                                        <a href="/documentation/boostrap">/documentation/bootstrap</a>
                                    </div>
                                </div>
                                <div class="card search-result">
                                    <div class="search-area"><i class="material-icons">toc</i></div>
                                    <div class="search-text grey-text text-darken-2">
                                        <h6 class="teal-text">Guide - Bootstrap</h6>
                                        <p>... sodales sagittis magna. Sed consequat, leo <b>eget</b> bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero ...</p>
                                        <a href="/guide/boostrap">/guide/bootstrap</a>
                                    </div>
                                </div>
                                <div class="card search-result">
                                    <div class="search-area"><i class="material-icons">question_answer</i></div>
                                    <div class="search-text grey-text text-darken-2">
                                        <h6 class="teal-text">Exchange - How do I bootstrap my application?</h6>
                                        <p>... sodales sagittis magna. Sed consequat, leo <b>eget</b> bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero ...</p>
                                        <a href="/exchange/2315/how-do-i-bootstrap-my-application">/exchange/2315/how-do-i-bootstrap-my-application</a>
                                    </div>
                                </div>
                            </div>
                            <div id="search-documentation">
                                <div class="card search-result">
                                    <div class="search-area"><i class="material-icons">library_books</i></div>
                                    <div class="search-text grey-text text-darken-2">
                                        <h6 class="teal-text">Documentation - Bootstrap</h6>
                                        <p>... sodales sagittis magna. Sed consequat, leo <b>eget</b> bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero ...</p>
                                        <a href="/documentation/boostrap">/documentation/bootstrap</a>
                                    </div>
                                </div>
                            </div>
                            <div id="search-exchange">
                                <div class="card search-result">
                                    <div class="search-area"><i class="material-icons">question_answer</i></div>
                                    <div class="search-text grey-text text-darken-2">
                                        <h6 class="teal-text">Exchange - How do I bootstrap my application?</h6>
                                        <p>... sodales sagittis magna. Sed consequat, leo <b>eget</b> bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero ...</p>
                                        <a href="/exchange/2315/how-do-i-bootstrap-my-application">/exchange/2315/how-do-i-bootstrap-my-application</a>
                                    </div>
                                </div>
                            </div>
                            <div id="search-guide">
                                <div class="card search-result">
                                    <div class="search-area"><i class="material-icons">toc</i></div>
                                    <div class="search-text grey-text text-darken-2">
                                        <h6 class="teal-text">Guide - Bootstrap</h6>
                                        <p>... sodales sagittis magna. Sed consequat, leo <b>eget</b> bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero ...</p>
                                        <a href="/guide/boostrap">/guide/bootstrap</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?= $v->section('content') ?>

                </div>

            </div>
        </div>

        <!-- Footer -->
        <footer class="page-footer teal darken-1">
            <div class="container">
                <div class="row">
                    <div class="col l6 s12">
                        <h5 class="white-text">Footer Content</h5>
                        <p class="grey-text text-lighten-4">You can use rows and columns here to organize your footer content.</p>
                    </div>
                    <div class="col l4 offset-l2 s12">
                        <h5 class="white-text">Links</h5>
                        <ul>
                            <li><a class="grey-text text-lighten-3" href="#!">Link 1</a></li>
                            <li><a class="grey-text text-lighten-3" href="#!">Link 2</a></li>
                            <li><a class="grey-text text-lighten-3" href="#!">Link 3</a></li>
                            <li><a class="grey-text text-lighten-3" href="#!">Link 4</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-copyright">
                <div class="container">
                    <div class="row">
                        <div class="col s12">
                            © 2018 Copyright Thomas Flori
                            <a class="grey-text text-lighten-4 right" href="https://github.com/tflori/riki-community">View on <b>GitHub</b></a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
        <script src="/bundle.js"></script>
    </body>
</html>
