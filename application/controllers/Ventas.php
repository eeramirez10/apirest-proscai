<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");

require APPPATH.'/libraries/REST_Controller.php';

class Ventas extends REST_Controller{

    public function __construct(){
        //llamado del constructor del padre
        parent::__construct();

        $this->load->database(); 
        $this->load->model('Ventas_model'); 
        $this->load->model('BranchOffices_model');
        $this->load->model('Families_model');     
        $this->load->helper('sucursal');
    }

    public function vendedores_get(){
        
        $branch_office = $this->uri->segment(3);
        $month = $this->uri->segment(4);
        $year = $this->uri->segment(5);

        if( empty($month) && empty($year) && empty($branch_office) ){
            // Si los parametros vienen vacios por defecto dara los resultados del mes actual y la sucursal 1
            $month = date('m');
            $month = intval($month);
            $year = date ('Y');
            $branch_office = 1;
        }

        if($branch_office > 6 ){
            $response = array(
                'err' => TRUE,
                'message' => 'La sucursal con el numero '.$branch_office.' no existe'
            );
            echo $this->response($response);
            return;
        }

        if( $month > 12 || $month < 1 ){
            $response = array(
                'err' => TRUE,
                'message' => 'El mes '.$month.' no existe'
            );
            echo $this->response($response);
            return;
        }





        //Obtenemos los vendedores 
        $sellers =  $this->Ventas_model->get_vendedores($branch_office, $month, $year );
        
        //Obtenemos la venta acumulada
        $sale = $this->Ventas_model->get_venta( $branch_office, $month, $year);
        //Obtenemos el nombre de la sucursal por medio del helper
        $sucursal = set_sucursal($branch_office - 1);
    
        $response = array(
            'err' => FALSE,
            'data' => $sellers,
            'sale' => $sale,
            'branch_office' => $sucursal,
            'total_result' => count($sellers)
        );
        
        
        echo $this->response($response);      
    }

    public function venta_get(){
   
        $data = $this->Ventas_model->get_venta();

        echo $this->response($data);
    }


    public function branchSaleMonth_get(){

        $month = $this->uri->segment(3);
        $year  = $this->uri->segment(4);

        if( empty($month) && empty($year)){
            $month = date('m');
            $month = intval($month);
            $year = date ('Y');
        }


        $branchOffices = $this->BranchOffices_model->get_BranchSellsMonth($year,$month);



        $response = array(
            "err" => FALSE,
            "data" => $branchOffices,
            "total_result" => count($branchOffices)
        );


        echo $this->response($response);
    }


    public function branchSaleYear_get(){
        $year = $this->uri->segment(3);

        if(empty($year)){
            $year = date('Y');
        }

        $branchOffices = $this->BranchOffices_model->get_BranchSellsYear($year);


        $response = array(
            "err" => FALSE,
            "data" => $branchOffices,
            "total_result" => count($branchOffices)
        );


        echo $this->response($response);
    }


    public function branchOfficeSalePerYear_get(){

        $branch_office = $this->uri->segment(3);
        $year = $this->uri->segment(4);

        if(  empty($year) && empty($branch_office) ){
            $year = date ('Y');
            $branch_office = 01;
        }

        if($branch_office > 5 ){
            $response = array(
                'err' => TRUE,
                'message' => 'La sucursal con el numero '.$branch_office.' no existe'
            );
            echo $this->response($response);
            return;
        }

        $branchOffice = $this->BranchOffices_model->get_BranchOfficePerYear($branch_office,$year);

        $response = array(
            "err" => FALSE,
            "data" => $branchOffice,
            "total_result" => count($branchOffice)
        );


        echo $this->response($response);

    }


    public function familias_get(){
        $year = $this->uri->segment(3);
        $month = $this->uri->segment(4);

        $families = $this->Families_model->get_familiesMonth($year, $month);

        $resp = array(
            "error" => false,
            "data" => $families,
            "total_result" => count($families)
        );

        echo $this->response($resp);
    }

  


    



}