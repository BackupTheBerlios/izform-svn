<?php


/**
* bibliothèque de classes permettant un mapping objet des méthodes
* principales d'IzForm
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
* Classe réalisant le mapping objet du prototype de la méthode {@link addText()}
* de la classe IzForm
* @package izForm
*/
class textElem {
	
	var $label;
	var $value;
	var $size;
	var $maxlength;
	var $id;
	var $before;
	var $after;
	var $lbl_attributes;
	var $elem_attributes;
	
	var $pter;
	var $indice;
	
	function textElem( &$classe, $indice) {
		$this->pter = &$classe;
		$this->indice = $indice;
		
		$this->label			= &$this->pter->elements[$this->indice]['attributes']['label'];
		$this->value			= &$this->pter->elements[$this->indice]['attributes']['value'];
		$this->size				= &$this->pter->elements[$this->indice]['attributes']['size'];
		$this->maxlength		= &$this->pter->elements[$this->indice]['attributes']['maxlength'];
		$this->id				= &$this->pter->elements[$this->indice]['attributes']['id'];
		$this->before			= &$this->pter->elements[$this->indice - 1]['type'];
		$this->after			= &$this->pter->elements[$this->indice + 1]['type'];
		$this->lbl_attributes	= &$this->pter->elements[$this->indice]['attributes']['lbl_attributes'];
		$this->elem_attributes	= &$this->pter->elements[$this->indice]['attributes']['elem_attributes'];
	}
}

/**
* Classe réalisant le mapping objet du prototype de la méthode {@link addPassword()}
* de la classe IzForm
* @package izForm
*/
class pwdElem {
	
	var $label;
	var $value;
	var $size;
	var $maxlength;
	var $id;
	var $before;
	var $after;
	var $lbl_attributes;
	var $elem_attributes;
	
	var $pter;
	var $indice;
	
	function pwdElem( &$classe, $indice) {
		$this->pter = &$classe;
		$this->indice = $indice;
		
		$this->label			= &$this->pter->elements[$this->indice]['attributes']['label'];
		$this->value			= &$this->pter->elements[$this->indice]['attributes']['value'];
		$this->size				= &$this->pter->elements[$this->indice]['attributes']['size'];
		$this->maxlength		= &$this->pter->elements[$this->indice]['attributes']['maxlength'];
		$this->id				= &$this->pter->elements[$this->indice]['attributes']['id'];
		$this->before			= &$this->pter->elements[$this->indice - 1]['type'];
		$this->after			= &$this->pter->elements[$this->indice + 1]['type'];
		$this->lbl_attributes	= &$this->pter->elements[$this->indice]['attributes']['lbl_attributes'];
		$this->elem_attributes	= &$this->pter->elements[$this->indice]['attributes']['elem_attributes'];
	}
}

/**
* Classe réalisant le mapping objet du prototype de la méthode {@link addCheckBox()}
* de la classe IzForm
* @package izForm
*/
class chkBoxElem {
	
	var $label;
	var $value;
	var $check;
	var $id;
	var $before;
	var $after;
	var $lbl_attributes;
	var $elem_attributes;
	
	var $pter;
	var $indice;
	
	function chkBoxElem( &$classe, $indice) {
		$this->pter = &$classe;
		$this->indice = $indice;
		
		$this->label			= &$this->pter->elements[$this->indice]['attributes']['label'];
		$this->value			= &$this->pter->elements[$this->indice]['attributes']['value'];
		$this->check			= &$this->pter->elements[$this->indice]['attributes']['check'];
		$this->id				= &$this->pter->elements[$this->indice]['attributes']['id'];
		$this->before			= &$this->pter->elements[$this->indice - 1]['type'];
		$this->after			= &$this->pter->elements[$this->indice + 1]['type'];
		$this->lbl_attributes	= &$this->pter->elements[$this->indice]['attributes']['lbl_attributes'];
		$this->elem_attributes	= &$this->pter->elements[$this->indice]['attributes']['elem_attributes'];
	}
}

/**
* Classe réalisant le mapping objet du prototype de la méthode {@link addRadio()}
* de la classe IzForm
* @package izForm
*/
class radioElem {
	
	var $label;
	var $value;
	var $check;
	var $id;
	var $before;
	var $after;
	var $lbl_attributes;
	var $elem_attributes;
	
	var $pter;
	var $indice;
	
	function radioElem( &$classe, $indice) {
		$this->pter = &$classe;
		$this->indice = $indice;
		
		$this->label			= &$this->pter->elements[$this->indice]['attributes']['label'];
		$this->value			= &$this->pter->elements[$this->indice]['attributes']['value'];
		$this->check			= &$this->pter->elements[$this->indice]['attributes']['check'];
		$this->id				= &$this->pter->elements[$this->indice]['attributes']['id'];
		$this->before			= &$this->pter->elements[$this->indice - 1]['type'];
		$this->after			= &$this->pter->elements[$this->indice + 1]['type'];
		$this->lbl_attributes	= &$this->pter->elements[$this->indice]['attributes']['lbl_attributes'];
		$this->elem_attributes	= &$this->pter->elements[$this->indice]['attributes']['elem_attributes'];
	}
}

/**
* Classe réalisant le mapping objet du prototype de la méthode {@link addSubmit()}
* de la classe IzForm
* @package izForm
*/
class submitElem {
	
	var $label;
	var $value;
	var $id;
	var $before;
	var $after;
	var $lbl_attributes;
	var $elem_attributes;
	
	var $pter;
	var $indice;
	
