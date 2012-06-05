# Sundial XC

Sundial XC is a software to facilitate time bakning and to support local communities built around the idea of a time bank.
If you want to know more about the concept of "time banks" - and you really should - just look up prof. Edgar Cahn on YouTube.

# Setup

To install Sundial XC you need a web server with PHP 5.3+ and MySQL 5.1+.

* Set up your web server to serve directory public/www/
* Create file env.txt with environment name in single line
* Copy config/example.json to config/<environment_name>.json and amend to suit your needs
* Create log/ directory with 777 permissions
* Set up a MySQL database and user
* Open create_db.php file in your browser
* Once installed delete create_db.php from server

# Credits and history

Sundial XC is based on [Local-Exchange-UK](https://github.com/cdmweb/Local-Exchange-UK) v1.01 translated into Spanish by
[Graeme Herbert](http://www.linkedin.com/pub/graeme-herbert/9/503/794) for local time bank
[A2Manos](http://www.bancodetiempomalasana.com) in Madrid, Spain. This is in turn based on
[Local-Exchange](http://sourceforge.net/projects/local-exchange/).

Sundial XC was massively refactored and largely reimplemented from scratch by Michał Rudnicki.
The URL structure, config entries, and UI flows remain the same as in original work,
but it's not guaranteed and may change in future.

# Future versions

* Installer
* Migration script from Local-Excahnge-UK
* Restful API version 0.0 with 100% test coverage
* Android client
* Database normalization
* Restful API version 1.0
* Interoperability between instances of Sundial XC
* Printed money and cards
* Internationalization with gettext
