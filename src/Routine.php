<?php
namespace App;

use App\abstract\Simulation;

class Routine extends Simulation {
    
    public function __construct($idUser, $name)
    {
        parent::__construct();
        $this->person = [
            'id' => $idUser,
            'name' => $name,
        ];
    }

    public function setAnxietyLevel($level)
    {
        $this->anxietyLevel = $level;
        $this->updateGlobalAnxietyLevel();
    }
}