<?php


/**
* Classe de génération et de contrôle de formulaires
*
* @package izForm
* @author Loic Mathaud <loic@mathaud.net>
* @copyright 2004-2005 Loic Mathaud
* http://bballizlife.com/expe/izform/
*
*
* ******* BEGIN LICENSE BLOCK *******
*
* Copyright (c) 2003, Loic Mathaud
* All rights reserved.
* 
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
* 
*	* Redistributions of source code must retain the above copyright notice,
*	  this list of conditions and the following disclaimer.
*	* Redistributions in binary form must reproduce the above copyright notice,
*	  this list of conditions and the following disclaimer in the documentation
*	  and/or other materials provided with the distribution.
*	* Neither the name of izForm nor the names of its contributors
*	  may be used to endorse or promote products derived from this software
*	  without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
* TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
* PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
* BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
* OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
* SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
* CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
* ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
* THE POSSIBILITY OF SUCH DAMAGE
*
* ******* END LICENSE BLOCK *******
*/

/**
* chemin racine d'IzForm
*/
define('IZF_PATH', dirname(__FILE__) . '/');

/**
* balise par d�faut qui encadre les éléments de formulaire
*/
define('IZF_DEFAULT_WRAPPER', 'P');

require IZF_PATH . 'izform-map.class.php';


/**
* classe IzForm
*
* @package izForm
*/
class IzForm {
	
	/**
	* Cha�ne de caractère contenant la sortie html du formulaire
	*
	* @var string
	* @access private
	*/
	var $elementsHtml;
	
	/**
	* Tableau contenant les éléments du formulaire
	*
	* @var array
	* @access private
	*/
	var $elements = array();
	
	/**
	* Tableau contenant les éléments à charger dans une liste déroulante
	*
	* @var array
	* @access private
	*/
	var $selectElements = array();
	
	/**
	* Tableau de 2 valeurs contenant les indices qui permettront de positionner un élément fieldset
	*
	* @var array
	* @access private
	*/
	var $fieldSetElements = array();
	
	/**
	* Tableau de 2 valeurs contenant les indices qui permettront de positionner un élément optgroup dans une liste déroulante
	*
	* @var array
	* @access private
	*/
	var $optGroupElements = array();
	
	/**
	* Tableau qui est la copie du tableau superglobal correspond à la méthode employée pour soumettre le formulaire
	* 
	* @var array
	* @access public
	*/
	var $submittedData = array();
	
	/**
	* Tableau des valeurs à contrôler une fois le formulaire soumis
	*
	* @var array
	* @access private
	*/
	var $verifData = array();
	
	/**
	* D�fini la manière dont est affiché le label
	*
	* Par défaut, vaut 'wrapp' : le label englobe l'élément de formulaire
	* Autre valeur possible : 'nowrapp' : le label est refermé avant l'élément de formulaire
	*
	* @var string
	*/
	var $labelMode = 'wrapp';
	
	
	/**
	* Constructeur de la classe
	*
	* Défini le script cible du formulaire, la méthode par laquelle il est soumis
	* et quel est le type d'encryption de celui-ci
	* Le constructeur enregistre la balise d'ouverture du formulaire,
	* copie dans la variable de classe {@link $submittedData} le bon tableau superglobal en fonction
	* de la méthode employ�e pour envoyer le formulaire
	*
	* @param string $method methode par laquelle est soumis le formulaire. Peut prendre les valeurs 'post' ou 'get' ('post' par defaut)
	* @param string $action script cible du formulaire. Si $action n'est pas sp�cifi�e elle prend pour valeur le script même qui contient le formulaire
	* @param string $id identifiant du formulaire. Prend 'form1' par défaut
	* @param int $enctype type d'encryption du formulaire (0: multipart/form-data, 1: application/x-www-form-urlencoded)
	* @access public
	*/
	function IzForm( $method = 'post', $action = '', $id = 'form1', $enctype = 1 ) {
		$this->method = strtoupper( $method );
		
		if ( $this->method == 'POST' ) {
			$this->submittedData = $_POST;
		} elseif ( $this->method == 'GET' ) {
			$this->submittedData == $_GET;
		}
		
		switch ($enctype) {
			case 0:
				$enctype = "multipart/form-data";
				break;
			case 1: 
				$enctype = "application/x-www-form-urlencoded";
				break;
		}
		$attributes['action']  = ( $action == '' ) ? $_SERVER['PHP_SELF'] : $action;
		$attributes['id']      = $id;
		$attributes['method']  = strtolower( $method );
		$attributes['enctype'] = $enctype;
		$res = array( 'type' => 'open_form', 'attributes' => $attributes );
		
		$this->setElement($res);
	}
	
