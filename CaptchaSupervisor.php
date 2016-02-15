<?php namespace Captcha;
/**
 * Created by PhpStorm.
 * User: Ninoslav
 * Date: 11/21/2015
 * Time: 2:47 PM
 */

class CaptchaSupervisor {

    function __construct()
    {
    }

    /** Checks if encrypted parameter matches with original parameter
     * @param array $params ['original','encrypted']
     * @return bool
     * @throws \Exception
     */
    public function paramMaches(Array $params){
//        header("Content-type: application/json");
        if(!isset($params['original'], $params['encrypted']))
            throw new \Exception("Bad parameters!!");
        return json_encode(['match'=>$params['original']==$this->decrypt($params['encrypted'])]);
    }
    /**
     * @param $value Raw string for encryption
     * @param string $key Encryption key
     * @return string Encrypted string
     */
    public function encrypt($value, $key = "12345678")
    {
        $key = str_pad($key, 8, "_", STR_PAD_LEFT);
        $key = substr($key, 0, 8);
        $value = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $value, MCRYPT_MODE_ECB);
        $value = base64_encode($value);
        return $value;
    }

    /**
     * @param $value Encrypted string
     * @param string $key Encryption key
     * @return string Decrypted string
     */
    public function decrypt($value, $key = "12345678")
    {
        $key = str_pad($key, 8, "_", STR_PAD_LEFT);
        $key = substr($key, 0, 8);
        $value = base64_decode($value);
        $value = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $value, MCRYPT_MODE_ECB);
        return rtrim($value);
    }

    /**
     * @return string json object with encrypted captcha question and captcha image url
     */
    public function getJson(){
        header("Content-type: application/json");
        $rand = (rand(1000,9999));
        $question = ($this->encrypt($rand));
        $server_self = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        return json_encode(['code'=>$question, 'img_url'=>"$server_self?question=$question"]);
    }

    /** Plots captcha question picture
     * @param $question encrypted captcha question
     */
    public function plotPicture($question){
        header("Content-type: image/jpeg");
        $fonts = glob('fonts/*.ttf');
        $font = $fonts[rand(0,count($fonts)-1)];
        $string = $this->decrypt($question);
        $font_size = 24;
//        $strlen = strlen($string);
//        $image_height = ImageFontHeight($font_size);
//        $image_width = ImageFontWidth($font_size)*$strlen;
        $image_height = 50;
        $image_width = 150;
        $image = imagecreate($image_width, $image_height);
        imagecolorallocate($image, 255, 255, 255);
        $font_color = imagecolorallocate($image, 0, 0, 0);
        for($i=0;$i<50;$i++)
            imageline($image,rand(0, $image_width), rand(0,$image_height), rand(0, $image_width), rand(0,$image_height), $font_color);
        imagettftext($image, $font_size, 0, 15, 30, $font_color, $font, $string);
        // imagestring($image, $font_size, 0, 0, $string, $font_color);
        imagejpeg($image);
    }
}