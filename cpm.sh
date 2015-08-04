#!/bin/bash
#
# Script to combine submitted metadata files
#
# Written by Thomas Howell <t-howell@northwestern.edu>
#
# Modified by PC on 3/15/11
#
# Latest version can be requested via e-mail
#
# Note: This script requires the existence of var metadata directories for now.
#

rm -rf /var/metadatafile/ShibTrain1-metadata.xml
rm -rf /var/metadatafiles/*
perl -pi -e 's/<\?xml version=\"1.0\" encoding=\"UTF-8\"\?>//g' /home/classuser/*.xml
cp /home/classuser/* /var/metadatafiles/
#cd /var/metadatafiles/
cat /var/metadatafile/metadata-start.xml >> /var/metadatafile/ShibTrain1-metadata.xml
cat /var/metadatafiles/* >> /var/metadatafile/ShibTrain1-metadata.xml
cat /var/metadatafile/metadata-end.xml >> /var/metadatafile/ShibTrain1-metadata.xml
#cp /var/metadatafile/ShibTrain1-metadata.xml /opt/shibboleth-idp/metadata/
#cp /var/metadatafile/ShibTrain1-metadata.xml /opt/shibboleth-ds/metadata/
#cp /var/metadatafile/ShibTrain1-metadata.xml /etc/shibboleth/
mv -f /var/www/html/downloads/ShibTrain1-metadata.xml /var/www/html/downloads/ShibTrain1-metadata.xml.prev
mv -f /var/metadatafile/ShibTrain1-metadata.xml /var/www/html/downloads/ShibTrain1-metadata.xml

