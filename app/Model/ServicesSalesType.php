<?php
App::uses('AppModel', 'Model');

class ServicesSalesType extends AppModel {
	public $displayField = 'id';
	
	public $hasMany = array(
		'InvoicedService' => array(
			'className' => 'InvoicedService',
			'foreignKey' => 'services_sales_type_id',
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
		'Type' => array(
			'className' => 'Type',
			'foreignKey' => 'services_sales_type_id',
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
	
	public function guardar_venta_tipo($fecha1, $fecha2) {
		if (!$this->save(array('ServicesSalesType' => array('fecha_inicio_tipo' => $fecha1, 'fecha_fin_tipo' => $fecha2)))) {
			return NULL;
		}
					
		// Obtiene el correlativo del código de los datos guardados en la tabla venta de servicios por tipo
		$query = $this->find('all', array('fields' => 'MAX(ServicesSalesType.id) id'));
		if (empty($query)) {
			return NULL;
		}
		else {
			return $query[0][0]['id'];
		}
	}
}