	function submitElem( &$classe, $indice) {
		$this->pter = &$classe;
		$this->indice = $indice;
		
		$this->label			= &$this->pter->elements[$this->indice]['attributes']['label'];
		$this->value			= &$this->pter->elements[$this->indice]['attributes']['value'];
		$this->id				= &$this->pter->elements[$this->indice]['attributes']['id'];
		$this->before			= &$this->pter->elements[$this->indice - 1]['type'];
		$this->after			= &$this->pter->elements[$this->indice + 1]['type'];
		$this->lbl_attributes	= &$this->pter->elements[$this->indice]['attributes']['lbl_attributes'];
		$this->elem_attributes	= &$this->pter->elements[$this->indice]['attributes']['elem_attributes'];
	}
}

/**
* Classe réalisant le mapping objet du prototype de la méthode {@link addReset()}
* de la classe IzForm
* @package izForm
*/
class resetElem {
	
	var $label;
	var $value;
	var $id;
	var $before;
	var $after;
	var $lbl_attributes;
	var $elem_attributes;
	
	var $pter;
	var $indice;
	
	function resetElem( &$classe, $indice) {
		$this->pter = &$classe;
		$this->indice = $indice;
		
		$this->label			= &$this->pter->elements[$this->indice]['attributes']['label'];
		$this->value			= &$this->pter->elements[$this->indice]['attributes']['value'];
		$this->id				= &$this->pter->elements[$this->indice]['attributes']['id'];
		$this->before			= &$this->pter->elements[$this->indice - 1]['type'];
		$this->after			= &$this->pter->elements[$this->indice + 1]['type'];
		$this->lbl_attributes	= &$this->pter->elements[$this->indice]['attributes']['lbl_attributes'];
		$this->elem_attributes	= &$this->pter->elements[$this->indice]['attributes']['elem_attributes'];
	}
}

/**
* Classe réalisant le mapping objet du prototype de la méthode {@link addFile()}
* de la classe IzForm
* @package izForm
*/
class fileElem {
	
	var $label;
	var $id;
	var $before;
	var $after;
	var $lbl_attributes;
	var $elem_attributes;
	
	var $pter;
	var $indice;
	
	function fileElem( &$classe, $indice) {
		$this->pter = &$classe;
		$this->indice = $indice;
		
		$this->label			= &$this->pter->elements[$this->indice]['attributes']['label'];
		$this->id				= &$this->pter->elements[$this->indice]['attributes']['id'];
		$this->before			= &$this->pter->elements[$this->indice - 1]['type'];
		$this->after			= &$this->pter->elements[$this->indice + 1]['type'];
		$this->lbl_attributes	= &$this->pter->elements[$this->indice]['attributes']['lbl_attributes'];
		$this->elem_attributes	= &$this->pter->elements[$this->indice]['attributes']['elem_attributes'];
	}
}

/**
* Classe réalisant le mapping objet du prototype de la méthode {@link addHidden()}
* de la classe IzForm
* @package izForm
*/
class hiddenElem {
	
	var $value;
	
	var $pter;
	var $indice;
	
	function hiddenElem( &$classe, $indice) {
		$this->pter = &$classe;
		$this->indice = $indice;
		
		$this->value	= &$this->pter->elements[$this->indice]['attributes']['value'];
	}
}

/**
* classe réalisant le mapping objet du prototype de la méthode {@link addTextArea()}
* de la classe IzForm
* @package izForm
*/
class textAreaElem {
	
	var $label;
	var $rows;
	var $cols;
	var $value;
	var $id;
	var $before;
	var $after;
	var $lbl_attributes;
	var $elem_attributes;
	
	var $pter;
	var $indice;
	
	function textAreaElem( &$classe, $indice) {
		$this->pter = &$classe;
		$this->indice = $indice;
		
		$this->label			= &$this->pter->elements[$this->indice]['attributes']['label'];
		$this->rows				= &$this->pter->elements[$this->indice]['attributes']['rows'];
		$this->cols				= &$this->pter->elements[$this->indice]['attributes']['cols'];
		$this->value			= &$this->pter->elements[$this->indice]['attributes']['value'];
		$this->id				= &$this->pter->elements[$this->indice]['attributes']['id'];
		$this->before			= &$this->pter->elements[$this->indice - 1]['type'];
		$this->after			= &$this->pter->elements[$this->indice + 1]['type'];
		$this->lbl_attributes	= &$this->pter->elements[$this->indice]['attributes']['lbl_attributes'];
		$this->elem_attributes	= &$this->pter->elements[$this->indice]['attributes']['elem_attributes'];
	}
}

/**
* Classe réalisant le mapping objet du prototype de la méthode {@link addSelect()}
* de la classe IzForm
* @package izForm
*/
class selectElem {
	
	var $label;
	var $selected;
	var $size;
	var $multiple;
	var $id;
	var $before;
	var $after;
	var $lbl_attributes;
	var $elem_attributes;
	
	var $pter;
	var $indice;
	
	function selectElem( &$classe, $indice) {
		$this->pter = &$classe;
		$this->indice = $indice;
		
		$this->label			= &$this->pter->elements[$this->indice]['attributes']['label'];
		$this->selected			= &$this->pter->elements[$this->indice]['attributes']['selected'];
		$this->size				= &$this->pter->elements[$this->indice]['attributes']['size'];
		$this->multiple			= &$this->pter->elements[$this->indice]['attributes']['multiple'];
		$this->id				= &$this->pter->elements[$this->indice]['attributes']['id'];
		$this->before			= &$this->pter->elements[$this->indice - 1]['type'];
		$this->after			= &$this->pter->elements[$this->indice + 1]['type'];
		$this->lbl_attributes	= &$this->pter->elements[$this->indice]['attributes']['lbl_attributes'];
		$this->elem_attributes	= &$this->pter->elements[$this->indice]['attributes']['elem_attributes'];
	}
}


?>
