<?php
App::uses('AppModel', 'Model');

class Type extends AppModel {
	public $displayField = 'id';
	
	public $belongsTo = array(
		'ServicesSalesType' => array(
			'className' => 'ServicesSalesType',
			'foreignKey' => 'services_sales_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	public function guardar_tipo($fecha1, $fecha2, $id) {
		return $this->query("INSERT INTO types(services_sales_type_id, tipo_servicio, cantidad_servicios_tipo, total_servicios_tipo)
		SELECT ? services_sales_type_id, tipo_servicio, COUNT(id) cantidad_por_tipo, SUM(tarifa) total_por_tipo FROM invoiced_services
		WHERE fecha BETWEEN ? AND ? GROUP BY tipo_servicio ORDER BY tipo_servicio", array($id, $fecha1, $fecha2));
	}
}