	/**
	* Ajoute un élément de type input type="text" au formulaire
	*
	* @param string $name nom du champs texte
	* @param string $label label du champs texte: texte placé devant le champ pour l'identifier
	* @param string $value valeur du champs texte
	* @param int $size taille du champs texte
	* @param int $maxlength taille maximale du champs texte
	* @param string $id identifiant du champs texte. Si n'est pas affecté, prend la valeur de $name
	* @param string $before élément ouvrant encadrant le champs texte. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $after élement fermant encadrant le champs texte. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $lbl_attributes attributs pouvant être ajoutés au label du champs texte pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant être ajoutés au champs texte pour les css (class, id, style inline) ou du javascript ou autre
	* @access public
	*/
	function addText( $name, $label = '', $value = '' , $size = '', $maxlength = '', $id = '', $before = '', $after = '', $elem_attributes = '', $lbl_attributes = '' ) {
		$attributes['name']				= $name;
		$attributes['label']			= $label;
		$attributes['value']			= $value;
		$attributes['size']				= $size;
		$attributes['maxlength']		= $maxlength;
		$attributes['id']				= ( empty($id) ? $name : $id );
		$before							= ( empty($before) ? IZF_DEFAULT_WRAPPER : $before );
		$after							= ( empty($after) ? IZF_DEFAULT_WRAPPER : $after );
		$attributes['before']			= $before;
		$attributes['after']			= $after;
		$attributes['lbl_attributes']	= '';
		$attributes['elem_attributes']	= '';
		
		if ( !empty($lbl_attributes) ) {
			$attributes['lbl_attributes'] = ' '. $lbl_attributes;
		}
		if ( !empty($elem_attributes) ) {
			$attributes['elem_attributes'] = ' '. $elem_attributes;
		}
		$res = array( 'type' => 'text', 'attributes' => $attributes);
		
		$wrappOpenAttributes['description']  = 'open';
		$wrappCloseAttributes['description'] = 'close';
		$wrappOpen  = array( 'type' => 'wrapp_'. $before, 'attributes' => $wrappOpenAttributes );
		$wrappClose = array( 'type' => 'wrapp_'. $after, 'attributes' => $wrappCloseAttributes );
		
		$this->setElement($wrappOpen);
		$this->setElement($res);
		$this->setElement($wrappClose);
		
		$indice = $this->getElementIndex($name);
		global $$name;
		$$name = new textElem($this, $indice);
	}
	
	
	/**
	* Ajoute un élément de type input type="password" au formulaire
	*
	* @param string $name nom du champs password
	* @param string $label label du champs password: texte placé devant le champ pour l'identifier
	* @param string $value valeur du champs password
	* @param int $size taille du champs password
	* @param int $maxlength taille maximale du champs password
	* @param string $id identifiant du champs password. Si n'est pas affecté, prend la valeur de $name
	* @param string $before élément ouvrant encadrant le champs password. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $after élement fermant encadrant le champs password. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $lbl_attributes attributs pouvant être ajoutés au label du champs password pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant être ajoutés au champs password pour les css (class, id, style inline) ou du javascript ou autre
	* @access public
	*/
	function addPassword( $name, $label = '', $value = '', $size = '', $maxlength = '', $id = '', $before = '', $after = '', $elem_attributes = '', $lbl_attributes = '' ) {
		$attributes['name']				= $name;
		$attributes['label']			= $label;
		$attributes['value']			= $value;
		$attributes['size']				= $size;
		$attributes['maxlength']		= $maxlength;
		$attributes['id']				= ( empty($id) ? $name : $id );
		$before							= ( empty($before) ? IZF_DEFAULT_WRAPPER : $before );
		$after							= ( empty($after) ? IZF_DEFAULT_WRAPPER : $after );
		$attributes['before']			= $before;
		$attributes['after']			= $after;
		$attributes['lbl_attributes']	= '';
		$attributes['elem_attributes']	= '';
		
		if ( !empty($lbl_attributes) ) {
			$attributes['lbl_attributes'] = ' '. $lbl_attributes;
		}
		if ( !empty($elem_attributes) ) {
			$attributes['elem_attributes'] = ' '. $elem_attributes;
		}
		$res = array( 'type' => 'password', 'attributes' => $attributes);
		
		$wrappOpenAttributes['description']  = 'open';
		$wrappCloseAttributes['description'] = 'close';
		$wrappOpen  = array( 'type' => 'wrapp_'. $before, 'attributes' => $wrappOpenAttributes );
		$wrappClose = array( 'type' => 'wrapp_'. $after, 'attributes' => $wrappCloseAttributes );
		
		$this->setElement($wrappOpen);
		$this->setElement($res);
		$this->setElement($wrappClose);
		
		$indice = $this->getElementIndex($name);
		global $$name;
		$$name = new pwdElem($this, $indice);
	}
	
	/**
	* Ajoute un élément de type input type="checkbox" au formulaire
	*
	* @param string $name nom de l'élément checkbox
	* @param string $label label de l'élément checkbox : texte place après l'élément pour l'identifier
	* @param string $value valeur de l'élément checkbox
	* @param boolean $check valeur par défaut de l'état de la case à cocher (0 = non coch�e, 1 = cochée)
	* @param string $id identifiant de l'élément checkbox. Si n'est pas affecté, prend la valeur de $name
	* @param string $before élément ouvrant encadrant l'élément checkbox. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $after élément fermant encadrant l'élément checkbox. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $lbl_attributes attributs pouvant être ajoutés au label de l'élément checkbox pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant être ajoutés à l'élément checkbox pour les css (class, id, style inline) ou du javascript ou autre
	* @access public
	*/
	function addCheckBox( $name, $label, $value, $check = 0, $id = '', $before = '', $after = '', $elem_attributes = '', $lbl_attributes = '' ) {
		$attributes['name']				= $name;
		$attributes['label']			= $label;
		$attributes['value']			= $value;
		$attributes['check']			= $check;
		$attributes['id']				= ( empty($id) ? $name . '-' . $value : $id );
		$before							= ( empty($before) ? IZF_DEFAULT_WRAPPER : $before );
		$after							= ( empty($after) ? IZF_DEFAULT_WRAPPER : $after );
		$attributes['before']			= $before;
		$attributes['after']			= $after;
		$attributes['lbl_attributes']	= '';
		$attributes['elem_attributes']	= '';
		
		if ( !empty($lbl_attributes) ) {
			$attributes['lbl_attributes'] = ' '. $lbl_attributes;
		}
		if ( !empty($elem_attributes) ) {
			$attributes['elem_attributes'] = ' '. $elem_attributes;
		}
		$res = array( 'type' => 'checkbox', 'attributes' => $attributes);
		
		$wrappOpenAttributes['description']  = 'open';
		$wrappCloseAttributes['description'] = 'close';
		$wrappOpen  = array( 'type' => 'wrapp_'. $before, 'attributes' => $wrappOpenAttributes );
		$wrappClose = array( 'type' => 'wrapp_'. $after, 'attributes' => $wrappCloseAttributes );
		
		$this->setElement($wrappOpen);
		$this->setElement($res);
		$this->setElement($wrappClose);
		
		$indice = $this->getElementIndex($name);
		global $$name;
		$$name = new chkBoxElem($this, $indice);
	}
	
	/**
	* Ajoute un élément de type input type="radio" au formulaire
	*
	* @param string $name nom de l'élément radio
	* @param string $label label de l'élément radio : texte place après l'élément pour l'identifier
	* @param string $value valeur de l'élément radio
	* @param boolean $check valeur par défaut de l'état du bouton radio (0 = non coché, 1 = coché)
	* @param string $id identifiant de l'élément radio. Si n'est pas affecté, prend la valeur de $name
	* @param string $before élément ouvrant encadrant l'élément radio. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $after élément fermant encadrant l'élément radio. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $lbl_attributes attributs pouvant être ajoutés au label de l'élément radio pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant être ajoutés à l'élément radio pour les css (class, id, style inline) ou du javascript ou autre
	* @access public
	*/
	function addRadio( $name, $label, $value, $check = 0, $id = '', $before = '', $after = '', $elem_attributes = '', $lbl_attributes = '' ) {
		$attributes['name']				= $name;
		$attributes['label']			= $label;
		$attributes['value']			= $value;
		$attributes['check']			= $check;
		$attributes['id']				= ( empty($id) ? $name . '-' . $value : $id );
		$before							= ( empty($before) ? IZF_DEFAULT_WRAPPER : $before );
		$after							= ( empty($after) ? IZF_DEFAULT_WRAPPER : $after );
		$attributes['before']			= $before;
		$attributes['after']			= $after;
		$attributes['lbl_attributes']	= '';
		$attributes['elem_attributes']	= '';
		
		if ( !empty($lbl_attributes) ) {
			$attributes['lbl_attributes'] = ' '. $lbl_attributes;
		}
		if ( !empty($elem_attributes) ) {
			$attributes['elem_attributes'] = ' '. $elem_attributes;
		}
		$res = array( 'type' => 'radio', 'attributes' => $attributes);
		
		$wrappOpenAttributes['description']  = 'open';
		$wrappCloseAttributes['description'] = 'close';
		$wrappOpen  = array( 'type' => 'wrapp_'. $before, 'attributes' => $wrappOpenAttributes );
		$wrappClose = array( 'type' => 'wrapp_'. $after, 'attributes' => $wrappCloseAttributes );
		
		$this->setElement($wrappOpen);
		$this->setElement($res);
		$this->setElement($wrappClose);
		
		$indice = $this->getElementIndex($name);
		global $$name;
		$$name = new radioElem($this, $indice);
	}
	
