<?php
App::uses('AppController', 'Controller');

class ReportsController extends AppController {
	public $helpers = array('Html', 'Form', 'Time');
	public $components = array('Session');
	
	public function show($reporte = null) {
		$this->set(array('reporte_encontrado' => TRUE, 'nombre_reporte' => $this->_nombre_reporte($reporte), 'reporte' => $reporte));
		
		if ($reporte == 6 || $reporte == 7) {
			$servicio = $reporte == 6 ? 'tipo' : ($reporte == 7 ? 'proveedor' : '');
			
			if ($this->request->is(array('post', 'put'))) {
				$fecha1 = $this->request->data['show_reporte_'.$reporte]['fecha1'];
				$fecha2 = $this->request->data['show_reporte_'.$reporte]['fecha2'];
				
				$validacion_fechas = $this->_validar_fechas($fecha1, $fecha2);
				if ($validacion_fechas != '') {
					$this->set('tipo_mensaje', 1);
					$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> %s', $validacion_fechas), 'default', array('class' => 'error-message'));
				}
				else {
					$this->loadModel('InvoicedService');
					$query = $this->InvoicedService->servicios($fecha1, $fecha2, $servicio);
					
					if (empty($query)) {
						$this->set('tipo_mensaje', 2);
						$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> No se encontraron ventas.'), 'default', array('class' => 'error-message'));
					}
					else {
						$this->set(array('query' => $query, 'tipo_mensaje' => 2));
						$this->Session->setFlash(__('<i class="fa fa-info-circle"></i> Resultado.'), 'default', array('class' => 'success'));
					}
				}
			}
		}
		elseif ($reporte == 8) {
			$this->loadModel('Airline');
			$this->set(array('aereolineas' => $this->Airline->find('list', array('fields' => 'Airline.id, Airline.name'))));
			
			if ($this->request->is(array('post', 'put'))) {
				$airline_id = $this->request->data['show_reporte_'.$reporte]['airline_id'];
				
				$this->loadModel('GoalAirline');
				$query = $this->GoalAirline->metas_aereolineas($airline_id);
				
				if (empty($query)) {
					$this->set('tipo_mensaje', 2);
					$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> No se encontraron metas.'), 'default', array('class' => 'error-message'));
				}
				else {
					$this->set(array('query' => $query, 'tipo_mensaje' => 2));
					$this->Session->setFlash(__('<i class="fa fa-info-circle"></i> Resultado.'), 'default', array('class' => 'success'));
				}
			}
		}
		else {
			$this->set('reporte_encontrado', FALSE);
			$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> Reporte no encontrado.'), 'default', array('class' => 'error-message'));
		}
	}
	
