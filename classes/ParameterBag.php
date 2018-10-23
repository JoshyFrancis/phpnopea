<?php
class ParameterBag implements \IteratorAggregate, \Countable{
    protected $parameters;
    //public function __construct(array $parameters = array())    {
    public function __construct( $data = [] ){
        $this->parameters = $data;
    }
    public function all()    {
        return $this->parameters;
    }
    public function keys()    {
        return array_keys($this->parameters);
    }
    public function replace(array $parameters = array())    {
        $this->parameters = $parameters;
    }
    public function add(array $parameters = array())    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }
    public function get($key, $default = null)    {
        //return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
        return isset($this->parameters[$key]) ? $this->parameters[$key] : $default;
    }
    public function set($key, $value)    {
        $this->parameters[$key] = $value;
    }
    public function put($key, $value)    {
        $this->parameters[$key] = $value;
        return $this;
    }
    public function has($key)    {
        //return array_key_exists($key, $this->parameters);
        return isset($this->parameters[$key]);
    }
    public function remove($key)    {
        unset($this->parameters[$key]);
        return $this;
    }
    public function any(){
        return count($this->parameters) > 0;
    }
    public function __call($method, $parameters){
        //return $this->$method(...$parameters);
        return $this->get(...$parameters);
    }
    public function getAlpha($key, $default = '')    {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
    }
    public function getAlnum($key, $default = '')    {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
    }
    public function getDigits($key, $default = '')    {
        // we need to remove - and + because they're allowed in the filter
        return str_replace(array('-', '+'), '', $this->filter($key, $default, FILTER_SANITIZE_NUMBER_INT));
    }
    public function getInt($key, $default = 0)    {
        return (int) $this->get($key, $default);
    }
    public function getBoolean($key, $default = false)    {
        return $this->filter($key, $default, FILTER_VALIDATE_BOOLEAN);
    }
    public function filter($key, $default = null, $filter = FILTER_DEFAULT, $options = array())    {
        $value = $this->get($key, $default);
        // Always turn $options into an array - this allows filter_var option shortcuts.
        if (!\is_array($options) && $options) {
            $options = array('flags' => $options);
        }
        // Add a convenience check for arrays.
        if (\is_array($value) && !isset($options['flags'])) {
            $options['flags'] = FILTER_REQUIRE_ARRAY;
        }
        return filter_var($value, $filter, $options);
    }
    public function getIterator()    {
        return new \ArrayIterator($this->parameters);
    }
    public function count()    {
        return \count($this->parameters);
    }
    public function __get($key){
        return $this->get($key);
    }
    public function __set($key, $value){
        $this->set($key, $value);
    }
	public function __unset($key){
        $this->remove($key);
    }
}
