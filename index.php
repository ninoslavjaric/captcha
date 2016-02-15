<?php
namespace Execute;
require_once('CaptchaSupervisor.php');
use Captcha\CaptchaSupervisor;
//var_dump($_POST);
//die();
$c = new CaptchaSupervisor();
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['original'], $_POST['question'])){
    echo $e = $c->paramMaches(['original'=>$_POST['original'], 'encrypted'=>$_POST['question']]);
}else{
    if (!isset($_GET['question'])) {
        echo $c->getJson();
    }else{
        $c->plotPicture($_GET['question']);
    }
}

