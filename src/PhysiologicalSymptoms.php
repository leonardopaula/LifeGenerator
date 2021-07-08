<?php
namespace App;

class PhysiologicalSymptoms 
{
    protected array $symptoms = [];

    public function __construct()
    {
        $levels = [
            'none', 'low', 'high', 'highest',
        ];

        $symptoms = [
            'sweat', 'palpitations', 'overheating', 'tremors', 'breathShort', 'unrest', 'irritation', 'unconcentrated'
        ];

        array_map(function($v) use ($levels) {
            $this->symptoms[ $v ] = $levels;
        }, $symptoms);
    }

    public function getPhisiologicalData(array $symptoms, int $anxietyLevel)
    {
        return 
        array_filter(
            array_map(function($v) use ($anxietyLevel) {
                $min = (int)(count($this->symptoms[ $v ]) * $anxietyLevel / 100) -1;
                $min = ($min <= 0) ? 0 : $min;
                $max = ($min >= 3) ? 3 : $min+1;

                $random = rand($max-1, $max);
                
                $level = $this->symptoms[ $v ][ $random ];

                if ($level != "none") {
                    return $v .":". $level;
                }
            }, $symptoms)
        , function($v) {
            return ($v !== NULL);
        });
    }
}
