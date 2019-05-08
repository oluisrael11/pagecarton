<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Ayoola_Form_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php 10-26-2011 9.13pm ayoola $
 */

/**
 * @see 
 */
 
require_once 'Ayoola/Abstract/Table.php';


/**
 * @category   PageCarton
 * @package    Ayoola_Form_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

abstract class Ayoola_Form_Abstract extends Ayoola_Abstract_Table
{	
	
    /**
     * Whether class is playable or not
     *
     * @var boolean
     */
	protected static $_playable = true;
	
    /**
     * Access level for player
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 99, 98 );
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'form_name' );
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'Ayoola_Form_Table';
	
    /**
     * 
     * @var string
     */
	protected $_idColumn = 'form_name';
	
    /**
     * creates the form for creating and editing form
     * 
     * param string The Value of the Submit Button
     * param string Value of the Legend
     * param array Default Values
     */
	public function createForm( $submitValue = null, $legend = null, Array $values = null )  
    {
	//	var_export( $values );
		//	Form to create a new form
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName() ) );
		$fieldset = new Ayoola_Form_Element;
		$form->submitValue = $submitValue ;
	//	$form->oneFieldSetAtATime = true;
	//	$fieldset->placeholderInPlaceOfLabel = false;
		$fieldset->addElement( array( 'name' => 'form_title', 'placeholder' => 'Enter a title...', 'type' => 'InputText', 'value' => @$values['form_title'] ) );
		$fieldset->addRequirement( 'form_title', array( 'WordCount' => array( 3, 50 )  ) );
		if( is_null( $values ) )
		{		
			$filter = new Ayoola_Filter_Name();
			$filter->replace = '-';
	//		$values['menu_name'] = strtolower( $filter->filter( $values['menu_label'] ) );
			$fieldset->addElement( array( 'name' => 'form_name', 'type' => 'Hidden', 'value' => @$values['form_name'] ) );
			$fieldset->addFilter( 'form_name', array( 'DefiniteValue' => strtolower( $filter->filter( $this->getGlobalValue( 'form_title' ) ) )  ) );
		}	

		$fieldset->addElement( array( 'name' => 'form_description', 'placeholder' => 'Enter a short description explaining the purpose of this form. This information will be displayed to users on top of the form. ', 'type' => 'TextArea', 'value' => @$values['form_description'] ) );

		Application_Article_Abstract::initHTMLEditor();
		
		$fieldset->addElement( array( 'name' => 'form_success_message', 'placeholder' => 'Enter a short success message. This information will be displayed to users when the form is submitted. ', 'data-html' => true, 'type' => 'TextArea', 'value' => @$values['form_success_message'] ) );
		$fieldset->addElement( array( 'name' => 'button_value', 'placeholder' => 'e.g. Submit', 'type' => 'InputText', 'value' => @$values['button_value'] ) );
		
		$options =  array( 
					//		'send_mail' => 'Send me an e-mail of the form data when someone fills the form.', 
							
							//	Saving all information to db by default 
					//		'database' => 'Save the data filled by user to database.', 
							'group' => 'Group this form into fieldsets', 
							'requirements' => 'This form has some requirements.', 
							'callbacks' => 'Perform some actions (Call some PHP classes) after the form is filled.', 
						);
		$fieldset->addElement( array( 'name' => 'form_options', 'label' => 'Form Options', 'type' => 'Checkbox', 'value' => @$values['form_options'] ), $options );
		
		//	Auth Level
		
		$authLevel = new Ayoola_Access_AuthLevel;
		$authLevel = $authLevel->select();
		require_once 'Ayoola/Filter/SelectListArray.php';
		$filter = new Ayoola_Filter_SelectListArray( 'auth_level', 'auth_name');
		$authLevel = $filter->filter( $authLevel );
	//	self::v( $values['auth_level'] );
	//	self::v( $authLevel ); 
		$fieldset->addElement( array( 'name' => 'auth_level', 'label' => 'Which user groups should this form be available to?', 'type' => 'SelectMultiple', 'value' => @$values['auth_level'] ? : array( 0 ) ), $authLevel ); 
