<?php
namespace App;

use App\Abstract\Simulation;
use DateInterval;
use DateTime;

class WakePlugin implements PluginInterface {

    private string $name = "WakeUp";
    private string $init;
    private string $removeState = "sleep";
    private int $wakeDelay; // seconds

    public function __construct(string $init)
    {
        $this->init = $init;
        $this->setWakeDelay();
    }

    public function configure(Simulation $simulation): void
    {
        $state = $simulation->getState();

        // Sleeping
        if (in_array($this->removeState, $state)) {

            // Check if is between time
            $instant = $simulation->getInstant();

            $init = clone $simulation->getStateInfo($this->removeState);
            $init->setTime(
                $this->parseTime($this->init)['hour'], 
                $this->parseTime($this->init)['minute'],
            )->add(new DateInterval('P1D'));

            // In time to sleep
            if ($instant >= $init) {

                // Check random delay
                $init->add(new DateInterval('PT'.$this->wakeDelay.'M'));

                if ($instant >= $init) {
                    array_splice($state, array_search($this->removeState, $state), 1);
                    $simulation->setState($state);
                    $simulation->setLocale('House');
                    $simulation->print([
                        'event' => 'WakeUp',
                    ]);
                } else {
                    // echo "Delay Acordar";
                }
            } else {
                // echo "Dormindo...";
            }
        }
    }

    private function parseTime(string $time) 
    {
        list($hour, $minute) = explode(":", $time);
        return [
            'hour' => $hour,
            'minute' => $minute,
        ];
    }

    
    public function setWakeDelay(int $min = 0, int $max = 5): void // Normal wake delay
    {
        $this->wakeDelay = rand($min, $max);
    }

    public function beforeActionsPlugin(Simulation $simulation): void
    {}

    public function afterActionsPlugin(Simulation $simulation): void
    {}
}