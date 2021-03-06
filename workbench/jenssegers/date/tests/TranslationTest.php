<?php

use Jenssegers\Date\Date;
use Jenssegers\Date\Translator;
use Symfony\Component\Translation\MessageSelector;

class TranslationTest extends \PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->languages = array_slice(scandir('src/lang'), 2);
    }

    public function testGetsAndSetsTranslator()
    {
        $translator = new Translator;
        $this->assertNotEquals($translator, Date::getTranslator());

        Date::setTranslator($translator);
        $this->assertEquals($translator, Date::getTranslator());
    }

    public function testMultiplePluralForms()
    {
        Date::setLocale('hr');

        $date = Date::parse('-1 years');
        $this->assertSame("Prije 1 godinu", $date->ago());

        $date = Date::parse('-2 years');
        $this->assertSame("Prije 2 godine", $date->ago());

        $date = Date::parse('-3 years');
        $this->assertSame("Prije 3 godine", $date->ago());

        $date = Date::parse('-5 years');
        $this->assertSame("Prije 5 godina", $date->ago());
    }

    public function testMultipleAgo()
    {
        Date::setLocale('de');

        $date = Date::parse('-1 month');
        $this->assertSame("vor 1 Monat", $date->ago());

        $date = Date::parse('-5 months');
        $this->assertSame("vor 5 Monaten", $date->ago());
    }

    public function testTranslatesMonths()
    {
        $months = array(
            'january',
            'february',
            'march',
            'april',
            'may',
            'june',
            'july',
            'august',
            'september',
            'october',
            'november',
            'december'
        );

        foreach ($this->languages as $language)
        {
            $translations = include "src/lang/$language/date.php";

            foreach ($months as $month)
            {
                $date = new Date("1 $month");
                $date->setLocale($language);

                $this->assertTrue(isset($translations[$month]));
                $this->assertEquals($translations[$month], $date->format('F'), "Language: $language"); // Full
                $this->assertEquals(substr($translations[$month], 0 , 3), $date->format('M'), "Language: $language"); // Short
            }
        }
    }

    public function testTranslatesDays()
    {
        $days = array(
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday'
        );

        foreach ($this->languages as $language)
        {
            $translations = include "src/lang/$language/date.php";

            foreach ($days as $day)
            {
                $date = new Date($day);
                $date->setLocale($language);

                $this->assertTrue(isset($translations[$day]));
                $this->assertEquals($translations[$day], $date->format('l'), "Language: $language"); // Full
                $this->assertEquals(substr($translations[$day], 0 , 3), $date->format('D'), "Language: $language"); // Short
            }
        }
    }

    public function testTranslatesDiffForHumans()
    {
        $items = array(
            'ago',
            'from now',
            'after',
            'before',
            'year',
            'month',
            'week',
            'day',
            'hour',
            'minute',
            'second'
        );

        foreach ($this->languages as $language)
        {
            $translations = include "src/lang/$language/date.php";

            foreach ($items as $item)
            {
                $this->assertTrue(isset($translations[$item]), "Language: $language");

                if ( ! $translations[$item])
                {
                    echo "\nWARNING! '$item' not set for language $language";
                    continue;
                }

                if (in_array($item, array('ago', 'from now', 'after', 'before')))
                {
                    $this->assertContains(':time', $translations[$item]);
                }
                else
                {
                    $this->assertContains(':count', $translations[$item]);
                }
            }
        }
    }

}
