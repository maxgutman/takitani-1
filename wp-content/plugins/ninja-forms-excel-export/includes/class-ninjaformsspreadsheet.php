<?php
class NinjaFormsSpreadsheet {
    private $subs_per_page = 100;
    /**
     * this function is only called once, when the plugin gets activated
     */
    function init() { 
    
    }

    function admin_page() {
        add_submenu_page( 'ninja-forms',  __('Excel Export','ninja-forms-spreadsheet'), __('Excel Export','ninja-forms-spreadsheet'), apply_filters( 'ninja_forms_admin_spreadsheet_capabilities', 'manage_options' ), 'ninja-forms-spreadsheet', "ninja_forms_admin" );
    }

    function admin_metaboxes(){
        $args = array(
            'name' => __('Excel Export','ninja-forms-spreadsheet'),
            'page' => 'ninja-forms-spreadsheet',
            'save_function' => array(&$this,'export_file'),
            'show_save' => false,
        );
         
        if( function_exists( 'ninja_forms_register_tab' ) ){
            ninja_forms_register_tab('spreadsheet-export', $args);
            $this->register_spreadsheet_metabox();
        }
    }


    function register_spreadsheet_metabox(){
        $forms = ninja_forms_get_all_forms();
        if(count($forms)==0)
            ;//_e('You don\'t have any forms yet.', 'ninja-forms-spreadsheet');
        else{
            $form_selection_array=array();
            foreach($forms as $form){
                $form_selection_array[]=array(
                    'name'=>$form['data']['form_title'],
                    'value'=>$form['id']
                );
            }
            $args = array(
                'page' => 'ninja-forms-spreadsheet',
                'tab' => 'spreadsheet-export',
                'slug' => 'spreadsheet-export-form-selection',
                'title' => __('Select a form','ninja-forms'),
                'settings' => array(
                    array(
                        'name' => 'spreadsheet_export_form_id',
                        'type' => 'select',
                        'label' => __('Select a form','ninja-forms'),
                        'options' => $form_selection_array,
                    ),
                    array(
                        'name' => 'spreadsheet_export_begin_date',
                        'type' => 'text',
                        'label' => __('Begin Date','ninja-forms')
                    ),
                    array(
                        'name' => 'spreadsheet_export_end_date',
                        'type' => 'text',
                        'label' => __('End Date','ninja-forms')
                    ),
                    array(
                        'name' => 'spreadsheet_export_tmp_name',
                        'type' => 'hidden',
                        'default_value'=> uniqid()
                    ),
                    array(
                        'name' => 'spreadsheet_export_iteration',
                        'type' => 'hidden',
                        'default_value'=> '0'
                    ),
                ),
            );
             
            if( function_exists( 'ninja_forms_register_tab_metabox' ) ) {
                ninja_forms_register_tab_metabox($args);
            }

            $current_form_id = $forms[0]['id'];
            if(isset($_GET['spreadsheet_export_form_id']))
                $current_form_id = $_GET['spreadsheet_export_form_id'];
            else if(isset($_POST['spreadsheet_export_form_id']))
                $current_form_id = $_POST['spreadsheet_export_form_id'];
            $fields = ninja_forms_get_fields_by_form_id($current_form_id);

            //print_r($fields);
            $field_selection_array=array();
            foreach($fields as $field){
                if(!in_array( $field['type'], array('_submit','_hr','_desc','_page_divider','cleverreach_auto','evalanche_auto'))){
                    $field_selection_array[]=array(
                        'name' => 'spreadsheet_export_field_ids['.$field['id'].']',
                        'type' => 'checkbox',
                        'label'=> $field['data']['label'].' <br><span style="font-size:10px;color:#999;text-transform:uppercase;">'.__('Field type','ninja-forms-spreadsheet').':</span> <span style="font-size:11px;">'.str_replace('_', ' ', $field['type']).'</span>',
                        'default_value'=>1
                    );
                }
            }
            //print_r($field_selection_array);
            $args = array(
                'page' => 'ninja-forms-spreadsheet',
                'tab' => 'spreadsheet-export',
                'slug' => 'spreadsheet-export-field-selection',
                'title' => __('Select fields','ninja-forms-spreadsheet'),
                'settings' => $field_selection_array,
            );
                 
            if( function_exists( 'ninja_forms_register_tab_metabox' ) ) {
                ninja_forms_register_tab_metabox($args);
            }

            $args = array(
                'page' => 'ninja-forms-spreadsheet',
                'tab' => 'spreadsheet-export',
                'slug' => 'spreadsheet-export-submit',
                'title' => __('Download Excel file','ninja-forms-spreadsheet'),
                'display_function' => array( $this, 'show_button_metabox' )
            );
                 
            if( function_exists( 'ninja_forms_register_tab_metabox' ) ) {
                ninja_forms_register_tab_metabox($args);
            }
        }
    }


