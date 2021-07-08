<?php
namespace App\abstract;

use App\PluginInterface;
use DateTime;

abstract class Simulation
{
    protected array $plugins = [];
    protected array $person = [];
    protected ?\DateTime $instant = NULL;
    protected array $state = [];
    protected array $stateInfo = [];
    protected int $anxietyLevel = 1;
    protected int $globalAnxietyLevel = 0;
    protected array $semanticPlace = [];

    public function __construct(\DateTime $instant = NULL, $anxietyLevel = 21) 
    {
        $this->instant = ($this->instant === NULL) 
                    ? new \DateTime()
                    : $instant;
        
        $this->anxietyLevel = $anxietyLevel;
        $this->updateGlobalAnxietyLevel();
    }

    public function updateGlobalAnxietyLevel()
    {
        $this->globalAnxietyLevel = ($this->anxietyLevel) * 100 / 21;
    }

    public function addPlugin(PluginInterface $plugin): self
    {
        $this->plugins[] = $plugin;

        return $this;
    }

    public function execute()
    {
        $this->configurePlugins();

        $this->beforeActionsPlugin();

        $this->afterActionsPlugin();
    }

    private function configurePlugins()
    {
        //var_dump($this->plugins);exit;
        foreach($this->plugins as $plugin):
            $plugin->configure($this);
        endforeach;
    }

    private function beforeActionsPlugin()
    {
        foreach($this->plugins as $plugin):
            $plugin->beforeActionsPlugin($this);
        endforeach;
    }

    private function afterActionsPlugin()
    {
        foreach($this->plugins as $plugin):
            $plugin->afterActionsPlugin($this);
        endforeach;
    }

    /**
     * Increment simulation time
     */
    public function tick(int $window = 5)
    {

        $this->instant->add(new \DateInterval('PT'.$window.'S'));
        // echo $this->instant->format('Y-m-d H:i:s') . "\n";
        $this->execute();
    }

    public function getInstant(): DateTime
    {
        return $this->instant;
    }

    public function getState(): array
    {
        return $this->state;
    }

    public function setState(array $state): void
    {
        $this->state = $state;
    }

    public function getStateInfo(string $state): mixed
    {
        return (isset($this->stateInfo[ $state ])) ? $this->stateInfo[ $state ] : NULL;
    }

    public function setStateInfo(string $state, mixed $data): void
    {
        $this->stateInfo[ $state ] = $data;
    }

    public function getGlobalAnxietyWeight(): float
    {
        $weight = 0.0;
        $perc = $this->getGlobalAnxiety();
        $random = rand(intval($perc / 2), intval($perc));

        if ($this->anxietyLevel >= 10) {
            $weight = $perc * ($random * 2) / 60;
        } else {
            $weight = $perc * ($random) / 60 + 20;
        }
        return $weight;
    }

    public function getGlobalAnxiety(): float
    {
        return ($this->anxietyLevel + 1) * 100 / 22;
    }

    public function setLocale(string $place)
    {
        array_push($this->semanticPlace, $place);
    }

    public function popLocale()
    {
        array_pop($this->semanticPlace);
    }

    public function print(array $data): void
    {
        // |DateTime|AnxietyLevel|Event|PhysiologicalStatus|SemanticLocale|
        echo $this->instant->format('Y-m-d H:i:s') . ";";
        echo $this->globalAnxietyLevel . ";";
        echo $data['event'] . ";";
        echo (empty($data['physiological'])) ? ";" : $data['physiological'] . ";";
        echo (count($this->semanticPlace) > 0) ? $this->semanticPlace[count($this->semanticPlace)-1] : "" . ";";
        echo "\n";
    }

}