	public function save($reporte) {
		if ($reporte == 6 || $reporte == 7) {
			$servicio = $reporte == 6 ? 'tipo' : ($reporte == 7 ? 'proveedor' : '');
			
			if ($this->request->is(array('post', 'put'))) {
				$fecha1 = $this->request->data['save_reporte_'.$reporte]['fecha1'];
				$fecha2 = $this->request->data['save_reporte_'.$reporte]['fecha2'];
				
				if ($servicio == 'tipo') {
					// Guarda los datos resultantes del reporte en la tabla venta de servicios por tipo
					$this->loadModel('ServicesSalesType');
					$id_venta = $this->ServicesSalesType->guardar_venta_tipo($fecha1, $fecha2);
				}
				elseif ($servicio == 'proveedor') {
					// Guarda los datos resultantes del reporte en la tabla venta de servicios por proveedor
					$this->loadModel('ServicesSalesProvider');
					$id_venta = $this->ServicesSalesProvider->guardar_venta_proveedor($fecha1, $fecha2);
				}
				
				if (empty($id_venta)) {
					$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> No se pudo guardar el '.$servicio.' de servicios de ventas.'), 'default', array('class' => 'error-message'));
				}
				
				if ($servicio == 'tipo') {
					// Guarda los datos totales de los servicios por tipo
					$this->loadModel('Type');
					$query = $this->Type->guardar_tipo($fecha1, $fecha2, $id_venta);
					
					// Actualiza el código de venta de servicios por tipo en la tabla de servicios facturados
					$this->loadModel('InvoicedService');
					$query = $this->InvoicedService->servicios($fecha1, $fecha2, $servicio);
					
					$i = 0;
					foreach ($query as $row) $tipos_servicios_array[$i++] = $row['invoiced_services']['tipo_servicio'];
					
					$tipos_servicios_string = '';
					for ($i = 0; $i < count($tipos_servicios_array); $i++) {
						$tipos_servicios_string .= "'".$tipos_servicios_array[$i]."'";
						if ($i < count($tipos_servicios_array) - 1) $tipos_servicios_string .= ", ";
					}
					
					$query = $this->InvoicedService->actualizar_servicios($fecha1, $fecha2, $servicio, $id_venta, $tipos_servicios_string);
				}
				elseif ($servicio == 'proveedor') {
					// Guarda los datos totales de los servicios por tipo
					$this->loadModel('Provider');
					$query = $this->Provider->guardar_proveedor($fecha1, $fecha2, $id_venta);
					
					// Actualiza el código de venta de servicios por proveedor en la tabla de servicios facturados
					$this->loadModel('InvoicedService');
					$query = $this->InvoicedService->actualizar_servicios($fecha1, $fecha2, $servicio, $id_venta);
				}
				
				$this->Session->setFlash(__('<i class="fa fa-info-circle"></i> Reporte guardado.'), 'default', array('class' => 'success'));
			}
			else {
				$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> <strong>Error:</strong> No se pudo guardar la venta.'), 'default', array('class' => 'error-message'));
			}
			return $this->redirect(array('controller' => 'reports', 'action' => 'show', $reporte));
		}
		else {
			$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> Reporte no encontrado.'), 'default', array('class' => 'error-message'));
			return $this->redirect(array('controller' => 'reports', 'action' => 'show', $reporte));
		}
	}
	
	protected function _validar_fechas($fecha1, $fecha2) {
		$fecha1 = empty($fecha1) ? '0000/00/00' : $fecha1;
		$fecha2 = empty($fecha2) ? '0000/00/00' : $fecha2;
		$valores_fecha1 = explode('/', $fecha1);
		$valores_fecha2 = explode('/', $fecha2);
		$dia1 = $valores_fecha1[2];
		$mes1 = $valores_fecha1[1];
		$anyo1 = $valores_fecha1[0];
		$dia2 = $valores_fecha2[2];
		$mes2 = $valores_fecha2[1];
		$anyo2 = $valores_fecha2[0];
		$dias_fecha1 = gregoriantojd($mes1, $dia1, $anyo1);
		$dias_fecha2 = gregoriantojd($mes2, $dia2, $anyo2);
		if ($dias_fecha1 == $dias_fecha2) {
			return 'La fecha inicial y la fecha final no deben de ser iguales.';
		}
		elseif ($dias_fecha1 > $dias_fecha2) {
			return 'La fecha inicial no puede ser mayor a la fecha final.';
		}
		else {
			return '';
		}
	}
	
	protected function _nombre_reporte($reporte) {
		$nombres_reportes = array(
			0 => '',
			6 => 'Semi-Resumen de Venta de Servicios Terrestres por Tipo de Servicio Semanal',
			7 => 'Usuarios por Tipo de Capacitados, Departamento y Municipio',
			8 => 'Usuarios por Departamento, Tipo de Capacitados y Fecha'
		);
		return $nombres_reportes[$reporte >= 6 && $reporte <= 8 ? $reporte : 0];
	}
	
