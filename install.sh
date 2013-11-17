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
