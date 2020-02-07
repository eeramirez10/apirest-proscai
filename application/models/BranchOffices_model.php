<?php


class BranchOffices_model extends CI_Model{

    public $branch;
    public $net_sale;

    public function get_BranchSellsYear ($year){

        $query = $this->db->query(
            "SELECT IF(DALMACEN='01','Mexico',IF(DALMACEN='02','Monterrey',IF(DALMACEN='03','Veracruz',IF(DALMACEN='04','Mexicali',IF(DALMACEN='05','Queretaro',IF(DALMACEN='06','Cancun','')))))) AS sucursal,
                DATE_FORMAT(DFECHA,'%m') AS mes,DATE_FORMAT(DFECHA,'%Y') AS anio,
                sum(AICANTF*AIPRECIO) AS venta,
                sum(AICANTF*AICOSTO) AS costo
                FROM FAXINV
                LEFT JOIN FDOC ON FDOC.DSEQ=FAXINV.DSEQ
                WHERE  DFECHA>='$year-01-01' AND DFECHA<='$year-12-31' AND  DESFACT=1 AND DSTATUSCFD=3 AND
                                (mid(DNUM,1,1)='F'   OR mid(DNUM,1,1)='D'  OR mid(DNUM,1,1)='C' )
            GROUP BY DALMACEN,DATE_FORMAT(DFECHA,'%m')
        "
        );

        $branchOffices = [];
         
        foreach( $query->result() as $campo ){
            $branchOffice = array(
               "branchOffice" => $campo->sucursal,
               "sell"         => $campo->venta,
               "cost"         => $campo->costo,
               "month"         => $campo->mes,
                                
                            
            );

            array_push($branchOffices, $branchOffice);
        }


        return $branchOffices;
    }

    public function get_BranchSellsMonth($year, $month){

        $query =  $this->db->query(
            "SELECT IF(DALMACEN='01','Mexico',IF(DALMACEN='02','Monterrey',IF(DALMACEN='03','Veracruz',IF(DALMACEN='04','Mexicali',IF(DALMACEN='05','Queretaro',IF(DALMACEN='06','Cancun','')))))) AS sucursal,
            DATE_FORMAT(DFECHA,'%m') AS mes,DATE_FORMAT(DFECHA,'%Y') AS anio,
            sum(AICANTF*AIPRECIO) AS venta,
            sum(AICANTF*AICOSTO) AS costo
            FROM FAXINV
            LEFT JOIN FDOC ON FDOC.DSEQ=FAXINV.DSEQ
            WHERE  DFECHA>='$year-$month-01' AND DFECHA<='$year-$month-31' AND  DESFACT=1 AND DSTATUSCFD=3 AND
                            (mid(DNUM,1,1)='F'   OR mid(DNUM,1,1)='D'  OR mid(DNUM,1,1)='C')
            GROUP BY DALMACEN,DATE_FORMAT(DFECHA,'%m')
        ");

         $branchOffices = [];
         
        foreach( $query->result() as $campo ){
            $branchOffice = array(
               "branchOffice" => $campo->sucursal,
               "sell"         => $campo->venta,
               "cost"         => $campo->costo,
               "date"         => array(
                                "month" => $campo->mes,
                                "year"  => $campo->anio
                            )
            );

            array_push($branchOffices, $branchOffice);
        }

        return $branchOffices;
    }

    public function get_BranchOfficePerYear ( $branchOffice, $year){

        $query = $this->db->query(
        "SELECT DALMACEN AS sucursal,
        DATE_FORMAT(DFECHA,'%m') AS mes,DATE_FORMAT(DFECHA,'%Y') AS anio,
        sum(AICANTF*AIPRECIO) AS venta,
        sum(AICANTF*AICOSTO) AS costo
        FROM FAXINV
        LEFT JOIN FDOC ON FDOC.DSEQ=FAXINV.DSEQ
        WHERE  DFECHA>='$year-01-01' AND DFECHA<='$year-12-31' AND  DESFACT=1 AND DSTATUSCFD=3 AND
                        (mid(DNUM,1,1)='F'   OR mid(DNUM,1,1)='D'  OR mid(DNUM,1,1)='C'  )
                        AND DALMACEN = $branchOffice
        GROUP BY DALMACEN,DATE_FORMAT(DFECHA,'%m')
        ");

        $branchOffices = [];
         
        foreach( $query->result() as $campo ){
            $branchOffice = array(
               "branchOffice" => $campo->sucursal,
               "sell"         => $campo->venta,
               "cost"         => $campo->costo,
               "month"         =>  $campo->mes,
         
            );

            array_push($branchOffices, $branchOffice);
        }

        return $branchOffices;
    }

}