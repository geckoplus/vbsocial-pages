<?php

global $wpsf_settings;

 global $wp_roles;


if ( ! isset( $wp_roles ) )
    $wp_roles = new WP_Roles();
    
 $vb_roles = $wp_roles->get_names();

 
 
// General Settings section
$wpsf_settings[] = array(
    'section_id' => 'general',
    'section_title' => 'General Settings',
    'section_description' => '',
    'section_order' => 5,
    'fields' => array(
    	
        array(
            'id' => 'vb_page_per_box',
            'title' => 'vBSocial Pages Per Page',
            'desc' => '',
             'class' => 'small',
            'type' => 'text',
            'std' => '15',
             'size' =>"2",
             'maxlength'=>"2" 
        ),
        /*
        array(
            'id' => 'textarea',
            'title' => 'Textarea',
            'desc' => 'This is a description.',
            'type' => 'textarea',
            'std' => 'This is std'
        ),
        array(
            'id' => 'select',
            'title' => 'Select',
            'desc' => 'This is a description.',
            'type' => 'select',
            'std' => 'green',
            'choices' => array(
                'red' => 'Red',
                'green' => 'Green',
                'blue' => 'Blue'
            )
        ),
        */
        array(
            'id' => 'vb_default_sort',
            'title' => 'Sort vBSocial Pages By',
           
            'desc' => '',
            'type' => 'radio',
            'std' => 'last_activity',
            'choices' => array(
                'last_activity' => 'Latest activity (Comment, Follow, Latest Post)',
                'last_comment' => 'Comment',
                'last_follow' => 'Follow',
                'last_create' => 'Latest Post'
            )
        ),
       
        
        array(
            'id' => 'vb_allowed_roles',
            'title' => 'Who Can Create Vb Social Pages',
            'desc' => '',
            'type' => 'checkboxes',
           
            'choices' => $vb_roles
        ),
        /*
        array(
            'id' => 'color',
            'title' => 'Color',
            'desc' => 'This is a description.',
            'type' => 'color',
            'std' => '#ffffff'
        ),
        array(
            'id' => 'file',
            'title' => 'File',
            'desc' => 'This is a description.',
            'type' => 'file',
            'std' => ''
        ),
        array(
            'id' => 'editor',
            'title' => 'Editor',
            'desc' => 'This is a description.',
            'type' => 'editor',
            'std' => ''
        )*/
    )
);


// More Settings section
$wpsf_settings[] = array(
    'section_id' => 'upload_settings',
    'section_title' => 'Upload Settings',
    'section_order' => 10,
    'fields' => array(
        array(
            'id' => 'vb_image_size',
            'class' => 'small',
            'title' => 'Max image upload size',
            'desc' => 'In Mega Bytes(Mb)',
            'type' => 'text',
            'std' => '5'
        ),
        
         array(
            'id' => 'vb_allowed_img_types',
            'title' => 'Allowed Image Types',
            'desc' => '',
            'type' => 'checkboxes',
           	'std' =>array(
            	'jpg',
            	'png',
            	'gif',
            	'jpeg',
            ),
            'choices' =>array(
            	'jpg'	=> 'Jpg',
            	'png'	=> 'Png',
            	'gif'	=> 'Gif',
            	'jpeg'	=> 'Jpeg',
            )
        ),
       
    )
);


?>
