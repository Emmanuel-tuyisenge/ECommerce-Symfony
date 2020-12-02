<?php

namespace App\Taxes;

class Detector
{
    public $amount;

    protected $threshold;

    public function __construct(int $threshold)
    {
        $this->threshold = $threshold;
    }

    public function detect(int $amount): bool
    {
        if ($amount > $this->threshold) {
            return true;
        } else {
            return false;
        }
    }
}
