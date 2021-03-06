<?php

/*
*  ACF Tab Field Class
*
*  All the logic for this field type
*
*  @class 		pdc_field_tab
*  @extends		pdc_field
*  @package		ACF
*  @subpackage	Fields
*/

if( ! class_exists('pdc_field_tab') ) :

class pdc_field_tab extends pdc_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// vars
		$this->name = 'tab';
		$this->label = __("Tab",'pdc');
		$this->category = 'layout';
		$this->defaults = array(
			'placement'	=> 'top',
			'endpoint'	=> 0 // added in 5.2.8
		);
		
		
		// do not delete!
    	parent::__construct();
	}
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field ) {
		
		// vars
		$atts = array(
			'class'				=> 'pdc-tab',
			'data-placement'	=> $field['placement'],
			'data-endpoint'		=> $field['endpoint']
		);
		
		?>
		<div <?php pdc_esc_attr_e( $atts ); ?>><?php echo $field['label']; ?></div>
		<?php
		
		
	}
	
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @param	$field	- an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field_settings( $field ) {
		
		// message
		$message = '';
		$message .= '<span class="pdc-error-message"><p>' . __("The tab field will display incorrectly when added to a Table style repeater field or flexible content field layout", 'pdc') . '</p></span>';
		$message .= '<p>' . __( 'Use "Tab Fields" to better organize your edit screen by grouping fields together.', 'pdc') . '</p>';
		$message .= '<p>' . __( 'All fields following this "tab field" (or until another "tab field" is defined) will be grouped together using this field\'s label as the tab heading.','pdc') . '</p>';
		
		// default_value
		pdc_render_field_setting( $field, array(
			'label'			=> __('Instructions','pdc'),
			'instructions'	=> '',
			'type'			=> 'message',
			'message'		=> $message,
			'new_lines'		=> ''
		));
		
		
		// preview_size
		pdc_render_field_setting( $field, array(
			'label'			=> __('Placement','pdc'),
			'type'			=> 'select',
			'name'			=> 'placement',
			'choices' 		=> array(
				'top'			=>	__("Top aligned",'pdc'),
				'left'			=>	__("Left Aligned",'pdc'),
			)
		));
		
		
		// endpoint
		pdc_render_field_setting( $field, array(
			'label'			=> __('End-point','pdc'),
			'instructions'	=> __('Use this field as an end-point and start a new group of tabs','pdc'),
			'name'			=> 'endpoint',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));
				
	}
	
	
	/*
	*  load_field()
	*
	*  This filter is appied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/
	
	function load_field( $field ) {
		
		// remove name to avoid caching issue
		$field['name'] = '';
		
		
		// remove required to avoid JS issues
		$field['required'] = 0;
		
		
		// set value other than 'null' to avoid ACF loading / caching issue
		$field['value'] = false;
		
		
		// return
		return $field;
		
	}
	
}


// initialize
pdc_register_field_type( new pdc_field_tab() );

endif; // class_exists check

?>