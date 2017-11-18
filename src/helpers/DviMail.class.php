<?php

/**
 * DviMail responsável pelo envio de emails do sistema
 *
 * @version    Dvi 1.0
 * @package    model
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
namespace Dvi\Adianti\Helpers;

use TMail;

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

    public function __construct($body, $mails = null)
    {
        $this->mail = new TMail();
        
        $this->setBody($body);
        $this->setSubject('Você tem uma nova mensagem');
        
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
        $ini = parse_ini_file('app/config/email.ini');
        
        $mail = new TMail;
        $mail->setFrom($ini['from'], $ini['name']);
        
        $mail->setSubject($this->getSubject());
        $mail->setHtmlBody($this->getBody());
        
        foreach ($this->toEmails as $value) {
            $mail->addAddress($value['email'], $value['nome']);
        }
        
        $mail->SetUseSmtp();
        $mail->SetSmtpHost($ini['host'], $ini['port']);
        $mail->SetSmtpUser($ini['user'], $ini['pass']);
        $mail->send();
    }
}
