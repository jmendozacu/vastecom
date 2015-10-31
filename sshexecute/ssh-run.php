<?php


$output = shell_exec('sh PATCH_SUPEE-6788_CE_1.9.1.0_v1-2015-10-27-09-06-11.sh');
echo "<pre>$output</pre>";

exit;
 include 'Crypt/RSA.php';
 include 'Net/SSH2.php';
 

 try{
     $key = new Crypt_RSA();
     //$key->setPassword('whatever');
     $key->loadKey(file_get_contents('id_rsa.ppk'));
 
     $ssh = new Net_SSH2('ftp.narayansoft.com');


     if (!$ssh->login('username', $key)) {
         exit('Login Failed');
     }
}catch(Exception $e){
	 echo 'Caught exception: ',  $e->getMessage(), "\n";
}


exit;
    echo $ssh->read('username@username:~$');
    $ssh->write("ls -la\n");
    echo $ssh->read('username@username:~$');

 ?>