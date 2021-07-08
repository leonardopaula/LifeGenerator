<?php
namespace App;
class Person {
    
    private $name;
    private $age;
    private $gad7Score;
    private $stressLevel;

    public function __construct($name, $age, $gad7Score)
    {
        $this->name = $name;
        $this->age = $age;
        $this->gad7Score = $gad7Score;
    }

    public function getStressLevel()
    {
        return $this->stressLevel;
    }
}