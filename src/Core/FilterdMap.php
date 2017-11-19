<?php

namespace Blog\Core;

class FilterdMap 
{
    private $map;

    public function __construct(array $baseMap)
    {
        $this->map = $baseMap;
    } // when a new instance of this class is created map is set to store a provided array

    public function has(string $name): bool 
    {
        return isset($this->map[$name]);
    } //returns true if a given key exists in the stored array returns false otherwise

    public function get(string $name) 
    {
        return $this->map[$name] ?? null;
    } // returns the value of a given key if key does not exists it returns null

    public function getInt(string $name) 
    {
        return (int) $this->get($name);
    } // returns the value of a given key as a integer if key does not exists it returns null

    public function getNumber(string $name) 
    {
        return (float) $this->get($name);
    } // returns the value of a given key as a float if key does not exists it returns null

    public function getString(string $name, $filter = true) 
    {   
        $value = (string) $this->get($name);
        
        return $filter ? addslashes($value) : $value;
    } // returns the value of a given key as a string after adding slashes to characters that need to be escaped if key does not exists it returns null

}