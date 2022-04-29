<?php 

namespace Synder\Analytics\Classes;

use Synder\Analytics\Models\Settings;


class DateTime extends \DateTime 
{
    const NOW = 'Y-m-d H:i:s';

    /**
     * Generator - Iterator on defined duration
     *
     * @param string $duration The DateInterval duration to iterate through.
     * @param integer $amount The amount of iterations to process.
     * @param string $format The desired output format.
     * @return iterable  
     */
    public function each($duration, $amount, $format = self::NOW)
    {
        if ($duration[0] === '-') {
            $mode = 'sub';
            $duration = substr($duration, 1);
        } else {
            $mode = 'add';
        }

        for ($i = 0; $i < $amount; $i++) {
            yield $i => $this->format($format);
            $this->$mode(new \DateInterval($duration));
        }
    }

    /**
     * Get Current Week
     *
     * @param string $format The desired output format.
     * @return array An 'start', 'end' array containing the whole week according to weekstart.
     */
    public function getCurrentWeek($format = self::NOW)
    {
        $weekStart = intval(Settings::get('weekstart'));
        $currentDay = intval($this->format('w'));

        $result = new DateTime();
        $diff = [0, -6, -5, -4, -3, -2, -1][abs($currentDay - $weekStart)];
        if ($diff !== 0) {
            $result->modify("$diff days");
        }

        // Return Result
        $start = $result->format($format);
        $end = $result->modify('+ 6 days')->format($format);
        return ['start' => $start, 'end' => $end];
    }

    /**
     * Get Current Month
     *
     * @param string $format The desired output format.
     * @return array An 'start', 'end' array containing the whole month.
     */
    public function getCurrentMonth($format = self::NOW)
    {
        $currentDay = intval($this->format('d'));
        $result = new DateTime();

        // Start Month
        if ($currentDay > 1) {
            $result->modify("-" . ($currentDay-1) . " days");
        }

        // Return Result
        return [
            'start' => $result->format($format),
            'end' => $result->modify("+ 1 month")->modify("- 1 day")->format($format)
        ];
    }
}
