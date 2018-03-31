<?php

/**
 * DviMail responsável pelo envio de emails do sistema
 *
 * @version    Dvi 1.0
 * @package    model
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
namespace Dvi\Adianti\Helpers;

use Adianti\Base\App\Lib\Util\TMail;
use Adianti\Base\Lib\Widget\Dialog\TMessage;

/**
 * Dvi Mail
 *
 * Formata o Email disparado pelo sistema em comunicações entre usuários
 * @author Davi Menezes - davimenezes.dev@gmail.com
 */
class DviMail
{
    private $mail ;
    private $body;
    private $toEmails = array();
    private $subject;
    private $error_msg;

    public function __construct($body, $mails = null)
    {
        $this->mail = new TMail();
        
        $this->setBody($body);

        $this->toEmails[] = $mails;
        
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

    public function send()
    {
        try {
            $ini = parse_ini_file('app/config/email.ini');

            $this->mail->setFrom($ini['from'], $ini['name']);

            $this->mail->setSubject($this->getSubject());
            $this->mail->setHtmlBody($this->getBody());

            foreach ($this->toEmails as $value) {
                $this->mail->addAddress($value['email'], $value['nome']);
            }

            $this->mail->SetUseSmtp();
            $this->mail->SetSmtpHost($ini['host'], $ini['port']);
            $this->mail->SetSmtpUser($ini['user'], $ini['pass']);
            $this->mail->send();

        } catch (\Exception $e) {
            $this->error_msg = $e->getMessage();
        }
    }

    public function addAttach($file)
    {
        $this->mail->addAttach($file);
    }

    public function success()
    {
        if ($this->error_msg) {
            return false;
        }
        return true;
    }

    public function getErrorMsg()
    {
        return $this->error_msg;
    }
}
