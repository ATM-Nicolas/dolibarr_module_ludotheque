<?php
/* Copyright (C) 2007-2017  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016  Juanjo Menent       <jmenent@2byte.es>
 * Copyright (C) 2015       Florian Henry       <florian.henry@open-concept.pro>
 * Copyright (C) 2015       Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
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
 * \file        htdocs/modulebuilder/template/class/myobject.class.php
 * \ingroup     mymodule
 * \brief       This file is a CRUD class file for MyObject (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class for MyObject
 */
class Produit extends CommonObject
{
	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'produit';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'ludotheque_produit';

	/**
	 * @var array  Does this field is linked to a thirdparty ?
	 */
	protected $isnolinkedbythird = 1;
	/**
	 * @var array  Does myobject support multicompany module ? 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
	 */
	protected $ismultientitymanaged = 1;
	/**
	 * @var string String with name of icon for myobject
	 */
	public $picto = 'myobject';


	/**
	 *             'type' if the field format, 'label' the translation key, 'enabled' is a condition when the filed must be managed,
	 *             'visible' says if field is visible in list (-1 means not shown by default but can be aded into list to be viewed)
	 *             'notnull' if not null in database
	 *             'index' if we want an index in database
	 *             'position' is the sort order of field
	 *             'searchall' is 1 if we want to search in this field when making a search from the quick search button
	 *             'isameasure' must be set to 1 if you want to have a total on list for this field. Field type must be summable like integer or double(24,8).
	 *             'comment' is not used. You can store here any text of your choice.
	 */

	// BEGIN MODULEBUILDER PROPERTIES
	/**
     * @var array  Array with all fields and their property
     */
	public $fields=array(
	    'rowid'         =>array('type'=>'integer',      'label'=>'TechnicalID',      'enabled'=>1, 'visible'=>-1, 'notnull'=>true, 'index'=>true, 'position'=>1,  'comment'=>'Id'),
	    'libelle'       =>array('type'=>'varchar(255)', 'label'=>'Libelle',          'enabled'=>1, 'visible'=>1,  'notnull'=>true),
	    'fk_categorie'  =>array('type'=>'integer',      'label'=>'Categorie',        'enabled'=>1, 'visible'=>1,  'notnull'=>true),
	    'description'   =>array('type'=>'varchar(255)', 'label'=>'Description',      'enabled'=>1, 'visible'=>1,  'notnull'=>true),
	    'date_achat'    =>array('type'=>'datetime',     'label'=>'DateAchat',        'enabled'=>1, 'visible'=>1,  'notnull'=>true),
	    'fk_emplacement'=>array('type'=>'integer',      'label'=>'Emplacement',      'enabled'=>1, 'visible'=>1,  'notnull'=>false, 'index'=>true),
	    'fk_user_creat' =>array('type'=>'integer',      'label'=>'userCreat',        'enabled'=>1, 'visible'=>-1, 'notnull'=>false),
	    
/*		'ref'           =>array('type'=>'varchar(64)',  'label'=>'Ref',              'enabled'=>1, 'visible'=>1,  'notnull'=>true, 'index'=>true, 'position'=>10, 'searchall'=>1, 'comment'=>'Reference of object'),
	    'entity'        =>array('type'=>'integer',      'label'=>'Entity',           'enabled'=>1, 'visible'=>0,  'notnull'=>true, 'index'=>true, 'position'=>20),
	    'label'         =>array('type'=>'varchar(255)', 'label'=>'Label',            'enabled'=>1, 'visible'=>1,  'position'=>30,  'searchall'=>1),
	    'qty'           =>array('type'=>'double(24,8)', 'label'=>'Qty',              'enabled'=>1, 'visible'=>1,  'position'=>40,  'searchall'=>0, 'isameasure'=>1),
	    'status'        =>array('type'=>'integer',      'label'=>'Status',           'enabled'=>1, 'visible'=>1,  'index'=>true,   'position'=>1000),
*/		'date_creat'    =>array('type'=>'datetime',     'label'=>'DateCreation',     'enabled'=>1, 'visible'=>-1, 'notnull'=>false, 'position'=>500),
	    'fk_user_modif' =>array('type'=>'integer',      'label'=>'userModif',        'enabled'=>1, 'visible'=>-1, 'notnull'=>false),
	    'tms'           =>array('type'=>'timestamp',    'label'=>'DateModification', 'enabled'=>1, 'visible'=>-1, 'notnull'=>false, 'position'=>500),
/*		'import_key'    =>array('type'=>'varchar(14)',  'label'=>'ImportId',         'enabled'=>1, 'visible'=>-1,  'index'=>true,  'position'=>1000, 'nullifempty'=>1),
*/
	);
	// END MODULEBUILDER PROPERTIES

