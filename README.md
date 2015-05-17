# Shibboleth Training Metadata Upload Facility

This is the [InCommon Shibboleth Training](https://incommon.org/shibtraining/) metadata upload utility.

The general idea here is that a user will upload their SAML metadata using index.php.  The script will then cause the browser to ping the metadata-aggregate (built by `cpm.sh` every 2 minutes) on the server & check for updates.  As mentioned, cpm.sh builds the aggregate & places it in a spot where the IdP and SP can download it.  Both the IdP and SP are set to download the metadata every minute or so.

This project, while open-source, is designed specifically for the InCommon training environment.  It has NOT been sanitized to work in a generic environment nor even be deployable into another environment without modification of the scripts to fit the layout of the environment.  