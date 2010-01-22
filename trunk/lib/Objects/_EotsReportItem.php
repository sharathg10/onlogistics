<?php

class _EotsReportItem extends Object {
    const STATE_REFUSED = 0;
    const STATE_ACCEPTED = 1;

    const DETAIL_ACCEPT = 0;
    const DETAIL_BROKEN_PALLET = 1;
    const DETAIL_BLENT_PALLET = 2;
    const DETAIL_UNKNOW_PALLET = 3;

    public static function getTableName() {
        return 'EotsReportItem';
    }

    public static function getProperties() {
        $return = array(
            'EotsReport' => 'EotsReport',
            'SN' => Object::TYPE_STRING,
            'State' => Object::TYPE_CONST,
            'Detail' => Object::TYPE_CONST,
        );
        return $return;
    }

    public static function getLinks() {
        $return = array(
        );
        return $return;
    }

    private $_EotsReport = false;

    public function getEotsReport() {
        if (is_int($this->_EotsReport) && $this->_EotsReport > 0) {
            $mapper = Mapper::singleton('EotsReport');
            $this->_EotsReport = $mapper->load(
                array('Id'=>$this->_EotsReport));
        }
        return $this->_EotsReport;
    }

    public function getEotsReportId() {
        if ($this->_EotsReport instanceof EotsReport) {
            return $this->_EotsReport->getId();
        }
        return (int)$this->_EotsReport;
    }

    public function setEotsReport($value) {
        if (is_numeric($value)) {
            $this->_EotsReport = (int) $value;
        } else {
            $this->_EotsReport = $value;
        }
    }

    private $_SN = '';

    public function setSN($value) {
        $this->_SN = $value;
    }

    public function getSN() {
        return $this->_SN;
    }

    private $_State = 0;

    public function setState($value) {
        $this->_State = $value;
    }

    public function getState() {
        return $this->_State;
    }

    public static function getStateConstArray($keys = false) {
        $array = array(
            _EotsReportItem::STATE_ACCEPTED => _("Accepted pallet"), 
            _EotsReportItem::STATE_REFUSED => _("Refused pallet"), 
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    private $_Detail = 0;

    public function setDetail($value) {
        $this->_Detail = $value;
    }

    public function getDetail() {
        return $this->_Detail;
    }

    public static function getDetailConstArray($keys = false) {
        $array = array(
            _EotsReportItem::DETAIL_ACCEPT => _("Accepted pallet"), 
            _EotsReportItem::DETAIL_BROKEN_PALLET => _("Broken pallet"), 
            _EotsReportItem::DETAIL_BLENT_PALLET => _("Blent pallet"), 
            _EotsReportItem::DETAIL_UNKNOW_PALLET => _("Unknow pallet"), 
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }
}