//		$fieldset->addRequirement( 'auth_level', array( 'InArray' => array_keys( $authLevel )  ) ); 
	//	unset( $authLevel );
		
		$fieldset->addLegend( $legend );
	//	$fieldset->addFilters( 'StripTags::Trim' );
		$form->addFieldset( $fieldset );   
	//	if( @$values['email'] ||  is_array( $this->getGlobalValue( 'form_options' ) ) && in_array( 'send_mail', $this->getGlobalValue( 'form_options' ) ) )
		{ 
			$fieldset = new Ayoola_Form_Element;
			$fieldset->addLegend( 'Enter the e-mail to send the form data to:' );
			$fieldset->addElement( array( 'name' => 'email', 'placeholder' => 'e.g. info@myCompany.com, support@myDepartment.com', 'type' => 'TextArea', 'value' => @$values['email'] ? : Ayoola_Application::getUserInfo( 'email' ) ) );
			$form->addFieldset( $fieldset );
		}
		$groupIds = $this->getGlobalValue( 'group_ids' ) ? : @$values['group_ids'];
		$groupTitle = $this->getGlobalValue( 'group_names' ) ? : @$values['group_names'];
		if( @$values['groups'] ||  is_array( $this->getGlobalValue( 'form_options' ) ) && in_array( 'group', $this->getGlobalValue( 'form_options' ) ) )
		{ 
			$i = 0;
			$newForm = new Ayoola_Form( array( 'name' => 'xxx', ) );
			$newForm->setParameter( array( 'no_fieldset' => true, 'no_form_element' => true, 'no_required_fieldset' => true ) );
			$newForm->wrapForm = false;
			do
			{
				$newFieldSet = new Ayoola_Form_Element;
				$newFieldSet->container = 'span';
				$newFieldSet->allowDuplication = true;
				$newFieldSet->duplicationData = array( 'add' => '+ Add New Fieldset Below', 'remove' => '- Remove Above Fieldset', 'counter' => 'group_counter', );
				$newFieldSet->container = 'span';
			//	$newFieldSet->wrapper = 'white-content-theme-border';
				$newFieldSet->wrapper = 'white-background';
				$newFieldSet->addLegend( 'Form Group <span name="group_counter">' . ( $i + 1 ) . '</span>' );
				$newFieldSet->addElement( array( 'name' => 'group_names', 'label' => 'Group Headline',  'multiple' => 'multiple', 'placeholder' => 'e.g. Group 1', 'type' => 'InputText', 'value' => @$values['group_names'][$i] ) );
				$newFieldSet->addElement( array( 'name' => 'group_descriptions', 'label' => 'Group Description',  'multiple' => 'multiple', 'placeholder' => 'e.g. Say how users should fill the fields in the group', 'type' => 'TextArea', 'value' => @$values['group_descriptions'][$i] ) );
				$options = new Ayoola_Object_Table_Wrapper;
				$options = $options->select();
				require_once 'Ayoola/Filter/SelectListArray.php';
				$filter = new Ayoola_Filter_SelectListArray( 'wrapper_name', 'wrapper_label');
				$options = $filter->filter( $options );
				$newFieldSet->addElement( array( 'name' => 'group_wrapper',  'multiple' => 'multiple', 'label' => 'Group Wrapper', 'placeholder' => '', 'type' => 'Select', 'value' => @$values['group_wrapper'][$i] ), array( '' => 'Default' ) + $options );
				$newFieldSet->addElement( array( 'name' => 'group_ids',  'multiple' => 'multiple', 'label' => 'Group ID', 'placeholder' => 'e.g. group-1', 'type' => 'Hidden', 'value' => @$values['group_ids'][$i] ) );
				
				$newForm->addFieldset( $newFieldSet );    
			//	self::v( $i );   
			//	var_export();
				$i++;
			}
			while( ! empty( $values['group_names'][$i] ) );
			$fieldset = new Ayoola_Form_Element;
			$fieldset->addElement( array( 'name' => 'xxxx', 'type' => 'Html', 'value' => '' ), array( 'html' => $newForm->view(), 'fields' => 'group_names,group_ids' ) );
			
			//	Autogenerate group ids
			if( $groupIds )
			{
				foreach( $groupIds as $eachKey => $eachGroup )
				{
					if( ! $eachGroup )
					{
						$groupIds[$eachKey] = md5( $groupTitle[$eachKey] );
					}
				}
				$fieldset->addFilter( 'group_ids', array( 'DefiniteValue' => array( $groupIds ) ) );						
			}
			$form->addFieldset( $fieldset );    
		}
	//	if( @$values['requirements'] ||  is_array( $this->getGlobalValue( 'form_options' ) ) && in_array( 'requirements', $this->getGlobalValue( 'form_options' ) ) )
		{ 
			$fieldset = new Ayoola_Form_Element;
		//	$fieldset->addLegend( 'Requirements of this form:' );
			$options = new Ayoola_Form_Requirement;
			$options = $options->select();
			if( $options ) 
			{
				require_once 'Ayoola/Filter/SelectListArray.php';
				$filter = new Ayoola_Filter_SelectListArray( 'requirement_name', 'requirement_label' );
				$options = $filter->filter( $options );
				$fieldset->addElement( array( 'name' => 'requirements', 'label' => 'Form Requirements', 'type' => 'SelectMultiple', 'value' => @$values['requirements'] ), $options );
			}
			$form->addFieldset( $fieldset );
		}
		if( @$values['callbacks'] ||  is_array( $this->getGlobalValue( 'form_options' ) ) && in_array( 'callbacks', $this->getGlobalValue( 'form_options' ) ) )
		{ 
			$fieldset = new Ayoola_Form_Element;
			$fieldset->addLegend( 'Callback objects for this form:' );
			$fieldset->addElement( array( 'name' => 'callbacks', 'placeholder' => 'e.g. Application_User_Creator', 'type' => 'MultipleInputText', 'value' => @$values['callbacks'] ), @$values['callbacks'] );
			$form->addFieldset( $fieldset ); 
		}
	//	if( is_array( $this->getGlobalValue( 'form_options' ) ) && in_array( 'send_mail', $this->getGlobalValue( 'form_options' ) ) )
		{ 
			$j = 0; // group count

			$reqOptions = Ayoola_Form_Validator::getInstance();
			$reqOptions = $reqOptions->select();
			$filter = new Ayoola_Filter_SelectListArray( 'validator_name', 'validator_title');
			$reqOptions = array( '' => 'No Validator' ) + $filter->filter( $reqOptions );  

			$multiOptions = Ayoola_Form_MultiOptions::getInstance();
			$multiOptions = $multiOptions->select();
			$filter = new Ayoola_Filter_SelectListArray( 'multioptions_name', 'multioptions_title');
			$multiOptions = $filter->filter( $multiOptions );  
	//		while( $j <= count( @$groupIds ) && $j < 9 )//	Do this for all each categories and don't forget the "uncategorized"
			{ 
				$i = 0;
				$newForm = new Ayoola_Form( array( 'name' => 'xxx', ) );
				$newForm->setParameter( array( 'no_fieldset' => true, 'no_form_element' => true, 'no_required_fieldset' => true ) );
			//	$newForm->setParameter( array( 'no_form_element' => true ) );
				$newForm->wrapForm = false;
				do
				{
/* 					if( isset( $values['element_group_name'][$i], $groupIds[$j] ) && $values['element_group_name'][$i] !== $groupIds[$j] )
					{
						$i++;
						continue;
					}
 */					$newFieldSet = new Ayoola_Form_Element;
					$newFieldSet->container = 'span';
					$newFieldSet->allowDuplication = true;
					$newFieldSet->duplicationData = array( 'add' => '+ Add new field below', 'remove' => '- Remove above field', 'counter' => 'field_counter', );  
					$newFieldSet->container = 'span';
				//	$newFieldSet->wrapper = 'white-content-theme-border';
					$newFieldSet->wrapper = 'white-background';
			//		$newFieldSet->addLegend( 'Field <span name="field_counter">' . ( $i + 1 ) . '</span>' );
			//		$newFieldSet->addLegend( ' Group Name:' . $groupTitle[$j] . ' <br>Form Field ' . ( $i + 1 ) );
					$newFieldSet->addElement( array( 'name' => 'element', 'type' => 'Html', 'value' => null ), array( 'html' => '<div style="padding: 1em 0 1em 0;">Field <span name="field_counter"> ' . ( $i + 1 ) . '</span></div>' ) );
					$newFieldSet->addElement( array( 'name' => 'element_title',  'multiple' => 'multiple', 'label' => '  ', 'style' => 'max-width: 240px;', 'placeholder' => 'Element title e.g. Full Name', 'type' => 'InputText', 'value' => @$values['element_title'][$i] ) ); 
					$newFieldSet->addElement( array( 'name' => 'element_name',  'multiple' => 'multiple', 'label' => '  ', 'style' => 'max-width: 240px;', 'placeholder' => 'Unique Name e.g. full_name', 'type' => 'InputText', 'value' => @$values['element_name'][$i] ) );
					$newFieldSet->addElement( array( 'name' => 'element_default_value',  'multiple' => 'multiple', 'label' => ' ', 'style' => 'max-width: 240px;', 'placeholder' => 'Default Value e.g. John C. Smith', 'type' => 'InputText', 'value' => @$values['element_default_value'][$i] ) );
					
				//	$newFieldSet->addRequirement( 'element_name', array( 'Name' => null  ) ); 
					$newFieldSet->addElement( array( 'name' => 'element_placeholder',  'multiple' => 'multiple', 'label' => '  ', 'style' => 'max-width: 240px;', 'placeholder' => 'Placeholder e.g. John Smith', 'type' => 'InputText', 'value' => @$values['element_placeholder'][$i] ) );
					$newFieldSet->addElement( array( 'name' => 'element_type',  'multiple' => 'multiple', 'label' => '  ', 'style' => 'max-width: 240px;', 'type' => 'Select', 'value' => @$values['element_type'][$i] ), 
					
						array( 
								'text' => 'Text Input', 
								'hidden' => 'Hidden Field', 
								'radio' => 'Radio Button', 
								'select' => 'Select Option', 
								'checkbox' => 'Checkbox', 
								'email' => 'Email Input', 
								'textarea' => 'Text Area', 
								'html' => 'HTML Editor', 
								'submit' => 'Submit Button', 
								'file' => 'File', 
								'document' => 'Document', 
								'document-multiple' => 'Multiple Documents', 
								'image' => 'Image', 
								'image-multiple' => 'Multiple Images', 
							//	'profile_picture' => 'Profile Picture', 
								'audio' => 'Audio',
								'date' => 'Date',
								'datetime' => 'Date & Time',
								 ) );
					$this->getGlobalValue( 'group_names' ) ? $newFieldSet->addElement( array( 'name' => 'element_group_name',  'multiple' => 'multiple', 'label' => 'Group Name (Optional)', 'type' => 'Select', 'value' => @$values['element_group_name'][$i] ), array_combine( $this->getGlobalValue( 'group_ids' ), $this->getGlobalValue( 'group_names' ) ) ) : null; 
					
					$newFieldSet->addElement( array( 'name' => 'element_validators', 'label' => '  ', 'style' => 'max-width: 240px;',  'multiple' => 'multiple', 'type' => 'Select', 'value' => @$values['element_validators'][$i] ), $reqOptions );   
					
					$newFieldSet->addElement( array( 'name' => 'element_multioptions', 'label' => '  ', 'style' => 'max-width: 240px;',  'multiple' => 'multiple',  'onchange' => 'ayoola.div.manageOptions( { database: "Ayoola_Form_MultiOptions", values: "multioptions_name", labels: "multioptions_title", element: this } );', 'type' => 'Select', 'value' => @$values['element_multioptions'][$i] ), array( '' => 'No Multi-Options' ) + $multiOptions + array( '__manage_options' => '[Manage Multi-Options]' ) );    
					
					$importanceOptions = array(
												'' => 'Optional',
												'required' => 'Required',
					);
			//		$newFieldSet->addElement( array( 'name' => 'element_importance', 'label' => '  ', 'style' => 'max-width: 240px;',  'multiple' => 'multiple', 'type' => 'Select', 'value' => @$values['element_importance'][$i] ), $importanceOptions ); 

					$newFieldSet->addElement( array( 'name' => 'element_access_level', 'label' => '  ', 'style' => 'max-width: 240px;',  'multiple' => 'multiple', 'type' => 'Select', 'value' => @$values['element_access_level'][$i] ), array( '' => 'Privacy' ) + $authLevel );   
					$newForm->addFieldset( $newFieldSet );    
				//	self::v( $authLevel );   
			//		self::v( $i );   
				//	var_export();
					$i++;
				}
				while( ! empty( $values['element_title'][$i] ) );
		//		var_export( $values );
				$fieldset = new Ayoola_Form_Element;
			//	$fieldset->allowDuplication = true;
				$fieldset->addElement( array( 'name' => 'xxxx', 'type' => 'Html', 'value' => '' ), array( 'html' => '<legend>Build form Elements and Fields</legend>' . $newForm->view(), 'fields' => 'element_title,element_name,element_group_name,element_default_value,element_placeholder,element_type,element_validators,element_access_level,element_multioptions' ) );
		//		self::v( $newForm->view() );  
		//		$fieldset->addElement( array( 'name' => 'xxxx', 'type' => 'Html', 'value' => '' ), array( 'html' => $newForm->view(), 'fields' => '' ) );
				$form->addFieldset( $fieldset );   
				
				$j++;
			}
		}
		$this->setForm( $form );
    } 
	// END OF CLASS
}
