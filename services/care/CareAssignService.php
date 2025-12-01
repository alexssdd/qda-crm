<?php

namespace app\services\care;

use Exception;
use DomainException;
use app\entities\User;
use app\entities\Care;

/**
 * Care assign service
 */
class CareAssignService
{
    private $_care;
    private $_user;

    /**
     * CareBotService constructor.
     * @param Care $care
     * @param User $user
     */
    public function __construct(Care $care, User $user)
    {
        $this->_care = $care;
        $this->_user = $user;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function assign(): void
    {
        $care = $this->_care;
        $user = $this->_user;
        $care->executor_id = $user->id;

        if ($care->created_by){
            $care->handler_id = $care->created_by;
        } else {
            $care->handler_id = $user->id;
        }

        if (!$care->save(false)) {
            throw new DomainException("Care id: $care->id, executor set error");
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function setHandler(): void
    {
        $care = $this->_care;

        if ($care->created_by){
            $care->handler_id = $care->created_by;
        } else {
            $care->handler_id = $this->_user->id;
        }

        if (!$care->save(false)) {
            throw new DomainException("Care id: $care->id, handled set error");
        }
    }
}