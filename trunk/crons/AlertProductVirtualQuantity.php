<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of Onlogistics, a web based ERP and supply chain 
 * management application. 
 *
 * Copyright (C) 2003-2008 ATEOR
 *
 * This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU Affero General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or (at your 
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public 
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5.1.0+
 *
 * @package   Onlogistics
 * @author    ATEOR dev team <dev@ateor.com>
 * @copyright 2003-2008 ATEOR <contact@ateor.com> 
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU AGPL
 * @version   SVN: $Id$
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

if (php_sapi_name() != 'cli') {
    exit(1);
}

require_once 'Objects/MovementType.const.php';

$filter = array();
$filter['State'] = array(
    ActivatedMovement::CREE,
    ActivatedMovement::ACM_EN_COURS,
    ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT,
    ActivatedMovement::BLOQUE
);
$filter['Type'] = array(
    ENTREE_NORMALE, ENTREE_INTERNE, // entrées
    SORTIE_NORMALE, SORTIE_INTERNE  // sorties
);

// corps du mail
$mailBody = '';

// collection de produits
$pdtCollection = Object::loadCollection('Product');
$count = $pdtCollection->getCount();

for ($i=0; $i<$count; $i++) {
    $pdt = $pdtCollection->getItem($i);
    // nombre de lpq
    $RQ = $pdt->getRealQuantity();
    // somme des entrées et des sorties
    $entries = 0;
    $exits = 0;
    $filter['Product'] = $pdt->getId();
    $acmCollection = Object::loadCollection('ActivatedMovement', $filter);
    $acmCount = $acmCollection->getCount();
    for ($j=0; $j<$acmCount; $j++) {
        $acm = $acmCollection->getItem($j);
        $type = $acm->getTypeId();
        if ($type == ENTREE_NORMALE || $type == ENTREE_INTERNE) {
            $entries += $acm->getQuantity();
            $coef = -1;  // servira s'il y a des mvts partiels
        } else {
            $exits += $acm->getQuantity();
            $coef = 1;
        }
        // Si mvt partiel, la QV sera mise a jour qd le mvt sera total
        // En attendant, $RQ + $entries - $exits != QV, et c'est NORMAL
        // On rectifie donc pour la verif, QR de la qte de pdt (commande!) deja
        // mouvemente
        // Si on modifie cette loi, il faut le faire sur tout ol, pas que ce script
        $isPartial = ($acm->getState() == ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT);
        if ($isPartial) {
            $exm = $acm->getExecutedMovement();
            $RQ += $coef * $exm->getProductMovedQuantity($pdt);
            // RQ est en fait desormais la qte reelle *avant* debut execution
        }
    }
    // Check de la quantité virtuelle
    $oldVQ = $pdt->getSellUnitVirtualQuantity();
    $newVQ = $RQ + $entries - $exits;
    if ($oldVQ != $newVQ) {
        $mailBody[] = array($pdt->getId(), $pdt->getBaseReference(), $oldVQ, $newVQ);
    }
}

// envoie du mail éventuel
if (!empty($mailBody)) {
    $db = Database::connection()->database;
    $subj  = "Quantités virtuelles suspectes sur la base \"$db\"";
    $body  = "<strong>Détail des quantités virtuelles suspectes ";
    $body .= "sur la base \"$db\":</strong>\n";
    $body .= "<p><table border=\"1\" cellpadding=\"5\">\n";
    $body .= "<tr><th>ID produit</th><th>Réf. produit</th>";
    $body .= "<th>QV actuelle</th><th>QV rectifiée</th>\n";
    foreach ($mailBody as $data) {
        $body .= sprintf(
            "<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>\n",
            $data[0], $data[1], $data[2], $data[3]
        );
    }
    $body .= "</table></p>\n\n";
    $body .= "Veuillez effectuer les vérifications qui s'imposent sur ces produits. ";
    $body .= "Si cela est nécessaire, vous pouvez lancer la tâche cron de correction des QV ponctuellement.\n";
    $body .= "(Penser à la supprimer après l'avoir lancée)";
    MailTools::send(array(MAIL_DEV), $subj, $body, true);
}

?>