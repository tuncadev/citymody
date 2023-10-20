=== Simple Google reCAPTCHA ===
Contributors: Minor
Tags: recaptcha, spam, protect, google, invisible
Requires at least: 4.6
Tested up to: 6.0
Stable tag: 4.0
Requires PHP: 7.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Donate link: https://www.paypal.me/NovaMi

Simply protect your WordPress against spam comments and brute-force attacks thanks to Google reCAPTCHA v3 or v2 Checkbox for free and without ads!

== Description ==
Simple Google reCAPTCHA will protect your WordPress! You have choice between default v2 Checkbox and v3 (like invisible reCAPTCHA).

No more spam comments and brute-force attacks against user accounts. Small plugin, only necessary code - no ads or tracking!

Google reCAPTCHA verification will be required only for not logged in users.

User, who newly setup the keys, will see (max for 10 days) emergency reCAPTCHA deactivate link - don't need FTP access to disable Simple Google reCAPTCHA in case of emergency now.

= What is protected with reCAPTCHA? =
* Login form
* Registration form
* Reset password form
* Comment form
* New password form

= Thank you! =
Thanks all of you, who are using this plugin, I really appreciate it!

If you write me (on support forum etc.), be patient, please. I work on this plugin in my free time, it's only my hobby.

== Installation ==
1. Upload plugin folder under standard plugins directory "/wp-content/plugins/" or install through the WordPress Plugins page.
2. Activate plugin via WordPress Plugins page.
3. Insert reCAPTCHA v3 or v2 Checkbox keys.
4. Done, your WordPress is protected now!

== Frequently Asked Questions ==
= Why to install this plugin? =
* No ads, user tracking, send statistics neither survey
* Only arround 20kB size (without readme file)
* Google reCAPTCHA v3 (invisible) support
* Possibility to replace v3 reCAPTCHA badge by text
* reCAPTCHA language based on WordPress settings
* Works in countries where Google domain is blocked
* Emergency reCAPTCHA deactivate link for admin
* Hooks - Possibility to customize where reCAPTCHA will be rendered and verified

= In case you have a problem =
1. Important message could be shown in browser console (F12) on problematic page
2. Double check if you have correct keys in settings - is those keys for correct version reCAPTCHA?
3. Try to create new website in Google reCAPTCHA console (and use different website name)

= How to disable this plugin? =
Use standard WordPress Plugins page. In emergency case, rename plugin folder under /wp-content/plugins/ over FTP access or use emergency reCAPTCHA deactivate link.

= How to use hooks? =
For example, you can use this in your global functions.php file:

<pre><code>
function customSgrRenderList(array $list): array //Where reCAPTCHA is rendered
{
    //unset($list[0]);
    $list[] = 'register_form';

    return $list;
}

add_action('sgr_render_list', 'customSgrRenderList');

function customSgrVerifyList(array $list): array //Where reCAPTCHA is verified
{
    //unset($list[0]);
    $list[] = 'lostpassword_post';

    return $list;
}

add_action('sgr_verify_list', 'customSgrVerifyList');
</code></pre>

Variable $list is array of default hooks, indexed by numbers.

== Screenshots ==
1. New comment
2. New password
3. Registration
4. Login
5. Settings
6. reCAPTCHA v3 text instead of badge
7. Emergency reCAPTCHA deactivate link

== Changelog ==
= 4.0 =
* Warning: I don't recommend Google reCAPTCHA v2. You should enable v3 in plugin settings (requires different keys)!
* New: Hooks - sgr_render_list + sgr_verify_list. You are able to customize where Google reCAPTCHA will be rendered and verified - e.g. via functions.php.

= 3.9 =
* Bugfix: reCAPTCHA verification has been rewritten. More reliable and prevents brute force attacks now.

= 3.8 =
* Bugfix: Fix against rare error 'The response is no longer valid: either is too old or has been used previously.' for reCAPTCHA v3
* New: Class converted to singleton - for possibility to use Simple Google reCAPTCHA in custom hooks

= 3.7 =
* Bugfix: Important! Everybody who has version 3.6 should update as soon as possible! Fixed a bug with disappearing site&secret key.

= 3.6 =
* New: Emergency reCAPTCHA deactivate link

= 3.5 =
* Bugfix: More reliable reCAPTCHA injection (init action)
* New: Dynamic action name to see stats in Google reCAPTCHA admin console for each page

