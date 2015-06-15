<?php
App::uses('AppController', 'Controller');

class ReportsController extends AppController {
	public $helpers = array('Html', 'Form', 'Time');
	public $components = array('Session');
	
	public function show($opcion = null) {
		switch ($opcion) {
			case 6:
				$this->set(array('reporte_encontrado' => true, 'nombre_reporte' => 'Semi-Resumen Venta de Servicios Terrestres por Tipo de Servicio Semanal', 'opcion' => 6));
				if ($this->request->is(array('post', 'put'))) {
					$fecha1 = $this->request->data['show_reporte_6']['fecha1'];
					$fecha2 = $this->request->data['show_reporte_6']['fecha2'];
					
					$validacion_fechas = $this->_validar_fechas($fecha1, $fecha2);
					if ($validacion_fechas != '') {
						$this->set('tipo_mensaje', 1);
						$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> %s', $validacion_fechas), 'default', array('class' => 'error-message'));
					}
					else {
						$this->loadModel('InvoicedService');
						$query = $this->InvoicedService->query("
						SELECT tipo_servicio, COUNT(id) cantidad_por_tipo, SUM(tarifa) total_por_tipo, SUM(iva) iva_por_tipo FROM invoiced_services
						WHERE fecha BETWEEN '".$fecha1."' AND '".$fecha2."' GROUP BY tipo_servicio ORDER BY tipo_servicio");
						
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
				break;
			case 7:
				$this->set(array('reporte_encontrado' => true, 'nombre_reporte' => 'Semi-Resumen Venta de Servicios Terrestres por Proveedor Semanal', 'opcion' => 7));
				if ($this->request->is(array('post', 'put'))) {
					$fecha1 = $this->request->data['show_reporte_7']['fecha1'];
					$fecha2 = $this->request->data['show_reporte_7']['fecha2'];
					
					$validacion_fechas = $this->_validar_fechas($fecha1, $fecha2);
					if ($validacion_fechas != '') {
						$this->set('tipo_mensaje', 1);
						$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> %s', $validacion_fechas), 'default', array('class' => 'error-message'));
					}
					else {
						$this->loadModel('InvoicedService');
						$query = $this->InvoicedService->query("
						SELECT proveedor_servicio, COUNT(id) cantidad_por_proveedor, SUM(tarifa) total_por_proveedor, SUM(iva) iva_por_proveedor
						FROM invoiced_services
						WHERE fecha BETWEEN '".$fecha1."' AND '".$fecha2."' GROUP BY proveedor_servicio ORDER BY proveedor_servicio;");
						
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
				break;
			case 8:
				$this->loadModel('Airline');
				$this->set(array('aereolineas' => $this->Airline->find('list', array('fields' => 'Airline.id, Airline.name')), 'reporte_encontrado' => true, 'nombre_reporte' => 'Total de Venta de Boletos Aéreos por Línea Aérea por Periodo BSP', 'opcion' => 8));
				
				if ($this->request->is(array('post', 'put'))) {
					$airline_id = $this->request->data['show_reporte_8']['airline_id'];
					
					$this->loadModel('GoalAirline');
					$query = $this->GoalAirline->query("
					SELECT periodo_bsp, fecha_inicio, fecha_fin, meta_bsp, boletos_periodo, total_periodo, faltante, porcentaje, comision, ingreso_comision
					FROM goal_airlines WHERE airline_id = ".$airline_id." ORDER BY fecha_inicio");
					
					if (empty($query)) {
						$this->set('tipo_mensaje', 2);
						$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> No se encontraron ventas.'), 'default', array('class' => 'error-message'));
					}
					else {
						$this->set(array('query' => $query, 'tipo_mensaje' => 2));
						$this->Session->setFlash(__('<i class="fa fa-info-circle"></i> Resultado.'), 'default', array('class' => 'success'));
					}
				}
				break;
			default:
				$this->set('reporte_encontrado', false);
				$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> Reporte no encontrado.'), 'default', array('class' => 'error-message'));
				break;
		}
	}
	
	public function save($opcion) {
		switch ($opcion) {
			case 6:
				if ($this->request->is(array('post', 'put'))) {
					$fecha1 = $this->request->data['save_reporte_6']['fecha1'];
					$fecha2 = $this->request->data['save_reporte_6']['fecha2'];
					
					// Guarda los datos resultantes del reporte en la tabla venta de servicios por tipo
					$this->loadModel('ServicesSalesType');
					if (!$this->ServicesSalesType->save(array('ServicesSalesType' => array('fecha_inicio_tipo' => $fecha1, 'fecha_fin_tipo' => $fecha2)))) {
						$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> No se pudo guardar el tipo de servicios de ventas.'), 'default', array('class' => 'error-message'));
					}
					
					// Obtiene el correlativo del código de los datos guardados en la tabla venta de servicios por tipo
					$query = $this->ServicesSalesType->find('all', array('fields' => 'MAX(ServicesSalesType.id) id'));
					$services_sales_type_id = $query[0][0]['id'];
					
					// Guarda los datos totales de los servicios por tipo
					$this->loadModel('Type');
					$query = $this->Type->query("
					INSERT INTO types(services_sales_type_id, tipo_servicio, cantidad_servicios_tipo, total_servicios_tipo)
					SELECT ".$services_sales_type_id." services_sales_type_id, tipo_servicio, COUNT(id) cantidad_por_tipo, SUM(tarifa) total_por_tipo
					FROM invoiced_services
					WHERE fecha BETWEEN '".$fecha1."' AND '".$fecha2."' GROUP BY tipo_servicio ORDER BY tipo_servicio");
					
					// Actualiza el código de venta de servicios por tipo en la tabla de servicios facturados
					$this->loadModel('InvoicedService');
					$query = $this->InvoicedService->query("
					SELECT tipo_servicio, COUNT(id) cantidad_por_tipo, SUM(tarifa) total_por_tipo, SUM(iva) iva_por_tipo
					FROM invoiced_services
					WHERE fecha BETWEEN '".$fecha1."' AND '".$fecha2."' GROUP BY tipo_servicio ORDER BY tipo_servicio");
					
					$i = 0;
					foreach ($query as $row) $tipos_servicios[$i++] = $row['invoiced_services']['tipo_servicio'];
					
					$tipos_servicios_string = '';
					for ($i = 0; $i < count($tipos_servicios); $i++) {
						$tipos_servicios_string .= "'".$tipos_servicios[$i]."'";
						if ($i < count($tipos_servicios) - 1) {
							$tipos_servicios_string .= ", ";
						}
					}
					
					$query = $this->InvoicedService->query("UPDATE invoiced_services SET services_sales_type_id = ".$services_sales_type_id." WHERE tipo_servicio IN(".$tipos_servicios_string.") AND fecha BETWEEN '".$fecha1."' AND '".$fecha2."'");
					
					$this->Session->setFlash(__('<i class="fa fa-info-circle"></i> Venta guardada.'), 'default', array('class' => 'success'));
				}
				else {
					$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> <strong>Error:</strong> No se pudo guardar la venta.'), 'default', array('class' => 'error-message'));
				}
				return $this->redirect(array('controller' => 'reports', 'action' => 'show', $opcion));
				break;
			case 7:
				if ($this->request->is(array('post', 'put'))) {
					$fecha1 = $this->request->data['save_reporte_7']['fecha1'];
					$fecha2 = $this->request->data['save_reporte_7']['fecha2'];
					
					// Guarda los datos resultantes del reporte en la tabla venta de servicios por proveedor
					$this->loadModel('ServicesSalesProvider');
					if (!$this->ServicesSalesProvider->save(array('ServicesSalesProvider' => array('fecha_inicio_proveedor' => $fecha1, 'fecha_fin_proveedor' => $fecha2)))) {
						$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> No se pudo guardar el tipo de servicios de proveedor.'), 'default', array('class' => 'error-message'));
					}
					
					// Obtiene el correlativo del código de los datos guardados en la tabla venta de servicios por proveedor
					$query = $this->ServicesSalesProvider->find('all', array('fields' => 'MAX(ServicesSalesProvider.id) id'));
					$services_sales_provider_id = $query[0][0]['id'];
					
					// Guarda los datos totales de los servicios por tipo
					$this->loadModel('Provider');
					$query = $this->Provider->query("
					INSERT INTO providers(services_sales_provider_id, proveedor_servicio, cantidad_servicios_proveedor, total_servicios_proveedor)
					SELECT ".$services_sales_provider_id." services_sales_provider_id, proveedor_servicio, COUNT(id) cantidad_por_proveedor, SUM(tarifa) total_por_proveedor
					FROM invoiced_services
					WHERE fecha BETWEEN '".$fecha1."' AND '".$fecha2."' GROUP BY proveedor_servicio ORDER BY proveedor_servicio");
					
					// Actualiza el código de venta de servicios por proveedor en la tabla de servicios facturados
					$this->loadModel('InvoicedService');
					$query = $this->InvoicedService->query("UPDATE invoiced_services SET services_sales_provider_id = ".$services_sales_provider_id." WHERE fecha BETWEEN '".$fecha1."' AND '".$fecha2."'");
					
					$this->Session->setFlash(__('<i class="fa fa-info-circle"></i> Venta guardada.'), 'default', array('class' => 'success'));
				}
				else {
					$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> <strong>Error:</strong> No se pudo guardar la venta.'), 'default', array('class' => 'error-message'));
				}
				return $this->redirect(array('controller' => 'reports', 'action' => 'show', $opcion));
				break;
			default:
				$this->Session->setFlash(__('<i class="fa fa-times-circle"></i> Reporte no encontrado.'), 'default', array('class' => 'error-message'));
				return $this->redirect(array('controller' => 'reports', 'action' => 'show', $opcion));
				break;
		}
	}
	
	private function _validar_fechas($fecha1, $fecha2) {
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
}
