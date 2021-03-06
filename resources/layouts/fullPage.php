<?php

/** @var callable $e */
/** @var Syna\View $v */

use App\Application;

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" href="/images/favicon.png" sizes="48x48">
    <link rel="apple-touch-icon" sizes="256x256" href="/images/favicon-2x.png">
    <meta name="theme-color" content="#00897b">
    <title>Ríki:Welcome!<?= $v->section('article-title') ? ' - ' . $v->section('article-title') : '' ?></title>
    <?php if ($v->section('article-title')) : ?>
      <meta property="og:site_name" content="<?= $v->section('article-title') ?>"/>
      <meta property="og:type" content="article"/>
      <meta property="og:description" content="<?= $v->section('article-description', $v->section('article-title')) ?>"/>
    <?php endif; ?>
    <link rel="stylesheet" href="<?= $v->cacheBuster('/bundle.css'); ?>">
  </head>
  <body>
    <div id="riki-community" :class="{ dark: darkModeEnabled }">

      <!-- Header -->
      <header>
        <nav>
          <div class="nav-wrapper">
            <div class="logo-wrapper">
              <div id="header-background-icon"></div>
              <div id="header-background-modules"></div>
              <div id="header-background-name"></div>
              <div id="header-background-subtitle"></div>
            </div>
            <a href="#" class="sidenav-toggle left hide-on-large-only">
              <i class="material-icons menu-icon">menu</i><i class="material-icons close-icon">close</i>
            </a>
            <ul class="right">
              <user-status></user-status>
            </ul>
            <div class="account">

            </div>
          </div>
        </nav>
      </header>

      <!-- Toolbar -->
      <div class="navbar-fixed">
        <nav>
          <div class="nav-wrapper">
            <a href="#" class="sidenav-toggle left hide-on-large-only">
              <i class="material-icons menu-icon">menu</i><i class="material-icons close-icon">close</i>
            </a>
            <a href="/home" class="brand-logo left">
              <div id="logo-icon"></div>
              <div id="logo-name"></div>
              <div id="logo-subtitle"></div>
            </a>
            <ul class="right">
              <user-status></user-status>
            </ul>
          </div>
        </nav>
      </div>

      <!-- Overlay container -->
      <div ref="overlayContainer"></div>

      <!-- Mobile Navigation -->
      <ul class="sidenav" id="mobile-nav">
        <li><a href="/home"><i class="material-icons">home</i> Home</a></li>
        <li><a href="/blog"><i class="material-icons">rss_feed</i> Blog</a></li>
        <li><a href="/guide"><i class="material-icons">toc</i> Guide</a></li>
        <li><a href="/docs"><i class="material-icons">library_books</i> Documentation</a></li>
        <li><a href="/exchange"><i class="material-icons">question_answer</i> Exchange</a></li>
        <li class="toggles">
          <hr style="width: 80%">
          <div class="center-align">
            <a class="btn btn-icon waves-effect waves-light darken-1" :class="{'teal': darkModeEnabled, 'grey': !darkModeEnabled}" @click="toggleDarkMode" title="Dark Mode"><i class="material-icons">bedtime</i></a>
          </div>
        </li>
      </ul>

      <!-- Content -->
      <main class="main container">
        <div class="row">

          <aside id="left-col" class="col l3 hide-on-med-and-down">
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
              <div class="toggles">
                <hr style="width: 80%">
                <div class="center-align">
                  <a class="btn btn-icon waves-effect waves-light darken-1" :class="{'teal': darkModeEnabled, 'grey': !darkModeEnabled}" @click="toggleDarkMode" title="Dark Mode"><i class="material-icons">bedtime</i></a>
                </div>
              </div>
            </div>
          </aside>

          <div id="right-col" class="col s12 l9 offset-l3">

            <!-- search result -->
            <aside class="card search show-on-medium-and-down" style="display: none;">
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
                      <p>... sodales sagittis magna. Sed consequat, leo <b>eget</b> bibendum sodales, augue velit cursus
                        nunc, quis gravida magna mi a libero ...</p>
                      <a href="/documentation/boostrap">/documentation/bootstrap</a>
                    </div>
                  </div>
                  <div class="card search-result">
                    <div class="search-area"><i class="material-icons">toc</i></div>
                    <div class="search-text grey-text text-darken-2">
                      <h6 class="teal-text">Guide - Bootstrap</h6>
                      <p>... sodales sagittis magna. Sed consequat, leo <b>eget</b> bibendum sodales, augue velit cursus
                        nunc, quis gravida magna mi a libero ...</p>
                      <a href="/guide/boostrap">/guide/bootstrap</a>
                    </div>
                  </div>
                  <div class="card search-result">
                    <div class="search-area"><i class="material-icons">question_answer</i></div>
                    <div class="search-text grey-text text-darken-2">
                      <h6 class="teal-text">Exchange - How do I bootstrap my application?</h6>
                      <p>... sodales sagittis magna. Sed consequat, leo <b>eget</b> bibendum sodales, augue velit cursus
                        nunc, quis gravida magna mi a libero ...</p>
                      <a href="/exchange/2315/how-do-i-bootstrap-my-application">
                        /exchange/2315/how-do-i-bootstrap-my-application
                      </a>
                    </div>
                  </div>
                </div>
                <div id="search-documentation">
                  <div class="card search-result">
                    <div class="search-area"><i class="material-icons">library_books</i></div>
                    <div class="search-text grey-text text-darken-2">
                      <h6 class="teal-text">Documentation - Bootstrap</h6>
                      <p>... sodales sagittis magna. Sed consequat, leo <b>eget</b> bibendum sodales, augue velit cursus
                        nunc, quis gravida magna mi a libero ...</p>
                      <a href="/documentation/boostrap">/documentation/bootstrap</a>
                    </div>
                  </div>
                </div>
                <div id="search-exchange">
                  <div class="card search-result">
                    <div class="search-area"><i class="material-icons">question_answer</i></div>
                    <div class="search-text grey-text text-darken-2">
                      <h6 class="teal-text">Exchange - How do I bootstrap my application?</h6>
                      <p>... sodales sagittis magna. Sed consequat, leo <b>eget</b> bibendum sodales, augue velit cursus
                        nunc, quis gravida magna mi a libero ...</p>
                      <a href="/exchange/2315/how-do-i-bootstrap-my-application">
                        /exchange/2315/how-do-i-bootstrap-my-application
                      </a>
                    </div>
                  </div>
                </div>
                <div id="search-guide">
                  <div class="card search-result">
                    <div class="search-area"><i class="material-icons">toc</i></div>
                    <div class="search-text grey-text text-darken-2">
                      <h6 class="teal-text">Guide - Bootstrap</h6>
                      <p>... sodales sagittis magna. Sed consequat, leo <b>eget</b> bibendum sodales, augue velit cursus
                        nunc, quis gravida magna mi a libero ...</p>
                      <a href="/guide/boostrap">/guide/bootstrap</a>
                    </div>
                  </div>
                </div>
              </div>
            </aside>

            <?= $v->section('content') ?>

          </div>

        </div>
      </main>

      <?= $v->fetch('partials/footer') ?>

    </div>

    <script>
      // noinspection JSAnnotator
      const AppConfig = <?= json_encode(Application::config()->frontEnd) ?>;
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.8/vue.min.js"></script>
    <script src="<?= $v->cacheBuster('/bundle.js') ?>"></script>
  </body>
</html>