    public function show_button_metabox(){
        ?>
        <input type="submit" value="<?php _e('Download Excel file','ninja-forms-spreadsheet');?>" id="ninja_forms_spreadsheet_submit" class="button-primary">
        <div class="spreadsheet-export-progress">
            <img src="<?php echo admin_url(); ?>/images/spinner.gif" title="" alt="">
            <?php _e( 'exporting ...', 'ninja-forms-spreadsheet' ); ?>
            <div class="percent">0 %</div>
            <progress max="100" value="0"></progress>
        </div>
        <?php
    }


    function export_file(){
        $form_id = $_POST['spreadsheet_export_form_id'];
        $field_id_raw = $_POST['spreadsheet_export_field_ids'];
        $begin_date = $_POST['spreadsheet_export_begin_date'];
        $end_date = $_POST['spreadsheet_export_end_date'];
        $tmp_file = 'form-submissions'.$_POST['spreadsheet_export_tmp_name'].'.xlsx';
        $tmp_file = trailingslashit( get_temp_dir() ).$tmp_file;
        $iteration = $_POST['spreadsheet_export_iteration'];


        $sub_results = $this->get_submissions($form_id,$begin_date,$end_date,$iteration);
        $num_submissions = $this->count_submissions($form_id,$begin_date,$end_date);

        // Array
        // (
        //     [0] => NF_Sub Object
        //         (
        //             [sub_id] => 13075
        //             [seq_num] => 276
        //             [form_id] => 6
        //             [fields] => Array
        //                 (
        //                     [44] => Array
        //                         (
        //                             [0] => Stadl in Altenbach
        //                             [1] => Lieschnegg
        //                         )

        //                     [45] => 
        //                     [46] => 25.03.2015
        //                     [47] => 
        //                     [48] => 1
        //                     [49] => 0
        //                     [50] => 0
        //                     [69] => 
        //                     [70] => 
        //                     [71] => 
        //                     [73] => 
        //                     [55] => Hannes
        //                     [56] => Etzelstorfer
        //                     [57] => Lendplatz 40
        //                     [58] => 46456
        //                     [59] => Graz
        //                     [60] => Äquatorialguinea
        //                     [61] => haet.at
        //                     [62] => +436642553055
        //                     [63] => hannes@haet.at
        //                     [65] => Nachricht
        //                     [102] => bitte wählen
        //                     [67] => unchecked
        //                 )

        //             [action] => submit
        //             [user_id] => 1
        //             [meta] => Array
        //                 (
        //                     [_seq_num] => 276
        //                     [_sub_id] => 13075
        //                     [_form_id] => 6
        //                     [_action] => submit
        //                 )

        //             [date_submitted] => 2015-03-06 10:02:05
        //             [date_modified] => 2015-03-06 10:02:05
        //         )
        $fields_meta = ninja_forms_get_fields_by_form_id( $form_id );
        
        $selected_fields = array();
        foreach($field_id_raw AS $id=>$active){
            if($active==1)
                $selected_fields[]=$id;
        }

        $field_names=array();
        $table=array();
        $header_row=array();
        $header_row['id'] = __('ID','ninja-forms-spreadsheet');
        $header_row['date_submitted'] = __('Submission date','ninja-forms-spreadsheet');
        foreach($fields_meta as $field){
            $field_names[$field['id']] = sanitize_title( $field['data']['label'].'-'.$field['id'] );
            $field_types[$field['id']] = $field['type'];
            if(in_array($field['id'], $selected_fields)){
                $header_row[$field_names[$field['id']]]=$field['data']['label'];
            }
        }

        // echo '<pre>';
        // print_r($fields_meta);
        // echo '</pre>';
        


        
        require_once(HAET_NINJAFORMSSPREADSHEET_PATH.'includes/PHPExcel.php');
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        if (!PHPExcel_Settings::setCacheStorageMethod($cacheMethod)) {
            die($cacheMethod . " caching method is not available" . EOL);
        }
        
        if( $iteration > 0 ){
            $objPHPExcel = PHPExcel_IOFactory::load($tmp_file);
            $row_number = $this->subs_per_page * $iteration+1;
        }else{
            $objPHPExcel = new PHPExcel(); 
            $row_number = 1;
        }



        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        // $objPHPExcel->getActiveSheet()
        //     ->fromArray(
        //         $table,  
        //         NULL,    
        //         'A'.$row_number     
        //     );
        
        // Table Headline
        if( $iteration==0 ){
            $col = 'A';
            foreach ($header_row as $headline) {
                $objPHPExcel->getActiveSheet()->getCell($col.$row_number)->setValue( $headline );
                $objPHPExcel->getActiveSheet()->getStyle($col.$row_number)->getFont()->setBold(true);
                $col++;
            }
            $row_number++;
        }

        // echo '<pre>';
        // print_r($sub_results);
        // echo '</pre>';
        foreach($sub_results as $sub){
            $col = 'A';
            $objPHPExcel->getActiveSheet()->getCell($col++.$row_number) ->setValueExplicit( $sub->seq_num, PHPExcel_Cell_DataType::TYPE_NUMERIC );
            $objPHPExcel->getActiveSheet()->getCell($col++.$row_number)->setValueExplicit( date_i18n( get_option( 'date_format' ).' '.get_option( 'time_format' ), strtotime( $sub->date_submitted ) ) );
            foreach ($selected_fields as $col_index => $col_field_id) {
                if( isset($sub->fields[$col_field_id]) ){
                    $field_value = $sub->fields[$col_field_id];
                    $col = chr( ord('C')+$col_index );

                    if( is_array($field_value) ){
                        $field_value = implode('; ', $field_value);
                    }
                    if( $field_types[$col_field_id] == '_number' )
                        $objPHPExcel->getActiveSheet()->getCell($col.$row_number) ->setValueExplicit( $field_value, PHPExcel_Cell_DataType::TYPE_NUMERIC );
                    else
                        $objPHPExcel->getActiveSheet()->getCell($col.$row_number) ->setValueExplicit( $field_value, PHPExcel_Cell_DataType::TYPE_STRING );
                }
            }
            $row_number++;
        }

        $col='A';
        foreach($fields_meta as $field){
            if(in_array($field['id'], $selected_fields)){
                $col++;
                
                $format = '0000';
                if( $field['type']=='_text' ){
                    $format = PHPExcel_Style_NumberFormat::FORMAT_TEXT;
                }elseif( $field['type']=='_number' ){
                    $format = '0000';
                }
                $objPHPExcel->getActiveSheet()->getStyle($col.$row_number.':'.$col.($row_number+$this->subs_per_page))
                    ->getNumberFormat()
                    ->setFormatCode($format);
            }
        }

        if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){
            echo json_encode(array(
                    'iteration'     => intval( $iteration ),
                    'num_iterations' => ceil( $num_submissions/$this->subs_per_page )
                )
            );
            $objWriter->save($tmp_file);
        }else{
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="form-submissions.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save("php://output");
        }
        