	public function excel($reporte = null) {
		$this->set(array('reporte_encontrado' => TRUE, 'nombre_reporte' => $this->_nombre_reporte($reporte), 'reporte' => $reporte));
		
		if ($reporte == 6 || $reporte == 7) {
			$servicio = $reporte == 6 ? 'tipo' : ($reporte == 7 ? 'proveedor' : '');
			
			if ($this->request->is(array('post', 'put'))) {
				$fecha1 = $this->request->data['excel_reporte_'.$reporte]['fecha1'];
				$fecha2 = $this->request->data['excel_reporte_'.$reporte]['fecha2'];
				
				$this->loadModel('InvoicedService');
				$query = $this->InvoicedService->servicios($fecha1, $fecha2, $servicio);
				
				if (empty($query)) {
					$this->set('tipo_mensaje', 2);
					$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> No se encontraron ventas.'), 'default', array('class' => 'error-message'));
				}
				else {
					$this->set(array('query' => $query, 'tipo_mensaje' => 2, 'fecha1' => $fecha1, 'fecha2' => $fecha2));
					$this->Session->setFlash(__('<i class="fa fa-info-circle"></i> Se generó el reporte en Excel.'), 'default', array('class' => 'success'));
				}
			}
		}
		elseif ($reporte == 8) {
			$this->loadModel('Airline');
			$this->set(array('aereolineas' => $this->Airline->find('list', array('fields' => 'Airline.id, Airline.name'))));
			
			if ($this->request->is(array('post', 'put'))) {
				$airline_id = $this->request->data['excel_reporte_'.$reporte]['airline_id'];
				
				$this->loadModel('GoalAirline');
				$query = $this->GoalAirline->metas_aereolineas($airline_id);
				
				if (empty($query)) {
					$this->set('tipo_mensaje', 2);
					$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> No se encontraron metas.'), 'default', array('class' => 'error-message'));
				}
				else {
					$this->set(array('query' => $query, 'tipo_mensaje' => 2, 'airline_id' => $airline_id));
					$this->Session->setFlash(__('<i class="fa fa-info-circle"></i> Se generó el reporte en Excel.'), 'default', array('class' => 'success'));
				}
			}
		}
		else {
			$this->set('reporte_encontrado', FALSE);
			$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> Reporte no encontrado.'), 'default', array('class' => 'error-message'));
		}
	}
	
	public function pdf($reporte = null) {
		$this->set(array('reporte_encontrado' => TRUE, 'nombre_reporte' => $this->_nombre_reporte($reporte), 'reporte' => $reporte));
		
		if ($reporte == 6 || $reporte == 7) {
			$servicio = $reporte == 6 ? 'tipo' : ($reporte == 7 ? 'proveedor' : '');
			
			if ($this->request->is(array('post', 'put'))) {
				$fecha1 = $this->request->data['pdf_reporte_'.$reporte]['fecha1'];
				$fecha2 = $this->request->data['pdf_reporte_'.$reporte]['fecha2'];
				
				$this->loadModel('InvoicedService');
				$query = $this->InvoicedService->servicios($fecha1, $fecha2, $servicio);
				
				if (empty($query)) {
					$this->set('tipo_mensaje', 2);
					$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> No se encontraron ventas.'), 'default', array('class' => 'error-message'));
				}
				else {
					$this->set(array('query' => $query, 'tipo_mensaje' => 2, 'fecha1' => $fecha1, 'fecha2' => $fecha2));
					$this->Session->setFlash(__('<i class="fa fa-info-circle"></i> Se generó el reporte en PDF.'), 'default', array('class' => 'success'));
				}
			}
		}
		elseif ($reporte == 8) {
			$this->loadModel('Airline');
			$this->set(array('aereolineas' => $this->Airline->find('list', array('fields' => 'Airline.id, Airline.name'))));
			
			if ($this->request->is(array('post', 'put'))) {
				$airline_id = $this->request->data['pdf_reporte_'.$reporte]['airline_id'];
				
				$this->loadModel('GoalAirline');
				$query = $this->GoalAirline->metas_aereolineas($airline_id);
				
				if (empty($query)) {
					$this->set('tipo_mensaje', 2);
					$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> No se encontraron metas.'), 'default', array('class' => 'error-message'));
				}
				else {
					$this->set(array('query' => $query, 'tipo_mensaje' => 2, 'airline_id' => $airline_id));
					$this->Session->setFlash(__('<i class="fa fa-info-circle"></i> Se generó el reporte en PDF.'), 'default', array('class' => 'success'));
				}
			}
		}
		else {
			$this->set('reporte_encontrado', FALSE);
			$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> Reporte no encontrado.'), 'default', array('class' => 'error-message'));
		}
	}

