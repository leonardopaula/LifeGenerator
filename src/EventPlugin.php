<?php
namespace App;

use App\Abstract\Simulation;
use DateInterval;
use DateTime;

class EventPlugin implements PluginInterface 
{
    private string $name;
    private string $begin;
    private string $end;
    private string $state;
    private string $place;

    public function __construct(string $name, string $begin, string $end, string $state, string $place = 'Home')
    {
        $this->name = $name;
        $this->begin = $begin;
        $this->end = $end;
        $this->state = $state;
        $this->place = $place;
    }

    private function betweenTime(DateTime $instant)
    {
        $begin = clone $instant;
        $begin->setTime(
            $this->parseTime($this->begin)['hour'], 
            $this->parseTime($this->begin)['minute'],
        );

        $end = clone $instant;
        $end->setTime(
            $this->parseTime($this->end)['hour'], 
            $this->parseTime($this->end)['minute'],
        );

        return ($instant >= $begin && $instant <= $end);
    }

    public function configure(Simulation $simulation): void
    {
        $state = $simulation->getState();

        if (!in_array($this->state, $state)) {
            
            if ($this->betweenTime($simulation->getInstant())) {
                array_push($state, $this->state);
                $simulation->setState($state);
                $simulation->setLocale($this->place);
                $simulation->print([
                    'event' => 'Start ' . $this->name,
                ]);
            }
        } else {

            if (!$this->betweenTime($simulation->getInstant())) {
                array_splice($state, array_search($this->state, $state), 1);
                $simulation->setState($state);
                $simulation->popLocale();
                $simulation->print([
                    'event' => 'End ' . $this->name,
                ]);
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

    public function getState()
    {
        return $this->state;
    }

    public function beforeActionsPlugin(Simulation $simulation): void
    {}

    public function afterActionsPlugin(Simulation $simulation): void
    {}
}