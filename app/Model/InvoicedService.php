<?php
App::uses('AppModel', 'Model');

class InvoicedService extends AppModel {
	public $displayField = 'id';
	
	public $belongsTo = array(
		'ServicesSalesProvider' => array(
			'className' => 'ServicesSalesProvider',
			'foreignKey' => 'services_sales_provider_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ServicesSalesType' => array(
			'className' => 'ServicesSalesType',
			'foreignKey' => 'services_sales_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'FulfillmentBranchOfficeGoal' => array(
			'className' => 'FulfillmentBranchOfficeGoal',
			'foreignKey' => 'fulfillment_branch_office_goal_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	public function servicios($fecha1, $fecha2, $servicio) {
		return $this->query("SELECT ".$servicio."_servicio, COUNT(id) cantidad_por_".$servicio.", SUM(tarifa) total_por_".$servicio.", SUM(iva) iva_por_".$servicio."
		FROM invoiced_services WHERE fecha BETWEEN ? AND ? GROUP BY ".$servicio."_servicio ORDER BY ".$servicio."_servicio", array($fecha1, $fecha2));
	}
	
	public function actualizar_servicios($fecha1, $fecha2, $servicio, $id, $tipos_servicios = NULL) {
		$id_servicio = $servicio == 'tipo' ? 'services_sales_type_id = ?' : 'services_sales_provider_id = ?';
		$tipo_servicio = empty($tipos_servicios) ? '' : 'tipo_servicio IN('.$tipos_servicios.') AND';
		
		return $this->query("UPDATE invoiced_services SET ".$id_servicio." WHERE ".$tipo_servicio." fecha BETWEEN ? AND ?", array($id, $fecha1, $fecha2));
	}
}
