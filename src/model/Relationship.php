<?php

namespace Dvi\Adianti\Model;

use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Model\Relationship\BelongsTo;
use Dvi\Adianti\Model\Relationship\HasOne;

/**
 *  Relationship
 *
 * @package    Model
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
class Relationship
{
    protected $relationships = array();

    public function hasOne(string $model)
    {
        $strtolower = Reflection::lowerName($model);
        $this->relationships[$strtolower] = new RelationshipModelType($model, new HasOne());
        return $this;
    }

    public function belongsTo(string $model)
    {
        $strtolower = Reflection::lowerName($model);

        $belongsTo = new BelongsTo();
        $modelType = new RelationshipModelType($model, $belongsTo);
        $this->relationships[$strtolower] = $modelType;

        return $belongsTo;
    }

    public function getStringJoin($self_model, $associated)
    {
        $selfName = Reflection::shortName($self_model);
        $associatedAlias = Reflection::shortName($associated);

        if (is_a($this->getRelationship($associated)->type, HasOne::class)) {
            $key1 = $associatedAlias . '.' . strtolower($selfName) . '_id';
            $key2 = $selfName . '.id';
            return $this->createStringJoin($associated, $key1, $key2);
        }

        $key1 = $associatedAlias . '.id';
        $key2 = $selfName . '.' . strtolower($associatedAlias) . '_id';
        return $this->createStringJoin($associated, $key1, $key2);
    }

    public function getRelationship($model): RelationshipModelType
    {
        return $this->relationships[Reflection::lowerName($model)];
    }

    private function createStringJoin($associated, $key1, $key2): string
    {
        $associatedAlias = (new \ReflectionClass($associated))->getShortName();

        return 'inner join '.$associated::TABLENAME.' '.$associatedAlias.' on ' . $key1.' = '.$key2;
    }

    public function relationships()
    {
        return collect($this->relationships);
    }
}
