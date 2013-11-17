#######################
#                     #
#    Set up script    #
#                     #
#######################


#######################
# Install php/phpunit #
#######################
apt-get update
apt-get install phpunit

#############################
# Install the php libraries #
#############################
pear update-channels
pear isntall PHP_CodeCoverage
pear install phpunit

############################
# Configure and install    #
# the apache configuration #
############################
DIRNAME=$(readlink -f .)/htdocs
sed -e "s|DOCUMENTROOT|$DIRNAME|" apache-web.conf > /etc/apache2/sites-available/default
service apache2 reload
#!/bin/bash

##########################
# Install and initialize #
# MySQL database         #
##########################
apt-get install mysql-server php5-mysql
mysqladmin -p create cs445
mysql -u root -p -e "grant all on cs445.* to cs445@localhost"
mysql -u cs445 -e "source sql/create.sql" cs445
