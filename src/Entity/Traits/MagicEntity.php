<?php

namespace App\Entity\Traits;

use Symfony\Component\Security\Core\User\UserInterface;

trait MagicEntity
{
    /**
     * @var array This field names cannot retrieve or setted by magic __call method
     */
    private $guardedFields = [];

    /**
     * Magic get and set methods
     * 
     * @param string $method
     * @param mixed $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $property = lcfirst(substr($method, 3));

        if (in_array($property, $this->guardedFields)) {
            throw new \Exception('Attempted to call guarded field an undefined method named "getBixy" of class "'. __CLASS__ .'".');
        } else {
            if (strncasecmp($method, 'get', 3) === 0) {
                return $this->$property;
            }
    
            if (strncasecmp($method, 'set', 3) === 0) {
                $this->$property = $arguments[0];
                return $this;
            }
        }
    }

    /**
     * Initialize data with setters methods of a class object
     *
     * @param array $data
     * @return object|UserInterface
     */
    public static function init(array $data)
    {
        $object = new static();

        foreach ($data as $key => $value) {
            call_user_func([$object, 'set' . $key], $value);
        }

        return $object;
    }
}