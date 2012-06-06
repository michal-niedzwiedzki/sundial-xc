# About Sundial XC

Sundial XC is a software to facilitate time bakning and to support local communities built around the idea of time bank.
If you want to know more about the concept of "time banks" - and you really should - just look up
[prof. Edgar Cahn on YouTube](www.youtube.com/results?search_query=Edgar+Cahn+RSA).

This software is not of production quality yet. You are advised to test it thoroughly before running your time bank on it.

Sundial XC is distributed under terms of [GNU Affero General Public License](http://www.gnu.org/licenses/agpl.html)
version 3.0 or later.

## Setup

To install Sundial XC you need a web server with PHP 5.3+ and MySQL 5.1+.

* Set up a MySQL database and user
* Set up your web server to serve directory _public/www/_
* Create file _env.txt_ with environment name in single line
* Copy _config/example.json_ to _config/environment_name.json_
* Amend config to suit your needs, specifically providing database connection information
* Create file _log/environment_name.log_ with 666 permissions 
* Open _create_db.php_ file in your browser
* Once installed delete _create_db.php_ from server

## Credits and history

Sundial XC is based on [Local-Exchange-UK](https://github.com/cdmweb/Local-Exchange-UK) v1.01 translated into Spanish by
[Graeme Herbert](http://www.linkedin.com/pub/graeme-herbert/9/503/794) for local time bank
[A2Manos](http://www.bancodetiempomalasana.com) in Madrid, Spain. This in turn is a fork of
[Local-Exchange](http://sourceforge.net/projects/local-exchange/).

Sundial XC was massively refactored and largely reimplemented from scratch by Michał Rudnicki.
The URL structure, config entries, and UI flows remain the same as in original work,
but it's not guaranteed and may change in future.

## Future versions

* **Installer.** Separate from the rest of the application. Several pages to guide user through installation process.
* **Migration script from Local-Excahnge-UK.**
* **Restful API version 0.0**. API for existing model classes shielded by authorization layer. Covered in 100% with tests.
* **Android client.** Nice to have to explore correctness and completeness of API v 0.0.
* **Database normalization.** You will see the need for this one as soon as you look into existing database.
* **Interoperability between instances of Sundial XC.** Very important to connect not only members of communities, but also communities with each other.
* **Internationalization with gettext.** Yeah, currently it's in Spanish.
