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


//function printList($user, $conf, DoliDB $db, $sql, $limit, $offset, $object, $langs, $search, $search_array_options, $parameters, $hookmanager, $form, $extrafields, &$arrayfields, $title, $page, $url, $param,
//$sortfield, $sortorder, $action)
function printList($user, $conf, $langs, DoliDB $db, $object, $TParam)
{
    // --------------------- Initialisation des variables ---------------------
    $sql = $TParam['sql'];
    $limit = $TParam['limit'];
    $offset = $TParam['offset'];
    $search = $TParam['search'];
    $search_array_options = $TParam['search_array_options'];
    $parameters = $TParam['parameters'];
    $hookmanager = $TParam['hookmanager'];
    $form = $TParam['form'];
    $extrafields = $TParam['extrafields'];
    $arrayfields = $TParam['arrafields'];
    $title = $TParam['title'];
    $page = $TParam['page'];
    $url = $TParam['url'];
    
    
    $param = $TParam['param'];
    $sortfield = $TParam['sortfield'];
    $sortorder = $TParam['sortorder'];
    $action = $TParam['action'];
    
    $tabTmp = explode('/', $url);
    
    if ($tabTmp[count($tabTmp)-1] == 'ludotheque_card.php')
    {
        $actionLudotheque = new ActionsLudotheque($db);
        $nbLigne = $actionLudotheque->printList($sql, $object, $langs);
        return $nbLigne;
    }
    
    $reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
    if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
    
    if (empty($reshook))
    {
        // Selection of new fields
        include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';
        
        // Purge search criteria
        if (GETPOST('button_removefilter_x','alpha') || GETPOST('button_removefilter.x','alpha') ||GETPOST('button_removefilter','alpha')) // All tests are required to be compatible with all browsers
        {
            foreach($object->fields as $key => $val)
            {
                $search[$key]='';
            }
            $toselect='';
            $search_array_options=array();
        }
        if (GETPOST('button_removefilter_x','alpha') || GETPOST('button_removefilter.x','alpha') || GETPOST('button_removefilter','alpha')
            || GETPOST('button_search_x','alpha') || GETPOST('button_search.x','alpha') || GETPOST('button_search','alpha'))
        {
            $massaction='';     // Protection to avoid mass action if we force a new search during a mass action confirmation
        }
        /*
         // Mass actions
         $objectclass='Ludotheque';
         $objectlabel='Ludotheque';
         $permtoread = $user->rights->ludotheque->read;
         $permtodelete = $user->rights->ludotheque->delete;
         $uploaddir = $conf->ludotheque->dir_output;
         include DOL_DOCUMENT_ROOT.'/core/actions_massactions.inc.php';*/
    }
    
    foreach($search as $key => $val)
    {
        if ($key == 'fk_emplacement' && is_string($val))
            $sql.=natural_search('l.libelle', $search[$key], 0);
        else if ($key == 'fk_categorie' && is_string($val))
            $sql.=natural_search('cp.libelle', $search[$key], 0);
        else if ($key == 'fk_gerant' && is_string($val))
            $sql.=natural_search('s.nom', $search[$key], 0);
        else if ($key == 'fk_user_creat')
            $sql.=natural_search('uc.lastname', $search[$key], 0);
        else if ($key == 'fk_user_modif')
            $sql.=natural_search('um.lastname', $search[$key], 0);
        else
            if ($search[$key] != '') $sql.=natural_search('t.'.$key, $search[$key], (($key == 'status')?2:($object->fields[$key]['type'] == 'integer'?1:0)));
    }
    
    
    if ($search_all) $sql.= natural_search(array_keys($fieldstosearchall), $search_all);
    // Add where from extra fields
    foreach ($search_array_options as $key => $val)
    {
        $crit=$val;
        $tmpkey=preg_replace('/search_options_/','',$key);
        $typ=$extrafields->attribute_type[$tmpkey];
        $mode=0;
        if (in_array($typ, array('int','double','real'))) $mode=1;    // Search on a numeric
        if ($crit != '' && (! in_array($typ, array('select')) || $crit != '0'))
        {
            $sql .= natural_search('ef.'.$tmpkey, $crit, $mode);
        }
    }
    
    // Add where from hooks
    $reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
    $sql.=$hookmanager->resPrint;
    $sql.=$db->order($sortfield,$sortorder);
    
    // Count total nb of records
    $nbtotalofrecords = '';
    if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
    {
        $result = $db->query($sql);
        $nbtotalofrecords = $db->num_rows($result);
    }
    
    $sql.= $db->plimit($limit+1, $offset);
    
    
    dol_syslog($script_file, LOG_DEBUG);
    $resql=$db->query($sql);
    if (! $resql)
    {
        dol_print_error($db);
        exit;
    }
    
    $num = $db->num_rows($resql);
    
    // Output page
    // --------------------------------------------------------------------
    
    $arrayofselected=is_array($toselect)?$toselect:array();
    
    $param='';
    if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
    if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
    foreach($search as $key => $val)
    {
        $param.= '&search_'.$key.'='.urlencode($search[$key]);
    }
    if ($optioncss != '')     $param.='&optioncss='.$optioncss;
    // Add $param from extra fields
    foreach ($search_array_options as $key => $val)
    {
        $crit=$val;
        $tmpkey=preg_replace('/search_options_/','',$key);
        if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
    }
    
    $arrayofmassactions =  array(
        'presend'=>$langs->trans("SendByMail"),
        'builddoc'=>$langs->trans("PDFMerge"),
    );
    if ($user->rights->mymodule->delete) $arrayofmassactions['delete']=$langs->trans("Delete");
    if ($massaction == 'presend') $arrayofmassactions=array();
    $massactionbutton=$form->selectMassAction('', $arrayofmassactions);
    
    print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
    if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
    print '<input type="hidden" name="action" value="list">';
    print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
    print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
    print '<input type="hidden" name="page" value="'.$page.'">';
    print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';
    
    print_barre_liste($title, $page, $url, $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);
    
    if ($sall)
    {
        foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
        print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
    }
    
    $moreforfilter = '';
    
    $varpage=empty($contextpage)?$url:$contextpage;
    $selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields
    $selectedfields.=$form->showCheckAddButtons('checkforselect', 1);
    
    print '<div class="div-table-responsive">';
    print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";
    
    
    // Fields title search
    // --------------------------------------------------------------------
    print '<tr class="liste_titre">';
    foreach($object->fields as $key => $val)
    {
        if (in_array($key, array('date_creation', 'tms', 'import_key', 'status'))) continue;
        $align='';
        if (in_array($val['type'], array('date','datetime','timestamp'))) $align='center';
        if (in_array($val['type'], array('timestamp'))) $align.=' nowrap';
        if (! empty($arrayfields['t.'.$key]['checked'])) print '<td class="liste_titre'.($align?' '.$align:'').'"><input type="text" class="flat maxwidth75" name="search_'.$key.'" value="'.dol_escape_htmltag($search[$key]).'"></td>';
    }
    
    // Extra fields
    if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
    {
        foreach($extrafields->attribute_label as $key => $val)
        {
            if (! empty($arrayfields["ef.".$key]['checked']))
            {
                $align=$extrafields->getAlignFlag($key);
                $typeofextrafield=$extrafields->attribute_type[$key];
                print '<td class="liste_titre'.($align?' '.$align:'').'">';
                if (in_array($typeofextrafield, array('varchar', 'int', 'double', 'select')) && empty($extrafields->attribute_computed[$key]))
                {
                    $crit=$val;
                    $tmpkey=preg_replace('/search_options_/','',$key);
                    $searchclass='';
                    if (in_array($typeofextrafield, array('varchar', 'select'))) $searchclass='searchstring';
                    if (in_array($typeofextrafield, array('int', 'double'))) $searchclass='searchnum';
                    print '<input class="flat'.($searchclass?' '.$searchclass:'').'" size="4" type="text" name="search_options_'.$tmpkey.'" value="'.dol_escape_htmltag($search_array_options['search_options_'.$tmpkey]).'">';
                }
                print '</td>';
            }
        }
    }
    // Fields from hook
    $parameters=array('arrayfields'=>$arrayfields);
    $reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
    print $hookmanager->resPrint;
    // Rest of fields search
    foreach($object->fields as $key => $val)
    {
        if (! in_array($key, array('date_creation', 'tms', 'import_key', 'status'))) continue;
        $align='';
        if (in_array($val['type'], array('date','datetime','timestamp'))) $align='center';
        if (in_array($val['type'], array('timestamp'))) $align.=' nowrap';
        if (! empty($arrayfields['t.'.$key]['checked'])) print '<td class="liste_titre'.($align?' '.$align:'').'"><input type="text" class="flat maxwidth75" name="search_'.$key.'" value="'.dol_escape_htmltag($search[$key]).'"></td>';
    }
    // Action column
    print '<td class="liste_titre" align="right">';
    $searchpicto=$form->showFilterButtons();
    print $searchpicto;
    print '</td>';
    print '</tr>'."\n";
    
    
    
    // Fields title label
    // --------------------------------------------------------------------
    print '<tr class="liste_titre">';
    
    foreach($object->fields as $key => $val)
    {
        if (in_array($key, array('date_creation', 'tms', 'import_key', 'status'))) continue;
        $align='';
        if (in_array($val['type'], array('date','datetime','timestamp'))) $align='center';
        if (in_array($val['type'], array('timestamp'))) $align.='nowrap';
        
        if (! empty($arrayfields['t.'.$key]['checked'])) print getTitleFieldOfList($arrayfields['t.'.$key]['label'], 0, $_SERVER['PHP_SELF'], 't.'.$key, '', $param, ($align?'class="'.$align.'"':''), $sortfield, $sortorder, $align.' ')."\n";
    }
    // Extra fields
    if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
    {
        foreach($extrafields->attribute_label as $key => $val)
        {
            if (! empty($arrayfields["ef.".$key]['checked']))
            {
                $align=$extrafields->getAlignFlag($key);
                $sortonfield = "ef.".$key;
                if (! empty($extrafields->attribute_computed[$key])) $sortonfield='';
                print getTitleFieldOfList($langs->trans($extralabels[$key]), 0, $_SERVER["PHP_SELF"], $sortonfield, "", $param, ($align?'align="'.$align.'"':''), $sortfield, $sortorder)."\n";
            }
        }
    }
    // Hook fields
    $parameters=array('arrayfields'=>$arrayfields);
    $reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
    print $hookmanager->resPrint;
    // Rest of fields title
    foreach($object->fields as $key => $val)
    {
        if (! in_array($key, array('date_creation', 'tms', 'import_key', 'status'))) continue;
        $align='';
        if (in_array($val['type'], array('date','datetime','timestamp'))) $align='center';
        if (in_array($val['type'], array('timestamp'))) $align.=' nowrap';
        if (! empty($arrayfields['t.'.$key]['checked'])) print getTitleFieldOfList($arrayfields['t.'.$key]['label'], 0, $_SERVER['PHP_SELF'], 't.'.$key, '', $param, ($align?'class="'.$align.'"':''), $sortfield, $sortorder, $align.' ')."\n";
    }
    print getTitleFieldOfList($selectedfields, 0, $_SERVER["PHP_SELF"],"",'','','align="center"',$sortfield,$sortorder,'maxwidthsearch ')."\n";
    print '</tr>'."\n";
    
    
    // Detect if we need a fetch on each output line
    $needToFetchEachLine=0;
    foreach ($extrafields->attribute_computed as $key => $val)
    {
        if (preg_match('/\$object/',$val)) $needToFetchEachLine++;  // There is at least one compute field that use $object
    }
    
    foreach($object->fields as $key => $val)
    {
        // If $val['visible']==0, then we never show the field
        if (! empty($val['visible'])) $arrayfields['p.'.$key]=array('label'=>$val['label'], 'checked'=>(($val['visible']<0)?0:1), 'enabled'=>$val['enabled']);
    }
    
    // ----------------------------------- Affichage des résultats -----------------------------------
    
    $i=0;
    $totalarray=array();
    while ($i < min($num, $limit))
    {
        $obj = $db->fetch_object($resql);
        if ($obj)
        {
            // Store properties in $object
            $object->id = $obj->rowid;
            foreach($object->fields as $key => $val)
            {
                if (isset($obj->$key)) $object->$key = $obj->$key;
            }
            
            // Show here line of result
            print '<tr class="oddeven">';
            foreach($object->fields as $key => $val)
            {
                if (in_array($key, array('date_creation', 'tms', 'import_key', 'status'))) continue;
                $align='';
                if (in_array($val['type'], array('date','datetime','timestamp'))) $align='center';
                if (in_array($val['type'], array('timestamp'))) $align.='nowrap';
                if ($key == 'status') $align.=($align?' ':'').'center';
                if (! empty($arrayfields['t.'.$key]['checked']))
                {
                    print '<td'.($align?' class="'.$align.'"':'').'>';
                    if (in_array($val['type'], array('date','datetime','timestamp'))) print dol_print_date($db->jdate($obj->$key), 'dayhour');
                    elseif ($key == 'ref') print $object->getNomUrl(1);
                    elseif ($key == 'status') print $object->getLibStatut(3);
                    else {
                        if ($key == 'libelle')
                        {
                            print '<a href="'.strtolower(get_class($object)).'_card.php?action=info&id='.$obj->rowid.'">';
                            $lien = true;
                        }
                        
                        if ($key == 'fk_emplacement' && $obj->fk_emplacement != null) print $object->getOneEmplacementLibelle($obj->$key);
                        elseif ($key == 'fk_categorie') print $object->getOneCategorieLibelle($obj->$key);
                        elseif ($key == 'fk_user_creat' || $key == 'fk_user_modif') print $object->getUserLibelle($user->id);
                        elseif ($key == 'fk_gerant') print $object->getSocieteLibelle($obj->$key);
                        else print $obj->$key;
                        
                        if ($lien === true)
                            print '</a>';
                    }
                    print '</td>';
                    if (! $i) $totalarray['nbfield']++;
                    if (! empty($val['isameasure']))
                    {
                        if (! $i) $totalarray['pos'][$totalarray['nbfield']]='t.'.$key;
                        $totalarray['val']['t.'.$key] += $obj->$key;
                    }
                }
            }
            // Extra fields
            if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
            {
                foreach($extrafields->attribute_label as $key => $val)
                {
                    if (! empty($arrayfields["ef.".$key]['checked']))
                    {
                        print '<td';
                        $align=$extrafields->getAlignFlag($key);
                        if ($align) print ' align="'.$align.'"';
                        print '>';
                        $tmpkey='options_'.$key;
                        print $extrafields->showOutputField($key, $obj->$tmpkey, '', 1);
                        print '</td>';
                        if (! $i) $totalarray['nbfield']++;
                        if (! empty($val['isameasure']))
                        {
                            if (! $i) $totalarray['pos'][$totalarray['nbfield']]='ef.'.$tmpkey;
                            $totalarray['val']['ef.'.$tmpkey] += $obj->$tmpkey;
                        }
                    }
                }
            }
            // Fields from hook
            $parameters=array('arrayfields'=>$arrayfields, 'obj'=>$obj);
            $reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);    // Note that $action and $object may have been modified by hook
            print $hookmanager->resPrint;
            // Rest of fields
            foreach($object->fields as $key => $val)
            {
                if (! in_array($key, array('date_creation', 'tms', 'import_key', 'status'))) continue;
                $align='';
                if (in_array($val['type'], array('date','datetime','timestamp'))) $align.=($align?' ':'').'center';
                if (in_array($val['type'], array('timestamp'))) $align.=($align?' ':'').'nowrap';
                if ($key == 'status') $align.=($align?' ':'').'center';
                if (! empty($arrayfields['t.'.$key]['checked']))
                {
                    print '<td'.($align?' class="'.$align.'"':'').'>';
                    if (in_array($val['type'], array('date','datetime','timestamp'))) print dol_print_date($db->jdate($obj->$key), 'dayhour');
                    elseif ($key == 'status') print $object->getLibStatut(3);
                    else print $obj->$key;
                    print '</td>';
                    if (! $i) $totalarray['nbfield']++;
                    if (! empty($val['isameasure']))
                    {
                        if (! $i) $totalarray['pos'][$totalarray['nbfield']]='t.'.$key;
                        $totalarray['val']['t.'.$key] += $obj->$key;
                    }
                }
            }
            // Action column
            print '<td class="nowrap" align="center">';
            if ($massactionbutton || $massaction)   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
            {
                $selected=0;
                if (in_array($obj->rowid, $arrayofselected)) $selected=1;
                print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
            }
            print '</td>';
            if (! $i) $totalarray['nbfield']++;
            
            print '</tr>';
        }
        $i++;
    }
    
    return $num;
}