	/**
	* Ajoute un élément de type input type="submit" au formulaire
	*
	* @param string $value valeur du bouton submit (texte affiché sur le bouton)
	* @param string $name nom du bouton submit
	* @param string $label label du bouton submit : texte placé devant le bouton pour l'identifier
	* @param string $id identifiant du bouton submit. Si n'est pas affecté, prend la valeur de $name
	* @param string $before élément ouvrant encadrant le bouton submit. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $after élément fermant encadrant le bouton submit. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $lbl_attributes attributs pouvant être ajoutés au label du bouton submit pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant être ajoutés au bouton submit pour les css (class, id, style inline) ou du javascript ou autre
	* @access public
	*/
	function addSubmit( $value, $name = '', $label = '', $id = '', $before = '', $after = '', $lbl_attributes = '', $elem_attributes = '' ) {
		$attributes['value']			= $value;
		$attributes['name']				= $name;
		$attributes['label']			= $label;
		$attributes['id']				= ( empty($id) ? $name : $id );
		$before							= ( empty($before) ? IZF_DEFAULT_WRAPPER : $before );
		$after							= ( empty($after) ? IZF_DEFAULT_WRAPPER : $after );
		$attributes['before']			= $before;
		$attributes['after']			= $after;
		$attributes['lbl_attributes']	= '';
		$attributes['elem_attributes']	= '';
		
		if ( !empty($lbl_attributes) ) {
			$attributes['lbl_attributes'] = ' '. $lbl_attributes;
		}
		if ( !empty($elem_attributes) ) {
			$attributes['elem_attributes'] = ' '. $elem_attributes;
		}
		$res = array( 'type' => 'submit', 'attributes' => $attributes);
		
		$wrappOpenAttributes['description']  = 'open';
		$wrappCloseAttributes['description'] = 'close';
		$wrappOpen  = array( 'type' => 'wrapp_'. $before, 'attributes' => $wrappOpenAttributes );
		$wrappClose = array( 'type' => 'wrapp_'. $after, 'attributes' => $wrappCloseAttributes );
		
		$this->setElement($wrappOpen);
		$this->setElement($res);
		$this->setElement($wrappClose);
		
		$indice = $this->getElementIndex($name);
		global $$name;
		$$name = new submitElem($this, $indice);
	}
	
	/**
	* Ajoute un élément de type input type="reset" au formulaire
	*
	* @param string $value valeur du bouton reset (texte affiché sur le bouton)
	* @param string $name nom du bouton reset
	* @param string $label label du bouton reset : texte placé devant le bouton pour l'identifier
	* @param string $id identifiant du bouton reset. Si n'est pas affecté, prend la valeur de $name
	* @param string $before élément ouvrant encadrant le bouton reset. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $after élément fermant encadrant le bouton reset. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $lbl_attributes attributs pouvant être ajoutés au label du bouton reset pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant être ajoutés au bouton reset pour les css (class, id, style inline) ou du javascript ou autre
	* @access public
	*/
	function addReset( $value, $name = '', $label = '', $id = '', $before = '', $after = '', $lbl_attributes = '', $elem_attributes = '' ) {
		$attributes['value']			= $value;
		$attributes['name']				= $name;
		$attributes['label']			= $label;
		$attributes['id']				= ( empty($id) ? $name : $id );
		$before							= ( empty($before) ? IZF_DEFAULT_WRAPPER : $before );
		$after							= ( empty($after) ? IZF_DEFAULT_WRAPPER : $after );
		$attributes['before']			= $before;
		$attributes['after']			= $after;
		$attributes['lbl_attributes']	= '';
		$attributes['elem_attributes']	= '';
		
		if ( !empty($lbl_attributes) ) {
			$attributes['lbl_attributes'] = ' '. $lbl_attributes;
		}
		if ( !empty($elem_attributes) ) {
			$attributes['elem_attributes'] = ' '. $elem_attributes;
		}
		$res = array( 'type' => 'reset', 'attributes' => $attributes);
		
		$wrappOpenAttributes['description']  = 'open';
		$wrappCloseAttributes['description'] = 'close';
		$wrappOpen  = array( 'type' => 'wrapp_'. $before, 'attributes' => $wrappOpenAttributes );
		$wrappClose = array( 'type' => 'wrapp_'. $after, 'attributes' => $wrappCloseAttributes );
		
		$this->setElement($wrappOpen);
		$this->setElement($res);
		$this->setElement($wrappClose);
		
		$indice = $this->getElementIndex($name);
		global $$name;
		$$name = new resetElem($this, $indice);
	}
	
	/**
	* Ajoute un élément de type input type="file" au formulaire
	*
	* @param string $name nom du bouton parcourir pour joindre un fichier
	* @param string $label label du bouton parcourir : texte placé devant le bouton pour l'identifier
	* @param string $id identifiant du bouton parcourir. Si n'est pas affecté, prend la valeur de $name
	* @param string $before élément ouvrant encadrant le bouton parcourir. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $after élément fermant encadrant le bouton parcourir. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $lbl_attributes attributs pouvant être ajoutés au label du bouton parcourir pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant être ajoutes au bouton parcourir pour les css (class, id, style inline) ou du javascript ou autre
	* @access public
	*/
	function addFile( $name , $label = '', $id = '', $before = '', $after = '', $lbl_attributes = '', $elem_attributes = '' ) {
		$attributes['name']				= $name;
		$attributes['label']			= $label;
		$attributes['id']				= ( empty($id) ? $name : $id );
		$before							= ( empty($before) ? IZF_DEFAULT_WRAPPER : $before );
		$after							= ( empty($after) ? IZF_DEFAULT_WRAPPER : $after );
		$attributes['before']			= $before;
		$attributes['after']			= $after;
		$attributes['lbl_attributes']	= '';
		$attributes['elem_attributes']	= '';
		
		if ( !empty($lbl_attributes) ) {
			$attributes['lbl_attributes'] = ' '. $lbl_attributes;
		}
		if ( !empty($elem_attributes) ) {
			$attributes['elem_attributes'] = ' '. $elem_attributes;
		}
		$res = array( 'type' => 'file', 'attributes' => $attributes);
		
		$wrappOpenAttributes['description']  = 'open';
		$wrappCloseAttributes['description'] = 'close';
		$wrappOpen  = array( 'type' => 'wrapp_'. $before, 'attributes' => $wrappOpenAttributes );
		$wrappClose = array( 'type' => 'wrapp_'. $after, 'attributes' => $wrappCloseAttributes );
		
		$this->setElement($wrappOpen);
		$this->setElement($res);
		$this->setElement($wrappClose);
		
		$indice = $this->getElementIndex($name);
		global $$name;
		$$name = new fileElem($this, $indice);
	}
	
