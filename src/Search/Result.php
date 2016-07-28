<?php

namespace HappyDemon\UitDatabank\Search;

class Result
{
    /**
     * @var int
     */
    protected $numResults = 0;

    /**
     * @var array
     */
    protected $events;

    /**
     * Add an event
     *
     * @param Event $event
     */
    public function addEvent($event)
    {
        $this->events[] = $event;
    }

    /**
     * @param array $events
     */
    protected function setEvents($events)
    {
        $this->events = $events;
    }

    /**
     * @return array[Event]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param int $numResults
     */
    public function setNumResults($numResults)
    {
        $this->numResults = $numResults;
    }

    /**
     * @return int
     */
    public function getNumResults()
    {
        return $this->numResults;
    }

    /**
     * Convert the raw XML into an object
     *
     * @param \SimpleXMLElement $xml
     * @return Result
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $result = new Result();

        if (isset($xml->nofrecords)) {
            $result->setNumResults((int) $xml->nofrecords);
        }
        if (isset($xml->event)) {
            foreach($xml->event as $event) {
                $result->addEvent(
                    \CultureFeed_Cdb_Item_Event::parseFromCdbXml($event)
                );
            }
        }

        return $result;
    }
}
