<?php
/**
 * Created by PhpStorm.
 * User: PetrV
 * Date: 25.4.2019
 * Time: 0:54
 */

namespace App\Model\System;

use Tracy\Debugger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

class Io
{
	private $mailConfig = null;

	public function __construct($mailConfig)
	{
		$this->mailConfig = (object)$mailConfig;
	}

	function send_mail($emailEntity){


		/* if(!$email_from) $email_from = $this->EMAIL_FROM;
		if(!$email_from_name) $email_from = $this->EMAIL_FROM_NAME;
		if(!$reply_to) $email_from = $this->EMAIL_FROM;*/


		if(empty($emailEntity->getEmailFrom()))
			$email_from = $this->mailConfig->EMAIL_FROM;

		if(empty($emailEntity->getEmailFromName()))
			$email_from_name = $this->mailConfig->EMAIL_FROM_NAME;


		if(empty($emailEntity->getEmailReplyTo()))
			$reply_to = $email_from;
		if(empty($emailEntity->getReturnPath()))
			 $return_path = $email_from;




		$MAIL = new PHPMailer;
		if($this->mailConfig->MAILER_TYPE=='smtp'){
			$MAIL->isSMTP();
			$MAIL->Host = $this->mailConfig->EMAIL_HOST;
			$MAIL->Port = $this->mailConfig->EMAIL_PORT;

			$MAIL->SMTPAuth = true;
			$MAIL->Username = $this->mailConfig->EMAIL_USER_NAME;
			$MAIL->Password =  $this->mailConfig->EMAIL_PASSWORD;

			//Set the encryption system to use - ssl (deprecated) or tls
			if($this->mailConfig->MAIL_SMTP_SECURE) $MAIL->SMTPSecure = $this->mailConfig->MAIL_SMTP_SECURE;
		}elseif(MAILER_TYPE=='sendmail'){
			$MAIL->isSendmail();
		}elseif(MAILER_TYPE == 'postfix')  {
			$MAIL->isSendmail();
			$MAIL->Host = $this->mailConfig->EMAIL_HOST;
			$MAIL->Port = $this->mailConfig->EMAIL_PORT;
		}elseif(MAILER_TYPE=='qmail'){
			$MAIL->isQmail();
		}else{
			$MAIL->isMail();
		}


		if($this->mailConfig->MAIL_USE_DKIM == true){
			$MAIL->DKIM_domain = $this->mailConfig->MAIL_USE_DKIM_DOMAIN;
			//See the DKIM_gen_keys.phps script for making a key pair -
			//here we assume you've already done that.
			//Path to your private key:
			$MAIL->DKIM_private = DIR_ROOT.$this->mailConfig->DKIM_PATHH;
			//Set this to your own selector
			$MAIL->DKIM_selector = 'phpmailer';
			//Put your private key's passphrase in here if it has one
			$MAIL->DKIM_passphrase = '';
			//The identity you're signing as - usually your From address
			$MAIL->DKIM_identity = $MAIL->From;
		}

		/* Relay access denied */

		$MAIL->CharSet = $this->mailConfig->MAILER_CHARSET; //'iso-8859-2';
		$MAIL->setFrom($email_from, $email_from_name);
		$MAIL->addReplyTo(trim($reply_to), $email_from_name);
		$MAIL->Subject = $emailEntity->getSubject();
		$MAIL->ContentType = "text/html";

		$MAIL->Sender=$return_path;
		$MAIL->ReturnPath=$return_path;

		// $MAIL->ConfirmReadingTo = EMAIL_FROM;  //info o precteni
		// $MAIL->ConfirmReadingTo = EMAIL_FROM;  //info o precteni
		// $MAIL->Sender = $email_from; //return path


		if (isset($_FILES)) {
			foreach ($_FILES as $var_name => $file_data) {
				$MAIL->addAttachment ($_FILES[$var_name]['tmp_name'], $_FILES[$var_name]['name']);
			}
		}


		if(is_array($emailEntity->getAttachment()) and !empty($emailEntity->getAttachment())){
			foreach($emailEntity->getAttachment() as $a=>$v){
				$MAIL->AddAttachment($v['path'].$v['name'],$v['name']);
			}
		}


		//	$MAIL->Body = iconv(MAILER_CHARSET, $this->mail_charset."//TRANSLIT",$content);
		$MAIL->Body = $emailEntity->getContent();


		if(!$emailEntity->getContentText()){
			$text = html_entity_decode($emailEntity->getContent());
			$search =array("#<p>#","#</p>#");
			$replace = array("",'BREAK');
			$text =  preg_replace($search,$replace,$text);
			$text = $this->purifyText($text);
			$text =  preg_replace('#BREAK#',CRLF,$text);
			$MAIL->AltBody = $text;
		}else{
			$text = $emailEntity->getContentText();
			$MAIL->AltBody = strip_tags($text);
		}


		if($this->mailConfig->DEBUG_EMAIL or $emailEntity->isDebug()){
			$address  = explode(",",$this->mailConfig->TEST_MAIL);
			foreach($address as $key=>$value){
				$MAIL->AddAddress(trim(preg_replace("#'#","",$value)));
			}

		}else{
			foreach($emailEntity->getAddress() as $key=>$value){
				$MAIL->AddAddress(trim(preg_replace("#'#","",$value)));
			}
		}

		if(ENV_DEV){
			Debugger::timer('send mail');
			Debugger::enable(Debugger::DETECT, DIR_LOG);
			Debugger::log('SEND MAIL ' . Debugger::timer('send mail') . 's\n'.print_r($MAIL,1)."\n", 'send-mail-' . date('Y-m-d'));
			$logFile = DIR_LOG."/MailLogLocal-".date("Y-m-d").".log";
			file_put_contents($logFile, date('Y-m-d H:i:s')."\n".print_r($MAIL,1)."\n######################\n", FILE_APPEND);
		}


		if($this->mailConfig->FAKE_SEND === true or $emailEntity->isFake() ) return true;

		try{
				$MAIL->Send();
				unset($MAIL);
				return true;
		}catch(MailerException $e){

		}


	}