= 3.4 =
* Bugfix: Translations works again correctly
* New: Works in countries where Google domain is blocked
* New: Possibility to replace v3 reCAPTCHA badge by text

= 3.3 =
* Bugfix: BuddyPress registration is now pass through
* Bugfix: Other minor fixes

= 3.2 =
* Warning: Keys validation after save was not reliable, validation removed
* New: Added support for Google reCAPTCHA v3

= 3.1 =
* New: Keys validation after save
* New: More detailed error messages
* New: Plugin is disabled until you set correct keys

= 3.0 =
* Bugfix: User login (including password in plaintext) could get into server error log in specific case.

= 2.9 =
* New: Option for disable reCAPTCHA on login page
* New: Error message instead of redirecting to the error page, besides the case of posting comments
* Bugfix: Loading Js file on unique HTML element to avoid collision

= 2.8 =
* Warning: New logic - Google reCAPTCHA js file will be loaded in the background on every page for non logged in users
* Warning: If Google reCAPTCHA verification fail, response code is 403 instead of 500 now. Thank you for contribution, Sara Kozińska!
* Bugfix: WooCommerce problem (JSON.parse error) in checkout process has been fixed. I'm sorry for a really big delay!

= 2.7 =
* Bugfix: Loading of Google reCAPTCHA form failed in some rare cases

= 2.6 =
* Bugfix: Fatal error on websites running on PHP 5

= 2.5 =
* Warning: Removed javascript function which disabling/enabling submit button If reCAPTCHA was passed, because of incompatibility with some websites in specific cases
* Bugfix: WooCommerce - If you have activated login and register form on one page, reCAPTCHA verification is require too for register
* New: Added uninstall script which clean settings from DB while uninstall process
* New: If you activate plugin and site or secret key is empty, you will be redirect to settings page

= 2.4 =
* New: reCAPTCHA verification added on every page that allows comments (not bothering registered users)

= 2.3 =
* New: Added donate link, you can buy me a coffee now :-)
* Bugfix: Plugin warnings on php7 - not quoted functions name

= 2.2 =
* Warning: Possibility to decide when reCAPTCHA will be shown was removed (not bothering registered users)
* New: Including BuddyPress and WooCommerce support
* Bugfix: Incompatibility with translations

= 2.1 =
* Bugfix: No more unnecessary loading reCAPTCHA on the other pages
* Bugfix: No more reCAPTCHA window over Clef waves (if you are using Clef plugin) on the login page

= 2.0 =
* Warning: reCAPTCHA verification on the BuddyPress registration page has been removed
* Warning: reCAPTCHA verification on the Add new comment form for logged in users has been removed
* Warning: Due to keep Simple Google reCAPTCHA as simple as possible some configuration options were removed
* New: Language settings of reCAPTCHA is based on WordPress locale now
* New: Default WordPress submit buttons are disabled until reCAPTCHA isn't solved
* New: Added reCAPTCHA for Resset password form
* Update: Text corrections
* Bugfix: reCAPTCHA verification just on the standard WordPress pages (unmodified by plugins/templates)

= 1.9 =
* Warning: Probably you will need to do a new translations
* New: Possibility to set language of reCAPTCHA
* Update: Minor updates for easier official translations

= 1.8 =
* New: reCAPTCHA verification on the BuddyPress registration page
* Bugfix: Translatable back button "Zpět"

= 1.7 =
* New: You can choose where reCAPTCHA will be required
* Bugfix: reCAPTCHA will be required only If a form has been submitted

= 1.6 =
* Bugfix: Name of settings has been changed - to avoid conflict with other plugins

= 1.5 =
* New: Possibility to disable reCAPTCHA in comment form for logged in users

= 1.4 =
* Update: Encoding has been converted from Windows to Unix
* Update: Text corrections

= 1.3 =
* New: Added "Settings" button to WordPress plugins page
* New: reCAPTCHA is required only after filled in settings
* Update: Text domain has been changed from simple-google-recaptcha to sgr - need to set up keys again

= 1.2 =
* Update: Simple Google reCAPTCHA folder - unnecessary files were deleted

= 1.1 =
* Update: Screenshots
* Update: Text corrections
* Bugfix: Logged in users are able to post comments

= 1.0 =
* New: Simple Google reCAPTCHA has been released!