        die();
        
    }

    private function get_submissions($form_id,$begin_date,$end_date,$iteration){
        $query_args = array(
            'post_type'         => 'nf_sub',
            'posts_per_page'    => $this->subs_per_page,
            'offset'            => $this->subs_per_page * $iteration,
            'date_query'        => array(
                'inclusive'     => true,
            ),
            'meta_query'        => array(
                array(
                    'key' => '_form_id',
                    'value' => $form_id,
                )
            ),
        );

        if( isset( $begin_date ) AND $begin_date != '') {
            $query_args['date_query']['after'] = nf_get_begin_date( $begin_date )->format("Y-m-d G:i:s");
        }

        if( isset( $end_date ) AND $end_date != '' ) {
            $query_args['date_query']['before'] = nf_get_end_date( $end_date )->format("Y-m-d G:i:s");
        }

        $subs = new WP_Query( $query_args );;

        $sub_objects = array();

        if ( is_array( $subs->posts ) && ! empty( $subs->posts ) ) {
            foreach ( $subs->posts as $sub ) {
                $sub_objects[] = Ninja_Forms()->sub( $sub->ID );
            }           
        }

        wp_reset_postdata();
        return $sub_objects;
    }


    private function count_submissions($form_id,$begin_date,$end_date){
        $query_args = array(
            'post_type'         => 'nf_sub',
            'posts_per_page'    => -1,
            'date_query'        => array(
                'inclusive'     => true,
            ),
            'meta_query'        => array(
                array(
                    'key' => '_form_id',
                    'value' => $form_id,
                )
            ),
            'fields' => 'ids'
        );

        if( isset( $begin_date ) AND $begin_date != '') {
            $query_args['date_query']['after'] = nf_get_begin_date( $begin_date )->format("Y-m-d G:i:s");
        }

        if( isset( $end_date ) AND $end_date != '' ) {
            $query_args['date_query']['before'] = nf_get_end_date( $end_date )->format("Y-m-d G:i:s");
        }

        $subs = new WP_Query( $query_args );;
        $num_submissions = $subs->found_posts;
        wp_reset_postdata();
        return $num_submissions;
    }


    function admin_page_scripts_and_styles($hook) {
        //echo $hook;
        if( strpos($hook, 'ninja-forms-spreadsheet') !== FALSE  ){
            wp_enqueue_script('haet_nf_spreadsheet_js',  HAET_NINJAFORMSSPREADSHEET_URL.'/js/nf-spreadsheet.js', array( 'jquery','jquery-ui-datepicker'));

            wp_enqueue_style( 'jquery-smoothness', NINJA_FORMS_URL .'css/smoothness/jquery-smoothness.css' );
            wp_enqueue_style( 'haet_nf_spreadsheet_css',  HAET_NINJAFORMSSPREADSHEET_URL.'/css/nf-spreadsheet.css',array('jquery-smoothness'));
            return;
        }  
    }
}
    