	function replaceWhitespace($str) {
		$result = $str;
		foreach (array(
						 "  ", " \t",  " \r",  " \n",
						 "\t\t", "\t ", "\t\r", "\t\n",
						 "\r\r", "\r ", "\r\t", "\r\n",
						 "\n\n", "\n ", "\n\t", "\n\r",
				 ) as $replacement) {
			$result = str_replace($replacement, $replacement[0], $result);
		}
		return $str !== $result ? $this->replaceWhitespace($result) : $result;
	}


	function purifyText($text, $dialect = false)
	{

		$text = preg_replace('/(<|>)\1{2}/is', '', $text);
		$text = preg_replace(
				array(// Remove invisible content
						'@<head[^>]*?>.*?</head>@siu',
						'@<title[^>]*?>.*?</title>@siu',
						'@<meta[^>]*?>.*?/>@siu',
						'@<style[^>]*?>.*?</style>@siu',
						'@<script[^>]*?.*?</script>@siu',
						'@<noscript[^>]*?.*?</noscript>@siu',
						'@<[\/\!]*?[^<>]*?>@si',
						'@\'@'
				),
				"", //replace above with nothing
				$text );
		$text = strip_tags($text);
		$text = $this->replaceWhitespace($text);

		/*
        $search = array ('@<script[^>]*?>.*?</script>@si', // Strip out javascript
                         '@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
                         '@[\r\n|\r|\n]+@',
                         '@\'@');
		*/

		/*|\s\s*/
		// evaluate as php
		// $replace = array ('', '', ' ', '"');
		// $text = preg_replace($search, $replace, $text);
		if ($dialect) {
			$text = $this->removeDialect($text);
		}

		return $text;
	}


