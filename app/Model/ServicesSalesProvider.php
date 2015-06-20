<?php
App::uses('AppModel', 'Model');

class ServicesSalesProvider extends AppModel {
	public $displayField = 'id';
	
	public $hasMany = array(
		'InvoicedService' => array(
			'className' => 'InvoicedService',
			'foreignKey' => 'services_sales_provider_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Provider' => array(
			'className' => 'Provider',
			'foreignKey' => 'services_sales_provider_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	
	public function guardar_venta_proveedor($fecha1, $fecha2) {
		if (!$this->save(array('ServicesSalesProvider' => array('fecha_inicio_proveedor' => $fecha1, 'fecha_fin_proveedor' => $fecha2)))) {
			return NULL;
		}
					
		// Obtiene el correlativo del código de los datos guardados en la tabla venta de servicios por proveedor
		$query = $this->find('all', array('fields' => 'MAX(ServicesSalesProvider.id) id'));
		if (empty($query)) {
			return NULL;
		}
		else {
			return $query[0][0]['id'];
		}
	}
}
