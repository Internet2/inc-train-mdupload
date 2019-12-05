# Shibboleth Training Metadata Upload Facility

This is the [InCommon Shibboleth Training](https://incommon.org/shibtraining/) metadata upload utility.

The general idea here is that a user will upload their SAML metadata using index.php.  The script will then cause the browser to ping the metadata-aggregate (built by `cpm.sh` every 2 minutes) on the server & check for updates.  As mentioned, cpm.sh builds the aggregate & places it in a spot where the IdP and SP can download it.  Both the IdP and SP are set to download the metadata every minute or so.

This project, while open-source, is designed specifically for the InCommon training environment.  It has NOT been sanitized to work in a generic environment nor even be deployable into another environment without modification of the scripts to fit the layout of the environment.  

# Setup

1. `index.php` needs to be placed somewhere where your webserver can serve it up.  You should edit this file and set an 
appropriate password as well as upload location for the metadata files.  The default is to store uploaded files in `/home/classuser`.
2. Your next step is to copy/edit `cpm.sh` to a suitable location and configure it to run when appropriate via cron.  It
will expect to be able to write to both a `/var/metadatafiles` and a `/var/metadatafile` directory.  It will also want to
write to a `/var/www/html/downloads` directory.
3. Copy the `metadata-start.xml` and `metadata-end.xml` files to `/var/metadatafile`.
4. Next, copy the contents of the `xmlschema` directory to `/software/xmlschema`.  
5. Finally, you need to ensure that the PHP-XML and xmllint libraries are installed as well as the xmlstarlet application.

You should now be able to upload and create a metadata aggregate.  Next steps would be to modify `cpm.sh` to use [xmlsectool](https://wiki.shibboleth.net/confluence/display/SHIB2/XmlSecTool)
to sign the aggregate before making it available for download.