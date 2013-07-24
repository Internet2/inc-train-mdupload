<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<?php 

$errors = "";
$results = "";

function special_formatting($input) {
    $output = htmlspecialchars($input, ENT_QUOTES);
    $output = str_replace(array('  ', "\n"), array('&nbsp;&nbsp;', '<br>'), $output);
    return str_replace('&nbsp; ', '&nbsp;&nbsp;', $output);
}

//pull the entityid and specifically the hostname of it out of the metadata
function nameFile($filename) {
    $returnMe = "";
    
    $xml = new XMLReader();
    if (!$xml->open($_FILES['userfile']['tmp_name'])) {
        die("Error trying to open XML: " . $_FILES['userfile']['tmp_name']);
    }

    $foundED = false;
    do{
        $xml->read();
        if(stripos($xml->name, "EntityDescriptor") > 0){
            $foundED = true;
        }
    }while(!$foundED);
    
    $entityid = $xml->getAttribute("entityID");
    
    $returnMe = str_replace("/", "-", $entityid).".metadata.xml";
    
    $xml->close();
    
    return $returnMe;
}

function isValidMetadataFile($filename) {
        return true;
        $xmllintCall = "xmllint --noout --schema /opt/xmlschema/saml-schema-metadata-2.0.xsd {$filename}";

        exec($xmllintCall, $xmllintOutput, $xmllintFeedback);

        if($xmllintFeedback == '0') {
            return true;

        } else {
            //print_r($xmllintOutput);
            //print_r($xmllintFeedback);
             return false; 
        }

}


if(isset($_POST['submit'])){
   //process
    //   $allowed_filetypes = array('.xml','.txt');           These will be the types of file that will pass the validation.
   $max_filesize = 40960;                              // Maximum filesize in BYTES (currently 40KB).
   $upload_path = '/opt/testshib-user-metadata/';                  // The place the files will be uploaded to, don't forget trailing slash '/'

   $filename = nameFile($_FILES['userfile']['tmp_name']);             // create the name of the file based on the entityID
   $ext = substr($filename, strrpos($filename,'.'));      // Get the extension from the filename.
 
   if(filesize($_FILES['userfile']['tmp_name']) > $max_filesize)
      $errors .= '<font color=\'red\'>The file you attempted to upload is too large.  Please ensure the file is less than 40k and try again.</font><br/>';

   if(!isValidMetadataFile($_FILES['userfile']['tmp_name'])){
       $errors .= '<font color=\'red\'>The file you are attempting to upload is not valid metadata.  Please correct any errors and try again.</font><br/>';
   }

   if(!is_writable($upload_path))
      $errors .= '<font color=\'red\'>Something horrible happened.  Please contact the Shibboleth Users list.</font><br/>';
   
  if (strlen($errors) == 0) {

        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_path . $filename)) {
            $results .= 'Your metadata was uploaded successfully.  Please proceed to <a href="configure.html">configuration</a> and <a href="test.html">testing</a>. <br /> <br />';                        // It worked
            $results .= 'Your complete metadata is below.  You don\'t need to understand the entire file, but it\'s helpful to recognize your entityID in the first element below, as well as your provider\'s certificate.  <a href="https://wiki.shibboleth.net/confluence/display/SHIB2/Home" target="_new">The Shibboleth wiki</a> can help you <a href="https://wiki.shibboleth.net/confluence/display/SHIB2/Metadata" target="_new">learn about metadata</a>.<br /> <br /> <br /> <p><tt>';

            // Build display array of metadata
            exec('xmlstarlet fo -o ' . $upload_path . $filename, $displayMetadata);
            foreach ($displayMetadata as $value) {
                $results .= (special_formatting($value) . '<br />');
            }
            $results .= '</tt></p> <br />';
            shell_exec('sleep 3; sh /opt/cpm.sh'); // Build metadata file after giving a moment to ensure upload completed
        } else {
            die('<font color=\'red\'>There was an error during the file upload.  Please try again.</font>');     // It failed
        }
    }


} else{
?>

<?php

}
?>
<html lang="en-US">
<head>
        <title>TestShib Two</title>
        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/dojo/1.9.0/dijit/themes/tundra/tundra.css">
        <link rel="stylesheet" type="text/css"
                   href="styles.css"
                   title="styles">
</head>


<body style="margin: 0px; font-family: sans-serif">
    
<center>
    
<!-- header bar -->

<div id="header"><a href="http://www.shibboleth.net/"><img border="0" align="left" src="testshibtwo.jpg"></a></div>
<div id="main">

<!--- start content --->
<div id="errors" style="padding: 3px">
    <?php
    echo $errors;
    ?>
</div>

<div id="results" style="padding: 3px">
    <?php echo $results; ?>
</div>
<br />
<br />
<form method="post" action="index.php" id="myForm"
enctype="multipart/form-data">
    <fieldset>
        <legend>Metadata Upload</legend>
        <input name="userfile" type="file" id="uploader"/>
        <input type="submit" label="Submit" name="submit" value="submit">
    </fieldset>
</form>

</div>
<br>
<font style="font-size:80%; color:#AAAAAA">&copy; Copyright 2006-2013 Internet2.</font>
</center>

</body>
</html>
