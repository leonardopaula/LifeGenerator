<?php
namespace App;

use App\Abstract\Simulation;
use DateInterval;
use DateTime;

class SleepPlugin implements PluginInterface {

    private string $name = "Sleeping";
    private string $init;
    private string $finish;
    private bool $forceSleep = FALSE;
    private int $variation = 300; // seconds
    private string $sleepingState = "sleep";
    private int $sleepDelay; // minutes

    public function __construct(string $init)
    {
        $this->init = $init;
        $this->setSleepDelay();
        $this->setWakeDelay();
    }

    public function configure(Simulation $simulation): void
    {
        // Check if is between time
        $instant = $simulation->getInstant();

        $init = clone $simulation->getInstant();
        $init->setTime(
            $this->parseTime($this->init)['hour'], 
            $this->parseTime($this->init)['minute'],
        )->sub(new DateInterval('PT'.$this->variation.'S'));

        $state = $simulation->getState();

        // Not sleeping
        if (!in_array($this->sleepingState, $state)) {

            // In time to sleep
            if ($instant >= $init || $this->forceSleep) {

                // Check random delay
                $init->add(new DateInterval('PT'.$this->sleepDelay.'M'));

                if ($instant >= $init || $this->forceSleep) {

                    if (!in_array('insomnia', $state)) {
                        array_push($state, $this->sleepingState);
                        $simulation->setState($state);
                        $simulation->setStateInfo($this->sleepingState, clone $instant);
                        $this->setForceSleep(FALSE);
                        $simulation->print([
                            'event' => 'Sleep',
                        ]);
                        $simulation->setLocale('House');
                    }
                } else {
                    //echo "delay" . $instant->format('Y-m-d H:i:s') ."\n";
                }
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

    
    public function setSleepDelay(int $min = 10, int $max = 20): void // Normal sleep delay
    {
        $this->sleepDelay = rand($min, $max);
    }

    public function sleepTime(Simulation $simulation): bool
    {
        // Check if is between time
        $instant = $simulation->getInstant();

        $init = clone $simulation->getInstant();
        $init->setTime(
            $this->parseTime($this->init)['hour'], 
            $this->parseTime($this->init)['minute'],
        )->sub(new DateInterval('PT'.$this->variation.'S'));

        return ($instant >= $init);
    }

    public function setWakeDelay(int $min = 0, int $max = 10): void
    {
        $this->wakeDelay = rand($min, $max);
    }

    public function setForceSleep(bool $forceSleep) 
    {
        $this->forceSleep = $forceSleep;
    }

    public function beforeActionsPlugin(Simulation $simulation): void
    {}

    public function afterActionsPlugin(Simulation $simulation): void
    {}
}