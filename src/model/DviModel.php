<?php

namespace Dvi\Adianti\Model;

use App\Adianti\Component\Model\Form\Fields\DBInteger;
use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Widget\Form\Field\Contract\FieldTypeInterface;
use Stringizer\Stringizer;

/**
 * Model DviModel
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class DviModel extends DviTRecord
{
    protected $model_fields;
    public $id;

    public function __construct($id = null, bool $callObjectLoad = true)
    {
        parent::__construct($id, $callObjectLoad);

        $this->addPublicAttributes();
        $this->setAttributeValues($this->getPublicProperties());
        $this->setPublicAttributeValues();
    }

    protected function setAttributeValues($properties)
    {
        foreach ($this->getAttributes() as $attribute) {
            $value = $properties[$attribute];
            if (!empty($value)) {
                $this->addAttributeValue($attribute, $value);
            }
        }
    }

    protected function setPublicAttributeValues()
    {
        if (!count($this->data)) {
            return;
        }
        foreach ($this->data as $property => $value) {
            $this->$property = $value;
        }
    }

    public function addAttributeValue($attribute, $value)
    {
        $this->__set($attribute, $value);
    }

    #region[FIELDS]
    protected function field(DBFormField $obj, FieldTypeInterface $type = null)
    {
        $name = $obj->getField()->getName();
        parent::addAttribute($name);

        $obj->getField()->setName($this->getTableFieldName($name));

        $this->model_fields[$name] = $obj;
        return $obj;
    }

    protected function getTableFieldName(string $name): string
    {
        $table_field_name = (new \ReflectionClass(get_called_class()))->getShortName() . '-' . $name;
        return $table_field_name;
    }

    public function getDviField($name):DBFormField
    {
        if (!array_key_exists($name, $this->model_fields)) {
            if (ENVIRONMENT == 'development') {
                $msg = 'O nome do campo ' . $name . ' não condiz com os atributos da classe ' . get_called_class();
            } else {
                $msg = 'Ocorreu um erro ao tentar montar o formulário. Entre em contato com o administrador';
            }

            foreach ($this->model_fields as $attribute => $value) {
                $msg .= "|".$attribute;
            }
            throw new \Exception($msg);
        }

        return $this->model_fields[$name];
    }

    public function getModelFields()
    {
        return $this->model_fields;
    }
    #endregion

    public function setMap($attribute_name, $class)
    {
        $this->foreign_keys[$attribute_name] = ['alias'=> $attribute_name, 'class'=>$class];
        $this->addAttribute((string)$attribute_name.'_id');
    }

    public function getAttributes()
    {
        if (count($this->attributes)) {
            return $this->attributes;
        }

        return parent::getAttributes();
    }

    protected function addAttributes(array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (is_string($attribute)) {
                $this->addAttribute($attribute);
            }
        }
    }

    public static function query(array $fields)
    {
        $query = new DB();
        $query->table(get_called_class())->fields($fields);
        return $query;
    }

    public function __call($name, $arguments)
    {
        $str = new Stringizer($name);

        if ($str->startsWith('set')) {
            $props = $this->getPublicProperties();

            $prop_name = $str->chopLeft('set')->lowercase()->getString();

            if (array_key_exists($prop_name, $props)) {
                $this->$prop_name = $arguments[0];
            }
        }
        return $this;
    }
}
