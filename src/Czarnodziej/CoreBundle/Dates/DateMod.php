<?php

/**
 * ShowTimeAgo in Polish language. Also parses the time of last modification of included files.
 * http://forum.pclab.pl/topic/470657-PHPFunkcja-formatuj%C4%85ca-date-w-format-wczoraj-dzisiaj-minut%C4%99-temu/page__view__findpost__p__6625687
 */

namespace Czarnodziej\CoreBundle\Dates;

class DateMod {

    public function dateMod($format, $timestamp = null) {

        $to_convert = array(// define translated data in an array
            'l' => array('dat' => 'N', 'str' => array('poniedziałek', 'wtorek', 'środa', 'czwartek', 'piątek', 'sobota', 'niedziela')),
            'F' => array('dat' => 'n', 'str' => array('styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień')),
            'f' => array('dat' => 'n', 'str' => array('stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia'))
        );

        $pieces = preg_split('#[:/.\-, ]#', $format); // splits translated arrays

        if ($timestamp === null) {
            $timestamp = time();
        }
        foreach ($pieces as $datepart) {
            if (array_key_exists($datepart, $to_convert)) { // checks if date format is present in translated array definied above
                $replace[] = // adds translated array element
                        $to_convert[$datepart]['str'][( // reference to array of weekday/months with an index of:
                        date(// date in form of:
                                $to_convert[$datepart]['dat'], // array of weekday/months: N for weekdays, F/f for months (1->Monday/January)
                                $timestamp
                        ) - 1)]; // array begins at 0.
            } else {
                $replace[] = date($datepart, $timestamp); //if date format is other than specified in translated array
            }
        }
        $result = strtr($format, array_combine($pieces, $replace)); //replaces results of translations in date()
        return $result;
    }

    public function getMinutes($minut) {
        // polish language
        switch ($minut) {
            case 0:
                return 0;

            case 1:
                return 1;

            case ($minut >= 2 && $minut <= 4):
            case ($minut >= 22 && $minut <= 24):
            case ($minut >= 32 && $minut <= 34):
            case ($minut >= 42 && $minut <= 44):
            case ($minut >= 52 && $minut <= 54):
                return "$minut minuty temu";

            default:
                return "$minut minut temu";
        }
        return -1;
    }

    public function showTimeAgo($data_wejsciowa) {

        $timestamp = strtotime($data_wejsciowa);

        $now = time();

        if ($timestamp > $now) {
            return 'Podana data nie może być większa od obecnej.';
        }

        $diff = $now - $timestamp;

        $minut = floor($diff / 60);
        $godzin = floor($minut / 60);
        $dni = floor($godzin / 24);

        if ($minut <= 60) {
            $res = self::getMinutes($minut);
            switch ($res) {
                case 0: return "przed chwilą";
                case 1: return "minutę temu";
                default: return $res;
            }
        }

        $timestamp_wczoraj = $now - (60 * 60 * 24);
        $timestamp_przedwczoraj = $now - (60 * 60 * 24 * 2);

        if ($godzin > 0 && $godzin <= 6) {

            $restMinutes = ($minut - (60 * $godzin));
            $res = self::getMinutes($restMinutes);
            if ($godzin == 1) {
                return "Godzinę temu "; //.$res
            } else {
                if ($godzin > 1 && $godzin < 5) {
                    return "$godzin godziny temu ";
                }
                if ($godzin > 4) {
                    return "$godzin godzin temu";
                }
            }
        } else if (date("d.m.Y", $timestamp) == date("d.m.Y", $now)) {//jesli dzisiaj
            return "dzisiaj o " . date("H:i", $timestamp);
        } else if (date("d.m.Y", $timestamp_wczoraj) == date("d.m.Y", $timestamp)) {//jesli wczoraj
            return "wczoraj o " . date("H:i", $timestamp);
        } else if (date("d.m.Y", $timestamp_przedwczoraj) == date("d.m.Y", $timestamp)) {//jesli przedwczoraj
            return "przedwczoraj o " . date("H:i", $timestamp);
        }

        switch ($dni) {
            case ($dni < 7):
                return "$dni dni temu, " . date("d.m.Y", $timestamp);

            case 7:
                return "Tydzień temu, " . date("d.m.Y", $timestamp);

            case ($dni > 7 && $dni < 14):
                return "ponad tydzień temu, " . date("d.m.Y", $timestamp);

            case 14:
                return "Dwa tygodnie temu, " . date("d.m.Y", $timestamp);

            case ($dni > 14 && $dni < 30):
                return "ponad 2 tygodnie temu, " . date("d.m.Y", $timestamp);

            case 30: case 31:
                return "miesiąc temu, " . date("d.m.Y", $timestamp);

            case ($dni > 31):
                return date("d.m.Y", $timestamp);
        }
        return self::dateMod("l j f Y");
    }

    public function getPageModTime() { //checks all website files for modification time
        $incls = get_included_files();
        $included = array_filter($incls, "is_file");
        $mod_times = array_map('filemtime', $included);
        $mod_time = max($mod_times);
        return $mod_time;
    }

    public function getName() {
        return 'date_mod';
    }

}