	// If this object has a subtable with lines

	/**
	 * @var int    Name of subtable line
	 */
	//public $table_element_line = 'myobjectdet';
	/**
	 * @var int    Field with ID of parent key if this field has a parent
	 */
	//public $fk_element = 'fk_myobject';
	/**
	 * @var int    Name of subtable class that manage subtable lines
	 */
	//public $class_element_line = 'MyObjectline';
	/**
	 * @var array  Array of child tables (child tables to delete before deleting a record)
	 */
	//protected $childtables=array('myobjectdet');
	/**
	 * @var MyObjectLine[]     Array of subtable lines
	 */
	//public $lines = array();

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
	}
	
	function getUserLibelle($fk_user)
	{
	    $sql = 'SELECT u.lastname';
	    $sql .= ' FROM '.MAIN_DB_PREFIX.'user as u';
	    $sql .= ' WHERE u.rowid='.$fk_user.';';
	    
	    $res = $this->db->query($sql);
	    if (! $res)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $num = $this->db->num_rows($res);
	    if ($num == 0)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    $obj = $this->db->fetch_object($res);
	    
	    return $obj->lastname;
	}
	
	function getAllCategories()
	{
	    $limit = 26;
	    $sql = 'SELECT rowid, libelle';
	    $sql .= ' FROM '.MAIN_DB_PREFIX.'ludotheque_c_categorie_produit';
	    $sql .= ' WHERE 1 IN (1) ORDER BY rowid ASC LIMIT '.$limit.';';
	    
	    $res = $this->db->query($sql);
	    if (! $res)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $num = $this->db->num_rows($res);
	    if ($num == 0)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $i = 0;
	    $table = array();
	    while($i < min($num, $limit))
	    {
	        $obj = $this->db->fetch_object($res);
	        if ($obj)
	            $table[$obj->rowid] = $obj->libelle;
	        $i++;
	    }
	    
	    return $table;
	}
	
	function getOneCategorieLibelle($id)
	{
	    $limit = 26;
	    $sql = 'SELECT rowid, libelle';
	    $sql .= ' FROM '.MAIN_DB_PREFIX.'ludotheque_c_categorie_produit';
	    $sql .= ' WHERE rowid='.$id.' ORDER BY rowid ASC LIMIT '.$limit.';';
	    
	    $res = $this->db->query($sql);
	    if (! $res)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $num = $this->db->num_rows($res);
	    if ($num == 0)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $table = array();
	    $obj = $this->db->fetch_object($res);
	    if ($obj)
	        return $obj->libelle;
	    return false;
	}
    
	function getAllEmplacementLibelle(DoliDB $db)
	{
	    $limit = 26;
	    $sql = 'SELECT l.rowid, l.libelle';
	    /*foreach($this->fields as $key => $val)
	    {
	        $sql.='t.'.$key.', ';
	    }*/
	    $sql .= ' FROM '.MAIN_DB_PREFIX.'ludotheque_ludotheque as l';
	    $sql .= ' WHERE 1 IN (1) ORDER BY l.rowid ASC LIMIT '.$limit.';';
	    
	    $res = $db->query($sql);
	    if (! $res)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $num = $db->num_rows($res);
	    if ($num == 0)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $i = 0;
	    $table = array();
	    $table[0] = '';
	    while($i < min($num, $limit))
	    {
	        $obj = $db->fetch_object($res);
            if ($obj)
                $table[$obj->rowid] = $obj->libelle;
            $i++;
	    }
	    
	    return $table;
	}
	
	function getOneEmplacementLibelle($rowid)
	{
	    if (! $rowid)
	        return '';
	    
	    $limit = 26;
	    $sql = 'SELECT l.rowid, l.libelle';
	    /*foreach($this->fields as $key => $val)
	     {
	     $sql.='t.'.$key.', ';
	     }*/
	    $sql .= ' FROM '.MAIN_DB_PREFIX.'ludotheque_ludotheque as l';
	    $sql .= ' WHERE l.rowid='.$rowid.' ORDER BY l.rowid ASC LIMIT '.$limit.';';
	    
	    $res = $this->db->query($sql);
	    if (! $res)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $num = $this->db->num_rows($res);
	    if ($num == 0)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $obj = $this->db->fetch_object($res);
	    if ($obj)
	       return $obj->libelle;
	    return false;
	}
	
	function getAll()
	{
	    $limit = 26;
	    $sql = 'SELECT l.rowid, l.libelle, l.fk_user_creat, l.date_creat, l.fk_user_modif, l.tms';
	    $sql .= ' FROM '.MAIN_DB_PREFIX.'ludotheque_produit as l';
	    $sql .= ' ORDER BY l.rowid ASC;';
	    
	    $res = $this->db->query($sql);
	    if (! $res)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $num = $this->db->num_rows($res);
	    if ($num == 0)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $i = 0;
	    $tab = array();
	    while($i < $num)
	    {
	        $obj = $this->db->fetch_object($res);
	        
	        foreach($obj as $k => $v)
	        {
	            $ludo[$k] = $v;
	        }
	        $tab[] = $ludo;
	        $i++;
	    }
	    return $tab;
	}
	
	function getAllNullEmplacement()
	{
	    $limit = 26;
	    $sql = 'SELECT l.rowid, l.libelle, l.fk_user_creat, l.date_creat, l.fk_user_modif, l.tms';
	    $sql .= ' FROM '.MAIN_DB_PREFIX.'ludotheque_produit as l';
	    $sql .= ' WHERE l.fk_emplacement is NULL';
	    $sql .= ' ORDER BY l.rowid ASC;';
	    
	    $res = $this->db->query($sql);
	    if (! $res)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $num = $this->db->num_rows($res);
	    if ($num == 0)
	        return -1;
	    
	    $i = 0;
	    $table = array();
	    $table[0] = '';
	    while($i < min($num, $limit))
	    {
	        $obj = $this->db->fetch_object($res);
	        if ($obj)
	            $table[$obj->rowid] = $obj->libelle;
	            $i++;
	    }
	    return $table;
	}
	
	function fetch($id = 0, $ref = '', $ref_ext='', $ignore_expression = 0)
	{
	    $limit = 26;
	    $sql = 'SELECT ';
	    foreach($this->fields as $key => $val)
	    {
	        $sql .= 'p.'.$key;
	        if($key !== 'tms')
	           $sql .= ', ';
	            
	    }
	    $sql .= ' FROM '.MAIN_DB_PREFIX.'ludotheque_produit as p';
	    $sql .= ' WHERE p.rowid='.$id.';';
	    
	    $res = $this->db->query($sql);
	    if (! $res)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    
	    $num = $this->db->num_rows($res);
	    if ($num == 0)
	    {
	        return -1;
	    }
	    
	    $obj = $this->db->fetch_object($res);
	    
	    $this->rowid = $obj->rowid;
	    $this->fk_categorie = $obj->fk_categorie;
	    $this->libelle = $obj->libelle;
	    $this->description = $obj->description;
	    $this->date_achat = $obj->date_achat;
	    $this->fk_emplacement = $obj->fk_emplacement;
	    $this->fk_user_creat = $obj->fk_user_creat;
	    $this->date_creat = $obj->date_creat;
	    $this->fk_user_modif = $obj->fk_user_modif;
	    $this->tms = $obj->tms;
	    
	    $allProduit = $this->getAll();
	    
	    $i = 0;
	    while($i < count($allProduit))
	    {
	        if ($allProduit[$i]['rowid'] === $id)
	        {
	            if (($i+1) < count($allProduit))
	                $this->ref_next = $allProduit[$i+1]['rowid'];
	                if ($i > 0)
	                    $this->ref_previous = $allProduit[$i-1]['rowid'];
	        }
	        $i++;
	    }
	    
	    return true;
	}
	
	function updateDate($id)
	{
	    $sql = 'UPDATE '.MAIN_DB_PREFIX.'ludotheque_produit as p';
	    $sql .= ' SET p.date_achat='.$this->db->idate(dol_now());
	    $sql .= ' WHERE p.rowid='.$id.';';
	    
	    $res = $this->db->query($sql);
	    if (! $res)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    return true;
	}
	
	function update($user, $id, $idCat, $lib, $desc, $fk_empl)
	{
	    if (! $fk_empl)
	        $fk_empl = 'null';
	    $sql = 'UPDATE '.MAIN_DB_PREFIX.'ludotheque_produit as p';
	    $sql .= ' SET p.fk_categorie="'.$idCat;
	    $sql .= '", p.libelle="'.$lib;
	    $sql .= '", p.description="'.$desc;
	    $sql .= '", p.fk_emplacement='.$fk_empl;
	    $sql .= ', p.fk_user_modif='.$user->id;
	    $sql .= ', p.tms="'.$this->db->idate(dol_now()).'"';
	    
	    $sql .= ' WHERE p.rowid='.$id.';';
	    
	    $res = $this->db->query($sql);
	    if (! $res)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    return true;
	}
	
	function create($user, $lib, $fk_cat, $desc, $fk_empl)
	{
	    if ($fk_empl == 0)
	        $fk_empl = 'null';
	    $sql = 'INSERT INTO '.MAIN_DB_PREFIX.'ludotheque_produit';
	    $sql .= ' VALUES(null,'.$fk_cat.',"'.$lib.'","'.$desc.'",null,'.$fk_empl.', '.$user->id.', '.$this->db->idate(dol_now()).', null, null);';
	    
	    $res = $this->db->query($sql);
	    if (! $res)
	    {
	        dol_print_error($this->db);
	        exit;
	    }
	    return true;
	}

	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
     *  @param	int  	$notooltip			1=Disable tooltip
     *  @param  string  $morecss            Add more css on link
	 *	@return	string						String with URL
	 */
	function getNomUrl($withpicto=0, $option='', $notooltip=0, $morecss='')
	{
		global $db, $conf, $langs;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;

        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

        $result = '';
        $companylink = '';

        $label = '<u>' . $langs->trans("MyObject") . '</u>';
        $label.= '<br>';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $url = $url = dol_buildpath('/mymodule/m_card.php',1).'?id='.$this->id;

        $linkclose='';
        if (empty($notooltip))
        {
            if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
            {
                $label=$langs->trans("ShowMyObject");
                $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
            }
            $linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
        }
        else $linkclose = ($morecss?' class="'.$morecss.'"':'');

		$linkstart = '<a href="'.$url.'"';
		$linkstart.=$linkclose.'>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($linkstart.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		$result.= $linkstart . $this->ref . $linkend;
		return $result;
	}

	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatut($status,$mode=0)
	{
		global $langs;

		/*if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}*/
	}


	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->initAsSpecimenCommon();
	}

}

/**
 * Class MyModuleObjectLine
 */
class MyModuleObjectLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	public $prop1;
	/**
	 * @var mixed Sample line property 2
	 */
	public $prop2;
}
