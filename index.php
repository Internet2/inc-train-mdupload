<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<?php 
/*
Copyright 2013 Internet2

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License
*/

date_default_timezone_set('America/Denver');
$current_password = 'CheckWithMe';

$errors = "";
$results = "";
$success = false;

$max_filesize = 40960;                              // Maximum filesize in BYTES (currently 40KB).
$upload_path = '/home/classuser/';                  // The place the files will be uploaded to, don't forget trailing slash '/'

if($_POST['submit'] == 'submit' && $_POST['password'] != $current_password){

	die('<font color=\'red\'>The password you supplied is not valid.</font>');
}

function special_formatting($input) {
    $output = htmlspecialchars($input, ENT_QUOTES);
    $output = str_replace(array('  ', "\n"), array('&nbsp;&nbsp;', '<br>'), $output);
    return str_replace('&nbsp; ', '&nbsp;&nbsp;', $output);
}

function doesMetadataExist($filename){
    global $upload_path;
    if(is_dir($upload_path)){
        
        if($dh = opendir($upload_path)){
            while(($file = readdir($dh)) !== false){
                if(strcmp($file, $filename) == 0){
                    closedir($dh);
                    return true;
                }
            }
            
            closedir($dh);
        }
    }
    return false;
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
	try{
        	if(!$xml->read()){
			break;
		}
	}catch (Exception $e){
		die("Error parsing metadata: $e");
		break;
	}

        if($xml->name === "EntityDescriptor" || $xml->name === "md:EntityDescriptor" ){
            $foundED = true;
        }
    }while(!$foundED);
    
    $entityid = $xml->getAttribute("entityID");
	
	if(strlen($entityid) < 3){
		die("unable to locate entityID");
	}
    
    $returnMe = str_replace("/", "-", $entityid).".metadata.xml";
    
    $xml->close();
    
    if(doesMetadataExist($returnMe)){
        echo "<center><font color='red'>Warning: metadata already exists on this server for entityid ".$entityid."</font></center>";
    }
    
    return $returnMe;
}

function isValidMetadataFile($filename) {

        $xmllintCall = "xmllint  --schema /software/xmlschema/saml-schema-metadata-2.0.xsd {$filename}";

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
   $filename = nameFile($_FILES['userfile']['tmp_name']);             // create the name of the file based on the entityID
   $ext = substr($filename, strrpos($filename,'.'));      // Get the extension from the filename.
 
   if(filesize($_FILES['userfile']['tmp_name']) > $max_filesize)
      $errors .= '<font color=\'red\'>The file you attempted to upload is too large.  Please ensure the file is less than 40k and try again.</font><br/>';

   if(!isValidMetadataFile($_FILES['userfile']['tmp_name'])){
       $errors .= '<font color=\'red\'>The file you are attempting to upload is not valid metadata.  Please correct any errors and try again.</font><br/>';
   }

   if(!is_writable($upload_path))
      $errors .= '<font color=\'red\'>Something horrible happened.  Please notify the instructor.</font><br/>';
   
  if (strlen($errors) == 0) {

        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_path . $filename)) {
            $results .= 'Your metadata was uploaded successfully. <br /><br /><div id="processing">Please stand by while we process it...</div> ';                        // It worked
	    $results .= '<div id="complete" style="display:none">Processing complete.  Please proceed with curriculum.</div><br /><br />';
	    $success = true;
            #shell_exec('sleep 3; sh /opt/cpm.sh'); // Build metadata file after giving a moment to ensure upload completed
        } else {
            die('<font color=\'red\'>There was an error during the file upload.  Please try again.</font>');     // It failed
        }
    }


} else{

}
?>
<html lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" type="text/css"
                   href="styles.css"
                   title="styles">
        <title>InCommon Training Metadata Upload</title>
<script type="text/javascript" src="//code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

</head>


<body style="margin: 0px; font-family: sans-serif">
    
<center>
    
<!-- header bar -->

<div id="header"><a href="/mdupload/"><img border="0" align="left" src="/images/incommon-training-logo.jpg" alt="incommon training logo"></a></div>
<div id="main">

<!--- start content --->
<div id="errors" style="padding: 3px">
    <?php
    echo $errors;
    ?>
</div>

<div id="results" style="padding: 3px">
    <?php echo $results; 
	if($success){
		?>
<div class="ui-widget-default">
    <div id="progressbar" style="height: 20px;"></div>
</div>	
	<script type="text/javascript">

//tracks size of metadata file on server
var etag = '';
var changedFile = false;
function checkFileSize(){
	var url = "/downloads/ShibTrain1-metadata.xml";
	var xhr = $.ajax({
		type: "HEAD",
		url: url,
		success: function(msg){
			if(etag === ''){
				etag = xhr.getResponseHeader('ETag');
			}
			if(etag === xhr.getResponseHeader('ETag')){
				changedFile = false;
			}
			else {
				changedFile = true;
			}
		}
	});

}

//metadata processing monitor
//metadata is aggregated on the server every 2 minutes
//spin for 2 minutes checking the metadata file on the server to see if it has changed
//if it doesn't change, we should probably notify somebody
$(document).ready(function() {
    $("#progressbar").progressbar();
    var tick_interval = 1;
    var tick_increment = 1.2;
    var tick_function = function() {
        var value = $("#progressbar").progressbar("option", "value");
        value += tick_increment;
        $("#progressbar").progressbar("option", "value", value);
        if (value < 100) {
            window.setTimeout(tick_function, tick_interval * 1500); 
	    checkFileSize();
	    if(changedFile){
		$("#progressbar").progressbar("option","value",100);
	    }
        } else {
	    //change the processing UI to show complete
	    if(changedFile){
	    	$("div#processing").toggle();
	    	$("div#complete").toggle();
	    }
	    else {
		//it's been 2 minutes & we haven't detected a change
		alert("We have failed to detect the application of your metadata.  Please notify an instructor.");
	    }
        }
    };
    window.setTimeout(tick_function, tick_interval * 1500);
});
</script>
<?php 
	}

?>
</div>
<br />
<br />
<form method="post" action="index.php" id="myForm"
enctype="multipart/form-data">
<p>
Check with an instructor for the current password.  
<p>
<label for="password">Password: </label>
<input type="password" name="password" />
</p>
    <fieldset>
        <legend>Metadata Upload</legend>
        <input name="userfile" type="file" id="uploader"/>
        <input type="submit" label="Submit" name="submit" value="submit">
    </fieldset>
</form>

</div>
<br>
<font style="font-size:80%; color:#AAAAAA">&copy; Copyright 2006-<?php echo date("Y"); ?> Internet2.</font>
</center>

</body>
</html>
