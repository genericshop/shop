<?php 

class App_Session 
{
    protected static $_instance;
    
    public $ns;
     
    private function __construct()
    {
        $this->ns = new Zend_Session_Namespace();
 
        if (!isset($this->ns->initialized)) {
            
            Zend_Session::regenerateId();
            $this->ns->initialized = true;
            
        }
    }
    
    public static function getInstance()
    {
        if (null === self::$_instance)
            self::$_instance = new self();
    
        return self::$_instance;
    }
    
    public function get($key, $default = null)
    {
        return isset($this->ns->$key) ? $this->ns->$key : $default;
    }
    
    public function set($key, $value)
    {
        $this->ns->$key = $value;
    }
    
}