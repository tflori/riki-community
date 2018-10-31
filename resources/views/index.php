<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="/favicon.ico">
        <link rel="icon" href="/images/favicon.png" sizes="48x48">
        <link rel="apple-touch-icon" sizes="256x256" href="/images/favicon-2x.png">
        <title>Ríki:Welcome!</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans">
        <link rel="stylesheet" href="/css/main.css">
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
        <div class="main container">
            <div class="row">
                <div class="col l4 hide-on-med-and-down" id="left-col">
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
                <div class="col s12 l9 offset-l3" id="right-col">
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
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title">Lorem Ipsum</span>
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu.<br><br></p>

                            <p>In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum.<br><br></p>

                            <p>Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus.<br><br></p>

                            <p>Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero. Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Nullam accumsan lorem in dui.<br><br></p>

                            <p>Cras ultricies mi eu turpis hendrerit fringilla. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; In ac dui quis mi consectetuer lacinia. Nam pretium turpis et arcu. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Sed aliquam ultrices mauris. Integer ante arcu, accumsan a, consectetuer eget, posuere ut, mauris. Praesent adipiscing.<br><br></p>

                            <p>Phasellus ullamcorper ipsum rutrum nunc. Nunc nonummy metus. Vestibulum volutpat pretium libero. Cras id dui. Aenean ut eros et nisl sagittis vestibulum. Nullam nulla eros, ultricies sit amet, nonummy id, imperdiet feugiat, pede. Sed lectus. Donec mollis hendrerit risus. Phasellus nec sem in justo pellentesque facilisis. Etiam imperdiet imperdiet orci. Nunc nec neque.<br><br></p>

                            <p>Phasellus leo dolor, tempus non, auctor et, hendrerit quis, nisi. Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Maecenas malesuada. Praesent congue erat at massa. Sed cursus turpis vitae tortor. Donec posuere vulputate arcu. Phasellus accumsan cursus velit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed aliquam, nisi quis porttitor congue, elit erat euismod orci, ac placerat dolor lectus quis orci.<br><br></p>

                            <p>Phasellus consectetuer vestibulum elit. Aenean tellus metus, bibendum sed, posuere ac, mattis non, nunc. Vestibulum fringilla pede sit amet augue. In turpis. Pellentesque posuere. Praesent turpis. Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc, eu sollicitudin urna dolor sagittis lacus. Donec elit libero, sodales nec, volutpat a, suscipit non, turpis. Nullam sagittis. Suspendisse pulvinar, augue ac venenatis condimentum, sem libero volutpat nibh, nec pellentesque velit pede quis nunc. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Fusce id purus. Ut varius tincidunt libero. Phasellus dolor. Maecenas vestibulum mollis<br><br></p>
                        </div>
                    </div>
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
                    © 2018 Copyright Thomas Flori
                    <a class="grey-text text-lighten-4 right" href="#!">More Links</a>
                </div>
            </div>
        </footer>

        <!-- JS sources -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.2/jquery.scrollTo.min.js"></script>
        <script src="/js/scrolling.js"></script>

    </body>
</html>
