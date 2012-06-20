# About Sundial XC

Sundial XC is a software to facilitate time bakning and to support local communities built around the idea of time bank.
If you want to know more about the concept of "time banks" - and you really should - just look up
[prof. Edgar Cahn on YouTube](www.youtube.com/results?search_query=Edgar+Cahn+RSA).

This software is not of production quality yet. You are advised to test it thoroughly before running your time bank on it.

Sundial XC is distributed under terms of [GNU Affero General Public License](http://www.gnu.org/licenses/agpl.html)
version 3.0 or later.

## Setup

To install Sundial XC you need a web server with PHP 5.3+ and MySQL 5.1+.

* Set up your web server to serve directory _public/www/_
* In your browser go to _http://example.com/install.php_
* Follow the instructions on screen

## Credits and history

Sundial XC is a fork followed by rewrite of:

* [Local-Exchange](http://sourceforge.net/projects/local-exchange/)
* [Local-Exchange-UK](https://github.com/cdmweb/Local-Exchange-UK) v1.01 translated into Spanish by
[Graeme Herbert](http://www.linkedin.com/pub/graeme-herbert/9/503/794) for local time bank
[A2Manos](http://www.bancodetiempomalasana.com) in Madrid, Spain

Sundial XC was massively refactored and largely reimplemented from scratch by Michał Rudnicki.
The URL structure, config entries, and UI flows remain the same as in original work,
but it's not guaranteed and may change in future.

## Refactoring old code

Code refactor is progressing in many fronts simultaneously:

* **Controllers.** Old _./*.php_ files from root directory are being trainsitioned into controller actions.
See directory _controllers/_ to see how this is done. Files not yet transitioned are included from within controllers.
If you want to help moving this code, this is probably the easiest bit to start with.
* **Forms.** Old in-line forms are extracted to _forms/_ directory as standalone, reusable, loosely coupled components.
This is also a good place to begin helping. Look at some of existing forms and start extracting in-code ones
from old _./*.php_ files to separate classes.
* **Templates.** PHTML templates are extracted from old in-line code to separate files that match controller action names.
See directory _templates/pages/_ to get to the core of this change.
* **Model.** Business logic is broken into separate files in _model/legacy/_ directory. The biggest part in this field
is to migrate from using global $cDB object to PDOHelper. Another challenge is to free the model from UI rendering code.

## Future versions

* **Internationalization with gettext.** Yeah, currently it's in Spanish.
* **Proper cron mechanism.** Existing _events_ need to run asynchronously.
* **Database normalization.** You will see the need for this one as soon as you look into existing database.
* **Migration script from Local-Excahnge-UK.**
* **Restful API version 0.0**. API for existing model classes shielded by authorization layer. Covered in 100% with tests.
* **Android client.** Nice to have to explore correctness and completeness of API v 0.0.
* **Interoperability between instances of Sundial XC.** Very important to connect members not only within community but also across communities.
