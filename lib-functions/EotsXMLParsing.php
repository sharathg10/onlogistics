<?php
require_once('Objects/Command.php');
require_once('Objects/EotsReport.php');

function parseEotsReport($file, $rang)
{
    $xml = simplexml_load_string($file);
    if (!$xml) {
        return "Erreur de parsing pour le rapport EOTS de rang $rang. <br />";
    }

    $auth = Auth::singleton();

    $cmdMapper = Mapper::singleton('Command');
    $Command = $cmdMapper->load(array('CommandNo' => (string)$xml->commandNo));

    $EotsReport = Object::load('EotsReport');
    $EotsReport->setCommand($Command);
    $EotsReport->setShipperId((string)$xml->shipperid);
    $EotsReport->save();

    foreach ($xml->eotsitem as $item) {
        $EotsItem = Object::load('EotsReportItem');
        $EotsItem->setEotsReport($EotsReport);
        $EotsItem->setSN((string)$item->sn);
        $EotsItem->setState((int)$item->state);
        $EotsItem->setDetail((int)$item->detail);
        $EotsItem->save();
    }
}
