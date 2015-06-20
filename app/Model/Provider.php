<?php
App::uses('AppModel', 'Model');

class Provider extends AppModel {
	public $displayField = 'id';
	
	public $belongsTo = array(
		'ServicesSalesProvider' => array(
			'className' => 'ServicesSalesProvider',
			'foreignKey' => 'services_sales_provider_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	public function guardar_proveedor($fecha1, $fecha2, $id) {
		return $this->query("INSERT INTO providers(services_sales_provider_id, proveedor_servicio, cantidad_servicios_proveedor, total_servicios_proveedor)
		SELECT ? services_sales_provider_id, proveedor_servicio, COUNT(id) cantidad_por_proveedor, SUM(tarifa) total_por_proveedor FROM invoiced_services
		WHERE fecha BETWEEN ? AND ? GROUP BY tipo_servicio ORDER BY tipo_servicio", array($id, $fecha1, $fecha2));
	}
}
