<?php

namespace Dvi\Adianti\Model;

use Adianti\Base\Lib\Registry\TSession;
use Adianti\Base\Modules\Admin\User\Model\SystemUser;

/**
 *  ObjectDvi
 * @package    model
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class ObjectDvi extends ActiveRecord
{
    const TABLENAME = 'object_dvi';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    private $created_by;
    private $updated_by;
    private $arquived_by;
    private $trashed_by;

    public function __construct($id = null, $callObjectLoad = true)
    {
        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('short_description');
        parent::addAttribute('created_at');
        parent::addAttribute('created_by_id');
        parent::addAttribute('updated_at');
        parent::addAttribute('updated_by_id');
        parent::addAttribute('arquived_at');
        parent::addAttribute('arquived_by_id');
        parent::addAttribute('trashed_at');
        parent::addAttribute('trashed_by_id');

        $this->created_by_id = TSession::getValue('userid');
    }

    
    public function set_created_by(SystemUser $object)
    {
        $this->created_by = $object;
        $this->created_by_id = $object->id;
    }
    
    public function get_created_by()
    {
        // loads the associated object
        if (empty($this->created_by)) {
            $this->created_by = new SystemUser($this->created_by_id);
        }
    
        // returns the associated object
        return $this->created_by;
    }
    
    public function set_updated_by(\Adianti\Base\Modules\Admin\User\Model\SystemUser $object)
    {
        $this->updated_by = $object;
        $this->updated_by_id = $object->id;
    }

    public function get_updated_by()
    {
        // loads the associated object
        if (empty($this->updated_by)) {
            $this->updated_by = new \Adianti\Base\Modules\Admin\User\Model\SystemUser($this->updated_by_id);
        }

        // returns the associated object
        return $this->updated_by;
    }

    public function set_arquived_by(SystemUser $object)
    {
        $this->arquived_by = $object;
        $this->arquived_by_id = $object->id;
    }
    
    public function get_arquived_by()
    {
        // loads the associated object
        if (empty($this->arquived_by)) {
            $this->arquived_by = new SystemUser($this->arquived_by_id);
        }
    
        // returns the associated object
        return $this->arquived_by;
    }
    
    public function set_trashed_by(SystemUser $object)
    {
        $this->trashed_by = $object;
        $this->trashed_by_id = $object->id;
    }
    
    public function get_trashed_by()
    {
        // loads the associated object
        if (empty($this->trashed_by)) {
            $this->trashed_by = new \Adianti\Base\Modules\Admin\User\Model\SystemUser($this->trashed_by_id);
        }
    
        // returns the associated object
        return $this->trashed_by;
    }

    public function store()
    {
        if (!isset($this->id)) {
            $this->created_at = date('Y-m-d H:i:s');
        } else {
            $this->updated_at = date('Y-m-d H:i:s');
            $this->updated_by_id = TSession::getValue('userid');
        }
        return parent::store();
    }
}
