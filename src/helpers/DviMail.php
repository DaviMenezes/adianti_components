<?php

/**
 * DviMail responsável pelo envio de emails do sistema

 * @package    model
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
namespace Dvi\Adianti\Helpers;

use Adianti\Base\App\Lib\Util\TMail;
use PHPMailer\PHPMailer\Exception;

/**
 * Dvi Mail
 *
 * Formata o Email disparado pelo sistema em comunicações entre usuários
 * @author Davi Menezes - davimenezes.dev@gmail.com
 */
class DviMail
{
    protected $mail ;
    protected $body;
    protected $toEmails = array();
    protected $subject;
    protected $obj_error;

    public function __construct($body, $mails = null, $debug = null)
    {
        $this->mail = new TMail();
        
        $this->setBody($body);

        $this->toEmails = $mails;

        if ($debug) {
            $this->mail->setDebug($debug);
        }
        
        //habilite para testar
        //$this->toEmails[] = ['email'=>'inclua um email de teste para acompanhamento', 'nome'=>'Acompanhamento'];
    }
    
    private function setBody($body)
    {
        $this->body = $body;
    }
    
    private function getBody()
    {
        if (empty($this->body)) {
            $this->body = 'Você recebeu uma nova mensagem <br>Faça login para a mensagem completa <br><a href="#" class="btn btn-primary">Login</a>';
        }
    
        return $this->body;
    }
    
    public function setSubject(string $subject)
    {
        $this->subject = strip_tags(trim($subject));
        
        return $this;
    }

    private function getSubject()
    {
        return $this->subject;
    }

    public function send($email_config = null)
    {
        try {
            if (!$email_config) {
                $email_config = parse_ini_file('app/config/email.ini');
            }

            $this->mail->setFrom($email_config['mail_from'], $email_config['name']);
            foreach ($this->toEmails as $value) {
                $this->mail->addAddress($value['email'], $value['nome']);
            }

            $this->mail->setSubject($this->getSubject());

            if ($email_config['smtp_auth']) {
                $this->mail->SetUseSmtp();
                $this->mail->SetSmtpHost($email_config['smtp_host'], $email_config['smtp_port']);
                $this->mail->SetSmtpUser($email_config['smtp_user'], $email_config['smtp_pass']);
            }
            $this->mail->setHtmlBody($this->getBody());

            if (!empty($email_config['mail_support'])) {
                $this->mail->setReplyTo($email_config['mail_support'], $email_config['name']);
            }
            $this->mail->send();
        } catch (\Exception $e) {
            $this->obj_error = $e;
        }
    }

    public function addAttach($file)
    {
        $this->mail->addAttach($file);
    }

    public function success()
    {
        if ($this->obj_error) {
            return false;
        }
        return true;
    }

    public function getError():Exception
    {
        return $this->obj_error;
    }
}
