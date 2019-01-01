<?php

namespace Dvi\Adianti\Model\Relationship;

/**
 * Relationship BelongsTo
 *
 * @package    Relationship
 * @subpackage Model
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 * @see https://t.me/davimenezes
 */
class BelongsTo
{
    private $on_delete;

    public function onDelete(string $foreignkey)
    {
        $this->on_delete = $foreignkey;
    }

    public function getOndelete()
    {
        return $this->on_delete;
    }
}
