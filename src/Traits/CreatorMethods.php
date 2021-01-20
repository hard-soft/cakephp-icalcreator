<?php

namespace ICalcreator\Traits;

use Kigkonsult\Icalcreator\Vcalendar;
use DateTime;
use DateTimezone;


trait CreatorMethods {
    var $errorCode = null;
    var $errorMessage = null;
    
    var $calendar;
    var $timezone = null;

    public function create ($description = "Meine Termine", $tz = "Europe/Berlin") {
        // $config = array("unique_id" => 'treatsoft.at');
        $this->timezone = $tz;

		$this->calendar = Vcalendar::factory([
            Vcalendar::UNIQUE_ID => "treatsoft.at",
        ])
        ->setMethod( Vcalendar::PUBLISH )
        ->setXprop(
            Vcalendar::X_WR_CALNAME,
            "Meine Termine"
       )
       ->setXprop(
            Vcalendar::X_WR_CALDESC,
            $description
       )
       ->setXprop(
            Vcalendar::X_WR_TIMEZONE,
            $this->timezone
       );
    }
    
    
    public function createSingle ($tz = "Europe/Berlin") {
        $this->timezone = $tz;

        $this->calendar = Vcalendar::factory([
                Vcalendar::UNIQUE_ID => "treatsoft.at",
            ])
            ->setMethod( Vcalendar::PUBLISH )
            ->setXprop(
                Vcalendar::X_WR_CALNAME,
                "Meine Termine"
            )
            ->setXprop(
                Vcalendar::X_WR_TIMEZONE,
                $this->timezone
            );
    }
    
    
    public function addEvent ($date, $article, $postfix = "") {
        $title          = 'Termin';
        $description    = 'Termin' . ((!empty($date['ressource']))?'bei ' . $date['ressource']:'');
        $location       = '';
        if (isset($article['description'])) {
            $title = $article['description'] . ((!empty($date['ressource']))?" bei " . $date['ressource']:'');
            $description = $article['description'] . ', ' . $article['duration'] . ' min';
            if (!empty($postfix)) {
                $description .= "\n" . $postfix;
            }
        }
        if (!empty($date['bezeichnung'])) {
            $lines = explode("\n", $date['bezeichnung']);
            foreach ($lines as $line) {
                if (strpos($line, "Ort: ") !== false) {
                    $location = str_replace("Ort: ", "", $line);
                    break;
                }
            }
        }

        $this->calendar->newVevent()
            ->setDtstart(new DateTime($date['beginn'], new DateTimezone($this->timezone)))
            ->setDtend(new DateTime($date['ende'], new DateTimezone($this->timezone)))
            ->setLocation($location)
            ->setSummary($title)
            ->setDescription($description);
    }
    
    
	public function addEvents ($dates = array(), $articles = array(), $postfix = "") {
		if (!empty($dates)) {
			foreach ($dates as $date) {
                $article = (!empty($articles[$date['article_id']]))?$articles[$date['article_id']]:[];
                $this->addEvent($date, $article, $postfix);
		    }
		}
	 }
    
    
    public function getCalendar () {
        return $this->calendar;
    }
    
    
    public function generate () {
        return $this->calendar->vtimezonePopulate()->createCalendar();
    }    
}