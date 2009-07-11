<?php
// This is the function that sends the results of the uploading process.
function SendResults( $errorNumber, $fileUrl = '', $fileName = '', $customMsg = '' )
{
	echo '<script type="text/javascript">' ;
	echo 'window.parent.OnUploadCompleted(' . $errorNumber . ',"' . str_replace( '"', '\\"', $fileUrl ) . '","' . str_replace( '"', '\\"', $fileName ) . '", "' . str_replace( '"', '\\"', $customMsg ) . '") ;' ;
	echo '</script>' ;
	exit ;
}

$file = file_upload('NewFile');
//SendResults( 0, $file , $file , '´íÎóÅ¶1') ;
?>