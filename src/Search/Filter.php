<?php

namespace HappyDemon\UitDatabank\Search;

class Filter
{
    /**
     * @var string
     */
    protected $q;

    /**
     * @var string
     */
    protected $fq;

    /**
     * @var int
     */
    protected $start = 0;

    /**
     * @var int
     */
    protected $rows = 50;

    /**
     * @var string
     */
    protected $sort = 'score desc';

    /**
     * @var string
     */
    protected $group;

    /**
     * @var array
     */
    protected $pt;

    /**
     * @var string
     */
    protected $sfield;

    /**
     * @var int
     */
    protected $d;

    /**
     * @var string
     */
    protected $facetField;

    /**
     * @var string
     */
    protected $transform;

    /**
     * @var bool
     */
    protected $past = false;

    /**
     * @var string
     */
    protected $dataType;

    /**
     * @param int $d
     */
    public function setD($d)
    {
        $this->d = $d;
    }

    /**
     * @return int
     */
    public function getD()
    {
        return $this->d;
    }

    /**
     * @param string $dataType
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;
    }

    /**
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @param string $facetField
     */
    public function setFacetField($facetField)
    {
        $this->facetField = $facetField;
    }

    /**
     * @return string
     */
    public function getFacetField()
    {
        return $this->facetField;
    }

    /**
     * @param string $fq
     */
    public function setFq($fq)
    {
        $this->fq = $fq;
    }

    /**
     * @return string
     */
    public function getFq()
    {
        return $this->fq;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param boolean $past
     */
    public function setPast($past)
    {
        $this->past = $past;
    }

    /**
     * @return boolean
     */
    public function getPast()
    {
        return $this->past;
    }

    /**
     * @param float $lat
     * @param float $lng
     */
    public function setPt($lat, $lng)
    {
        $this->pt = array(
            'lat' => $lat,
            'lng' => $lng,
        );
    }

    /**
     * @return array
     */
    public function getPt()
    {
        return $this->pt;
    }

    /**
     * @param string $q
     */
    public function setQ($q)
    {
        $this->q = $q;
    }

    /**
     * @return string
     */
    public function getQ()
    {
        return $this->q;
    }

    /**
     * @param int $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param string $sfield
     */
    public function setSfield($sfield)
    {
        $this->sfield = $sfield;
    }

    /**
     * @return string
     */
    public function getSfield()
    {
        return $this->sfield;
    }

    /**
     * @param string $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param string $transform
     */
    public function setTransform($transform)
    {
        $this->transform = $transform;
    }

    /**
     * @return string
     */
    public function getTransform()
    {
        return $this->transform;
    }

    public function buildForRequest()
    {
        $parameters = array();
        $parameters['q'] = $this->getQ();

        if ($this->getFq()) {
            $parameters['fq'] = $this->getFq();
        }
        if ($this->getStart()) {
            $parameters['start'] = $this->getStart();
        }
        if ($this->getRows()) {
            $parameters['rows'] = $this->getRows();
        }
        if ($this->getSort()) {
            $parameters['sort'] = $this->getSort();
        }
        if ($this->getGroup()) {
            $parameters['group'] = $this->getGroup();
        }
        if ($this->getPt()) {
            $parameters['pt'] = $this->getPt();
        }
        if ($this->getSfield()) {
            $parameters['sfield'] = $this->getSfield();
        }
        if ($this->getD()) {
            $parameters['d'] = $this->getD();
        }
        if ($this->getFacetField()) {
            $parameters['facetField'] = $this->getFacetField();
        }
        if ($this->getTransform()) {
            $parameters['transform'] = $this->getTransform();
        }
        if ($this->getPast()) {
            $parameters['past'] = $this->getPast();
        }
        if ($this->getDataType()) {
            $parameters['datatype'] = $this->getDataType();
        }

        return $parameters;
    }
}