	/**
	* Ajoute un élément de type input type="hidden" au formulaire
	*
	* @param string $name nom du champ caché
	* @param string $value valeur du champ caché
	* @access public
	*/
	function addHidden( $name, $value) {
		$attributes['name']		= $name;
		$attributes['value']	= $value;
		
		$res = array( 'type' => 'hidden', 'attributes' => $attributes);
		
		$this->setElement($res);
		
		$indice = $this->getElementIndex($name);
		global $$name;
		$$name = new hiddenElem($this, $indice);
	}
	
	/**
	* Ajoute un element de type textarea au formulaire
	*
	* @param string $name nom de la zone de texte
	* @param int $rows nombre de lignes de la zone de texte
	* @param int $cols nombre de colonnes de la zone de texte
	* @param string $label label de la zone de texte : texte placé devant cette zone pour l'identifier
	* @param string $value valeur de la zone de texte
	* @param string $id identifiant unique de la zone de texte. Si n'est pas affecté, prend la valeur de $name
	* @param string $before élément ouvrant encadrant la zone de texte. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $after élément fermant encadrant la zone de texte. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $lbl_attributes attributs pouvant être ajoutés au label de la zone de texte pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant être ajoutés à la zone de texte pour les css (class, id, style inline) ou du javascript ou autre
	* @access public
	*/
	function addTextArea( $name, $rows, $cols, $label = '', $value = '', $id = '', $before = '', $after = '', $lbl_attributes = '', $elem_attributes = '' ) {
		$attributes['name']				= $name;
		$attributes['rows']				= $rows;
		$attributes['cols']				= $cols;
		$attributes['label']			= $label;
		$attributes['value']			= $value;
		$attributes['id']				= ( empty($id) ? $name : $id );
		$before							= ( empty($before) ? IZF_DEFAULT_WRAPPER : $before );
		$after							= ( empty($after) ? IZF_DEFAULT_WRAPPER : $after );
		$attributes['before']			= $before;
		$attributes['after']			= $after;
		$attributes['lbl_attributes']	= '';
		$attributes['elem_attributes']	= '';
		
		if ( !empty($lbl_attributes) ) {
			$attributes['lbl_attributes'] = ' '. $lbl_attributes;
		}
		if ( !empty($elem_attributes) ) {
			$attributes['elem_attributes'] = ' '. $elem_attributes;
		}
		$res = array( 'type' => 'textarea', 'attributes' => $attributes);
		
		$wrappOpenAttributes['description']  = 'open';
		$wrappCloseAttributes['description'] = 'close';
		$wrappOpen  = array( 'type' => 'wrapp_'. IZF_DEFAULT_WRAPPER, 'attributes' => $wrappOpenAttributes );
		$wrappClose = array( 'type' => 'wrapp_'. IZF_DEFAULT_WRAPPER, 'attributes' => $wrappCloseAttributes );
		
		$this->setElement($wrappOpen);
		$this->setElement($res);
		$this->setElement($wrappClose);
		
		$indice = $this->getElementIndex($name);
		global $$name;
		$$name = new textAreaElem($this, $indice);
	}
	
	/**
	* Ajoute un paragraphe de texte dans le formulaire
	*
	* @param string $value texte du paragraphe
	* @param string $elem_attributes attributs pouvant être ajoutés au paragraphe pour les css (class, id, style inline)
	* @access public
	*/
	function addParagraph( $value, $name = '', $elem_attributes = '' ) {
		$attributes['value']			= $value;
		$attributes['name']				= $name;
		$attributes['elem_attributes']	= '';
		
		if ( !empty($elem_attributes) ) {
			$attributes['elem_attributes'] = ' '. $elem_attributes;
		}
		$res = array( 'type' => 'paragraph', 'attributes' => $attributes);
		
		$this->setElement($res);
	}
	
	/**
	* Ajoute un élément '<option></option> dans une liste déroulante
	* Charge la paire valeur/label dans la liste déroulante
	*
	* addDataInSelect précharge en fait les données qui seront dans la liste déroulante
	* Il faut donc précharger toutes les valeurs que l'on souhaite pour la liste avant d'ajouter celle-ci
	* avec la méthode {@link addSelect()}
	*
	* @param string $value valeur de l'attribut 'value' de la balise 'option'
	* @param string $label texte visible de l'élément option de la liste déroulante
	* @access public
	*/
	function addDataInSelect( $value, $label ) {
		$attributes['value'] = $value;
		$attributes['label'] = $label;
		$res = array( 'type' => 'option', 'attributes' => $attributes);
		
		$this->setSelectElement($res);
	}
	
	/**
	* Ajoute un élément dans le tableau selectElement[], tableau de préchargement des éléments pour une liste déroulante
	*
	* @param array $res tableau associatif contenu la valeur et le label de la balise '<option>' à rajouter à la liste déroulante
	* @access private
	*/
	function setSelectElement( $res ) {
		$this->selectElements[] = $res;
	}
	
	/**
	* Défini les limites pour mettre des éléments '<option>' d'une liste déroulante dans une balise '<optgroup>'
	*
	* Cette méthode défini les bornes parmi les éléments déjà préchargés par la méthode {@link addDataInSelect()}
	*
	* @param string $start élément de d�but. Correspond à l'attribut $value du premier élément qui sera dans l'optgroup
	* @param string $end élément de fin. Correspond à l'attribut $value du dernier élément qui sera dans l'optgroup
	* @access public
	*/
	function setOptGroupLimiters( $start, $end ) {
		foreach ( $this->selectElements as $id => $elem ) {
			foreach ( $elem as $k => $attribut ) {
				if ( $k == 'attributes' ) {
					if ( isset($attribut['value']) && $attribut['value'] == $start ) {
						$this->optGroupElements[0] = $id;
						break 2;
					}
				}
			}
		}
		
		$elements = array_reverse($this->selectElements, TRUE);
		
		foreach ( $elements as $id => $elem ) {
			foreach ( $elem as $k => $attribut ) {
				if ( $k == 'attributes' ) {
					if ( isset($attribut['value']) && $attribut['value'] == $end ) {
						$this->optGroupElements[1] = $id;
						break 2;
					}
				}
			}
		}
	}
	
