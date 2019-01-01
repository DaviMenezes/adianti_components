<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

use Adianti\Base\Lib\Core\AdiantiCoreTranslator;

/**
 * Validator CpfValidator
 *
 * @package    Validator
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class CpfValidator extends FieldValidator
{
    protected $label;
    protected $value;
    protected $cpf;
    protected $errors= array();
    /**
     * @var bool
     */
    private $debug;

    public function __construct($debug = false, string $error_msg = null)
    {
        parent::__construct($error_msg);
        $this->debug = $debug;
    }

    public function validate($label, $value, $parameters = null)
    {
        if (empty($value)) {
            return true;
        }

        $this->label = $label;
        $this->value = $value;

        $this->validateLength($value);

        $this->validateNonNumericCharacters();

        $this->validateInvalidBasicValues();

        $this->validatePenultimateDigitVerifier();

        $this->validateLastDigitVerifier();

        if (count($this->errors)) {
            foreach ($this->errors as $key => $error) {
                $this->error_msg .= $error;
                if ($key + 1 < count($this->errors)) {
                    $this->error_msg .= "<br>";
                }
            }
        }
        if (isset($this->error_msg)) {
            return false;
        }
        return true;
    }

    private function validateLength($value)
    {
        //Retira todos os caracteres que nao sejam 0-9
        $this->cpf = preg_replace("/[^0-9]/", "", $value);

        if (strlen($this->cpf) <> 11) {
            $this->addInvalidCpfMessage('Comprimento inválido');
        }
    }

    private function addInvalidCpfMessage($msg = null)
    {
        $msg = $msg;
        if ($this->debug or !$msg) {
            $msg = AdiantiCoreTranslator::translate('The field ^1 has not a valid CPF', $this->label);
        }
        if (!in_array($msg, $this->errors)) {
            $this->errors[] = $msg;
        }
    }

    private function validateNonNumericCharacters()
    {
        // Retorna falso se houver letras no cpf
        if (!(preg_match("/[0-9]/", $this->cpf))) {
            $this->addInvalidCpfMessage('Apenas números são permitidos');
        }
    }

    private function validateInvalidBasicValues()
    {
        // Retorna falso se o cpf for nulo
        // cpfs inválidos
        $nulos = array("12345678909", "11111111111", "22222222222", "33333333333",
            "44444444444", "55555555555", "66666666666", "77777777777",
            "88888888888", "99999999999", "00000000000");
        if (in_array($this->cpf, $nulos)) {
            $this->addInvalidCpfMessage();
        }
    }

    /** Calcula o penúltimo dígito verificador */
    private function validatePenultimateDigitVerifier()
    {
        $acum = 0;
        for ($i = 0; $i < 9; $i++) {
            $acum += $this->cpf[$i] * (10 - $i);
        }

        $x = $acum % 11;
        $acum = ($x > 1) ? (11 - $x) : 0;
        // Retorna falso se o digito calculado eh diferente do passado na string
        if ($acum != $this->cpf[9]) {
            $this->addInvalidCpfMessage('Penúltimo dígito inválido');
        }
    }

    /** Calcula o último dígito verificador */
    private function validateLastDigitVerifier()
    {
        $acum = 0;
        for ($i = 0; $i < 10; $i++) {
            $acum += $this->cpf[$i] * (11 - $i);
        }

        $x = $acum % 11;
        $acum = ($x > 1) ? (11 - $x) : 0;
        // Retorna falso se o digito calculado eh diferente do passado na string
        if (strlen($this->cpf) == 11) {
            if ($acum != $this->cpf[10]) {
                $this->addInvalidCpfMessage('Último dígito inválido');
            }
        }
    }
}