        // Salida 10
        public function ventaServicioTerrestreTipoServicioMensual() { //Acumulado venta de servicios terrestres por tipo de servicio mensual
                
                //Si el formulario se envió
                if ($this->request->is(array('post', 'put'))) {                    
                    //Saca la fecha año del request
                    $fechaAnio=$this->request->data["services_sales_types"]['fecha_anio'];
                    //Saca la fecha mes del request
                    $fechaMes=$this->request->data["services_sales_types"]['fecha_mes'];
                    //Saca la fecha del request
                    $fechaInicio=$this->request->data["services_sales_types"]['fecha_inicio'];
                    //Saca la fecha del request
                    $fechaFin=$this->request->data["services_sales_types"]['fecha_fin'];
                    
                    $this->loadModel('ServicesSalesType');
                    //ejecuta consulta la venta de servicios terrestres por proveedor por Mes
                    $queryConsultaServiciosTipo="SELECT  services_sales_types.id, types.tipo_servicio, types.cantidad_servicios_tipo, types.total_servicios_tipo, services_sales_types.fecha_inicio_tipo, services_sales_types.fecha_fin_tipo "
                            . "FROM services_sales_types inner join types ON services_sales_types.id = types.services_sales_type_id  "
                            . "WHERE  fecha_inicio_tipo >= '". $fechaInicio . "' AND fecha_inicio_tipo <= '". $fechaFin . "' ORDER BY tipo_servicio;";
                    $consultaServiciosTipo=$this->ServicesSalesType->query($queryConsultaServiciosTipo);
                    
                    //Si la consulta retorna vacía
                    if(empty($consultaServiciosTipo)):
                        $this->Session->setFlash(__('No se encontro registros servicios terrestres vendidos por tipo de servicio para este mes. '));
                    
                    else:
                        $this->set('consultaServiciosTipo',$consultaServiciosTipo);
                        $this->set('fechaInicio',$fechaInicio);
                        $this->set('fechaFin',$fechaFin);
                        $this->set('fechaAnio',$fechaAnio);
                        $this->set('fechaMes',$fechaMes);
                    endif;                    
		}
        }

