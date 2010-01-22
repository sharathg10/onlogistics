<?php

class _EotsReport extends Object {

    public static function getTableName() {
        return 'EotsReport';
    }

    public static function getProperties() {
        $return = array(
            'Command' => 'Command',
            'ShipperID' => Object::TYPE_STRING,
        );
        return $return;
    }

    public static function getLinks() {
        $return = array(
            'EotsReportItem' => array(
                'linkClass'     => 'EotsReportItem',
                'field'         => 'EotsReport',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany',
            ),
        );
        return $return;
    }

    private $_Command = false;

    public function getCommand() {
        if (is_int($this->_Command) && $this->_Command > 0) {
            $mapper = Mapper::singleton('Command');
            $this->_Command = $mapper->load(
                array('Id'=>$this->_Command));
        }
        
        return $this->_Command;
    }

    public function getCommandId() {
        if ($this->_Command instanceof Command) {
            return $this->_Command->getId();
        }
        return (int)$this->_Command;
    }

    public function setCommand($value) {
        if (is_numeric($value)) {
            $this->_Command = (int)$value;
        } else {
            $this->_Command = $value;
        }
    }

    private $_ShipperID = '';

    public function setShipperID($value) {
        $this->_ShipperID = $value;
    }

    public function getShipperID() {
        return $this->_ShipperID;
    }

    public static function getObjectLabel() {
        return _('View EOTS reports');
    }

    public static function getFeatures() {
        return array('grid', 'searchform', 'del', 'view');
    }

    public static function getMapping() {
        $return = array(
            'Command'=>array(
                'label'        => _('Commandes'),
                'shortlabel'   => _('Cmd'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('Report')
            ),
            'ShipperID'=>array(
                'label'        => _('Livreur'),
                'shortlabel'   => _('Livreur'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('Report')
            ),
        );
        return $return;
    }

}
