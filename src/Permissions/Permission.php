<?php

namespace Betalabs\Engine\Permissions;

class Permission
{

    /** @var string */
    protected $name;

    /** @var string */
    protected $displayName;

    /** @var string */
    protected $description;

    /**
     * Permission constructor.
     *
     * @param string $name
     * @param string $displayName
     * @param string $description
     */
    public function __construct($name, $displayName, $description)
    {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

}