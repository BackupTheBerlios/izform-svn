<?php


/**
* Classe de g�n�ration et de contr�le de formulaires
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
* balise par d�faut qui encadre les �l�ments de formulaire
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
	* Cha�ne de caract�re contenant la sortie html du formulaire
	*
	* @var string
	* @access private
	*/
	var $elementsHtml;
	
	/**
	* Tableau contenant les �l�ments du formulaire
	*
	* @var array
	* @access private
	*/
	var $elements = array();
	
	/**
	* Tableau contenant les �l�ments � charger dans une liste d�roulante
	*
	* @var array
	* @access private
	*/
	var $selectElements = array();
	
	/**
	* Tableau de 2 valeurs contenant les indices qui permettront de positionner un �l�ment fieldset
	*
	* @var array
	* @access private
	*/
	var $fieldSetElements = array();
	
	/**
	* Tableau de 2 valeurs contenant les indices qui permettront de positionner un �l�ment optgroup dans une liste d�roulante
	*
	* @var array
	* @access private
	*/
	var $optGroupElements = array();
	
	/**
	* Tableau qui est la copie du tableau superglobal correspond � la m�thode employ�e pour soumettre le formulaire
	* 
	* @var array
	* @access public
	*/
	var $submittedData = array();
	
	/**
	* Tableau des valeurs � contr�ler une fois le formulaire soumis
	*
	* @var array
	* @access private
	*/
	var $verifData = array();
	
	/**
	* D�fini la mani�re dont est affich� le label
	*
	* Par d�faut, vaut 'wrapp' : le label englobe l'�l�ment de formulaire
	* Autre valeur possible : 'nowrapp' : le label est referm� avant l'�l�ment de formulaire
	*
	* @var string
	*/
	var $labelMode = 'wrapp';
	
	
	/**
	* Constructeur de la classe
	*
	* D�fini le script cible du formulaire, la m�thode par laquelle il est soumis
	* et quel est le type d'encryption de celui-ci
	* Le constructeur enregistre la balise d'ouverture du formulaire,
	* copie dans la variable de classe {@link $submittedData} le bon tableau superglobal en fonction
	* de la m�thode employ�e pour envoyer le formulaire
	*
	* @param string $method methode par laquelle est soumis le formulaire. Peut prendre les valeurs 'post' ou 'get' ('post' par defaut)
	* @param string $action script cible du formulaire. Si $action n'est pas sp�cifi�e elle prend pour valeur le script m�me qui contient le formulaire
	* @param string $id identifiant du formulaire. Prend 'form1' par d�faut
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
	* Ajoute un element de type input type="text" au formulaire
	*
	* @param string $name nom du champs texte
	* @param string $label label du champs texte: texte plac� devant le champ pour l'identifier
	* @param string $value valeur du champs texte
	* @param int $size taille du champs texte
	* @param int $maxlength taille maximale du champs texte
	* @param string $id identifiant du champs texte. Si n'est pas affect�, prend la valeur de $name
	* @param string $before �lement ouvrant encadrant le champs texte. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $after �lement fermant encadrant le champs texte. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $lbl_attributes attributs pouvant �tre ajout�s au label du champs texte pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant �tre ajout�s au champs texte pour les css (class, id, style inline) ou du javascript ou autre
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
	* Ajoute un element de type input type="password" au formulaire
	*
	* @param string $name nom du champs password
	* @param string $label label du champs password: texte place devant le champ pour l'identifier
	* @param string $value valeur du champs password
	* @param int $size taille du champs password
	* @param int $maxlength taille maximale du champs password
	* @param string $id identifiant du champs password. Si n'est pas affect�, prend la valeur de $name
	* @param string $before �lement ouvrant encadrant le champs password. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $after �lement fermant encadrant le champs password. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $lbl_attributes attributs pouvant �tre ajout�s au label du champs password pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant �tre ajout�s au champs password pour les css (class, id, style inline) ou du javascript ou autre
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
	* Ajoute un element de type input type="checkbox" au formulaire
	*
	* @param string $name nom de l'�l�ment checkbox
	* @param string $label label de l'�l�ment checkbox : texte place apr�s l'�lement pour l'identifier
	* @param string $value valeur de l'�l�ment checkbox
	* @param boolen $check valeur par d�faut de l'�tat de la case � cocher (0 = non coch�e, 1 = coch�e)
	* @param string $id identifiant de l'�l�ment checkbox. Si n'est pas affect�, prend la valeur de $name
	* @param string $before �lement ouvrant encadrant l'�l�ment checkbox. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $after �lement fermant encadrant l'�l�ment checkbox. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $lbl_attributes attributs pouvant �tre ajout�s au label de l'�l�ment checkbox pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant �tre ajout�s � l'�l�ment checkbox pour les css (class, id, style inline) ou du javascript ou autre
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
	* Ajoute un element de type input type="radio" au formulaire
	*
	* @param string $name nom de l'�l�ment radio
	* @param string $label label de l'�l�ment radio : texte place apr�s l'�lement pour l'identifier
	* @param string $value valeur de l'�l�ment radio
	* @param boolen $check valeur par d�faut de l'�tat du bouton radio (0 = non coch�, 1 = coch�)
	* @param string $id identifiant de l'�l�ment radio. Si n'est pas affect�, prend la valeur de $name
	* @param string $before �lement ouvrant encadrant l'�l�ment radio. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $after �lement fermant encadrant l'�l�ment radio. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $lbl_attributes attributs pouvant �tre ajout�s au label de l'�l�ment radio pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant �tre ajout�s � l'�l�ment radio pour les css (class, id, style inline) ou du javascript ou autre
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
	* Ajoute un element de type input type="submit" au formulaire
	*
	* @param string $value valeur du bouton submit (texte affich� sur le bouton)
	* @param string $name nom du bouton submit
	* @param string $label label du bouton submit : texte place devant le bouton pour l'identifier
	* @param string $id identifiant du bouton submit. Si n'est pas affect�, prend la valeur de $name
	* @param string $before �lement ouvrant encadrant le bouton submit. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $after �lement fermant encadrant le bouton submit. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $lbl_attributes attributs pouvant �tre ajout�s au label du bouton submit pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant �tre ajout�s au bouton submit pour les css (class, id, style inline) ou du javascript ou autre
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
	* Ajoute un element de type input type="reset" au formulaire
	*
	* @param string $value valeur du bouton reset (texte affich� sur le bouton)
	* @param string $name nom du bouton reset
	* @param string $label label du bouton reset : texte place devant le bouton pour l'identifier
	* @param string $id identifiant du bouton reset. Si n'est pas affect�, prend la valeur de $name
	* @param string $before �lement ouvrant encadrant le bouton reset. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $after �lement fermant encadrant le bouton reset. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $lbl_attributes attributs pouvant �tre ajout�s au label du bouton reset pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant �tre ajout�s au bouton reset pour les css (class, id, style inline) ou du javascript ou autre
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
	* Ajoute un element de type input type="file" au formulaire
	*
	* @param string $name nom du bouton parcourir pour joindre un fichier
	* @param string $label label du bouton parcourir : texte place devant le bouton pour l'identifier
	* @param string $id identifiant du bouton parcourir. Si n'est pas affect�, prend la valeur de $name
	* @param string $before �lement ouvrant encadrant le bouton parcourir. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $after �lement fermant encadrant le bouton parcourir. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $lbl_attributes attributs pouvant �tre ajout�s au label du bouton parcourir pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant �tre ajout�s au bouton parcourir pour les css (class, id, style inline) ou du javascript ou autre
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
	* Ajoute un element de type input type="hidden" au formulaire
	*
	* @param string $name nom du champ cach�
	* @param string $value valeur du champ cach�
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
	* @param string $label label de la zone de texte : texte place devant cette zone pour l'identifier
	* @param string $value valeur de la zone de texte
	* @param string $id identifiant unique de la zone de texte. Si n'est pas affect�, prend la valeur de $name
	* @param string $before �lement ouvrant encadrant la zone de texte. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $after �lement fermant encadrant la zone de texte. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $lbl_attributes attributs pouvant �tre ajout�s au label de la zone de texte pour les css (class, id, style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant �tre ajout�s � la zone de texte pour les css (class, id, style inline) ou du javascript ou autre
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
	* @param string $elem_attributes attributs pouvant �tre ajout�s au paragraphe pour les css (class, id, style inline)
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
	* Ajoute un �l�ment '<option></option> dans une liste d�roulante
	* Charge la paire valeur/label dans la liste deroulante
	*
	* addDataInSelect pr�charge en fait les donn�es qui seront dans la liste d�roulante
	* Il faut donc pr�charger toutes les valaurs que l'on souhaite pour la liste avant d'ajouter celle-ci
	* avec la m�thode {@link addSelect()}
	*
	* @param string $value valeur de l'attribut 'value' de la balise 'option'
	* @param string $label texte visible de l'element option de la liste deroulante
	* @access public
	*/
	function addDataInSelect( $value, $label ) {
		$attributes['value'] = $value;
		$attributes['label'] = $label;
		$res = array( 'type' => 'option', 'attributes' => $attributes);
		
		$this->setSelectElement($res);
	}
	
	/**
	* Ajoute un �l�ment dans le tableau selectElement[], tableau de pr�chargement des �l�ments pour une lsite d�roulante
	*
	* @param array $res tableau associatif contenu la valeur et le label de la balise '<option>' � rajouter � la liste d�roulante
	* @access private
	*/
	function setSelectElement( $res ) {
		$this->selectElements[] = $res;
	}
	
	/**
	* D�fini les limites pour mettre des �l�ments '<option>' d'une liste d�roulante dans une balsie '<optgroup>'
	*
	* Cette m�thode d�fini les bornes parmi les �l�ments d�j� pr�charg�s par la m�thode {@link addDataInSelect()}
	*
	* @param string $start �lement de d�but. Correspond � l'attribut $value du premier �l�ment qui sera dans l'optgroup
	* @param string $end �lement de fin. Correspond � l'attribut $value du dernier �l�ment qui sera dans l'optgroup
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
	* Ajoute la balise '<optgroup>' apr�s avoir d�fini ses bornes
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
	* Ajoute une liste deroulante au formulaire
	*
	* Vous devez tout d'abord charger les elements constituants la liste avec les m�thodes
	* {@link addDataInSelect()}, {@link setOptGroupLimiters()} et {@link addOptGroup()}
	* Une fois la liste ajout�e, le tableau des �l�ments qui ont �t� pr�charg�s est vid�
	*
	* @param string $name nom de la liste d�roulante
	* @param string $label label de la liste deroulante: texte plac� devant pour l'identifier
	* @param string $selected indique l'�l�ment de la liste d�roulante s�l�ctionn� par defaut. Correspond � l'attribut 'value' de la balsie '<option>'
	* @param string $id identifiant unique de la liste d�roulante
	* @param int $size nombre de lignes visibles de la liste d�roulante. Vaut 1 par defaut
	* @param string $before �lement ouvrant encadrant laliste d�roulante. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $after �lement fermant encadrant la liste d�roulante. Peut prendre 3 valeurs : P (paragraphe), D (div) ou B (espace). Vaut P par d�faut
	* @param string $lbl_attributes attributs pouvant �tre ajout�s au label de la liste d�roulante pour les css (class, id ou style inline) ou du javascript
	* @param string $elem_attributes attributs pouvant �tre ajout�s � la liste d�roulante pour les css (class, id ou style inline) ou du javascript
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
	* Vide la liste des �l�ments � charger dans une liste d�roulante
	*
	* @access private
	*/
	function resetDataInSelect() {
		$this->selectElements = array();
	}
	
	/**
	* D�fini les limites pour mettre un �l�ment fieldset dans le formulaire
	*
	* Cette m�thode d�fini les bornes parmi les �l�ments du formulaire
	*
	* @param string $start �lement de d�but. Correspond � l'attribut $name du premier �l�ment qui sera dans l'optgroup
	* @param string $end �lement de fin. Correspond � l'attribut $name du dernier �l�ment qui sera dans l'optgroup
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
	* Ajoute un fieldset apr�s avoir d�fini ses bornes avec la m�thode {@link setFieldSetLimiters()}
	*
	* @param string $legend l�gende associ�e au fieldset
	* @param string $elem_attributes attributs pouvant �tre ajout�s au fieldset pour les css (class, id ou style inline) ou du javascript
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
	* Ajoute un �l�ment de formulaire dans le tableau elements[] qui les contient tous
	*
	* @param array tableau contenant les param�tres d'un �l�ment de formulaire � ajouter
	* @access private
	*/
	function setElement( $res ) {
		$this->elements[] = $res;
	}
	
	/**
	* Affiche le formulaire
	* 
	* Cette m�thode ajoute le dernier �l�ment au formulaire (balise de fermeture de celui-ci) puis
	* va faire g�n�rer la sortie html pour chaque �l�ment avant de l'afficher
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
	* G�n�re la sortie html pour les �l�ments de formulaire
	*
	* Stock la sortie html dans la chaine {@link elementsHtml}
	*
	* @param array $res tableau contenant les param�tres d'un �lement de formulaire
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
	* Sp�cifie si le formulaire a �t� envoy� ou non
	*
	* @return boolen renvoi vrai si le formulaire a �t� soumis, falsse sinon
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
	* Ajoute un message en d�but du formulaire dans un paragraphe
	*
	* Peut �tre utilis� pour des messages d'erreur par exemple
	*
	* @param string $msg message � afficher
	* @param $container balise contenant le message. Peut prendre les valeurs 'p' pour un paragraphe ou 'd' pour une div. Vaut 'p' par d�faut
	* @param $elem_attributes attributs pouvant �tre ajout�s � la balise du message pour les css (class, id ou style inline) ou du javascript
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
	* Ajoute une valeur soumise � la liste de celles � v�rifier
	* 
	* Permet de d�finir les variables � tester parmis celles pr�sentent
	* dans le formulaire en pr�cisant le type de v�rification
	*
	* @param string $name nom de l'�l�ment � v�rifier
	* @param string $dataType type de v�rification � effectuer sur cet �l�ment. Voir la m�thode {@link verifyData()} pour la liste des types
	* @param string $option options possibles � passer pour le type de v�rification � effectuer
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
	* V�rifie les variables sp�cifi�es par la m�thode {@link addDataToVerif()}
	*
	* @return boolean Renvoi vrai si les donn�es sont valides, faux sinon
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
	* Modifie le type de mise en page du label avec l'�l�ment de formulaire auquel il est associ�
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
	*
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
