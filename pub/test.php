<?php 
phpinfo();

die;
ini_set( 'display_errors', 1 );
error_reporting( E_ALL );
$from = "info@boffinbutler.com";
$to = "punithavelkt@gmail.com";
$subject = "PHP Mail Test script";
$message = "This is a test to check the PHP Mail functionality";
$headers = "From:" . $from;
echo $message;
die;
if(mail($to,$subject,$message, $headers)){
	echo ' Mail Sending to '.$to;
	echo '<br>';

}
$to = "surendar2343@gmail.com";
if(mail($to,$subject,$message, $headers)){
	echo ' Mail Sending to '.$to;
	echo '<br>';
}
else{
 echo 'Mail not Send'; 
}

?>