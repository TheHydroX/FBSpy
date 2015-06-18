> # FaceBookSpy #
> 
> **Description**
> This Tool tracks your facebook-friends online activity. 
> Everytime they go online or offline this tool save it to a mysql database for later evaluation.
> The frontend let you see the online statistic of choosen friends.
> 
> **Attention**
> The tool start tracking user when they go for the first time online since the myTracker.php is started.
> 
-----

# 1. Requirements #
- **PHP5.4**
- **PHP5-mysql**
- **mysql**
- **screen**

# 2. Installation #
**2.1. Clone Project to filesystem**

> **2.1.1. without GIT**
> Extract fbspy.0.1.zip to your www-path:

    unzip fbspy.0.1.zip /var/www/fbspy

> **2.1.1. with GIT**
> Clone from online repository

	git clone https://github.com/NinthArt89/FBSpy.git

**2.2. Create Database-Schema**
Execute fbspy_schema.sql.

Connect to your mysql database and create a new database

	mysql> CREATE DATABASE fbspy;
	mysql> USE fbspy;
	mysql> SOURCE /path/to/fbspy_schema.sql;

Create User with password

	mysql> GRANT ALL ON fbspy.* TO fbspy IDENTIFIED BY 'fbspy';
	
**2.3. Configurate api/config.php**

Change the values for your mysql host.
Also edit your Facebook account to local settings.

**Congratulation, you set up fbspy!**

# 3. Execution and start tracking #

**3.1. start Tracker**

	screen php api/myTracker.php

Deattach the Termin with CTRL+A and CTRL+D

> **Hint**
> When sucessfully set up, the tracker spams information about adding user to database

**3.2. visit your index.html**

Call the location of the index.html with any browser. When everything working correct, you can choose anyone of your friends and watch their online activity. 

> **Hint**
> When nothing displayed there is not any data to load.

# 4. Credits #

> 
> **Based on the idea from whatsspy Public**
> https://gitlab.maikel.pro/maikeldus/WhatsSpy-Public/wikis/home
> 
> **Tracker was programmed by Dirk 'NinthArt' Rößler**
> http://www.brainolution.de/
> 
> **Designed by Nick 'The_HydroX' Flache**
> https://1337-server.eu/
> 
------------
