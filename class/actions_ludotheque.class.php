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
 * \file    htdocs/modulebuilder/template/class/actions_mymodule.class.php
 * \ingroup mymodule
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */
require_once DOL_DOCUMENT_ROOT.'/main.inc.php';
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
dol_include_once('/ludotheque/class/ludotheque.class.php');
/**
 * Class ActionsMyModule
 */
class ActionsLudotheque
{
    /**
     * @var DoliDB Database handler.
     */
    public $db;
    /**
     * @var string Error
     */
    public $error = '';
    /**
     * @var array Errors
     */
    public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
	    $this->db = $db;
	}
	
	public function test(&$object, $extrafields, $searchActive = 0)
	{
	    
	    require_once DOL_DOCUMENT_ROOT.'/main.inc.php';
	    require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
	    require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
	    require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
	    dol_include_once('/ludotheque/class/ludotheque.class.php');
	    dol_include_once('/ludotheque/class/produit.class.php');
	    
	    $langs->loadLangs(array("ludotheque","other"));
	    
	    $sortfield = GETPOST('sortfield','alpha');
	    $sortorder = GETPOST('sortorder','alpha');
	    
	    
	    // Definition of fields for list
	    $arrayfields=array();
	    foreach($object->fields as $key => $val)
	    {
	        // If $val['visible']==0, then we never show the field
	        if (! empty($val['visible'])) $arrayfields['t.'.$key]=array('label'=>$val['label'], 'checked'=>(($val['visible']<0)?0:1), 'enabled'=>$val['enabled']);
	    }
	    // Extra fields
	    if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
	    {
	        foreach($extrafields->attribute_label as $key => $val)
	        {
	            $arrayfields["ef.".$key]=array('label'=>$extrafields->attribute_label[$key], 'checked'=>$extrafields->attribute_list[$key], 'position'=>$extrafields->attribute_pos[$key], 'enabled'=>$extrafields->attribute_perms[$key]);
	        }
	    }
	    
	    if (GETPOST('cancel')) { $action='list'; $massaction=''; }
	    if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }
	    
	    $form=new Form($this->db);
	    
	    $now=dol_now();
	    
	    // Build and execute select
	    // --------------------------------------------------------------------
	    $sql = 'SELECT ';
	    foreach($object->fields as $key => $val)
	    {
	        $sql.='t.'.$key.', ';
	    }
	    // Add fields from extrafields
	    foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ", ef.".$key.' as options_'.$key : '');
	    // Add fields from hooks
	    $parameters=array();
	    //$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
	    $sql.=$hookmanager->resPrint;
	    $sql=preg_replace('/, $/','', $sql);
	    $sql.= " FROM ".MAIN_DB_PREFIX;
	    if (get_class($object) == 'Produit') $sql.= "produit";
	    else $sql.="ludotheque";
	    $sql.= " as t";
	    if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."myobject_extrafields as ef on (t.rowid = ef.fk_object)";
	    $sql.= " WHERE 1 IN (".getEntity('myobject').")";
    	if ($searchActive === 1)
    	{
    	    foreach($search as $key => $val)
    	    {
    	        if ($search[$key] != '') $sql.=natural_search($key, $search[$key], (($key == 'status')?2:($object->fields[$key]['type'] == 'integer'?1:0)));
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
    	}
    	
	    // Add where from hooks
	    $parameters=array();
	    $sql.=$this->db->order($sortfield,$sortorder);
	    
	    // Count total nb of records
	    $nbtotalofrecords = '';
	    if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
	    {
	        $result = $this->db->query($sql);
	        $nbtotalofrecords = $this->db->num_rows($result);
	    }
	    
	    $sql.= $this->db->plimit($limit+1, $offset);
	    
	    dol_syslog($script_file, LOG_DEBUG);
	    
	    $resql=$this->db->query($sql);
	    if (! $resql)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $num = $this->db->num_rows($resql);
	    
	    // --------------------- Affichage ---------------------
	    $arrayofselected=is_array($toselect)?$toselect:array();
	    
	    $param='';
	    if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
	    if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
	    if ($searchActive === 1)
	    {
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
	    
	    print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);
	    
	    if ($sall)
	    {
	        foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
	        print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
	    }
	    
	    $moreforfilter = '';
	    /*$moreforfilter.='<div class="divsearchfield">';
	     $moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
	     $moreforfilter.= '</div>';
	     
	     $parameters=array();
	     $reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
	     if (empty($reshook)) $moreforfilter .= $hookmanager->resPrint;
	     else $moreforfilter = $hookmanager->resPrint;
	     
	     if (! empty($moreforfilter))
	     {
	     print '<div class="liste_titre liste_titre_bydiv centpercent">';
	     print $moreforfilter;
	     print '</div>';
	     }
	     */
	    $varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
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
	    
	    // Loop on record
	    // --------------------------------------------------------------------
	    $i=0;
	    $totalarray=array();
	    while ($i < min($num, $limit))
	    {
	        $obj = $this->db->fetch_object($resql);
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
	                    if (in_array($val['type'], array('date','datetime','timestamp'))) print dol_print_date($this->db->jdate($obj->$key), 'dayhour');
	                    elseif ($key == 'ref') print $object->getNomUrl(1);
	                    elseif ($key == 'status') print $object->getLibStatut(3);
	                    else
	                    {
	                        if ($val['label'] == 'Libelle')
	                        {
	                            print '<a href="ludotheque_card.php?action=info&id='.$obj->rowid.'">';
	                            $lien = true;
	                        }
	                        print $obj->$key;
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
	                    if (in_array($val['type'], array('date','datetime','timestamp'))) print dol_print_date($this->db->jdate($obj->$key), 'dayhour');
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
	    
	    // Show total line
	    if (isset($totalarray['pos']))
	    {
	        print '<tr class="liste_total">';
	        $i=0;
	        while ($i < $totalarray['nbfield'])
	        {
	            $i++;
	            if (! empty($totalarray['pos'][$i]))  print '<td align="right">'.price($totalarray['val'][$totalarray['pos'][$i]]).'</td>';
	            else
	            {
	                if ($i == 1)
	                {
	                    if ($num < $limit) print '<td align="left">'.$langs->trans("Total").'</td>';
	                    else print '<td align="left">'.$langs->trans("Totalforthispage").'</td>';
	                }
	                print '<td></td>';
	            }
	        }
	        print '</tr>';
	    }
	    
	    // If no record found
	    if ($num == 0)
	    {
	        $colspan=1;
	        foreach($arrayfields as $key => $val) { if (! empty($val['checked'])) $colspan++; }
	        print '<tr><td colspan="'.$colspan.'" class="opacitymedium">'.$langs->trans("NoRecordFound").'</td></tr>';
	    }
	    
	    
	    $this->db->free($resql);
	    
	    $parameters=array('arrayfields'=>$arrayfields, 'sql'=>$sql);
	    $reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
	    print $hookmanager->resPrint;
	    
	    print '</table>'."\n";
	    print '</div>'."\n";
	    
	    print '</form>'."\n";
	}
	
	// Affiche une liste d'objet du type de $object
	public function printList($sql, &$object)
	{
	    //$langs->loadLangs(array("ludotheque","other"));
	    
	    $arrayfields=array();
	    $objClass = get_class($object);
	    
	    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	    print '<input type="hidden" name="action" value="addProduitInOneLudo">';
	    
	    print '<div class="div-table-responsive">';
	    print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";
	    
	    // ----------------------------------- Affichage des entêtes -----------------------------------
	    
	    
	    
	    if ($objClass == 'Produit');
	    {
	        $produit = new Produit($this->db);
	        
	        print '<tr class="liste_titre">';
	        
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
	        
	        print '<th>';
	        print '<input class="button" type="submit" name="add" value="Ajouter">';
	        print '</th>';
	        
	        print '</tr>';
	        
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
	        $ludo = new Ludotheque($this->db);
	        
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
	    $res = $this->db->query($sql);
	    if (! $res)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $num = $this->db->num_rows($res);
	    
	    // ----------------------------------- Affichage des résultats -----------------------------------
	    
	    $i = 0;
	    $table = array();
	    while($i < $num)
	    {
	        $obj = $this->db->fetch_object($res);
	        if (! $obj)
	        {
	            dol_print_error($this->db);
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
	            
	            if (in_array($key, array('date_achat'))) print dol_print_date($this->db->jdate($obj->$key), 'dayhour');
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
	        print '<tr><td colspan="'.$colspan.'" class="opacitymedium">'./*$langs->trans("*/NoRecordFound/*")*/.'</td></tr>';
	    }
	    
	    print '</table>'."\n";
	    print '</div>'."\n";
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

        /*
		print_r($parameters);
		print_r($object);
		echo "action: " . $action;
        */

	    if (in_array($parameters['currentcontext'], array('somecontext1','somecontext2'))) {    // do something only for the context 'somecontext1' or 'somecontext2'


		}

		if (! $error) {
			$this->results = array('myreturn' => 999);
			$this->resprints = 'A text to show';
			return 0;                                    // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}


	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doMassActions($parameters, &$object, &$action, $hookmanager)
	{
	    global $conf, $user, $langs;

	    $error = 0; // Error counter

        /*
	    print_r($parameters);
	    print_r($object);
	    echo "action: " . $action;
        */

	    if (in_array($parameters['currentcontext'], array('somecontext1','somecontext2'))) {  // do something only for the context 'somecontext1' or 'somecontext2'

	        foreach($parameters['toselect'] as $objectid)
	        {
	            // Do action on each object id

	        }
	    }

	    if (! $error) {
	        $this->results = array('myreturn' => 999);
	        $this->resprints = 'A text to show';
	        return 0;                                    // or return 1 to replace standard code
	    } else {
	        $this->errors[] = 'Error message';
	        return -1;
	    }
	}


	/**
	 * Overloading the addMoreMassActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreMassActions($parameters, &$object, &$action, $hookmanager)
	{
	    global $conf, $user, $langs;

	    $error = 0; // Error counter

	    if (in_array($parameters['currentcontext'], array('somecontext1','somecontext2')))  // do something only for the context 'somecontext'
	    {
	        $this->resprints = '<option value="0"'.($disabled?' disabled="disabled"':'').'>'.$langs->trans("MyModuleMassAction").'</option>';
	    }

	    if (! $error) {
	        return 0;                                    // or return 1 to replace standard code
	    } else {
	        $this->errors[] = 'Error message';
	        return -1;
	    }
	}



}
