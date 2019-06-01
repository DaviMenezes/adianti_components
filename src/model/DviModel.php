<?php

namespace Dvi\Adianti\Model;

use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Model\Fields\DBFormField;
use Dvi\Adianti\Model\Relationship\BelongsTo;
use Dvi\Adianti\Widget\Form\Field\Contract\FieldTypeInterface;
use Stringizer\Stringizer;

/**
 * DviModel
 * @package    Model
 * @subpackage Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class DviModel extends ActiveRecord
{
    protected $model_fields;
    public $id;
    /**@var Relationship */
    protected $relationship;

    public function __construct($id = null, bool $callObjectLoad = true)
    {
        parent::__construct($id, $callObjectLoad);

        $this->addPublicAttributes();
        $this->setAttributeValues($this->getPublicProperties());
        $this->setPublicAttributeValues();

        $this->relationship = new Relationship();
    }

    public function associateds(): Relationship
    {
        return $this->relationship ?? $this->relationship = new Relationship();
    }

    public function addBelongsTo(string $class, $field = null)
    {
        if (!$field) {
            $class_short_name = strtolower((new \ReflectionClass($class))->getShortName());
            $foreing_key = $class_short_name . '_id';
        }
        parent::addAttribute($field ?? $foreing_key);
        return $this->associateds()->belongsTo($class);
    }

    public function addHasOne(string $class):Relationship
    {
        return $this->associateds()->hasOne($class);
    }

    public function relationships()
    {
        return $this->relationship->relationships();
    }

    public function getRelationship($model): ?RelationshipModelType
    {
        return $this->relationships()->get($model);
    }

    public function getJoin($model)
    {
        return $this->relationship->getStringJoin(get_called_class(), $model);
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
        if (!isset($this->data)) {
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

    public function getDviField($name): DBFormField
    {
        if (!array_key_exists($name, $this->model_fields)) {
            if (ENVIRONMENT == 'development') {
                $msg = 'O nome do campo ' . $name . ' não condiz com os atributos da classe ' . get_called_class();
            } else {
                $msg = 'Ocorreu um erro ao tentar montar o formulário. Entre em contato com o administrador';
            }

            foreach ($this->model_fields as $attribute => $value) {
                $msg .= "|" . $attribute;
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
    public function hasOne(string $model)
    {
        /**@var DviModel $model */
        return $model::where(Reflection::lowerName(get_called_class()) . '_id', '=', $this->id)->first() ?? new $model();
    }

    public function belongsTo(string $model): DviModel
    {
        $foreign_key = Reflection::lowerName($model) . '_id';
        return new $model($this->$foreign_key);
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
        $str = str($name);

        if ($str->startsWith('set')) {
            $props = $this->getPublicProperties();

            $prop_name = $str->removeLeft('set')->toLowerCase()->str();

            if (array_key_exists($prop_name, $props)) {
                $this->$prop_name = $arguments[0];
            }
        }
        return $this;
    }

    public static function remove($id = null):bool
    {
        return parent::remove($id);
    }

    /**
     * Deleta dados de associados de acordo com o relacionamento
     * Critérios: o tipo deve ser BelongsTo e ter a propriedade on_delete == CASCATE
     * @param null $id
     * @return bool|false|\PDOStatement
     * @throws \Exception
     */
    public function delete($id = null)
    {
        //Todo testar este metodo, verificar documentacao
        $this->relationships()->each(function (RelationshipModelType $relationship_type, $foreignkey_class_name) use ($id) {
            if (is_a($relationship_type->type, BelongsTo::class)) {
                /**@var BelongsTo $type*/
                $type = $relationship_type->type;

                if ($type->getOnDelete() == Foreignkey::CASCATE) {
                    if (!$this->id) {
                        $self = new $this($id);
                        $fk_id = $self->{$foreignkey_class_name.'_id'};
                    } else {
                        $fk_id = $this->{$foreignkey_class_name.'_id'};
                    }

                    /**@var DviModel $class*/
                    $class = $relationship_type->getClassName();
                    $class = new $class();
                    $class->delete($fk_id);
                }
            }
        });
        parent::delete($id);
    }
}
