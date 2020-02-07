<?php


    class Families_model extends CI_Model{

        public function get_familiesMonth( $year, $month){

            $sucursales = ['Mexico','Monterrey','Veracruz','Mexicali','Queretaro','Cancun'];

            $query = $this->db->query("SELECT FAMB.FAMDESCR as familia,SUM(AICANTF) AS pzas,round(SUM(AICANTF*AIPRECIO),2) AS importe,DMULTICIA as sucursal FROM FAXINV
            LEFT JOIN FDOC ON FDOC.DSEQ=FAXINV.DSEQ
            LEFT JOIN FINV ON FINV.ISEQ=FAXINV.ISEQ
            LEFT JOIN FFAM AS FAMB ON FAMB.FAMTNUM=FINV.IFAMB
            WHERE DFECHA>='$year-$month-01' AND DFECHA<='$year-$month-31' AND (MID(DNUM,1,1)='F' OR MID(DNUM,1,1)='D' ) AND DSTATUSCFD=3 AND ITIPO=1
            GROUP BY DMULTICIA,IFAMB
            ");
            $families = [];
            foreach( $query->result() as $campo ){
                $family = array(
                    "family" => $campo->familia,
                    "pzas" => $campo->pzas,
                    "import" => $campo->importe,
                    "branchoffice" => $sucursales[ intval($campo->sucursal) - 1 ] 
                );

                array_push($families,$family);
            }

            return $families;

        }


    
    }