	function removeDialect($string)
	{
		//utf->iso
		$string = preg_replace('#[^\x00-\x7F\xa0\xa4\xa7\xa8\xad\xb0\xb4\xb8\xc1\xc2\xc4\xc7\xc9\xcb\xcd\xce\xd3\xd4\xd6\xd7\xda\xdc\xdd\xdf\xe1\xe2\xe4\xe7\xe9\xeb\xed\xee\xf3\xf4\xf6\xf7\xfa\xfc\xfd\x{102}-\x{107}\x{10c}-\x{111}\x{118}-\x{11b}\x{139}\x{13a}\x{13d}\x{13e}\x{141}-\x{144}\x{147}\x{148}\x{150}\x{151}\x{154}\x{155}\x{158}-\x{15b}\x{15e}-\x{165}\x{16e}-\x{171}\x{179}-\x{17e}\x{2c7}\x{2d8}\x{2d9}\x{2db}\x{2dd}]#u', '', $string);
		static $tbl = array("\xc2\xa0"=>"\xa0","\xc4\x84"=>"\xa1","\xcb\x98"=>"\xa2","\xc5\x81"=>"\xa3","\xc2\xa4"=>"\xa4","\xc4\xbd"=>"\xa5","\xc5\x9a"=>"\xa6","\xc2\xa7"=>"\xa7","\xc2\xa8"=>"\xa8","\xc5\xa0"=>"\xa9","\xc5\x9e"=>"\xaa","\xc5\xa4"=>"\xab","\xc5\xb9"=>"\xac","\xc2\xad"=>"\xad","\xc5\xbd"=>"\xae","\xc5\xbb"=>"\xaf","\xc2\xb0"=>"\xb0","\xc4\x85"=>"\xb1","\xcb\x9b"=>"\xb2","\xc5\x82"=>"\xb3","\xc2\xb4"=>"\xb4","\xc4\xbe"=>"\xb5","\xc5\x9b"=>"\xb6","\xcb\x87"=>"\xb7","\xc2\xb8"=>"\xb8","\xc5\xa1"=>"\xb9","\xc5\x9f"=>"\xba","\xc5\xa5"=>"\xbb","\xc5\xba"=>"\xbc","\xcb\x9d"=>"\xbd","\xc5\xbe"=>"\xbe","\xc5\xbc"=>"\xbf","\xc5\x94"=>"\xc0","\xc3\x81"=>"\xc1","\xc3\x82"=>"\xc2","\xc4\x82"=>"\xc3","\xc3\x84"=>"\xc4","\xc4\xb9"=>"\xc5","\xc4\x86"=>"\xc6","\xc3\x87"=>"\xc7","\xc4\x8c"=>"\xc8","\xc3\x89"=>"\xc9","\xc4\x98"=>"\xca","\xc3\x8b"=>"\xcb","\xc4\x9a"=>"\xcc","\xc3\x8d"=>"\xcd","\xc3\x8e"=>"\xce","\xc4\x8e"=>"\xcf","\xc4\x90"=>"\xd0","\xc5\x83"=>"\xd1","\xc5\x87"=>"\xd2","\xc3\x93"=>"\xd3","\xc3\x94"=>"\xd4","\xc5\x90"=>"\xd5","\xc3\x96"=>"\xd6","\xc3\x97"=>"\xd7","\xc5\x98"=>"\xd8","\xc5\xae"=>"\xd9","\xc3\x9a"=>"\xda","\xc5\xb0"=>"\xdb","\xc3\x9c"=>"\xdc","\xc3\x9d"=>"\xdd","\xc5\xa2"=>"\xde","\xc3\x9f"=>"\xdf","\xc5\x95"=>"\xe0","\xc3\xa1"=>"\xe1","\xc3\xa2"=>"\xe2","\xc4\x83"=>"\xe3","\xc3\xa4"=>"\xe4","\xc4\xba"=>"\xe5","\xc4\x87"=>"\xe6","\xc3\xa7"=>"\xe7","\xc4\x8d"=>"\xe8","\xc3\xa9"=>"\xe9","\xc4\x99"=>"\xea","\xc3\xab"=>"\xeb","\xc4\x9b"=>"\xec","\xc3\xad"=>"\xed","\xc3\xae"=>"\xee","\xc4\x8f"=>"\xef","\xc4\x91"=>"\xf0","\xc5\x84"=>"\xf1","\xc5\x88"=>"\xf2","\xc3\xb3"=>"\xf3","\xc3\xb4"=>"\xf4","\xc5\x91"=>"\xf5","\xc3\xb6"=>"\xf6","\xc3\xb7"=>"\xf7","\xc5\x99"=>"\xf8","\xc5\xaf"=>"\xf9","\xc3\xba"=>"\xfa","\xc5\xb1"=>"\xfb","\xc3\xbc"=>"\xfc","\xc3\xbd"=>"\xfd","\xc5\xa3"=>"\xfe","\xcb\x99"=>"\xff");
		$string = strtr($string, $tbl);


		// win 1250 -> iso
		$string = strTr($string, "\x8A\x8D\x8E\x9A\x9D\x9E", "\xA9\xAB\xAE\xB9\xBB\xBE");
		// iso -> ascii
		$string = strtr(
				$string,
				"\xB5\xC1\xC8\xCF\xC9\xCC\xCD\xD2\xD3\xD8\xA9\xAB\xDA\xD9\xDD\xAE\xE1\xE8\xEF\xE9\xEC\xED\xF2\xF3\xF8\xB9\xBB\xFA\xF9\xFD\xBE",
				"\x6C\x41\x43\x44\x45\x45\x49\x4E\x4F\x52\x53\x54\x55\x55\x59\x5A\x61\x63\x64\x65\x65\x69\x6E\x6F\x72\x73\x74\x75\x75\x79\x7A"
		);
		// some others
		$string = strtr($string, "\xe1\xc1\xe0\xc0\xe2\xc2\xe4\xc4\xe3\xc3\xe5\xc5".
				"\xaa\xe7\xc7\xe9\xc9\xe8\xc8\xea\xca\xeb\xcb\xed\xcd\xec\xcc\xee\xce\xef\xcf\xf1\xd1\xf3\xd3\xf2".
				"\xd2\xf4\xd4\xf6\xd6\xf5\xd5\x8\xd8\xba\xf0\xfa\xda\xf9\xd9\xfb\xdb\xfc\xdc\xfd\xdd\xff\xe6\xc6\xdf",
				"aAaAaAaAaAaAacCeEeEeEeEiIiIiIiInNoOoOoOoOoOoOoouUuUuUuUyYyaAs");

		return $string;
	}
}