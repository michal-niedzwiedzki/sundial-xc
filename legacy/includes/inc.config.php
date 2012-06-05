<?php

if (file_exists("upgrade.php")) {
	die("<font color=red>The file 'upgrade.php' was located on this server.</font>
	<p>If you are in the process of upgrading, that's fine, please <a href=upgrade.php>Click here</a> to run the upgrade script.<p>If you are NOT in the process of upgrading then leaving this file on the server poses a serious security hazard. Please remove this file immediately.");
}

$config = Config::getInstance();
define ("SERVER_DOMAIN", $config->server->host);	// no http://
define ("SERVER_PATH_URL", $config->server->relative); // no ending slash
#define ("HTTP_BASE",SERVER_DOMAIN.SERVER_PATH_URL);
define ("HTTP_BASE", $config->server->base);
define ("CLASSES_PATH",$_SERVER["DOCUMENT_ROOT"].SERVER_PATH_URL."/classes/");
define ("IMAGES_PATH",SERVER_DOMAIN.SERVER_PATH_URL."/images/");
define ("UPLOADS_PATH",$_SERVER["DOCUMENT_ROOT"].SERVER_PATH_URL."/uploads/");
define ("PROFILE_UPLOADS_PATH",$_SERVER["DOCUMENT_ROOT"].SERVER_PATH_URL."/uploads/");

# Site names
define ("SITE_LONG_TITLE", $config->site->title);
define ("SITE_SHORT_TITLE", $config->site->short);

# Maintenance mode
define ("DOWN_FOR_MAINTENANCE", $config->site->maintenance);
define ("MAINTENANCE_MESSAGE", SITE_LONG_TITLE ." no estÃ¡ disponible en este momento. Vuelve a intentarlo mÃ¡s adelante. ");

/***************************************************************************************************/
/***************** 01-12-08 - 19-12-08 Chris Macdonald (chris@cdmweb.co.uk) ************************/

// The following preferences can be set to turn on/off any of the new features

/* Set the MINIMUM Permission Level a member must hold to be able to submit ANY and ALL HTML
 * 0 = Members, 1 = Committee, 2 = Admins
 * Note: This group will be allowed to submit any HTML tags and will not be restricted by the 'Safe List' defined below */
define("HTML_PERMISSION_LEVEL",1);

// ... HTML Safe List - define the tags that you want to allow all other users (who are below HTML_PERMISSION_LEVEL) to submit
//  Note the format should be just the tag name itself WITHOUT brackets (i.e. 'table' and not '<table>')
$allowedHTML = array('em','i','b','a','br','ul','ol','li','center','img','p');
// [TODO] Taking this a step further we could also specify whether or not a tag is allowed with parameters - currently by default parameters are allowed

// Should we remove any JavaScript found in incoming data? Yes we should.
define("STRIP_JSCRIPT",true);

// Member images are resized 'on-the-fly', keeping the original dimensions. Specify the maximum width the image is to be DOWN-sized to here.
define("MEMBER_PHOTO_WIDTH",200); // in pixels

// Do we want to UP-scale images that are smaller than MEMBER_PHOTO_WIDTH (may look a bit ugly and pixelated)?
define("UPSCALE_SMALL_MEMBER_PHOTO",false);

// The options available in the 'How old is you?' dropdown (trying to be as innocuous as possible here with the defaults (e.g. 40's)- but feel free to provide more specific options)
$agesArr = array('---','Under 18', '18-30','30\'s','40\'s','50\'s','60\'s','70\'s','Over 80','n/a',);

// The options available in the 'What Sex are you?' dropdown. At the time of writing (01-12-2008) the defaults should be fine
$sexArr = array("--","F", "M");

// Enable JavaScript bits on the Dropdown Member Select Box?
// This applies to the Transfer form; the idea is that it makes it simpler to find the member we're after if the dropdown list is lengthy
define("JS_MEMBER_SELECT",true);
// [TODO] Need to make this better - AJAX is probably the best method for this


// END 01-12-08 changes by chris

/**************************************************************/
/******************** SITE CUSTOMIZATION **********************/

// email addresses & phone number to be listed in the site
define ("EMAIL_FEATURE_REQUEST","info@your-domain.org"); // (is this actually used anywhere???)
define ("EMAIL_ADMIN","info@your-domain.org");
define ("PHONE_ADMIN","000-000-0000"); // an email address may be substituted...

// What should appear at the front of all pages?
// Titles will look like "PAGE_TITLE_HEADER - PAGE_TITLE", or something
// like "Local Exchange - Member Directory";
define ("PAGE_TITLE_HEADER", SITE_LONG_TITLE);

// What keywords should be included in all pages?
define ("SITE_KEYWORDS", "banco de tiempo, a2manos, malasaña, madrid, ". SITE_LONG_TITLE);

// Logo Graphic for Header
define ("HEADER_LOGO", "a2manos_logo.png");

// Title Graphic for Header
define ("HEADER_TITLE", "localx_title.png");

// Logo for Home Page
define ("HOME_LOGO", "localx_black.png");

// Picture appearing left of logo on Home Page
define ("HOME_PIC", "localx_home.png");

/**********************************************************/
/**************** DEFINE SITE SECTIONS ********************/

define ("EXCHANGES",0);
define ("LISTINGS",1);
define ("EVENTS",2);
define ("ADMINISTRATION",3);
define ("PROFILE",4);
define ("SECTION_FEEDBACK",5);
define ("SECTION_EMAIL",6);
define ("SECTION_INFO",7);
define ("SECTION_DIRECTORY",8);

/**********************************************************/
/******************* GENERAL SETTINGS *********************/

define ("UNITS", "Horas");  // This setting affects functionality, not just text displayed, so if you want to use hours/minutes this needs to read "Hours" exactly.  All other unit descriptions are ok, but receive no special treatment (i.e. there is no handling of "minutes").

/**************** Monthly fee related settings ********************/

define("SYSTEM_ACCOUNT_ID", "system");
$monthly_fee_exempt_list = array("ADMIN", SYSTEM_ACCOUNT_ID);

// End of monthly fee related settings.

define ("MAX_FILE_UPLOAD","5000000"); // Maximum file size, in bytes, allowed for uploads to the server

// The following text will appear at the beggining of the email update messages
define ("LISTING_UPDATES_MESSAGE", "<h1>".SITE_LONG_TITLE."</h1>The following listings are new or updated.<p>If you would prefer not to receive automatic email updates, or if you would like to change their frequency, you can do so at the <a href=http://".HTTP_BASE."/member_edit.php?mode=self>Member Profile</a> area of our website.");

// Should inactive accounts have their listings automatically expired?
// This can be a useful feature.  It is an attempt to deal with the
// age-old local currency problem of new members joining and then not
// keeping their listings up to date or using the system in any way.
// It is designed so that if a member doesn't record a trade OR update
// a listing in a given period of time (default is six months), their
// listings will be set to expire and they will receive an email to
// that effect (as will the admin).
define ("EXPIRE_INACTIVE_ACCOUNTS",false);

// If above is set, after this many days, accounts that have had no
// activity will have their listings set to expire.  They will have
// to reactiveate them individually if they still want them.
define ("MAX_DAYS_INACTIVE","180");

// How many days in the future the expiration date will be set for
define ("EXPIRATION_WINDOW","15");

// How long should expired listings hang around before they are deleted?
define ("DELETE_EXPIRED_AFTER","90");


// The following message is the one that will be emailed to the person
// whose listings have been expired (a delicate matter).
define ("EXPIRED_LISTINGS_MESSAGE", "Hello,\n\nDue to inactivity, your ".SITE_SHORT_TITLE." listings have been set to automatically expire ". EXPIRATION_WINDOW ." days from now.\n\nIn order to keep the ".SITE_LONG_TITLE." system up to date and working smoothly for all members, we have developed an automatic system to expire listings for members who haven't recorded exchanges or updated their listings during a period of ".MAX_DAYS_INACTIVE." days. We want the directory to be up to date, so that members do not encounter listings that are out of date or expired. This works to everyone's advantage.\n\nWe apologize for any inconvenience this may cause you and thank you for your participation. If you have any questions or comments, or are unsure how to best use the system, please reply to this email message or call us at ".PHONE_ADMIN.".\n\nYou have ". EXPIRATION_WINDOW ." days to login to the system and reactivate listings that you would still like to have in the directory.  If you do not reactivate them during that timeframe, your listings will no longer appear in the directory, but will still be stored in the system for another ". DELETE_EXPIRED_AFTER ." days, during which time you can still edit and reactivate them.\n\n\nInstructions to reactivate listings:\n1) Login to the website\n2) Go to Update Listings\n3) Select Edit Offered (or Wanted) Listings\n4) Select the listing to edit\n5) Uncheck the box next to 'Should this listing be set to automatically expire?'\n6) Press the Update button\n7) Repeat steps 1-6 for all listings you wish to reactivate\n");

// The year your local currency started -- the lowest year shown
// in the Join Year menu option for accounts.
define ("JOIN_YEAR_MINIMUM", "2011");

define ("DEFAULT_COUNTRY", "España");
define ("DEFAULT_ZIP_CODE", "28004"); // This is the postcode.
define ("DEFAULT_CITY", "Madrid");
define ("DEFAULT_STATE", "Madrid");
define ("DEFAULT_PHONE_AREA", "91");

// Should short date formats display month before day (US convention)?
define ("MONTH_FIRST", false);

define ("PASSWORD_RESET_SUBJECT", "Tu Cuenta con el ". SITE_LONG_TITLE);
define ("PASSWORD_RESET_MESSAGE", "Tu contraseña para  ". SITE_LONG_TITLE ." ha sido cambiada. Si no has pedido este cambio, es posible que alguien ha accedido a tu cuenta y debes llamar al equipo del banco en el número de teléfono ".PHONE_ADMIN.".\n\nTu id de soci@ y la contraseña nueva estan incluidos en este mensaje. Siempre puedes cambiar la contraseña en la sección de Perfil de soci@ despues de entrar en la aplicación.");
define ("NEW_MEMBER_SUBJECT", "Bienvenid@ al ". SITE_LONG_TITLE);
define ("NEW_MEMBER_MESSAGE", "Hola, bienvenid@ a la comunidad ". SITE_LONG_TITLE ." !\n\nTu cuenta de soci@ ha sido creada en:\nhttp://".SERVER_DOMAIN.SERVER_PATH_URL."/member_login.php\n\nPuedes entrar y crear tus servicios ofrecidos y los que quieres solicitar.  Tu id de soci@ y la contraseña estan incluidos en este mensaje. Siempre puedes cambiar la contraseña en la sección de Perfil de soci@ despues de entrar en la aplicación.\n\nMuchas gracias por colaborar con el proyecto.");

/********************************************************************/
/************************* ADVANCED SETTINGS ************************/
// Normally, the defaults for the settings that follow don't need
// to be changed.

// What's the name and location of the stylesheet?
define ("SITE_STYLESHEET", "style.css");

// How long should trades be listed on the "leave feedback for
// a recent exchange" page?  After this # of days they will be
// dropped from that list.
define ("DAYS_REQUEST_FEEDBACK", "30");

// Is debug mode on? (display errors to the general UI?)
define ("DEBUG", false);

// Should adminstrative activity be logged?  Set to 0 for no logging; 1 to
// log trades recorded by administrators; 2 to also log changes to member
// settings (LEVEL 2 NOT YET IMPLEMENTED)
define ("LOG_LEVEL", 1);

// How many consecutive failed logins should be allowed before locking out an account?
// This is important to protect against dictionary attacks.  Don't set higher than 10 or 20.
define ("FAILED_LOGIN_LIMIT", 10);

// CSS-related settings.  If you'r looking to change colors,
// best to edit the CSS rather than add to this...
$CONTENT_TABLE = array("id"=>"contenttable", "cellspacing"=>"0", "cellpadding"=>"3");

// System events are processes which only need to run periodically,
// and so are run at intervals rather than weighing the system
// down by running them each time a particlular page is loaded.
// System Event Codes (such as ACCOUNT_EXPIRATION) are defined in inc.global.php
// System Event Frequency (how many minutes between triggering of events)
$SYSTEM_EVENTS = array(ACCOUT_EXPIRATION => 1440);  // Expire accounts once a day (every 1440 minutes)

/**********************************************************/
//	Everything below this line simply sets up the config.
//	Nothing should need to be changed, here.

define ("ADDRESS_LINE_1", "Calle");
define ("ADDRESS_LINE_2", "Barrio");
define ("STATE_TEXT", "Ciudad");
define ("ZIP_TEXT", "Codigo Postal");

define("LOAD_FROM_SESSION",-1);  // Not currently in use

// URL to PHP page which handles redirects and such.
define ("REDIRECT_URL",SERVER_PATH_URL."/redirect.php");
