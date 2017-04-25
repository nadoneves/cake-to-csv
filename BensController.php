public function excel()
{
  Configure::write('debug',2);
    $this->autoRender = false;
    $caminhoServidor = "/mnt/nfs/";
    $nomearquivo = "bens".date("dmYHis").".csv";
    $arquivoServidor = $caminhoServidor.$nomearquivo;

    // $sql = "SELECT 'nu_patrimonio', 'ds_item', 'nu_contab', 'ds_bem', 'qt_mes_vdutil', 'ds_status', 'ds_lotacao', 'tipo_bem', 'dtAquisicao', 'dtIncorporacao', 'dtBaixa', 'deprec', 'vlbem'";
    // $sql .= " UNION ALL";
    // $sql .= " (SELECT `Patrimonio`.`nu_patrimonio`, `Item`.`ds_item`, `Classifs`.`nu_contab`, `Bem`.`ds_bem`, `Classifs`.`qt_mes_vdutil`, `Status`.`ds_status`, `Lotacao`.`ds_lotacao`, (((CASE `Bem`.`tp_bem` WHEN 1 THEN 'Comum' WHEN 2 THEN 'Livro' WHEN 3 THEN 'VeÃ­culo' WHEN 4 THEN 'Software' END))) AS `Bem__tipo_bem`, (IFNULL(DATE_FORMAT(`Bem`.`dt_aquisicao`,'%d/%m/%Y'),'')) AS `Bem__dtA`, (IFNULL(DATE_FORMAT(`Bem`.`dt_incorp`,'%d/%m/%Y'),'')) AS `Bem__dtI`, (IFNULL(DATE_FORMAT(`Bem`.`dt_baixa`,'%d/%m/%Y'),'')) AS `Bem__dtB`, (replace(`Classifs`.`pc_deprec`, '.', ',')) AS `Bem__deprec`, (replace(`Bem`.`vl_bem`, '.', ',')) AS `Bem__vlbem` FROM `gbens_t`.`bens` AS `Bem` INNER JOIN `gbens_t`.`patrimonios` AS `Patrimonio` ON (`Bem`.`patrimonio_id` = `Patrimonio`.`id`) INNER JOIN `gbens_t`.`itens` AS `Item` ON (`Bem`.`item_id` = `Item`.`id`) INNER JOIN `gbens_t`.`classifs` AS `Classifs` ON (`Item`.`classif_id` = `Classifs`.`id`) INNER JOIN `gbens_t`.`status` AS `Status` ON (`Bem`.`status_id` = `Status`.`id`) LEFT JOIN `gbens_t`.`locais_resps` AS `Local` ON (`Bem`.`local_resp_id` = `Local`.`id`) LEFT JOIN `gbens_t`.`vw_lotacoes` AS `Lotacao` ON (`Local`.`lotacao_id_resp` = `Lotacao`.`id`) WHERE '1' = '1' ORDER BY `Patrimonio`.`nu_patrimonio` ASC";
    // $sql .= " INTO OUTFILE '".$arquivoServidor."' CHARACTER SET utf8 FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n')";

    $this->getResultBens();

    $log = $this->Bem->getDataSource()->getLog(false,false);
    $logf = $log['log'][0]['query'];

    $sql = "SELECT";
    $fields = $this->paginate['fields'];
    foreach($fields as $k => $v)
    {
        if($k <= 0)
        $sql .= " '".split("\.",$v)[1]."'";
        else
        $sql .= ",'".split("\.",$v)[1]."'";
    }
    $sql .= " UNION ALL";
    $sql .= " (".split("LIMIT", $logf)[0];
    $sql .= " INTO OUTFILE '".$arquivoServidor."' CHARACTER SET utf8)";

    //	echo "<pre>";
    //	var_dump($log);
    //	echo "</pre>";
    //	echo $sql;
    //	die();



    $db = ConnectionManager::getDataSource('default');
    $db->query($sql);

    $arquivoGbens = "arquivos_gerados/".$nomearquivo;
    if(file_exists($arquivoGbens))
    {
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="'.$nomearquivo.'"');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        //header('Content-Length: ' . filesize($aquivoNome));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');
        // Envia o arquivo para o cliente
        readfile($arquivoGbens);
    }
    else
    {
        echo "Erro ao tentar criar o csv.";
    }
}