	/**
	* Ajoute la balise '<optgroup>' après avoir défini ses bornes
	*
	* @param string $label label de la balise <optgroup>
	* @access public
	*/
	function addOptGroup($label) {
		foreach ( $this->selectElements as $k => $elem ) {
			if ( $k == $this->optGroupElements[0] ) {
				$attributes['label'] = $label;
				$res = array( 'type' => 'optgroup_open', 'attributes' => $attributes);
				
				$elemCopy[] = $res;
				$elemCopy[] = $elem;
			} elseif ( $k == $this->optGroupElements[1] ) {
				$res = array( 'type' => 'optgroup_close');
				
				$elemCopy[] = $elem;
				$elemCopy[] = $res;
			} else {
				$elemCopy[] = $elem;
			}
		}
		$this->selectElements = $elemCopy;
	}
	
	/**
	* Ajoute une liste déroulante au formulaire
	*
	* Vous devez tout d'abord charger les éléments constituants la liste avec les méthodes
	* {@link addDataInSelect()}, {@link setOptGroupLimiters()} et {@link addOptGroup()}
	* Une fois la liste ajoutée, le tableau des éléments qui ont été préchargés est vidé
	*
	* @param string $name nom de la liste déroulante
	* @param string $label label de la liste déroulante: texte placé devant pour l'identifier
	* @param string $selected indique l'élément de la liste déroulante séléctionné par defaut. Correspond à l'attribut 'value' de la balise '<option>'
	* @param string $id identifiant unique de la liste déroulante
	* @param int $size nombre de lignes visibles de la liste déroulante. Vaut 1 par défaut
	* @param string $before élément ouvrant encadrant la liste déroulante. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $after élément fermant encadrant la liste déroulante. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par défaut
	* @param string $lbl_attributes attributs pouvant être ajoutés au label de la liste déroulante pour les css (class, id ou style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant être ajoutés à la liste déroulante pour les css (class, id ou style inline) ou du javascript
	* @access public 
	*/
	function addSelect( $name, $label = '', $selected = '', $id = '', $size = '1', $multiple = '0', $before = '', $after = '', $lbl_attributes = '', $elem_attributes = '' ) {
		$attributes['name']				= $name;
		$attributes['label']			= $label;
		$attributes['selected']			= $selected;
		$attributes['size']				= $size;
		$attributes['multiple']			= $multiple;
		$attributes['id']				= ( empty($id) ? $name : $id );
		$before							= ( empty($before) ? IZF_DEFAULT_WRAPPER : $before );
		$after							= ( empty($after) ? IZF_DEFAULT_WRAPPER : $after );
		$attributes['before']			= $before;
		$attributes['after']			= $after;
		$attributes['lbl_attributes']	= '';
		$attributes['elem_attributes']	= '';
		
		if ( !empty($lbl_attributes) ) {
			$attributes['lbl_attributes'] = ' '. $lbl_attributes;
		}
		if ( !empty($elem_attributes) ) {
			$attributes['elem_attributes'] = ' '. $elem_attributes;
		}
		$attributes['data'] = $this->selectElements;
		$res = array( 'type' => 'select', 'attributes' => $attributes);
		
		$wrappOpenAttributes['description']  = 'open';
		$wrappCloseAttributes['description'] = 'close';
		$wrappOpen  = array( 'type' => 'wrapp_'. $before, 'attributes' => $wrappOpenAttributes );
		$wrappClose = array( 'type' => 'wrapp_'. $after, 'attributes' => $wrappCloseAttributes );
		
		$this->setElement($wrappOpen);
		$this->setElement($res);
		$this->setElement($wrappClose);
		$this->resetDataInSelect();
		
		$indice = $this->getElementIndex($name);
		global $$name;
		$$name = new selectElem($this, $indice);
	}
	
	/**
	* Vide la liste des éléments à charger dans une liste déroulante
	*
	* @access private
	*/
	function resetDataInSelect() {
		$this->selectElements = array();
	}
	
	/**
	* Défini les limites pour mettre un élément fieldset dans le formulaire
	*
	* Cette méthode défini les bornes parmi les éléments du formulaire
	*
	* @param string $start élément de début. Correspond à l'attribut $name du premier élément qui sera dans l'optgroup
	* @param string $end élément de fin. Correspond à l'attribut $name du dernier élément qui sera dans l'optgroup
	* @access public
	*/
	function setFieldSetLimiters( $start, $end ) {
		foreach ( $this->elements as $id => $elem ) {
			foreach ( $elem as $k => $attribut ) {
				if ( $k == 'attributes' ) {
					if ( isset($attribut['name']) && $attribut['name'] == $start ) {
						$this->fieldSetElements[0] = $id - 1;
						break 2;
					}
				}
			}
		}
		
		$elements = array_reverse($this->elements, TRUE);
		
		foreach ( $elements as $id => $elem ) {
			foreach ( $elem as $k => $attribut ) {
				if ( $k == 'attributes' ) {
					if ( isset($attribut['name']) && $attribut['name'] == $end ) {
						$this->fieldSetElements[1] = $id + 1;
						break 2;
					}
				}
			}
		}
	}
	
	/**
	* Ajoute un fieldset après avoir défini ses bornes avec la méthode {@link setFieldSetLimiters()}
	*
	* @param string $legend légende associée au fieldset
	* @param string $elem_attributes attributs pouvant être ajoutés au fieldset pour les css (class, id ou style inline) ou du javascript
	* @access public
	*/
	function addFieldSet( $legend = '', $elem_attributes = '' ) {
		foreach ( $this->elements as $k => $elem ) {
			if ( $k == $this->fieldSetElements[0] ) {
				$attributes['legend'] = $legend;
				$attributes['elem_attributes'] = '';
				
				if ( !empty($elem_attributes) ) {
					$attributes['elem_attributes'] = ' '. $elem_attributes;
				}
				$res = array( 'type' => 'fieldset_open', 'attributes' => $attributes);
				
				$elemCopy[] = $res;
				$elemCopy[] = $elem;
			} elseif ( $k == $this->fieldSetElements[1] ) {
				$res = array( 'type' => 'fieldset_close');
				
				$elemCopy[] = $elem;
				$elemCopy[] = $res;
			} else {
				$elemCopy[] = $elem;
			}
		}
		$this->elements = $elemCopy;
	}
	
	/**
	* Ajoute un élément de formulaire dans le tableau elements[] qui les contient tous
	*
	* @param array tableau contenant les paramètres d'un élément de formulaire à ajouter
	* @access private
	*/
	function setElement( $res ) {
		$this->elements[] = $res;
	}
	
