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
