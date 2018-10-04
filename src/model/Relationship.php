<?php

namespace Dvi\Adianti\Model;

use Dvi\Adianti\Helpers\Reflection;

/**
 * Model Relationship
 *
 * @package    Model
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 */
class Relationship
{
    const HASONE = 1;
    const BELONGSTO = 2;
    private $relationships = array();

    public function hasOne(string $model)
    {
        $strtolower = Reflection::lowerName($model);
        $this->relationships[$strtolower] = new RelationshipModelType($model, self::HASONE);
        return $this;
    }

    public function belongsTo(string $model)
    {
        $strtolower = Reflection::lowerName($model);

        $this->relationships[$strtolower] = new RelationshipModelType($model, self::BELONGSTO);

        return $this;
    }

    public function getJoin($self_model, $associated)
    {
        $selfName = Reflection::shortName($self_model);
        $associatedAlias = Reflection::shortName($associated);

        if ($this->getRelationship($associated)->type == self::HASONE) {
            return $this->createJoin($associated, $associatedAlias.'.'.strtolower($selfName).'_id', $selfName.'.id');
        }

        return $this->createJoin($associated, $associatedAlias.'.id', $selfName.'.'.strtolower($associatedAlias).'_id');
    }

    public function getRelationship($model): RelationshipModelType
    {
        return $this->relationships[Reflection::lowerName($model)];
    }

    private function createJoin($associated, $key1, $key2): string
    {
        $associatedAlias = (new \ReflectionClass($associated))->getShortName();

        return 'inner join '.$associated::TABLENAME.' '.$associatedAlias.' on ' . $key1.' = '.$key2;
    }

    public function getRelationships()
    {
        return $this->relationships;
    }
}
