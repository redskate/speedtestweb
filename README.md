# speedtestweb
Stores and visualizes internet Download and Upload Speed data graphically, without PUB.
These scripts are based on https://github.com/sivel/speedtest-cli and http://dygraphs.com/download.html
The project is ready to be an eclipse project (used on a mac computer) but you can just unzip it and use it as described below.

<h4>You need</h4>

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
Adapt parameters inside utilities.php if needed<br>
  $DB_HOST='127.0.0.1:3306';<br>
  $DB_UNAME='speeduser';  <br>
  $DB_PWORD='the rightmysqlISPEEDpassword';

<h4>Installation step 4</h4>
crontab to call the datasampler.php at reboot:

Add line in `crontab -e`:
/usr/bin/php /path/to/speedtest/src/php/datasampler.php > /tmp/datasampler.log




<h4>Use inside your browser (see your DB historized measurements graphically):</h4>

Start the web server and call:  http://yourwebserver/src/php/dataserver.php

<h5>Visualize last 100 datapoints:</h5>
http://yourwebserver/src/php/dataserver.php?last=100
<h5>Visualize last 200 datapoints and reload every minute:</h5>
http://yourwebserver/src/php/dataserver.php?last=200&reload=60

<h4>Example of what you should get:</h4>
![Alt Example of dataserver.php graphical output](https://github.com/redskate/speedtestweb/blob/master/images/picexample.png "Example of dataserver.php graphical output")

<h2>ENJOY!</h2>
