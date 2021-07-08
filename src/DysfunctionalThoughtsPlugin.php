<?php
namespace App;

use App\Abstract\Simulation;
use DateInterval;
use DateTime;
use stdClass;

class DysfunctionalThoughtsPlugin implements PluginInterface 
{
    private string $name;
    private string $state;
    private array $affected; // [event, weight]
    private array $physiological = [
        'sweat', 
        'palpitations', 
        'overheating', 
        'tremors', 
        'breathShort', 
        'unrest', 
        'irritation', 
        'unconcentrated',
    ];

    public function __construct(array $physiological = NULL)
    { 
        if ($physiological !== NULL) {
            $this->physiological = $physiological;
        }
    }

    public function configure(Simulation $simulation): void
    {
        $state = $simulation->getState();

        foreach($this->affected as $k => $v):
            if (in_array($v->getState(), $state)) {
                $rand = rand(0, 100);
                if ($rand > 90) {
                    $physio = new PhysiologicalSymptoms();
                    $anxiety = (int)$simulation->getGlobalAnxiety();

                    $simulation->print([
                        'event' => 'DysfunctionalThoughts',
                        'physiological' => implode(",", $physio->getPhisiologicalData($this->physiological, $anxiety)),
                    ]);
                }
            }
        endforeach;
    }

    public function affect(array $affected)
    {
        // var_dump($affected);exit;
        $this->affected = $affected;
    }

    public function beforeActionsPlugin(Simulation $simulation): void
    {}

    public function afterActionsPlugin(Simulation $simulation): void
    {}
}