# About Sundial XC

Sundial XC is a software to facilitate time bakning and to support local communities built around the idea of time bank.
If you want to know more about the concept of "time banks" - and you really should - just look up
[prof. Edgar Cahn on YouTube](www.youtube.com/results?search_query=Edgar+Cahn+RSA).

This software is not of production quality yet. You are advised to test it thoroughly before running your time bank on it.

Sundial XC is distributed under terms of [GNU Affero General Public License](http://www.gnu.org/licenses/agpl.html)
version 3.0 or later.

## Credits and history

Sundial XC is a fork followed by rewrite of:

* [Local-Exchange](http://sourceforge.net/projects/local-exchange/)
* [Local-Exchange-UK](https://github.com/cdmweb/Local-Exchange-UK) v1.01 translated into Spanish by
[Graeme Herbert](http://www.linkedin.com/pub/graeme-herbert/9/503/794) for local time bank
[A2Manos](http://www.bancodetiempomalasana.com) in Madrid, Spain

## Setup

To install Sundial XC you need a web server with PHP 5.3+ and MySQL 5.1+.

* Set up your web server to serve directory _public/www/_
* In your browser go to _http://example.com/install.php_
* Follow the instructions on screen

## Refactoring old code

So far the following architectural changes were made:

* Global defines were replaced with Config singleton and can be controlled via _config/*.json_ file.
Existing config parameters can be found in _legacy_ section.
* Use of global $cDB object was replaced with PDO and PDOHelper singleton. All database queries are now run
as prepared statements. Parameter escaping is handled automatically via bound parameters. Database connection
details are controlled by config section _database_. Separate db silos are provided for testing and normal opration.
* Use of global $cErr class was replaced with Debug class and multiple logging backend. File logging is handled by
class LogWriterFile, while on-screen logging is handled by class LogWriterScreen. Writers can be controlled in
config section _log_. Error and exception handlers are set up to intercepts all such events and forward them to Debug.
* In-line HTML was separated from business logic and replaced with templates and views. Page wrapper was created
to provide common header/footer and notification area. All template strings are automatically escaped.
* Forms were extracted from pages and moved to separate files. Unified form validation was added. CSRF checks
were added. Fixes in QuickForm component were made to make it compatible with PHP 5.3.
* Code from page files was moved to controllers. Essential controllers completely replaced existing page files,
other are used as wrappers for such files. Controllers can be annotated to allow access control, page title setting,
HTTP response codes, etc.
* Model code was extracted to separate files. UI related code removed or replaced with views and templates.
* Leftovers from legacy code were moved to _legacy_ directory.
* Fully asynchronous Cron mechanism was added. Email messages are now being recorded and dispatched by cron job.
* Database migration system was developed with schema versioning and full upgrade/downgrade track.
* REST API endpoint was created.

Code refactor is progressing in many fronts simultaneously:

* **Controllers.** Legacy php files from root directory are being trainsitioned into controller actions.
See directory _controllers/_ to see how this is done. Files not yet transitioned are included from within controllers.
If you want to help moving this code, this is probably the easiest bit to start with.
* **Forms.** Old in-line forms are extracted to _forms/_ directory as standalone, reusable, loosely coupled components.
This is also a good place to begin helping. Look at some of existing forms and start extracting in-code ones
from legacy php files to separate classes.
* **Templates.** PHTML templates are extracted from old in-line code to separate files that match controller action names.
See directory _templates/pages/_ to get to the core of this change.
* **Model.** Business logic is broken into separate files in _model/legacy/_ directory. The biggest part in this field
is to migrate from using global $cDB object to PDOHelper. Another challenge is to free the model from UI rendering code.

Controllers are now heavily annotated. This is the meaning of some annotations:

* `@Title "some string"` - HTML page title.
* `@Public` - page accessible to non logged in users.
* `@Level 1` - access allowed only to users with given privilege level.
* `@ResponseCode 404` - HTTP response code.
* `@Page "some_file.phtml"` - directive to use different than default template file for certain action.

## Future versions

* **Internationalization with gettext.** Yeah, currently it's in Spanish.
* **Database normalization.** You will see the need for this one as soon as you look into existing database.
* **Migration script from Local-Excahnge-UK.**
* **Restful API version 0.0**. API for existing model classes shielded by authorization layer. Covered in 100% with tests.
* **Android client.** Nice to have to explore correctness and completeness of API v 0.0.
* **Interoperability between instances of Sundial XC.** Very important to connect members not only within community but also across communities.
