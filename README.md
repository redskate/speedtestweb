# speedtestweb
Stores and visualizes internet Download and Upload Speeds as data series.

This scripts are based on https://github.com/sivel/speedtest-cli

You need

1. A mysql database
2. A web server


<h4>Installation step 1</h4>

Create a mysql schema "ISPEED" and a table using:

  CREATE TABLE `log` (<br>
    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,<br>
    `distance` float DEFAULT NULL COMMENT 'km',<br>
    `testfrom` text,<br>
    `hostedby` text,<br>
    `ping` float DEFAULT NULL COMMENT 'msec ping time',<br>
    `downloadspeed` float DEFAULT NULL COMMENT 'in Mbit/s',<br>
    `uploadspeed` float DEFAULT NULL COMMENT 'in Mbit/s',<br>
    PRIMARY KEY (`timestamp`)<br>
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1<br>

Then add a user on ISPEED (SELECT, INSERT)



<h4>Installation step 2</h4>

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



<h4>Installation step 3</h4>
crontab to call the datasampler.php at reboot:

Add line in `crontab -e`:
/usr/bin/php /path/to/speedtest/src/php/datasampler.php > /tmp/datasampler.log




USE of web interface:

Start the web server and call:  http://yourwebserver/src/php/dataserver.php
