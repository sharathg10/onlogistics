<?php

class EotsReportGrid extends GenericGrid {
    public function renderSearchFormCommand() {
        $this->searchForm->addElement(
                'text', 'Command', _('Commande'), array(),
                array('Path' => 'Command.CommandNo'));
    }

    public function renderColumnCommand() {
        $this->grid->newColumn('FieldMapper', _('Commande'), array('Macro'=>'%Command.CommandNo%'));
    }
}
