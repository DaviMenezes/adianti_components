<?php

namespace Dvi\Adianti\Helpers;

use Adianti\Base\Lib\Core\AdiantiCoreApplication;
use Adianti\Base\Lib\Database\TRecord;
use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Dvi\Adianti\Route;

/**
 * Helpers CommonActions
 * @Metodos compartilhados entre views e controllers
 * @package    Helpers
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait CommonActions
{
    public static function onClear($param)
    {
        $className = Reflection::getClassName(get_called_class());
        TSession::setValue($className . '_form_data', null);
        TSession::setValue($className . '_filters', null);
        TSession::setValue($className . '_listOrder', null);
        TSession::setValue('method', $param['method']);

        $params = Utils::getNewParams();
        unset($params['id'], $params['key'], $params['method'], $params['static']);

        AdiantiCoreApplication::loadPage($className, null, $params);
    }

    /**
     * check if form has token and if is valid(session value)
     */
    protected function validateToken()
    {
        $called_class = Route::getClassName(get_called_class());
        $token = $this->params[$called_class . '_form_token'];
        if (!empty($token) and (
                $token === TSession::getValue($called_class.'_form_token'))) {
            return true;
        }
        return false;
    }

    protected function createArrayModels($result, $models_to_check): array
    {
        $array_models = array();
        foreach ($result as $attribute => $value) {
            if (!isset($value)) {
                continue;
            }
            if (!$this->isObjectAttribute($attribute, $models_to_check)) {
                continue;
            }

            $position = strpos($attribute, '-');

            if ($position === false) {
                continue;
            }

            $model = substr($attribute, 0, $position);

            if (in_array($model, array_keys($models_to_check))) {
                $attribute = substr($attribute, $position+1);
                $parent = $models_to_check[$model]['parent'];

                $array_models[$parent??$model][$attribute] = $value;
            }
        };

        return $array_models;
    }

    protected function isObjectAttribute($attribute, $models_to_save)
    {
        foreach ($models_to_save as $item) {
            /**@var TRecord $rf*/
            $model_class = $item['class'];
            $rf = new $model_class();
            $model_attributes = $rf->getAttributes();
            $model_attributes[] = 'id';

            $position_separator = strpos($attribute, '-');
            $invalid = $this->invalidAttribute($attribute, $position_separator, $model_attributes);
            if ($invalid) {
                continue;
            }
            if (in_array($attribute, array_values($model_attributes))) {
                return true;
            }

            $attribute_name = $attribute;
            if ($position_separator !== false) {
                $model = (new \ReflectionClass($item['class']))->getShortName();

                $array_attribute = explode('-', $attribute);

                if ($array_attribute[0] === $model) {
                    $attribute_name = $array_attribute[1];
                }
            }

            if (in_array($attribute_name, array_values($model_attributes))) {
                return true;
            }
        }
        return false;
    }

    protected function invalidAttribute($attribute, $position_separator, $model_attributes): bool
    {
        if ($position_separator === false and !in_array($attribute, array_values($model_attributes))) {
            return true;
        }
        return false;
    }

    public function showErrorMsg($param)
    {
        new TMessage('error', $param['msg']);
    }
}
