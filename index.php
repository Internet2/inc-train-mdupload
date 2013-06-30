<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en-US">
<head>
        <title>TestShib Two</title>
        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/dojo/1.9.0/dijit/themes/tundra/tundra.css">
        <link rel="stylesheet" type="text/css"
                   href="styles.css"
                   title="styles">
</head>


<body style="margin: 0px; font-family: sans-serif" class='tundra'>
    <script data-dojo-config="async: 1, parseOnLoad: true, dojoBlankHtmlUrl: '/arcit/blank.html'"
    src="//ajax.googleapis.com/ajax/libs/dojo/1.9.0/dojo/dojo.js"></script>
    <script src='xmllint.js'></script>
    <script language="javascript">

require(["dojox/form/Uploader","dojox/form/Manager", "dijit/form/TextBox"], function(){
    
});

</script>
<center>
    
<!-- header bar -->

<div id="header"><a href="http://www.shibboleth.net/"><img border="0" align="left" src="testshibtwo.jpg"></a></div>
<div id="main">



<!--- start content --->

<br />
<br />
<form method="post" action="procUpload.php" id="myForm"
enctype="multipart/form-data">
    <fieldset>
        <legend>Metadata Upload</legend>
        <input name="uploadedfile" multiple="true" type="file" id="uploader"
        data-dojo-type="dojox/form/Uploader" data-dojo-props="label: 'Select Metadata File'">
        <input type="submit" label="Submit" data-dojo-type="dijit/form/Button">
        <div id="files" data-dojo-type="dojox/form/uploader/FileList"
            data-dojo-props="uploaderId: 'uploader'"></div>
    </fieldset>
</form>

<br />
<br />
<br />
<br />

</div>
<br>
<font style="font-size:80%; color:#AAAAAA">&copy; Copyright 2006-2013 Internet2.</font>
</center>

</body>
</html>
