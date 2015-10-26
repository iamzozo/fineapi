<?php

class Finehelper
{
    public function upload() {
        if ( !function_exists( 'media_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
        }
        $movefile = wp_handle_upload( $_FILES['userfile'], array( 'test_form' => FALSE ) );
        if ( !isset( $movefile['error'] ) ) {
            $attachment = array(
                'post_mime_type' => $movefile['type'],
                'post_title' => '',
                'post_content' => '',
                'post_status' => 'inherit',
                'post_parent' => $_GET['id']
            );
            $attach_id = wp_insert_attachment( $attachment, $movefile['file'] );
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            $attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
            // Set a custom metadata for the attachment,
            // to retrieve easily on filelisting
            // update_post_meta( $attach_id, 'project_id', $_POST['project_id'] );
            // wp_update_attachment_metadata( $attach_id, $attach_data );
            $attach_data['file_url'] = wp_get_attachment_image_src( $attach_id, 'thumbnail' );
            $image = wp_get_attachment_image_src( $attach_id, 'thumb');
            $file = wp_get_attachment_metadata( $attach_id );
            $file['id'] = $attach_id;
            $file['file_url'] = wp_get_attachment_url( $attach_id );
            $file['file_name'] = basename(wp_get_attachment_url( $attach_id ));
            $file['file_thumb'] = $image[0] ? $image[0] : get_template_directory_uri() . '/img/icons/default.png';
            wp_send_json( $file );
        }
        else {
            wp_send_json_error( $movefile['error'] );
        }
    }
}

$finehelper = new Finehelper();