	/**
	* Affiche le formulaire
	* 
	* Cette méthode ajoute le dernier élément au formulaire (balise de fermeture de celui-ci) puis
	* va faire générer la sortie html pour chaque élément avant de l'afficher
	* @access public
	*/
	function displayForm() {
		$res['type'] = 'close_form';
		$this->setElement($res);
		
		foreach ( $this->elements as $elem ) {
			$this->setElementHtml( $elem );
		}
		
		echo $this->elementsHtml;
	}
	
	/**
	* Génère la sortie html pour les éléments de formulaire
	*
	* Stock la sortie html dans la chaine {@link elementsHtml}
	*
	* @param array $res tableau contenant les paramètres d'un élément de formulaire
	* @access private
	*/
	function setElementHtml( $res ) {
		if ( ( is_array( $res ) && count( $res ) >= 1 ) ) {
			switch ( $res['type'] ) {
				case 'wrapp_P':
					if ( $res['attributes']['description'] == 'open' ) {
						$this->elementsHtml .= '<p>' . "\n";
					} elseif ( $res['attributes']['description'] == 'close' ) {
						$this->elementsHtml .= '</p>' . "\n";
					}else {
						$this->elementsHtml .= '' . "\n";
					}
					break;
				case 'wrapp_D':
					if ( $res['attributes']['description'] == 'open' ) {
						$this->elementsHtml .= '<div>' . "\n";
					} elseif ( $res['attributes']['description'] == 'close' ) {
						$this->elementsHtml .= '</div>' . "\n";
					}else {
						$this->elementsHtml .= '' . "\n";
					}
					break;
				case 'wrapp_B':
					if ( $res['attributes']['description'] == 'open' ) {
						$this->elementsHtml .= '' . "\n";
					} elseif ( $res['attributes']['description'] == 'close' ) {
						$this->elementsHtml .= ' ' . "\n";
					}else {
						$this->elementsHtml .= ' ' . "\n";
					}
					break;
				case 'fieldset_open':
					$this->elementsHtml .= '<fieldset'. $res['attributes']['elem_attributes'] .'>' . "\n";
					if ( !empty($res['attributes']['legend']) ) {
						$this->elementsHtml .= '<legend>'. $res['attributes']['legend'] .'</legend>' . "\n";
					}
					break;
				case 'fieldset_close':
					$this->elementsHtml .= '</fieldset>' . "\n";
					break;
				case 'optgroup_open':
					$this->elementsHtml .= "\n<optgroup label=\"". $res['attributes']['label'] ."\">";
					break;
				case 'optgroup_close':
					$this->elementsHtml .= "</optgroup>\n";
					break;
				case 'open_form':
					$this->elementsHtml .= "<form action=\"". $res['attributes']['action'] . "\" ".
											"method=\"". $res['attributes']['method'] . "\" ".
											"id=\"". $res['attributes']['id'] ."\" ".
											"enctype=\"". $res['attributes']['enctype'] . "\">\n";
					break;
				case 'close_form':
					$this->elementsHtml .= "</form>\n";
					break;
				case 'label':
					$this->elementsHtml .= "<label for=\"". $res['attributes']['id'] ."\"".
											$res['attributes']['lbl_attributes'] .">";
					
					break;
				case 'text':
					if ( !empty($res['attributes']['label']) ) {
						$this->setElementHtml( array( 	'type' => 'label',
														'attributes' => array( 'id' => $res['attributes']['id'],
																				'label' => $res['attributes']['label'],
																				'lbl_attributes' => $res['attributes']['lbl_attributes'] ) ) );
						$this->elementsHtml .= $res['attributes']['label'];
						if ( $this->labelMode == 'nowrapp' ) {
							$this->elementsHtml .= "</label>\n";
						}
					}
					$this->elementsHtml .= "<input type=\"text\" id=\"". $res['attributes']['id'] ."\" ".
											"name=\"". $res['attributes']['name'] ."\" ".
											"value=\"". $res['attributes']['value'] ."\" ".
											"size=\"". $res['attributes']['size'] ."\" ".
											"maxlength=\"" . $res['attributes']['maxlength'] ."\"".
											$res['attributes']['elem_attributes'] ." />";
					
					if ( !empty($res['attributes']['label']) && $this->labelMode == 'wrapp' ) {
						$this->elementsHtml .= "</label>\n";
					}
					break;
				case 'password':
					if ( !empty($res['attributes']['label']) ) {
						$this->setElementHtml( array( 	'type' => 'label',
														'attributes' => array( 'id' => $res['attributes']['id'],
																				'label' => $res['attributes']['label'],
																				'lbl_attributes' => $res['attributes']['lbl_attributes'] ) ) );
						$this->elementsHtml .= $res['attributes']['label'];
						if ( $this->labelMode == 'nowrapp' ) {
							$this->elementsHtml .= "</label>\n";
						}
					}
					$this->elementsHtml .= "<input type=\"password\" id=\"". $res['attributes']['id'] ."\" ".
											"name=\"". $res['attributes']['name'] ."\" ".
											"value=\"". $res['attributes']['value'] ."\" ".
											"size=\"". $res['attributes']['size'] ."\" ".
											"maxlength=\"" . $res['attributes']['maxlength'] ."\"".
											$res['attributes']['elem_attributes'] ." />";
					if ( !empty($res['attributes']['label']) && $this->labelMode == 'wrapp' ) {
						$this->elementsHtml .= "</label>\n";
					}
					break;
				case 'checkbox':
					if ( !empty($res['attributes']['label']) && $this->labelMode == 'wrapp' ) {
						$this->setElementHtml( array( 	'type' => 'label',
														'attributes' => array( 'id' => $res['attributes']['id'],
																				'label' => $res['attributes']['label'],
																				'lbl_attributes' => $res['attributes']['lbl_attributes'] ) ) );
					}
					$this->elementsHtml .= "<input type=\"checkbox\" id=\"". $res['attributes']['id'] ."\" ".
											"name=\"". $res['attributes']['name'] ."[".  $res['attributes']['value'] ."]\" ".
											"value=\"". $res['attributes']['value'] ."\" ";
					if ( isset($this->submittedData[$res['attributes']['name']][$res['attributes']['value']]) ||
						($res['attributes']['check'] == 1 && empty($this->submittedData) ) ) {
						$this->elementsHtml .= "checked=\"checked\"";
					}
					$this->elementsHtml .=	$res['attributes']['elem_attributes'] ." />";
					if ( !empty($res['attributes']['label']) && $this->labelMode == 'nowrapp' ) {
						$this->setElementHtml( array( 	'type' => 'label',
														'attributes' => array( 'id' => $res['attributes']['id'],
																				'label' => $res['attributes']['label'],
																				'lbl_attributes' => $res['attributes']['lbl_attributes'] ) ) );
					}
					if ( !empty($res['attributes']['label']) ) {
						$this->elementsHtml .= $res['attributes']['label']. "</label>";
					}
					break;
				case 'textarea':
					if ( !empty($res['attributes']['label']) ) {
						$this->setElementHtml( array( 	'type' => 'label',
														'attributes' => array( 'id' => $res['attributes']['id'],
																				'label' => $res['attributes']['label'],
																				'lbl_attributes' => $res['attributes']['lbl_attributes'] ) ) );
						$this->elementsHtml .= $res['attributes']['label'];
						if ( $this->labelMode == 'nowrapp' ) {
							$this->elementsHtml .= "</label>\n";
						}
					}
					$this->elementsHtml .= "<br /><textarea id=\"". $res['attributes']['id'] ."\" ".
											"name=\"". $res['attributes']['name'] ."\" ".
											"rows=\"". $res['attributes']['rows'] ."\" ".
											"cols=\"". $res['attributes']['cols'] ."\" ".
											$res['attributes']['elem_attributes'] .">".
											$res['attributes']['value'].
											"</textarea>\n";
					if ( !empty($res['attributes']['label']) && $this->labelMode == 'wrapp' ) {
						$this->elementsHtml .= "</label>\n";
					}
					break;
				case 'hidden':
					$this->elementsHtml .= "\n<input type =\"hidden\" ".
											"name=\"". $res['attributes']['name'] ."\" ".
											"value=\"". $res['attributes']['value'] ."\" />\n";
					break;
				case 'submit':
					if ( !empty($res['attributes']['label']) ) {
						$this->setElementHtml( array( 	'type' => 'label',
														'attributes' => array( 'id' => $res['attributes']['id'],
																				'label' => $res['attributes']['label'],
																					'lbl_attributes' => $res['attributes']['lbl_attributes'] ) ) );
						if ( $this->labelMode == 'nowrapp' ) {
							$this->elementsHtml .= "</label>";
						}
					}
					$this->elementsHtml .= "<input type=\"submit\" id=\"". $res['attributes']['id'] ."\" ".
											"name=\"". $res['attributes']['name'] ."\" ".
											"value=\"". $res['attributes']['value'] ."\" ".
											$res['attributes']['elem_attributes'] ." />";
					if ( !empty($res['attributes']['label']) && $this->labelMode == 'wrapp' ) {
						$this->elementsHtml .= "</label>";
					}
					break;
				case 'reset':
					if ( !empty($res['attributes']['label']) ) {
						$this->setElementHtml( array( 	'type' => 'label',
														'attributes' => array( 'id' => $res['attributes']['id'],
																				'label' => $res['attributes']['label'],
																				'lbl_attributes' => $res['attributes']['lbl_attributes'] ) ) );
						if ( $this->labelMode == 'nowrapp' ) {
							$this->elementsHtml .= "</label>";
						}
					}
					$this->elementsHtml .= "<input type=\"reset\" id=\"". $res['attributes']['id'] ."\" ".
											"name=\"". $res['attributes']['name'] ."\" ".
											"value=\"". $res['attributes']['value'] ."\" ".
											$res['attributes']['elem_attributes'] ." />";
					if ( !empty($res['attributes']['label']) && $this->labelMode == 'wrapp' ) {
						$this->elementsHtml .= "</label>";
					}
					break;
				case 'radio':
					if ( !empty($res['attributes']['label']) && $this->labelMode == 'wrapp' ) {
						$this->setElementHtml( array( 	'type' => 'label',
														'attributes' => array( 'id' => $res['attributes']['id'],
																				'label' => $res['attributes']['label'],
																				'lbl_attributes' => $res['attributes']['lbl_attributes'] ) ) );
					}
					$this->elementsHtml .= "<input type=\"radio\" id=\"". $res['attributes']['id'] ."\" ".
											"name=\"". $res['attributes']['name'] ."\" ".
											"value=\"". $res['attributes']['value'] ."\" ";
					if (  (isset($this->submittedData[$res['attributes']['name']]) &&
							$this->submittedData[$res['attributes']['name']] == $res['attributes']['value']) ||
						($res['attributes']['check'] == 1 && empty($this->submittedData) ) ) {
						$this->elementsHtml .= "checked=\"checked\"";
					}
					$this->elementsHtml .=	$res['attributes']['elem_attributes'] ." />";
					if ( !empty($res['attributes']['label']) && $this->labelMode == 'nowrapp' ) {
						$this->setElementHtml( array( 	'type' => 'label',
														'attributes' => array( 'id' => $res['attributes']['id'],
																				'label' => $res['attributes']['label'],
																				'lbl_attributes' => $res['attributes']['lbl_attributes'] ) ) );
					}
					if ( !empty($res['attributes']['label']) ) {
						$this->elementsHtml .= $res['attributes']['label'] ."</label>\n";
					}
					break;
				case 'select':
					if ( !empty($res['attributes']['label']) ) {
						$this->setElementHtml( array( 	'type' => 'label',
														'attributes' => array( 'id' => $res['attributes']['id'],
																				'label' => $res['attributes']['label'],
																				'lbl_attributes' => $res['attributes']['lbl_attributes'] ) ) );
						$this->elementsHtml .= $res['attributes']['label'];
						if ( $this->labelMode == 'nowrapp' ) {
							$this->elementsHtml .= "</label>\n";
						}
					}
					$this->elementsHtml .= "<select name=\"". $res['attributes']['name'] ."\" ".
											"id=\"". $res['attributes']['id'] ."\" ".
											"size=\"". $res['attributes']['size'] ."\"";
					if ( $res['attributes']['multiple'] == 1 ) {
						$this->elementsHtml .= "multiple=\"multiple\"";
					}
					$this->elementsHtml .= $res['attributes']['elem_attributes'] .">";
					foreach ( $res['attributes']['data'] as $data ) {
						if ( $data['type'] == 'optgroup_open' || $data['type'] == 'optgroup_close' ) {
							$this->setElementHtml($data);
						}
						if ( $data['type'] == 'option' ) {
							$this->elementsHtml .= "\n<option value=\"". $data['attributes']['value'] ."\"";
							if ( $data['attributes']['value'] == $res['attributes']['selected'] ) {
								$this->elementsHtml .= " selected=\"selected\"";
							}
							$this->elementsHtml .= ">". $data['attributes']['label'] ."</option>";
						}
					}
					$this->elementsHtml .= "\n</select>";
					if ( !empty($res['attributes']['label']) && $this->labelMode == 'wrapp' ) {
						$this->elementsHtml .= "</label>\n";
					}
					break;
				case 'paragraph':
					$this->elementsHtml .= "<p". $res['attributes']['elem_attributes'] .">".
											$res['attributes']['value'] ."</p>\n";
					break;
				case 'container':
					$this->elementsHtml .= "<div". $res['attributes']['elem_attributes'] .">".
											$res['attributes']['value'] ."</div>\n";
					break;
				case 'file':
					if ( !empty($res['attributes']['label']) ) {
						$this->setElementHtml( array( 	'type' => 'label',
														'attributes' => array( 'id' => $res['attributes']['id'],
																				'label' => $res['attributes']['label'],
																				'lbl_attributes' => $res['attributes']['lbl_attributes'] ) ) );
						$this->elementsHtml .= $res['attributes']['label'];
						if ( $this->labelMode == 'nowrapp' ) {
							$this->elementsHtml .= "</label>\n";
						}
					}
					$this->elementsHtml .= "<input type=\"file\" id=\"". $res['attributes']['id'] ."\" ".
											"name=\"". $res['attributes']['name'] ."\" ".
											$res['attributes']['elem_attributes'] ." />";
					
					if ( !empty($res['attributes']['label']) && $this->labelMode == 'wrapp' ) {
						$this->elementsHtml .= "</label>\n";
					}
					break;
			}
		}
	}
	
