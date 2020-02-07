<?php

class Ventas_model extends CI_Model{

    public $name;
    public $description;
    public $group;
    public $budget;
    public $net_sale;
    public $iva_sale;
    public $exercised;
    public $utility;
    public $porcent;


    public function get_vendedores( $branch_office, $month, $year ){

        $dateFrom =  "$year-$month-01";
        $dateTo = "$year-$month-31";

        /*
        //Consulta con codeigniter
        $this->db->select(
            "AGCIANAME as name,  AGDESCR as description, AGPAPA as grupo ,FORMAT(AGPRESUP7,2) AS budget, FORMAT(SUM(AIPRECIO*AICANTF),2) AS net_sale, 
            FORMAT(SUM(AIPRECIO*AICANTF)-AGPRESUP7,2) AS iva_sale, FORMAT((SUM(AIPRECIO*AICANTF)/AGPRESUP7),4)*100  AS exercised,
            FORMAT(SUM((AICANTF*(AIPRECIO-AICOSTO))),2) AS utility, FORMAT((SUM((AICANTF*(AIPRECIO-AICOSTO)))/SUM(AIPRECIO*AICANTF))*100,2) AS porcent,
            FORMAT(AGHORAS,0) AS NUM_COT, FORMAT(AGCREDITO,0) AS IMP_COT"
        );
        $this->db->from('FAXINV');
        $this->db->join('FDOC', 'FDOC.DSEQ=FAXINV.DSEQ','left');
        $this->db->join('FINV', 'FINV.ISEQ=FAXINV.ISEQ','left');
        $this->db->join('FAG', 'FAG.AGTNUM=FDOC.DPAR1','left');
        $this->db->join('FCLI', 'FCLI.CLISEQ=FAXINV.CLISEQ','left');
        $this->db->where( 
            array(
                "DFECHA >=" => "2019-$mes_actual-01", 
                "DFECHA <=" => "2019-12-31", 
                "DESFACT " => 1, 
                "mid(DNUM,1,2) " => "FE"               
            )
        );
        $this->db->or_where('mid(DNUM,1,2)','DA');
        $this->db->or_where('mid(DNUM,1,2)','CA');
        $this->db->group_by('AGDESCR');

        $query = $this->db->get();
        */
       
        $query = $this->db->query(
            " SELECT AGCIANAME as nombre,  AGDESCR as descripcion, AGPAPA AS grupo, AGPRESUP7 AS presupuesto, SUM(AIPRECIO*AICANTF) AS venta_neta, 
                SUM(AIPRECIO*AICANTF)-AGPRESUP7 AS venta_iva, (SUM(AIPRECIO*AICANTF)/AGPRESUP7)*100  AS ejercido,
                SUM((AICANTF*(AIPRECIO-AICOSTO))) AS utilidad, FORMAT((SUM((AICANTF*(AIPRECIO-AICOSTO)))/SUM(AIPRECIO*AICANTF))*100,2) AS porcentaje,
                AGHORAS AS NUM_COT, AGCREDITO AS IMP_COT

                FROM FAXINV
                LEFT JOIN FDOC ON FDOC.DSEQ=FAXINV.DSEQ
                LEFT JOIN FINV ON FINV.ISEQ=FAXINV.ISEQ
                LEFT JOIN FAG ON FAG.AGTNUM=FDOC.DPAR1
                LEFT JOIN FCLI ON FCLI.CLISEQ=FAXINV.CLISEQ

                WHERE  DFECHA>='$dateFrom' AND DFECHA<='$dateTo' AND DESFACT=1 AND  DMULTICIA = $branch_office AND DSTATUSCFD = 3  AND ( mid(DNUM,1,2) <> 'AF' AND mid(DNUM,1,2) <> 'AN')
                GROUP BY AGDESCR ORDER BY venta_neta desc
            ");
        
        $vendedores = [];

        foreach ( $query->result() as $campo ) {
            
            $vendedor = array(

                "name"          => $campo->nombre,
                "description"   => $campo->descripcion,
                "group"         => $campo->grupo,
                "budget"        => doubleval( $campo->presupuesto ),
                "net_sale"      => doubleval( $campo->venta_neta ),
                "iva_sale"      => doubleval( $campo->venta_iva ),
                "exercised"     => doubleval($campo->ejercido),
                "utility"       => doubleval( $campo->utilidad ),
                "porcent"       => doubleval( $campo->porcentaje ),
                "date"          => array( 
                                            "from " =>  $dateFrom, 
                                            "to" => $dateTo 
                                )  
            );

            array_push($vendedores, $vendedor);
        }
        
        return  $vendedores;
       
        
      
    }


    public function get_venta( $branch_office, $month, $year ){
        
        $query = $this->db->query(" SELECT SUM(AGPRESUP$month) AS budget,
		SUM(AIPRECIO*AICANTF) AS net_sale
		FROM FAXINV
		LEFT JOIN FDOC ON FDOC.DSEQ=FAXINV.DSEQ
		LEFT JOIN FINV ON FINV.ISEQ=FAXINV.ISEQ
		LEFT JOIN FAG ON FAG.AGTNUM=FDOC.DPAR1
		LEFT JOIN FCLI ON FCLI.CLISEQ=FAXINV.CLISEQ
		WHERE  DFECHA>='$year-$month-01' AND DFECHA<='$year-$month-31' AND DESFACT=1 AND DMULTICIA = $branch_office AND DSTATUSCFD = 3 
                AND ( mid(DNUM,1,2) <> 'AF' AND mid(DNUM,1,2) <> 'AN')
        
		ORDER BY AGDESCR

        ");

        $row = $query->row();

        $sale = array(
            "budget" => $row->budget,
            "net_sale" => $row->net_sale,
            "date" => array( "month" => $month, "year" => $year)
        );

        
        return $sale;

    }
}