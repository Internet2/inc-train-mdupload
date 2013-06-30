<?php

//   $allowed_filetypes = array('.xml','.txt');           These will be the types of file that will pass the validation.
   $max_filesize = 40960;                              // Maximum filesize in BYTES (currently 40KB).
   $upload_path = '/opt/testshib-user-metadata/';                  // The place the files will be uploaded to, don't forget trailing slash '/'

   $filename = $_FILES['userfile']['name'];             // Get the name of the file (including file extension).
   $ext = substr($filename, strrpos($filename,'.'));      // Get the extension from the filename.
 
// We don't want to bother checking file types
//   if(!in_array($ext,$allowed_filetypes))
//      die('<font color=\'red\'>The file type you attempted to upload is not allowed.  You must upload a .xml file.</font>');
 
   if(filesize($_FILES['userfile']['tmp_name']) > $max_filesize)
      die('<font color=\'red\'>The file you attempted to upload is too large.  Please ensure the file is less than 40k and try again.</font>');

   if(!isUniqueFileName($filename))
       die("{file:\"$filename\",type:\"xml\",width:\"\",height:\"\",error:\"you are an idiot\"}");
      //die('<font color=\'red\'>The filename you chose, "' . $filename . '", is a common default file name for metadata.  Please rename the file to something unique and then reattempt this upload.</font>');

   if(!isValidMetadataFile($_FILES['userfile']['tmp_name']))
      die('<font color=\'red\'>The file you are attempting to upload is not valid metadata.  Please correct any errors and try again.</font>');

   if(!is_writable($upload_path))
      die('<font color=\'red\'>Something horrible happened.  Please contact the Shibboleth Users list.</font>'); 

   if(move_uploaded_file($_FILES['userfile']['tmp_name'],$upload_path . $filename))
      {
         echo 'Your metadata was uploaded successfully.  Please proceed to <a href="configure.html">configuration</a> and <a href="test.html">testing</a>. <br /> <br />';                        // It worked
         echo 'Your metadata filename is <b>' . $filename . '</b>.  Please keep this filename so you can overwrite your metadata file in the event you need to update your entry. <br /><br />';
         echo 'Your complete metadata is below.  You don\'t need to understand the entire file, but it\'s helpful to recognize your entityID in the first element below, as well as your provider\'s certificate.  <a href="https://wiki.shibboleth.net/confluence/display/SHIB2/Home" target="_new">The Shibboleth wiki</a> can help you <a href="https://wiki.shibboleth.net/confluence/display/SHIB2/Metadata" target="_new">learn about metadata</a>.<br /> <br /> <br /> <p><tt>'; 

         // Build display array of metadata
         exec('xmlstarlet fo -o ' . $upload_path . $filename, $displayMetadata);
	 foreach ($displayMetadata as $value) {        
         echo (special_formatting($value) . '<br />');
         }
         echo '</tt></p> <br />';
         shell_exec('sleep 3; sh /opt/cpm.sh'); // Build metadata file after giving a moment to ensure upload completed
      } else {
          die('<font color=\'red\'>There was an error during the file upload.  Please try again.</font>');     // It failed
      }



function special_formatting($input) {
    $output = htmlspecialchars($input, ENT_QUOTES);
    $output = str_replace(array('  ', "\n"), array('&nbsp;&nbsp;', '<br>'), $output);
    return str_replace('&nbsp; ', '&nbsp;&nbsp;', $output);
}

function isUniqueFileName($filename) {
        if($filename == '') {
            return false;
        }

        if($filename == 'Metadata') {  // Prevent use of common default
            return false;
        }

        if($filename == 'idp-metadata.xml') { // Prevent use of common default
            return false;
        }

        if($filename == 'metadata.xml') { // Prevent use of common default
            return false;
        }
       return true;
   }


function isValidMetadataFile($filename) {

        $xmllintCall = "xmllint --noout --schema /opt/xmlschema/saml-schema-metadata-2.0.xsd {$filename}";

        exec($xmllintCall, $xmllintOutput, $xmllintFeedback);

        if($xmllintFeedback == '0') {

            return true;

        } else {

             //echo $xmllintOutput;
             return false; 
        }

    }
 
?>