	/**
	* Sp�cifie si le formulaire a été envoyé ou non
	*
	* @return boolean renvoi vrai si le formulaire a été soumis, false sinon
	* @access public
	*/
	function isSubmitted() {
		if ( !empty($this->submittedData) ) {
			# fonction qui permettrait d'effectuer un traitement
			$this->manageSubmittedData();
			return true;
		}
		return false;
	}
	
	/**
	* Effectue un traitement sur les valeurs soumises du formulaire
	*
	* @access private
	*/
	function manageSubmittedData() {
		# si besoin...
	}
	
	/**
	* Ajoute un message en début du formulaire dans un paragraphe
	*
	* Peut être utilisé pour des messages d'erreur par exemple
	*
	* @param string $msg message à afficher
	* @param $container balise contenant le message. Peut prendre les valeurs 'p' pour un paragraphe ou 'd' pour une div. Vaut 'p' par défaut
	* @param $elem_attributes attributs pouvant être ajoutés à la balise du message pour les css (class, id ou style inline) ou du javascript
	* @access public
	*/
	function addMsgAtTop( $msg, $container = 'p', $elem_attributes = '' ) {
		$attributes['value']			= $msg;
		$attributes['elem_attributes']	= '';
		
		if ( !empty($elem_attributes) ) {
			$attributes['elem_attributes'] = ' '. $elem_attributes;
		}
		
		if ( $container == 'p' ) {
			$type = 'paragraph';
		} elseif ( $container == 'd' ) {
			$type = 'container';
		}
		$res = array( 'type' => $type, 'attributes' => $attributes);
		array_unshift( $this->elements, $res);
	}
	
