<?php
// Importamos la clase PHPExcel
App::import('Vendor', 'Classes/PHPExcel');

$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("..\Template\Reporte6.xlsx");

switch ($opcion) {
  case 6:
    $objPHPExcel->getActiveSheet()->setCellValue('B7', $fecha1);
    $objPHPExcel->getActiveSheet()->setCellValue('C7', $fecha2);

    $sumatorias = array('cantidad_por_tipo' => 0, 'total_por_tipo' => 0, 'iva_por_tipo' => 0);

    $row = 0;

    if (!empty($query)):
        $baseRow = 17;
        foreach ($query as $r => $qrow) {
          $row = $baseRow + $r;
          $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
          $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $qrow['invoiced_services']['tipo_servicio'])
                                        ->setCellValue('C'.$row, $qrow[0]['cantidad_por_tipo'])
                                        ->setCellValue('D'.$row, $qrow[0]['total_por_tipo'])
                                        ->setCellValue('E'.$row, $qrow[0]['iva_por_tipo']);

          $sumatorias['cantidad_por_tipo'] = $sumatorias['cantidad_por_tipo'] + $qrow[0]['cantidad_por_tipo'];
          $sumatorias['total_por_tipo'] = $sumatorias['total_por_tipo'] + $qrow[0]['total_por_tipo'];
          $sumatorias['iva_por_tipo'] = $sumatorias['iva_por_tipo'] + $qrow[0]['iva_por_tipo'];
        }
        $objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
    endif;

    $objPHPExcel->getActiveSheet()->setCellValue('D7', number_format($sumatorias['cantidad_por_tipo'], 0, '.', ','));
    $objPHPExcel->getActiveSheet()->setCellValue('E7', '$ '.number_format($sumatorias['total_por_tipo'], 2, '.', ','));

    $nombreReporte = "semiResumenVSTTipoServicioSemanalReporteExcel.xlsx";
    break;
  case 7:
   $objPHPExcel = $objReader->load("..\Template\Reporte7.xlsx");
    $objPHPExcel->getActiveSheet()->setCellValue('B7', $fecha1);
    $objPHPExcel->getActiveSheet()->setCellValue('C7', $fecha2);

    $sumatorias = array('cantidad_por_proveedor' => 0, 'total_por_proveedor' => 0, 'iva_por_proveedor' => 0);

    $row = 0;

    if (!empty($query)):
        $baseRow = 17;
        foreach ($query as $r => $qrow) {
          $row = $baseRow + $r;
          $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
          $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $qrow['invoiced_services']['proveedor_servicio'])
                                        ->setCellValue('C'.$row, $qrow[0]['cantidad_por_proveedor'])
                                        ->setCellValue('D'.$row, $qrow[0]['total_por_proveedor'])
                                        ->setCellValue('E'.$row, $qrow[0]['iva_por_proveedor']);

          $sumatorias['cantidad_por_proveedor'] = $sumatorias['cantidad_por_proveedor'] + $qrow[0]['cantidad_por_proveedor'];
          $sumatorias['total_por_proveedor'] = $sumatorias['total_por_proveedor'] + $qrow[0]['total_por_proveedor'];
          $sumatorias['iva_por_proveedor'] = $sumatorias['iva_por_proveedor'] + $qrow[0]['iva_por_proveedor'];
        }
        $objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
    endif;

    $objPHPExcel->getActiveSheet()->setCellValue('D7', number_format($sumatorias['cantidad_por_proveedor'], 0, '.', ','));
    $objPHPExcel->getActiveSheet()->setCellValue('E7', '$ '.number_format($sumatorias['total_por_proveedor'], 2, '.', ','));

    $nombreReporte = "semiResumenVSTProveedorSemanalReporteExcel.xlsx";
    break;
  case 8:
    $objPHPExcel = $objReader->load("..\Template\Reporte8.xlsx");
    $objPHPExcel->getActiveSheet()->setCellValue('B7', $aereolineas[$airline_id]);

    $sumatorias = array('meta_bsp' => 0, 'boletos_periodo' => 0, 'total_periodo' => 0, 'porcentaje' => 0);

    $row = 0;

    if (!empty($query)):
        $baseRow = 21;
        foreach ($query as $r => $qrow) {
          $row = $baseRow + $r;
          $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
          $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $qrow['goal_airlines']['periodo_bsp'])
                                        ->setCellValue('C'.$row, $qrow['goal_airlines']['fecha_inicio'])
                                        ->setCellValue('D'.$row, $qrow['goal_airlines']['fecha_fin'])
                                        ->setCellValue('E'.$row, $qrow['goal_airlines']['meta_bsp'])
                                        ->setCellValue('F'.$row, $qrow['goal_airlines']['boletos_periodo'])
                                        ->setCellValue('G'.$row, $qrow['goal_airlines']['total_periodo'])
                                        ->setCellValue('H'.$row, $qrow['goal_airlines']['faltante'])
                                        ->setCellValue('I'.$row, $qrow['goal_airlines']['porcentaje'])
                                        ->setCellValue('J'.$row, $qrow['goal_airlines']['comision'])
                                        ->setCellValue('K'.$row, $qrow['goal_airlines']['ingreso_comision']);

          $sumatorias['meta_bsp'] += $qrow['goal_airlines']['meta_bsp'];
          $sumatorias['boletos_periodo'] += $qrow['goal_airlines']['boletos_periodo'];
          $sumatorias['total_periodo'] += $qrow['goal_airlines']['total_periodo'];
          $sumatorias['porcentaje'] += $qrow['goal_airlines']['porcentaje'];
        }
        $objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
    endif;

    $objPHPExcel->getActiveSheet()->setCellValue('H7', $sumatorias['boletos_periodo']);
    $objPHPExcel->getActiveSheet()->setCellValue('I7', '$ '.number_format($sumatorias['total_periodo'], 2, '.', ','));
    $objPHPExcel->getActiveSheet()->setCellValue('J7', '$ '.number_format($sumatorias['meta_bsp'], 0, '.', ','));
    $objPHPExcel->getActiveSheet()->setCellValue('K7', number_format($sumatorias['porcentaje'], 2, '.', ',').' %');

    $nombreReporte = "TotalVentaBALíneaAéreaPeriodoBSPReporteExcel.xlsx";
    break;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$nombreReporte.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>