         // Salida 10 para reporte en excel
        public function ventaServicioTerrestreTipoServicioMensualReporteExcel() { //Acumulado venta de servicios terrestres por tipo de servicio mensual
                
                //Si el formulario se envió
                if ($this->request->is(array('post', 'put'))) {                    
                    //Saca la fecha año del request
                    $fechaAnio=$this->request->data["reporte_excel"]['fecha_anio'];
                    //Saca la fecha mes del request
                    $fechaMes=$this->request->data["reporte_excel"]['fecha_mes'];
                    //Saca la fecha del request
                    $fechaInicio=$this->request->data["reporte_excel"]['fecha_inicio'];
                    //Saca la fecha del request
                    $fechaFin=$this->request->data["reporte_excel"]['fecha_fin'];
                    
                    $this->loadModel('ServicesSalesType');
                    //ejecuta consulta la venta de servicios terrestres por proveedor por Mes
                    $queryConsultaServiciosTipo="SELECT  services_sales_types.id, types.tipo_servicio, types.cantidad_servicios_tipo, types.total_servicios_tipo, services_sales_types.fecha_inicio_tipo, services_sales_types.fecha_fin_tipo "
                            . "FROM services_sales_types inner join types ON services_sales_types.id = types.services_sales_type_id  "
                            . "WHERE  fecha_inicio_tipo >= '". $fechaInicio . "' AND fecha_inicio_tipo <= '". $fechaFin . "' ORDER BY tipo_servicio;";
                    $consultaServiciosTipo=$this->ServicesSalesType->query($queryConsultaServiciosTipo);
                    
                    //Si la consulta retorna vacía
                    if(empty($consultaServiciosTipo)):
                        $this->Session->setFlash(__('No encontro registros servicios terrestres vendidos por tipo de servicio para este mes. '));
                    
                    else:
                        $this->set('consultaServiciosTipo',$consultaServiciosTipo);
                        $this->set('fechaAnio',$fechaAnio);
                        $this->set('fechaMes',$fechaMes);
                    endif;                    
    }
        }      // Salida 10 para reporte en pdf
        public function ventaServicioTerrestreTipoServicioMensualReportePdf() { //Acumulado venta de servicios terrestres por tipo de servicio mensual
                
                //Si el formulario se envió
                if ($this->request->is(array('post', 'put'))) {                    
                    //Saca la fecha año del request
                    $fechaAnio=$this->request->data["reporte_pdf"]['fecha_anio'];
                    //Saca la fecha mes del request
                    $fechaMes=$this->request->data["reporte_pdf"]['fecha_mes'];
                    //Saca la fecha del request
                    $fechaInicio=$this->request->data["reporte_pdf"]['fecha_inicio'];
                    //Saca la fecha del request
                    $fechaFin=$this->request->data["reporte_pdf"]['fecha_fin'];
                    
                    $this->loadModel('ServicesSalesType');
                    //ejecuta consulta la venta de servicios terrestres por proveedor por Mes
                    $queryConsultaServiciosTipo="SELECT  services_sales_types.id, types.tipo_servicio, types.cantidad_servicios_tipo, types.total_servicios_tipo, services_sales_types.fecha_inicio_tipo, services_sales_types.fecha_fin_tipo "
                            . "FROM services_sales_types inner join types ON services_sales_types.id = types.services_sales_type_id  "
                            . "WHERE  fecha_inicio_tipo >= '". $fechaInicio . "' AND fecha_inicio_tipo <= '". $fechaFin . "' ORDER BY tipo_servicio;";
                    $consultaServiciosTipo=$this->ServicesSalesType->query($queryConsultaServiciosTipo);
                    
                    //Si la consulta retorna vacía
                    if(empty($consultaServiciosTipo)):
                        $this->Session->setFlash(__('No encontro registros servicios terrestres vendidos por tipo de servicio para este mes. '));
                    
                    else:
                        $this->set('consultaServiciosTipo',$consultaServiciosTipo);
                        $this->set('fechaAnio',$fechaAnio);
                        $this->set('fechaMes',$fechaMes);
                    endif;                    
    }
        }

        // Salida 11 
        public function ventaProveedorServicioTerrestreMensual() { //Acumulado de venta por proveedor de servicios terrestres mensual
                
                //Si el formulario se envió
                if ($this->request->is(array('post', 'put'))) {                    
                    //Saca la fecha año del request
                    $fechaAnio=$this->request->data["ServicesSalesProvider"]['fecha_anio'];
                    //Saca la fecha mes del request
                    $fechaMes=$this->request->data["ServicesSalesProvider"]['fecha_mes'];
                    //Saca la fecha del request
                    $fechaInicio=$this->request->data["ServicesSalesProvider"]['fecha_inicio'];
                    //Saca la fecha del request
                    $fechaFin=$this->request->data["ServicesSalesProvider"]['fecha_fin'];
                    
                    $this->loadModel('Providers');
                    //ejecuta consulta la venta de servicios terrestres por proveedor por Mes
                    $queryConsultaServicios="SELECT services_sales_providers.id, proveedor_servicio, cantidad_servicios_proveedor, total_servicios_proveedor, fecha_inicio_proveedor, fecha_fin_proveedor "
                            . "FROM services_sales_providers inner join providers ON providers.services_sales_provider_id = services_sales_providers.id "
                            . "WHERE fecha_inicio_proveedor >= '". $fechaInicio . "' AND fecha_inicio_proveedor <= '". $fechaFin . "' ORDER BY proveedor_servicio;";
                    $consultaServicios=$this->Providers->query($queryConsultaServicios);
                    
                    //Si la consulta retorna vacía
                    if(empty($consultaServicios)):
                        $this->Session->setFlash(__('No se encontro registros servicios terrestres vendidos de proveerdor para este mes. '));
                    
                    else:
                        $this->set('consultaServicios',$consultaServicios);
                        $this->set('fechaInicio',$fechaInicio);
                        $this->set('fechaFin',$fechaFin);
                        $this->set('fechaAnio',$fechaAnio);
                        $this->set('fechaMes',$fechaMes);
                    endif;                    
		}
       
        }   