	/**
	* Ajoute une valeur soumise à la liste des valeurs à vérifier
	* 
	* Permet de définir les variables à tester parmis celles présentes
	* dans le formulaire en précisant le type de vérification
	*
	* @param string $name nom de l'élément à vérifier
	* @param string $dataType type de vérification éeffectuer sur cet élément. Voir la méthode {@link verifyData()} pour la liste des types
	* @param string $option options possibles à passer pour le type de vérification à effectuer
	* @access public
	*/
	function addDataToVerif($name, $dataType, $option = '' ) {
		if ( array_key_exists( trim( $name ), $this->submittedData ) ) {
			$nb = count($this->verifData);
			$this->verifData[$nb]['name']     = trim( strtolower( $name ) );
			$this->verifData[$nb]['value']    = trim($this->submittedData[$name]);
			$this->verifData[$nb]['dataType'] = trim( strtolower( $dataType ) );
			$this->verifData[$nb]['option']   = trim( strtolower( $option ) );
		} else {
			$this->addMsgAtTop( 'impossible de verifier le champs <em>'. $name .'</em>. il n\'existe pas dans le formulaire' );
		}
	}
	
	/**
	* Vérifie les variables spécifiées par la méthode {@link addDataToVerif()}
	*
	* @return boolean Retourne vrai si les données sont valides, faux sinon
	* @access public
	*/
	function isValidData() {
		$error = false;
		foreach( $this->verifData as $data ) {
			switch ( $data['dataType'] ) {
				case 'notnull':
					if ( empty($data['value']) ) {
						$this->addMsgAtTop( 'le champ <em>'. $data['name'] .'</em> est requis. Veuillez le remplir svp' );
						$error = true;
					}
					break;
				case 'maxlength':
					if ( empty($data['option']) ) {
						$this->addMsgAtTop( 'Veuillez pr�ciser la longueur maximale demand�e pour le champ <em>'. $data['name'] .'</em>' );
						$error = true;
					} elseif ( strlen( $data['value'] ) > $data['option'] ) {
						$this->addMsgAtTop( 'le champ <em>'. $data['name'] .'</em> est trop long. (longueur max: '. $data['option'] .')' );
						$error = false;
					}
					break;
				case 'minlength':
					if (  empty($data['option']) ) {
						$this->addMsgAtTop( 'Veuillez pr�ciser la longueur minimale demand�e pour le champ <em>'. $data['name'] .'</em>' );
						$error = true;
					} elseif ( strlen( $data['value'] ) < $data['option'] ) {
						$this->addMsgAtTop( 'le champ <em>'. $data['name'] .'</em> est trop court. (longueur min: '. $data['option'] .')' );
						$error = true;
					}
					break;
				case 'email':
					if ( !ereg( "^.+@.+\\..+$", $data['value'] ) ) {
						$this->adMsgAtTop( 'le champ <em>'. $data['name'] .'</em> doit etre une adresse email valide svp');
						$error = true;
					}
					break;
				default:
					$this->adMsgAtTop( 'le type de v�rification demand� n\'existe pas');
					break;
			}
		}
		if ($error == true) {
			return false;
		}
		return true;
	}
	
	/**
	* Modifie le type de mise en page du label avec l'élément de formulaire auquel il est associé
	*
	* @param string $mode type d'affichage : 'wrapp' ou 'nowrapp'
	* @access public
	*/
	function setLabelMode( $mode ) {
		if ( $mode == 'wrapp' || $mode == 'nowrapp' ) {
			$this->labelMode = $mode;
		}
	}
	
	
	/**
	* Retourne l'indice du tableau {@link elements} pour l'élément désigné par $name
	*
	* @param string $name nom de l'élement dans le tableau {@link elements}
	* @access private
	*/
	function getElementIndex($name) {
		foreach ($this->elements as $k => $v) {
			foreach ($v as $k_1 => $v_1) {
				if ($k_1 == 'attributes') {
					foreach ($v_1 as $k_11 => $v_11) {
						if ($k_11 == 'name') {
							if ($v_11 == $name) {
								return $k;
							}
						}
					}
				}
				
			}
		}
	}
}


?>
