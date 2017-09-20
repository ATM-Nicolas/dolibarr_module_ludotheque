<?php
/* Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    lib/mymodule.lib.php
 * \ingroup mymodule
 * \brief   Example module library.
 *
 * Put detailed description here.
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function ludothequeAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load("ludotheque@ludotheque");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/ludotheque/ludotheque_card.php?action=info&id=".GETPOST('id'), 1);
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;
	$head[$h][0] = dol_buildpath("/ludotheque/ludotheque_card.php?action=moreInfo&id=".GETPOST('id'), 1);
	$head[$h][1] = $langs->trans("Events");
	$head[$h][2] = 'events';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'ludotheque');

	return $head;
}

function printList(DoliDB $db, $sql, &$object, $langs)
{
    $arrayfields=array();
    $objClass = get_class($object);
    $form = new Form($db);
    $produit = new Produit($db);
    //$tab = $produit->getAllNullEmplacement();
    
    /*
     print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
     print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
     print '<input type="hidden" name="action" value="addProduitInOneLudo">';
     */
    
    /*print '<div>';
    
    print $langs->trans('AddProduit');
    print $form->selectarray('idProduit', $tab);
    print ' &nbsp; <a class="button" href="ludotheque_card.php?action=addProduitInOneLudo">Ajouter</a>';
    
    print '</div><br>';*/
    
    //print '</form>';
    
    print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";
    
    // ----------------------------------- Affichage des entêtes -----------------------------------
    
    
    
    if ($objClass == 'Produit');
    {
        
        if (GETPOST('id'))
        {
            /*print '<tr class="liste_titre">';
            
            // TODO: Afficher un select avec tous les produits déjà crées et qui ne sont pas
            
            foreach($produit->fields as $key => $val)
            {
            if (in_array($key, array('rowid', 'tms', 'date_creat', 'fk_user_creat', 'fk_user_modif'))) continue;
            
            if (in_array($key, array('date_achat'))) print '<input type="hidden" name="'.$key.'" value="null">';
            else
            {
            print '<th>';
            if ($key == 'fk_emplacement')
            print '<input type="hidden" name="'.$key.'" value="'.$produit->getOneEmplacementLibelle(GETPOST('id')).'">';
            else
            print '<input type="text" name="'.$key.'"';
            
            print '</th>';
            }
            }
            
            print '</tr>';
            */
        }
        
        foreach($produit->fields as $key => $val)
        {
            // If $val['visible']==0, then we never show the field
            if (! empty($val['visible'])) $arrayfields['p.'.$key]=array('label'=>$val['label'], 'checked'=>(($val['visible']<0)?0:1), 'enabled'=>$val['enabled']);
        }
        
        // ----------------------------------- Titres -----------------------------------
        print '<tr class="liste_titre">';
        foreach($produit->fields as $key => $val)
        {
            $align='';
            if (in_array($val['type'], array('date','datetime','timestamp'))) $align='center';
            if (in_array($val['type'], array('timestamp'))) $align.='nowrap';
            if (! empty($arrayfields['p.'.$key]['checked'])) print getTitleFieldOfList($arrayfields['p.'.$key]['label'], 0, $_SERVER['PHP_SELF'], 'p.'.$key, '', $param, ($align?'class="'.$align.'"':''), $sortfield, $sortorder, $align.' ')."\n";
        }
    }
    /*if ($objClass == 'Ludotheque')   // get_class($object) = 'Ludotheque'
     {
     $ludo = new Ludotheque($db);
     
     foreach($ludo->fields as $key => $val)
     {
     // If $val['visible']==0, then we never show the field
     if (! empty($val['visible'])) $arrayfields['p.'.$key]=array('label'=>$val['label'], 'checked'=>(($val['visible']<0)?0:1), 'enabled'=>$val['enabled']);
     }
     
     // ----------------------------------- Titres -----------------------------------
     print '<tr class="liste_titre">';
     foreach($ludo->fields as $key => $val)
     {
     $align='';
     if (in_array($val['type'], array('date','datetime','timestamp'))) $align='center';
     if (in_array($val['type'], array('timestamp'))) $align.='nowrap';
     if (! empty($arrayfields['p.'.$key]['checked'])) print getTitleFieldOfList($arrayfields['p.'.$key]['label'], 0, $_SERVER['PHP_SELF'], 'p.'.$key, '', $param, ($align?'class="'.$align.'"':''), $sortfield, $sortorder, $align.' ')."\n";
     }
     }*/
    
    // ----------------------------------- Exécution de la requête -----------------------------------
    $res = $db->query($sql);
    if (! $res)
    {
        dol_print_error($db);
        exit;
    }
    
    $num = $db->num_rows($res);
    
    // ----------------------------------- Affichage des résultats -----------------------------------
    
    $i = 0;
    $table = array();
    while($i < $num)
    {
        $obj = $db->fetch_object($res);
        if (! $obj)
        {
            dol_print_error($db);
            exit;
        }
        
        print '<tr class="oddeven">';
        
        foreach($obj as $key => $val)
        {
            if ($key == 'rowid') continue;
            print '<td';
            if ($key == 'date_achat') print ' class="center nowrap"';
            print '>';
            
            if ($key == 'libelle')
            {
                $lien == true;
                print '<a href="';
                
                if ($objClass == 'Produit') print 'produit';
                else print 'ludotheque';
                
                print '_card.php?action=info&id='.$obj->rowid.'">';
            }
            
            if (in_array($key, array('date_achat'))) print dol_print_date($db->jdate($obj->$key), 'dayhour');
            else print $val;
            
            if ($lien === true) print '</a>';
            print '</td>';
        }
        print '</tr>';
        $i++;
        
    }
    
    if ($num == 0)
    {
        $colspan=1;
        foreach($arrayfields as $key => $val) { if (! empty($val['checked'])) $colspan++; }
        // TODO: Régler le problème avec la variable $langs
        print '<tr><td colspan="'.$colspan.'" class="opacitymedium">'.$langs->trans("NoRecordFound").'</td></tr>';
    }
    
    
    print '</table>'."\n";
    /*
     print '</div>'."\n";
     */
    return $num;
}
