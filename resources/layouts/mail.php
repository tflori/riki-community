<?php /** @var Syna\View $v */ ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?= $v->section('subject') ?></title>
    <style>
      body, div, td {
        font-family: "Open Sans",
          -apple-system, BlinkMacSystemFont,
          "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue",
          sans-serif;
        vertical-align: top;
      }

      html {
        font-size: 14px;
        line-height: 1.5;
        font-weight: normal;
        color: rgba(0,0,0,0.87);
      }

      table {
        width: 100%;
      }

      .body .background {
        background-color: #f0f0f0;
      }

      .container {
        width: 640px;
        padding: 1em;
        background-color: #ffffff;
      }

      .container.colored {
        background-color: #00887a;
        color: #ffffff;
      }

      @media only screen and (max-width: 620px) {
        .body .container { width: 100% !important; }
        .body .background { display: none !important; }
        img { max-width: 100%; }
      }
    </style>
  </head>
  <body>
    <table class="body" cellspacing="0" cellpadding="0" border="0">
      <tr>
        <td class="background">&nbsp;</td>
        <td class="background">&nbsp;</td>
        <td class="background">&nbsp;</td>
      </tr>
      <tr>
        <td class="background">&nbsp;</td>
        <td class="container colored">
          <a href="https://riki.w00tserver.org"><img src="images/logo-horizontal.png" width="320"/></a>
        </td>
        <td class="background">&nbsp;</td>
      </tr>
      <tr>
        <td class="background">&nbsp;</td>
        <td class="container">
            <?= $v->section('content') ?>
        </td>
        <td class="background">&nbsp;</td>
      </tr>
      <tr>
        <td class="background">&nbsp;</td>
        <td class="container colored">
          <table>
            <tr>
              <td>
                <h4 style="margin: 0;">ríki community</h4>
                <p style="margin: 0; font-size: 10px;">&copy; 2019 Thomas Flori</p>
              </td>
              <td align="right">
                <a href="https://github.com/tflori/riki-community"><img src="images/github-light.png" width="32"/></a>
              </td>
            </tr>
          </table>
        </td>
        <td class="background">&nbsp;</td>
      </tr>
      <tr>
        <td class="background">&nbsp;</td>
        <td class="background">&nbsp;</td>
        <td class="background">&nbsp;</td>
      </tr>
    </table>
  </body>
</html>
