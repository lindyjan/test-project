<?php 

class pdc_settings_tools {
	
	var $view = 'settings-tools',
		$data = array();
	
	
	/*
	*  __construct
	*
	*  Initialize filters, action, variables and includes
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
	
		// actions
		add_action('admin_menu', array($this, 'admin_menu'));
		
	}
	
	
	/*
	*  admin_menu
	*
	*  This function will add the pdc menu item to the WP admin
	*
	*  @type	action (admin_menu)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_menu() {
		
		// bail early if no show_admin
		if( !pdc_get_setting('show_admin') ) return;
		
		
		// add page
		$page = add_submenu_page('edit.php?post_type=pdc-field-group', __('Tools','pdc'), __('Tools','pdc'), pdc_get_setting('capability'),'pdc-settings-tools', array($this,'html') );
		
		
		// actions
		add_action('load-' . $page, array($this,'load'));
		
	}
	
	
	/*
	*  load
	*
	*  This function will look at the $_POST data and run any functions if needed
	*
	*  @type	function
	*  @date	7/01/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function load() {
		
		// disable filters to ensure pdc loads raw data from DB
		pdc_disable_filters();
		
		
		// run import / export
		if( pdc_verify_nonce('import') ) {
			
			$this->import();
		
		} elseif( pdc_verify_nonce('export') ) {
			
			if( isset($_POST['generate']) ) {
				
				$this->generate();
			
			} else {
				
				$this->export();
			
			}
		
		}
		
		
		// load pdc scripts
		pdc_enqueue_scripts();
		
	}
	
	
	/*
	*  html
	*
	*  This function will render the view
	*
	*  @type	function
	*  @date	7/01/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function html() {
		
		// load view
		pdc_get_view($this->view, $this->data);
		
	}
	
	
	/*
	*  export
	*
	*  This function will export field groups to a .json file
	*
	*  @type	function
	*  @date	11/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function export() {
		
		// vars
		$json = $this->get_json();
		
		
		// validate
		if( $json === false ) {
			
			pdc_add_admin_notice( __("No field groups selected", 'pdc') , 'error');
			return;
		
		}
		
		
		// set headers
		$file_name = 'pdc-export-' . date('Y-m-d') . '.json';
		
		header( "Content-Description: File Transfer" );
		header( "Content-Disposition: attachment; filename={$file_name}" );
		header( "Content-Type: application/json; charset=utf-8" );
		
		echo pdc_json_encode( $json );
		die;
		
	}
	
	
	/*
	*  import
	*
	*  This function will import a .json file of field groups
	*
	*  @type	function
	*  @date	11/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function import() {
		
		// validate
		if( empty($_FILES['pdc_import_file']) ) {
			
			pdc_add_admin_notice( __("No file selected", 'pdc') , 'error');
			return;
		
		}
		
		
		// vars
		$file = $_FILES['pdc_import_file'];
		
		
		// validate error
		if( $file['error'] ) {
			
			pdc_add_admin_notice(__('Error uploading file. Please try again', 'pdc'), 'error');
			return;
		
		}
		
		
		// validate type
		if( pathinfo($file['name'], PATHINFO_EXTENSION) !== 'json' ) {
		
			pdc_add_admin_notice(__('Incorrect file type', 'pdc'), 'error');
			return;
			
		}
		
		
		// read file
		$json = file_get_contents( $file['tmp_name'] );
		
		
		// decode json
		$json = json_decode($json, true);
		
		
		// validate json
    	if( empty($json) ) {
    	
    		pdc_add_admin_notice(__('Import file empty', 'pdc'), 'error');
	    	return;
    	
    	}
    	
    	
    	// if importing an auto-json, wrap field group in array
    	if( isset($json['key']) ) {
	    	
	    	$json = array( $json );
	    	
    	}
    	
    	
    	// vars
    	$ids = array();
    	$keys = array();
    	$imported = array();
    	
    	
    	// populate keys
    	foreach( $json as $field_group ) {
	    	
	    	// append key
	    	$keys[] = $field_group['key'];
	    	
	    }
	    
	    
    	// look for existing ids
    	foreach( $keys as $key ) {
	    	
	    	// attempt find ID
	    	$field_group = _pdc_get_field_group_by_key( $key );
	    	
	    	
	    	// bail early if no field group
	    	if( !$field_group ) continue;
	    	
	    	
	    	// append
	    	$ids[ $key ] = $field_group['ID'];
	    	
	    }
	    
    	
    	// enable local
		pdc_enable_local();
		
		
		// reset local (JSON class has already included .json field groups which may conflict)
		pdc_reset_local();
		
    	
    	// add local field groups
    	foreach( $json as $field_group ) {
	    	
	    	// add field group
	    	pdc_add_local_field_group( $field_group );
	    	
	    }
	    
	    
    	// loop over keys
    	foreach( $keys as $key ) {
	    	
	    	// vars
	    	$field_group = pdc_get_local_field_group( $key );
	    	
	    	
	    	// attempt get id
	    	$id = pdc_maybe_get( $ids, $key );
	    	
	    	if( $id ) {
		    	
		    	$field_group['ID'] = $id;
		    	
	    	}
	    	
	    	
	    	// append fields
			if( pdc_have_local_fields($key) ) {
				
				$field_group['fields'] = pdc_get_local_fields( $key );
				
			}
			
			
			// import
			$field_group = pdc_import_field_group( $field_group );
			
			
			// append message
			$imported[] = array(
				'ID'		=> $field_group['ID'],
				'title'		=> $field_group['title'],
				'updated'	=> $id ? 1 : 0
			);
			
    	}
    	
    	
    	// messages
    	if( !empty($imported) ) {
    		
    		// vars
    		$links = array();
    		$count = count($imported);
    		$message = sprintf(_n( 'Imported 1 field group', 'Imported %s field groups', $count, 'pdc' ), $count) . '.';
    		
    		
    		// populate links
    		foreach( $imported as $import ) {
	    		
	    		$links[] = '<a href="' . admin_url("post.php?post={$import['ID']}&action=edit") . '" target="_blank">' . $import['title'] . '</a>';
	    			
	    	}
	    	
	    	
	    	// append links
	    	$message .= ' ' . implode(', ', $links);
	    	
	    	
	    	// add notice
	    	pdc_add_admin_notice( $message );
    	
    	}
		
	}
	
	
	/*
	*  generate
	*
	*  This function will generate PHP code to include in your theme
	*
	*  @type	function
	*  @date	11/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function generate() {
		
		// prevent default translation and fake __() within string
		pdc_update_setting('l10n_var_export', true);
		
		
		// vars
		$json = $this->get_json();
		
		
		// validate
		if( $json === false ) {
			
			pdc_add_admin_notice( __("No field groups selected", 'pdc') , 'error');
			return;
		
		}
		
				
		// update view
		$this->view = 'settings-tools-export';
		$this->data['field_groups'] = $json;
		
	}
	
	
	/*
	*  get_json
	*
	*  This function will return the JSON data for given $_POST args
	*
	*  @type	function
	*  @date	3/02/2015
	*  @since	5.1.5
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function get_json() {
		
		// validate
		if( empty($_POST['pdc_export_keys']) ) {
			
			return false;
				
		}
		
		
		// vars
		$json = array();
		
		
		// construct JSON
		foreach( $_POST['pdc_export_keys'] as $key ) {
			
			// load field group
			$field_group = pdc_get_field_group( $key );
			
			
			// validate field group
			if( empty($field_group) ) continue;
			
			
			// load fields
			$field_group['fields'] = pdc_get_fields( $field_group );
	
	
			// prepare for export
			$field_group = pdc_prepare_field_group_for_export( $field_group );
			
			
			// add to json array
			$json[] = $field_group;
			
		}
		
		
		// return
		return $json;
		
	}
	
}


// initialize
new pdc_settings_tools();

?>