<?php
namespace App;

use App\Abstract\Simulation;

interface PluginInterface
{
    public function configure(Simulation $simulation): void;

    public function beforeActionsPlugin(Simulation $simulation): void;

    public function afterActionsPlugin(Simulation $simulation): void;
}