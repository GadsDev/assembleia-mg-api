<?php

use Illuminate\Database\Seeder;
use App\ReembolsoIndenisacao;
use App\Deputado;

class ReembolsoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createGastos();
    }

    public function createGastos() {
        $remmbolsos = $this->getReembolsos();    
        foreach ($remmbolsos as $key1 => $value1) {    
            $id =  $key1;
            $gasto = $value1['valorTotalMes'];
            $mes = $value1['mes'];
            if(isset($mes,$id,$gasto)){
                $reembolso = new ReembolsoIndenisacao;
                $reembolso->mes = $mes;
                $reembolso->total_reembolsado = $gasto;
                $reembolso->deputado_id = $id;
                $reembolso->save();
            }            
           
        }  
    }

    // Traz os gastos de todos os deputados com a API da Assembleia
    public function getReembolsos()
    {   
        $deputadosId = $this->getAllDeputados();
        
        for ($i=1; $i <= 12; $i++) {            
            foreach ($deputadosId as $key => $value) {
                $url = "http://dadosabertos.almg.gov.br/ws/prestacao_contas/".
                "verbas_indenizatorias/deputados/".$value."/2017/$i?formato=json";  
                $gasto = json_decode(file_get_contents($url));
                if(isset($gasto->list[0])){
                    $reembolsoArr[$value] = array (                       
                        'valorTotalMes' => $gasto->list[0]->valor,
                        'mes' => $i,
                    );
                }else{
                    $reembolsoArr[$value] = array (                      
                        'valorTotalMes' => 0,
                        'mes' => $i,
                    );
                }    
            }           
        } 

        return $reembolsoArr;
    }

    public function getAllDeputados()
    {   
        $deputados = Deputado::where('id' ,'>' ,0)->pluck('id')->toArray();
        
        return $deputados;
    }
}
