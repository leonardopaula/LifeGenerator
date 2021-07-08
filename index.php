<?php
namespace App;

use DateTime;

require_once("vendor/autoload.php");
define('TIME_WINDOW', 300);

$physiological = new PhysiologicalSymptoms();
// $dysThoughts = new DysfunctionalThoughtsPlugin();

$person = json_decode(file_get_contents("perfil1.json"));

$gad7 = array_reduce($person->gad7, function($sum, $v) {
    $sum += $v;
    return $sum;
});
$routine = new Routine(
    $person->user->id, 
    $person->user->name
);
$routine->setAnxietyLevel($gad7);

$sleep = new SleepPlugin($person->sleep->time);
$wakeup = new WakePlugin($person->wake);

$routine->addPlugin($sleep)
        ->addPlugin($wakeup);

if (!empty($person->sleep->physiological)) {
    $insomnia = new InsomniaPlugin($person->sleep->physiological);
    $insomnia->affect($sleep, $wakeup);
    $routine->addPlugin($insomnia);
}


        
$i = 0;

$event = $dys = [];
for ($i = 0; $i < count($person->events); $i++) {

    $event[$i] = new EventPlugin(
        $person->events[$i]->name, 
        $person->events[$i]->time[0],
        $person->events[$i]->time[1],
        strtolower($person->events[$i]->name)."_".$i,
        $person->events[$i]->place,
    );
    $routine->addPlugin($event[$i]);

    if (!empty($person->events[$i]->physiological)) {
        $dys[$i] = new DysfunctionalThoughtsPlugin($person->events[$i]->physiological);
        $dys[$i]->affect([$event[$i]]);
        $routine->addPlugin($dys[$i]);
    }
}

/*
$workMorning = new EventPlugin('Work', '08:30', '12:00', 'work_morning');
$workAfternoon = new EventPlugin('Work', '13:00', '18:00', 'work_afternoon');
$dysThoughts->affect([
    $workMorning, 
    $workAfternoon,
]);


$routine->addPlugin($sleep)
        ->addPlugin($wakeup)
        ->addPlugin($insomnia)
        ->addPlugin($workMorning)
        ->addPlugin($workAfternoon)
        ->addPlugin($dysThoughts);
// $routine->addPerson($person);
*/


while (true) {
    $routine->tick(TIME_WINDOW);

    //sleep(1);
}