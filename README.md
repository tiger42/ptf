Ptf
===
Ptf thin framework.

A minimal PHP framework without any required dependencies to other PHP libraries or components.

See [here](https://github.com/tiger42/ptf_demo) for an example application implemented with Ptf.

Requirements
------------
* PHP 7.1 or higher
* MySQL/MariaDB 5.5 or higher
* PDO_MYSQL or MySQLi extension for PHP

Optional dependencies
---------------------
* Smarty 3.1+ template engine [(http://smarty.net)](http://smarty.net)
* Memcached daemon for session storage [(http://memcached.org)](http://memcached.org).
The PHP Memcached (with "d" - __not__ Memcache!) extension must also be installed in order to use Memcached.

Getting started
---------------
For an easy start the shell script `bin/generate-app` is provided with the Ptf framework.
The script will generate a minimal Ptf based application for you to build on.
You can control the type of the application the script generates with the `-t` parameter.
Run the script without any parameters to get a short overview of its options and parameters.
Note: Depending on your web server configuration it may be necessary to set the correct
"RewriteBase" in the __.htaccess__ file of the generated application.
