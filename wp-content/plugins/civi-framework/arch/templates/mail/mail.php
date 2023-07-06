<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch></o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Newsletter</title>

    <style type="text/css">
        a,a[href],a:hover, a:link, a:visited {
            text-decoration: none!important;
            color: #007456 !important;
        }
        .link {
            text-decoration: underline!important;
        }
        p, p:visited {
            font-size:15px;
            margin-bottom: 12px;
            margin-top: 0;
            text-decoration:none;
            color: #555555;
        }
        h1 {
            font-size:22px;
            font-family:'Inter';
            font-weight:600;
            margin-bottom: 0;
            text-decoration:none;
            color: #007456;
        }
        .mail-footer p{
            font-size: 14px;
        }
        .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td {line-height: 100%;}
        .ExternalClass {width: 100%;}
    </style>
</head>

<body style="text-align: center; margin: 0; padding-top: 10px; padding-bottom: 10px; padding-left: 0; padding-right: 0; -webkit-text-size-adjust: 100%;background-color: #f2f4f6; color: #000000" align="center">
<div style="text-align: center;">

    <table align="center" style="text-align: center; border-bottom:1px solid #eee; vertical-align: top; width: 600px; max-width: 600px; background-color: #ffffff;" width="600">
        <tbody>
        <tr>
            <td style="width: 596px; vertical-align: top; padding: 30px;" width="596">
                <?php civi_get_template('mail/header.php'); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table align="center" style="text-align: left; vertical-align: top; width: 600px; max-width: 600px; background-color: #ffffff;" width="600">
        <tbody>
        <tr>
            <td style="width: 596px; vertical-align: top;padding:30px;" width="596">
               <?php if(!empty($content)){
                   echo  $content;
               } ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table class="mail-footer" align="center" style="text-align: center;border-top:1px solid #eee; vertical-align: top; width: 600px; max-width: 600px;background-color: #ffffff;" width="600">
        <tbody>
        <tr>
            <td style="width: 596px; vertical-align: top; padding-left: 30px; padding-right: 30px; padding-top: 30px; padding-bottom: 30px;" width="596">
                <?php civi_get_template('mail/footer.php'); ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>