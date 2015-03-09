<!DOCTYPE html>
<html>
  <head>
    <title>Image Uploader!</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <style type="text/css">
      img {border-width: 0}
      * {font-family:'Lucida Grande', sans-serif;}
    </style>
    <link href="<? echo DIR;?>app/views/custom/css/uploadfilemulti.css" rel="stylesheet">

<script src="<? echo DIR;?>app/views/custom/js/jquery-1.8.0.min.js"></script>
<script src="<? echo DIR;?>app/views/custom/js/jquery.fileuploadmulti.min.js"></script>

  </head>
  <body>
      <h2>Image Uploader</h2>
<div id="mulitplefileuploader">Upload</div>
<div id="res" style="position: absolute;right: 0px;top: 5px;"></div>
<div id="status"></div>
<script>
function copyToClipboard(text)
{
    if (window.clipboardData) // Internet Explorer
    {  
        window.clipboardData.setData("Text", text);
    }
    else
    {  
        unsafeWindow.netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");  
        const clipboardHelper = Components.classes["@mozilla.org/widget/clipboardhelper;1"].getService(Components.interfaces.nsIClipboardHelper);  
        clipboardHelper.copyString(text);
    }
}
$(document).ready(function()
{

var settings = {
	url: "<? echo DIR;?>app/views/custom/upload.php",
	method: "POST",
	allowedTypes:"jpg,png,gif,doc,pdf,zip",
	fileName: "myfile",
	multiple: true,
	onSuccess:function(files,data,xhr)
	{
		if ( data == "-1" )alert("Error uploading this image... Dunno why.. :P");
		//alert(data);
		$("#status").html("<font color='green'>Upload is success</font>");
		var arr = JSON.parse(data);
		var code = $("#res").html();
		for( var i = 0; i < arr.length ; i++ ){
			var enc = encodeURI("http://images.ebayreports.com/"+arr[i].url);
			code  = "<div><img src='http://images.ebayreports.com/"+arr[i].url+"' style='width:200px;height:auto;' /><input type='text' style='width:400px' onmouseup='return false;' onfocus='this.select();' value='http://images.ebayreports.com/"+arr[i].url+"' /></div>" + code;
		}
		$("#res").html (code) ;
	},
    afterUploadAll:function()
    {
        //alert("all images uploaded!!");
    },
	onError: function(files,status,errMsg)
	{		
		$("#status").html("<font color='red'>Upload is Failed</font>");
	}
}
$("#mulitplefileuploader").uploadFile(settings);

});
</script>
</body>
</html>