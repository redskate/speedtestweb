# speedtestweb
Stores and visualizes internet Download and Upload Speeds as data series.

This scripts are based on https://github.com/sivel/speedtest-cli

You need

1. A mysql database
2. A web server



Installation step 1

Create a mysql schema "ISPEED" and a table using:

CREATE TABLE `log` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `distance` float DEFAULT NULL COMMENT 'km',
  `testfrom` text,
  `hostedby` text,
  `ping` float DEFAULT NULL COMMENT 'msec ping time',
  `downloadspeed` float DEFAULT NULL COMMENT 'in Mbit/s',
  `uploadspeed` float DEFAULT NULL COMMENT 'in Mbit/s',
  PRIMARY KEY (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1

Then add a user on ISPEED (SELECT, INSERT)



Installation step 2

Install or take a web server (I took an apache web server), define a virtual server to contain a
documentRoot=/path/to/speedtestweb/

  <VirtualHost *:20000>
    ServerName cerere:20000
    DocumentRoot "/Users/you/Documents/workspace/speedtest"
    ErrorLog /private/var/log/apache2/servername_20000_semweb.local.err
    TransferLog "/private/var/log/apache2/servername_20000_access.log"

    <Directory "/path/to/speedtest">
        Order allow,deny
        Allow from all
        AllowOverride All
        Options +Indexes +FollowSymLinks +ExecCGI +Includes
        Require all granted
    </Directory>
  </VirtualHost>



Installation step 3
crontab to call the datasampler.php at reboot:

Add line in `crontab -e`:
/usr/bin/php /path/to/speedtest/src/php/datasampler.php > /tmp/datasampler.log




USE of web interface:

Start the web server and call:  http://yourwebserver/src/php/dataserver.php
