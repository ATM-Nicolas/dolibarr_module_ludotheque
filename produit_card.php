<?php
/* Copyright (C) 2007-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *   	\file       htdocs/modulebuilder/template/myobject_card.php
 *		\ingroup    mymodule
 *		\brief      Page to create/edit/view myobject
 */

//if (! defined('NOREQUIREUSER'))          define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))            define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))           define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))          define('NOREQUIRETRAN','1');
//if (! defined('NOSCANGETFORINJECTION'))  define('NOSCANGETFORINJECTION','1');			// Do not check anti CSRF attack test
//if (! defined('NOSCANPOSTFORINJECTION')) define('NOSCANPOSTFORINJECTION','1');			// Do not check anti CSRF attack test
//if (! defined('NOCSRFCHECK'))            define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))           define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL'))         define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))          define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))          define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))          define('NOREQUIREAJAX','1');         // Do not load ajax.lib.php library
//if (! defined("NOLOGIN"))                define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php');

dol_include_once('/ludotheque/lib/produit.lib.php');

dol_include_once('/ludotheque/class/produit.class.php');

// Load traductions files requiredby by page
$langs->loadLangs(array("mymodule","other"));

// Get parameters
$id			= GETPOST('id', 'int');

$action		= GETPOST('action', 'alpha');
$cancel     = GETPOST('cancel', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');

$modifier   = GETPOST('modifier', 'aZ09');

// Initialize technical objects
$object=new Produit($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction=$conf->ludotheque->dir_output . '/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array('myobjectcard'));     // Note that conf->hooks_modules contains array
// Fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('myobject');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Initialize array of search criterias
$search_all=trim(GETPOST("search_all",'alpha'));
$search=array();
foreach($object->fields as $key => $val)
{
    if (GETPOST('search_'.$key,'alpha')) $search[$key]=GETPOST('search_'.$key,'alpha');
}

if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'mymodule', $id);

// fetch optionals attributes and labels
//$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
//include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

/*
 * ACTIONS
 *
 * Put here all code to do according to value of "action" parameter
 */

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	$error=0;

	if ($cancel)
	{
		if ($action != 'addlink' && $action != 'update')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/ludotheque/produit_list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($action == 'update')
		{
		    $urltogo=$backtopage?$backtopage:dol_buildpath('/ludotheque/produit_card.php?action=info&id='.$id,1);
		    header("Location: ".$urltogo);
		    exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}
	
	if ($action == 'buy')
	{
	    // Modifier l'enregistrement pour ajouter la date d'achat
	    //$object->fetch($id);
	    $object->updateDate($id);
	    $action = 'info';
	}
	
	// Action to add record
	if ($action == 'add' && ! empty($user->rights->ludotheque->create))
	{
        foreach ($object->fields as $key => $val)
        {
            if (in_array($key, array('rowid', 'date_achat'))) continue;	// Ignore special fields

            $object->$key=GETPOST($key,'alpha');
            
            if ($val['notnull'] && $object->$key == '')
            {
                $error++;
                //setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv($val['label'])), null, 'errors');
                setEventMessages($langs->trans("FieldRequired",$langs->transnoentitiesnoconv($val['label'])), null, 'errors');
            }
        }

		if (! $error)
		{
			$result=$object->create($user, GETPOST('libelle'), GETPOST('fk_categorie'), GETPOST('description'), GETPOST('fk_emplacement'));
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/ludotheque/produit_list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			else
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}
	
	// Action to update record
	if ($action == 'update' && ! empty($user->rights->ludotheque->create))
	{
	    foreach ($object->fields as $key => $val)
        {
            $object->$key=GETPOST($key,'alpha');
            //if ($key == 'description' && empty($object->$key)) $object->$key = '-';
            
            if (in_array($key, array('rowid', 'date_achat'))) continue;
            if ($val['notnull'] && $object->$key == '')
            {
                $error++;
                //setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv($val['label'])), null, 'errors');
                setEventMessages($langs->trans("FieldRequired",$langs->transnoentitiesnoconv($val['label'])), null, 'errors');
            }
        }

		if (! $error)
		{
			$result=$object->update($user, $id, $object->fk_categorie, $object->libelle, $object->description, $object->fk_emplacement);
			if ($result > 0)
			{
			    //$action='info';
			    // Creation OK
			    $urltogo=$backtopage?$backtopage:dol_buildpath('/ludotheque/produit_card.php?action=info&id='.$id,1);
			    header("Location: ".$urltogo);
			    exit;
			}
			else
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete' && ! empty($user->rights->ludotheque->delete))
	{
		$result=$object->deleteCommon($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/ludotheque/produit_list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
}


/*
 * VIEW
 *
 * Put here all code to build page
 */

$form=new Form($db);

llxHeader('','Produit','');

$produitHead = produitAdminPrepareHead();

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';

if ($action == 'moreInfo' && ! empty($id))
{
    $object->fetch($id);
    
    print load_fiche_titre($langs->trans("MyLudo"));
    
    dol_fiche_head($produitHead, 'events');
    
    $linkback = '<a href="'.DOL_URL_ROOT.'/custom/ludotheque/produit_list.php">'.$langs->trans("BackToList").'</a>';
    
    dol_banner_tab($object, 'action=moreInfo&id', $linkback, ($user->societe_id?0:1), 'rowid', 'libelle');
    
    print '<table class="border centpercent">'."\n";
    foreach($object->fields as $key => $val)
    {
        if (! in_array($key, array('fk_user_creat', 'fk_user_modif', 'tms', 'date_creat'))) continue;
        print '<tr><td';
        print ' class="titlefieldcreate"';
        
        print '>'.$langs->trans($val['label']).'</td><td>';
        
        if ($key == 'date_creat' || $key == 'tms')
            print dol_print_date($db->jdate($object->$key), 'dayhour');
            if ($key == 'fk_user_creat')
            {
                print '<a href="../../user/card.php?id='.$object->fk_user_creat.'">';
                print img_picto('', 'object_user.png').' ';
                print $object->getUserLibelle($object->fk_user_creat);
                print '</a>';
            }
            if ($key == 'fk_user_modif')
            {
                print '<a href="../../user/card.php?id='.$object->fk_user_modif.'">';
                print img_picto('', 'object_user.png').' ';
                print $object->getUserLibelle($object->fk_user_modif);
                print '</a>';
            }
            
            print '</td></tr>';
    }
    print '</table>'."\n";
    
    dol_fiche_end();
}

if ($action == 'info' && ! empty($id))
{
    $object->fetch($id);
    
    print load_fiche_titre($langs->trans("MyProduct"));
    
    dol_fiche_head($produitHead, 'card');
    
    $linkback = '<a href="'.DOL_URL_ROOT.'/custom/ludotheque/produit_list.php">'.$langs->trans("BackToList").'</a>';
    
    dol_banner_tab($object, 'action=info&id', $linkback, ($user->societe_id?0:1), 'rowid', 'libelle');
    
    $object->fetch($id);
    $libEmpl = $object->getOneEmplacementLibelle($object->fk_emplacement);
    $libCat = $object->getOneCategorieLibelle($object->fk_categorie);
    
    print '<input type="hidden" name="id" value="'.$object->rowid.'">';
    
    print '<table class="border centpercent">'."\n";
    foreach($object->fields as $key => $val)
    {
        if (in_array($key, array('rowid', 'libelle', 'tms', 'date_creat', 'fk_user_creat', 'fk_user_modif'))) continue;
        if ($key == 'date_achat' && empty($object->$key)) continue;
        print '<tr><td';
        print ' class="titlefieldcreate';
        if ($val['notnull']) print ' fieldrequired';
        print '"';
        print '>'.$langs->trans($val['label']).'</td><td>';
        
        switch($key)
        {
            case 'fk_categorie':
                print $libCat;
                break;
            case 'libelle':
                print $object->libelle;
                break;
            case 'description':
                print preg_replace('/\n/', '<br>', $object->description);
                break;
            case 'date_achat':
                print dol_print_date($db->jdate($object->date_achat), 'dayhour');
                break;
            case 'fk_emplacement':
                print '<a href="ludotheque_card.php?action=info&id='.$object->fk_emplacement.'">';
                print $libEmpl;
                print '</a>';
                break;
            default:
                print '';
        }
        
        print '</td></tr>';
    }
    print '</table>'."\n";
    
    dol_fiche_end();
    
    print '<div class="center">';
/*    if(empty($object->date_achat))
        print '<a class="button" href="produit_card.php?action=buy&id='.$id.'">Acheter</a> &nbsp; ';*/
//        print '<input type="submit" class="butAction" name="buy" value="'.dol_escape_htmltag($langs->trans("Buy")).'">';
//    print '<input type="submit" class="button" name="modifier" value="'.dol_escape_htmltag($langs->trans("Edit")).'"> &nbsp; ';
    print '<a class="button" href="produit_card.php?action=edit&id='.$id.'">Modifier</a> &nbsp; ';
//    print '<input type="submit" class="butAction" name="cancel" value="'.dol_escape_htmltag($langs->trans("Cancel")).'"></div>';
    print '<a class="button" href="produit_list.php">Retour liste</a>';
    print '</div>';
    
    print '</form>';
}

// Part to create
if ($action == 'create')
{
    $tab = $object->getAllEmplacementLibelle($db);
    
	print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("Produit")));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head(array(), '');

	print '<table class="border centpercent">'."\n";
	foreach($object->fields as $key => $val)
	{
	    if (in_array($key, array('rowid', 'date_achat', 'fk_user_creat', 'date_creat', 'fk_user_modif', 'tms'))) continue;

	    // Sélection de l'emplacement dans une liste déroulante
	    if (in_array($key, array('fk_emplacement')))
	    {	        
	        print '<tr><td class="titlefieldcreate">'.$langs->trans($val['label']).'</td>';
	        print '<td>';
	        
	        print $form->selectarray('fk_emplacement', $tab);
	        
	        print '</td></tr>';
	    }
	    // Sélection de la catégorie dans une liste déroulante
	    else if (in_array($key, array('fk_categorie')))
	    {
	        print '<tr><td class="titlefieldcreate fieldrequired">'.$langs->trans($val['label']).'</td>';
	        print '<td>';
	        
	        $tabCat = $object->getAllCategories();
	        
	        print $form->selectarray('fk_categorie', $tabCat, 'Jeu');
	        
	        print '</td></tr>';
	    }
	    else if (in_array($key, array('description')))
	    {
	        print '<tr><td class="titlefieldcreate fieldrequired">'.$langs->trans($val['label']).'</td>';
	        
	        print '<td>';
	        
	        $doleditor = new DolEditor('description', '', '', 80, 'dolibarr_notes', 'In', 0, false, true, ROWS_3, '90%');
	        print $doleditor->Create(1);
	        
	        //print '<textarea class="quatrevingtpercent" name="'.$key.'" id="'.$key.'" style="height: 60px;"></textarea>';
	        print '</td></tr>';
	    }
	    else
	    {
	        print '<tr><td';
	        print ' class="titlefieldcreate';
	        if ($val['notnull']) print ' fieldrequired';
	        print '"';
	        print '>'.$langs->trans($val['label']).'</td><td><input class="flat" type="text" name="'.$key.'" value="'.(GETPOST($key,'alpha')?GETPOST($key,'alpha'):'').'"></td></tr>';
	    }
    	
	}
	
	
	
	
	print '</table>'."\n";

	dol_fiche_end();
	
	print '<div class="center">';
	print '<input type="submit" class="button" name="add" value="'.dol_escape_htmltag($langs->trans("Create")).'"> &nbsp;';
	print '<input type="submit" class="button" name="cancel" value="'.dol_escape_htmltag($langs->trans("Cancel")).'">';
	print '</div>';

	print '</form>';
}

// Part to edit record
if (($id || $ref) && $action == 'edit')
{
    $tab = $object->getAllEmplacementLibelle($db);
    $object->fetch($id);
    
	print load_fiche_titre($langs->trans("MyProduct"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	//print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	
	foreach($object->fields as $key => $val)
	{
	    
	    if (in_array($key, array('rowid', 'date_achat', 'tms', 'date_creat', 'fk_user_creat', 'fk_user_modif'))) continue;
	    
	    // Sélection de l'emplacement dans une liste déroulante
	    if (in_array($key, array('fk_emplacement')))
	    {
	        print '<tr><td class="titlefieldcreate">'.$langs->trans($val['label']).'</td>';
	        print '<td>';
	        
	        print $form->selectarray('fk_emplacement', $tab, $object->fk_emplacement);
	        
	        print '</td></tr>';
	    }
	    // Sélection de la catgorie dans une liste déroulante
	    else if (in_array($key, array('fk_categorie')))
	    {
	        print '<tr><td class="titlefieldcreate fieldrequired">'.$langs->trans($val['label']).'</td>';
	        print '<td>';
	        
	        $tabCat = $object->getAllCategories();
	        
	        print $form->selectarray('fk_categorie', $tabCat, $object->$key);
	        
	        print '</td></tr>';
	    }
	    else if (in_array($key, array('description')))
	    {
	        print '<tr><td class="titlefieldcreate fieldrequired">'.$langs->trans($val['label']).'</td>';
	        
	        /*$doleditor = new DolEditor('description', $val, '', 80, 'dolibarr_notes', 'In', 0, false, true, ROWS_3, '90%');
	        print $doleditor->Create(1);*/
	        
	        print '<td><textarea class="quatrevingtpercent" name="'.$key.'" id="'.$key.'" style="height: 60px;">'.$object->$key.'</textarea>';
	        print '</td></tr>';
	    }
	    else
	    {
	        print '<tr><td';
	        print ' class="titlefieldcreate';
	        if ($val['notnull']) print ' fieldrequired';
	        print '"';
	        print '>'.$langs->trans($val['label']).'</td><td><input class="flat" type="text" name="'.$key.'" value="'.$object->$key.'"></td></tr>';
	    }
	    
	}
	
	// LIST_OF_TD_LABEL_FIELDS_EDIT
	print '</table>';

	dol_fiche_end();

	print '<div class="center">';
	print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'"> &nbsp; ';
	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}

// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
    $res = $object->fetch_optionals($object->id, $extralabels);

	$head = mymodule_prepare_head($object);
	dol_fiche_head($head, 'order', $langs->trans("CustomerOrder"), -1, 'order');

	$formconfirm = '';

	// Confirmation to delete
	if ($action == 'delete') {
	    $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteOrder'), $langs->trans('ConfirmDeleteOrder'), 'confirm_delete', '', 0, 1);
	}

	// Confirmation of action xxxx
	if ($action == 'xxx')
	{
	    $formquestion=array();
	    /*
	        $formquestion = array(
	            // 'text' => $langs->trans("ConfirmClone"),
	            // array('type' => 'checkbox', 'name' => 'clone_content', 'label' => $langs->trans("CloneMainAttributes"), 'value' => 1),
	            // array('type' => 'checkbox', 'name' => 'update_prices', 'label' => $langs->trans("PuttingPricesUpToDate"), 'value' => 1),
	            // array('type' => 'other',    'name' => 'idwarehouse',   'label' => $langs->trans("SelectWarehouseForStockDecrease"), 'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse')?GETPOST('idwarehouse'):'ifone', 'idwarehouse', '', 1)));
	    }*/
	    $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('XXX'), $text, 'confirm_xxx', $formquestion, 0, 1, 220);
	}

	if (! $formconfirm) {
	    $parameters = array('lineid' => $lineid);
	    $reshook = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	    if (empty($reshook)) $formconfirm.=$hookmanager->resPrint;
	    elseif ($reshook > 0) $formconfirm=$hookmanager->resPrint;
	}

	// Print form confirm
	print $formconfirm;



	// Object card
	// ------------------------------------------------------------

	$linkback = '<a href="' . DOL_URL_ROOT . '/custom/ludotheque/produit_list.php' . (! empty($socid) ? '?socid=' . $socid : '') . '">' . $langs->trans("BackToList") . '</a>';


	$morehtmlref='<div class="refidno">';
	/*
	// Ref bis
	$morehtmlref.=$form->editfieldkey("RefBis", 'ref_client', $object->ref_client, $object, $user->rights->mymodule->creer, 'string', '', 0, 1);
	$morehtmlref.=$form->editfieldval("RefBis", 'ref_client', $object->ref_client, $object, $user->rights->mymodule->creer, 'string', '', null, null, '', 1);
	// Thirdparty
	$morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . $soc->getNomUrl(1);
	// Project
	if (! empty($conf->projet->enabled))
	{
	    $langs->load("projects");
	    $morehtmlref.='<br>'.$langs->trans('Project') . ' ';
	    if ($user->rights->mymodule->creer)
	    {
	        if ($action != 'classify')
	        {
	            $morehtmlref.='<a href="' . $_SERVER['PHP_SELF'] . '?action=classify&amp;id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetProject')) . '</a> : ';
	            if ($action == 'classify') {
	                //$morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'projectid', 0, 0, 1, 1);
	                $morehtmlref.='<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
	                $morehtmlref.='<input type="hidden" name="action" value="classin">';
	                $morehtmlref.='<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	                $morehtmlref.=$formproject->select_projects($object->socid, $object->fk_project, 'projectid', $maxlength, 0, 1, 0, 1, 0, 0, '', 1);
	                $morehtmlref.='<input type="submit" class="button valignmiddle" value="'.$langs->trans("Modify").'">';
	                $morehtmlref.='</form>';
	            } else {
	                $morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'none', 0, 0, 0, 1);
	            }
	        }
	    } else {
	        if (! empty($object->fk_project)) {
	            $proj = new Project($db);
	            $proj->fetch($object->fk_project);
	            $morehtmlref.='<a href="'.DOL_URL_ROOT.'/projet/card.php?id=' . $object->fk_project . '" title="' . $langs->trans('ShowProject') . '">';
	            $morehtmlref.=$proj->ref;
	            $morehtmlref.='</a>';
	        } else {
	            $morehtmlref.='';
	        }
	    }
	}
	*/
	$morehtmlref.='</div>';


	dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);


	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	// LIST_OF_TD_LABEL_FIELDS_VIEW


	// Other attributes
	include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_view.tpl.php';

	print '</table>';
	print '</div>';
	print '<div class="fichehalfright">';
	print '<div class="ficheaddleft">';
	print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent">';



	print '</table>';
	print '</div>';
	print '</div>';
	print '</div>';

	print '<div class="clearboth"></div><br>';

    dol_fiche_end();


	// Buttons for actions
	if ($action != 'presend' && $action != 'editline') {
    	print '<div class="tabsAction">'."\n";
    	$parameters=array();
    	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
    	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

    	if (empty($reshook))
    	{
    	    // Send
            print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&action=presend&mode=init#formmailbeforetitle">' . $langs->trans('SendByMail') . '</a></div>'."\n";

    		if ($user->rights->mymodule->write)
    		{
    			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
    		}

    		if ($user->rights->mymodule->delete)
    		{
    			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
    		}
    	}
    	print '</div>'."\n";
	}


	// Select mail models is same action as presend
	if (GETPOST('modelselected')) {
	    $action = 'presend';
	}

	if ($action != 'presend')
	{
	    print '<div class="fichecenter"><div class="fichehalfleft">';
	    print '<a name="builddoc"></a>'; // ancre
	    // Documents
	    $comref = dol_sanitizeFileName($object->ref);
	    $relativepath = $comref . '/' . $comref . '.pdf';
	    $filedir = $conf->mymodule->dir_output . '/' . $comref;
	    $urlsource = $_SERVER["PHP_SELF"] . "?id=" . $object->id;
	    $genallowed = $user->rights->mymodule->creer;
	    $delallowed = $user->rights->mymodule->supprimer;
	    print $formfile->showdocuments('mymodule', $comref, $filedir, $urlsource, $genallowed, $delallowed, $object->modelpdf, 1, 0, 0, 28, 0, '', '', '', $soc->default_lang);


	    // Show links to link elements
	    $linktoelem = $form->showLinkToObjectBlock($object, null, array('order'));
	    $somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);


	    print '</div><div class="fichehalfright"><div class="ficheaddleft">';

	    // List of actions on element
	    include_once DOL_DOCUMENT_ROOT . '/core/class/html.formactions.class.php';
	    $formactions = new FormActions($db);
	    $somethingshown = $formactions->showactions($object, 'order', $socid);

	    print '</div></div></div>';
	}
	
}


// End of page
llxFooter();
$db->close();