        // Salida 11 para reporte excel
        public function ventaProveedorServicioTerrestreMensualReporteExcel() { //Acumulado de venta por proveedor de servicios terrestres mensual
                
                //Si el formulario se envió
                if ($this->request->is(array('post', 'put'))) {                    
                    //Saca la fecha año del request
                    $fechaAnio=$this->request->data["reporte_excel"]['fecha_anio'];
                    //Saca la fecha mes del request
                    $fechaMes=$this->request->data["reporte_excel"]['fecha_mes'];
                    //Saca la fecha del request
                    $fechaInicio=$this->request->data["reporte_excel"]['fecha_inicio'];
                    //Saca la fecha del request
                    $fechaFin=$this->request->data["reporte_excel"]['fecha_fin'];
                    
                    $this->loadModel('Providers');
                    //ejecuta consulta la venta de servicios terrestres por proveedor por Mes
                    $queryConsultaServicios="SELECT services_sales_providers.id, proveedor_servicio, cantidad_servicios_proveedor, total_servicios_proveedor, fecha_inicio_proveedor, fecha_fin_proveedor "
                            . "FROM services_sales_providers inner join providers ON providers.services_sales_provider_id = services_sales_providers.id "
                            . "WHERE fecha_inicio_proveedor >= '". $fechaInicio . "' AND fecha_inicio_proveedor <= '". $fechaFin . "' ORDER BY proveedor_servicio;";
                    $consultaServicios=$this->Providers->query($queryConsultaServicios);
                    
                    //Si la consulta retorna vacía
                    if(empty($consultaServicios)):
                        $this->Session->setFlash(__('No encontro registros servicios terrestres vendidos de proveerdor para este mes. '));
                    
                    else:
                        $this->set('consultaServicios',$consultaServicios);
                        $this->set('fechaAnio',$fechaAnio);
                        $this->set('fechaMes',$fechaMes);
                    endif;                    
    }
        }// Salida 11 para reporte pdf
        public function ventaProveedorServicioTerrestreMensualReportePdf() { //Acumulado de venta por proveedor de servicios terrestres mensual
                
                //Si el formulario se envió
                if ($this->request->is(array('post', 'put'))) {                    
                    //Saca la fecha año del request
                    $fechaAnio=$this->request->data["reporte_pdf"]['fecha_anio'];
                    //Saca la fecha mes del request
                    $fechaMes=$this->request->data["reporte_pdf"]['fecha_mes'];
                    //Saca la fecha del request
                    $fechaInicio=$this->request->data["reporte_pdf"]['fecha_inicio'];
                    //Saca la fecha del request
                    $fechaFin=$this->request->data["reporte_pdf"]['fecha_fin'];
                    
                    $this->loadModel('Providers');
                    //ejecuta consulta la venta de servicios terrestres por proveedor por Mes
                    $queryConsultaServicios="SELECT services_sales_providers.id, proveedor_servicio, cantidad_servicios_proveedor, total_servicios_proveedor, fecha_inicio_proveedor, fecha_fin_proveedor "
                            . "FROM services_sales_providers inner join providers ON providers.services_sales_provider_id = services_sales_providers.id "
                            . "WHERE fecha_inicio_proveedor >= '". $fechaInicio . "' AND fecha_inicio_proveedor <= '". $fechaFin . "' ORDER BY proveedor_servicio;";
                    $consultaServicios=$this->Providers->query($queryConsultaServicios);
                    
                    //Si la consulta retorna vacía
                    if(empty($consultaServicios)):
                        $this->Session->setFlash(__('No encontro registros de servicios terrestres vendidos de proveerdor para este mes. '));
                    
                    else:
                        $this->set('consultaServicios',$consultaServicios);
                        $this->set('fechaAnio',$fechaAnio);
                        $this->set('fechaMes',$fechaMes);
                    endif;                    
    }
        }
     
}
