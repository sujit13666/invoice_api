<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
function pdf_create($html)
{
    require_once("dompdf/dompdf_config.inc.php");

    $dompdf = new DOMPDF();

    $dompdf->load_html($html);
    $dompdf->set_paper('A4', 'portrait');
    $dompdf->render();
    return $dompdf->output();

}
?>