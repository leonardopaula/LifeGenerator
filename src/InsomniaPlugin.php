<?php
namespace App;

use App\Abstract\Simulation;
use DateInterval;
use DateTime;

class InsomniaPlugin implements PluginInterface {

    private string $name = "Insomnia";
    private string $state = "insomnia";
    private array $physiological = [
        'palpitations',
        'irritation',
    ];
    private SleepPlugin $sp;
    private WakePlugin $wp;

    public function __construct($physiological = [])
    { 
        $this->physiological = $physiological;
    }

    public function configure(Simulation $simulation): void
    {
        $state = $simulation->getState();

        if ($this->sp->sleepTime($simulation)) {

            if (!in_array('sleeping', $state) && !in_array($this->state, $state)) {
                if ($simulation->getStateInfo($this->state) !== null) {
                    $diff = $simulation->getInstant()->diff($simulation->getStateInfo($this->state));
                    $diff = $diff->format('%a');
                } else {
                    $diff = 1;
                }

                if ($diff > 0) {
                    $anxiety = (int)$simulation->getGlobalAnxietyWeight();

                    $delay = clone $simulation->getInstant();
                    $delay->add(new DateInterval('PT'. $anxiety .'M'));
                    array_push($state, $this->state);
                    $simulation->setState($state);
                    $simulation->setStateInfo(
                        $this->state,
                        $delay,
                    );
                    $simulation->setLocale('House');
                }
            }
        }

        if (in_array($this->state, $state)) {
            $delay = clone $simulation->getStateInfo($this->state);
            if ($simulation->getInstant() >= $delay) {
                array_splice($state, array_search($this->state, $state), 1);
                $simulation->setState($state);
                $this->sp->setForceSleep(TRUE);
            } else {
                $anxiety = (int)$simulation->getGlobalAnxiety();
                $physio = new PhysiologicalSymptoms();
                $simulation->print([
                    'event' => 'Insomnia',
                    'physiological' => implode(",", $physio->getPhisiologicalData($this->physiological, $anxiety)),
                ]);
            }
        }
    }

    public function affect(SleepPlugin $sp, WakePlugin $wp)
    {
        $this->sp = $sp;
        $this->wp = $wp;
    }

    public function beforeActionsPlugin(Simulation $simulation): void
    {}

    public function afterActionsPlugin(Simulation $simulation): void
    {}
}