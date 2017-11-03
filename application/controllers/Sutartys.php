<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ************************ CONTROLLERS ************************
 * @property Pasarai            $pasarai            Pasarai controller
 * @property Paseliai           $paseliai           Paseliai controller
 * @property Ukininkai          $ukininkai          Ukininkai controller
 * @property Galvijai           $galvijai           Galvijai controller
 * @property Sutartys           $sutartys           Sutartys controller
 * @property Auth               $auth               Auth controller
 * @property Main               $main               Main controller
 * @property Admin              $admin              Admin controller
 * ************************ MODELS *****************************
 * @property Pasarai_model      $pasarai_model      Pasarai models
 * @property Paseliai_model     $paseliai_model     Paseliai models
 * @property Ukininkai_model    $ukininkai_model    Ukininkai models
 * @property Galvijai_model     $galvijai_model     Galvijai models
 * @property Ion_auth_model     $ion_auth_model     Ion_Auth models
 * @property Main_model         $main_model         Main models
 * @property Admin_model        $admin_model        Admin models
 * @property Sutartys_model     $sutartys_model     Sutartys models
 * ************************* LIBRARY ****************************
 * @property Ion_auth           $ion_auth           Ion_auth library
 */
class Sutartys extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        error_reporting(E_ERROR | E_WARNING | E_PARSE);

        //uzkraunam MODEL
        //$this->load->model('ukininkai_model');
        //$this->load->model('galvijai_model');
        //$this->load->model('main_model');

        //$this->load->library('linksniai');

        if (!$this->ion_auth->logged_in()) {
            redirect('main/auth_error');
        }
    }

    //jei kas bandys atidaryti index puslapi bus nukreiptas i pagrindini
    public function index()
    {
        redirect('main');
    }

    public function skaitciuokle(){
        //$file = './DATA/Darbsut.xls';

        /*$this->load->library('Excel');

        $inputFileName = './DATA/Darbsut.xls';


// Read the existing excel file
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);

// Update it's data
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

// Add column headers
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A6', 'Genute Norkiene')
        ;

// Generate an updated excel file
// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $inputFileName . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');*/


        $this->load->view('main_view');
    }

    public function sutartys(){

        $this->load->view('main_view');
    }

}