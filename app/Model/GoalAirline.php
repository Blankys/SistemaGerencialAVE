<?php
App::uses('AppModel', 'Model');
/**
 * GoalAirline Model
 *
 * @property Airline $Airline
 */
class GoalAirline extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
*/
        public $validate=array(
            'fecha_inicio'=>array(
                'rule'=>'notEmpty',
                'message'=>'Por favor seleccione una fecha'
            ),
            'fecha_fin'=>array(
                'rule'=>'notEmpty',
                'message'=>'Por favor seleccione una fecha'
            ),
            'periodo_bsp' => array(
            'number' => array(
                'rule' => array('range',0,13),
                'required' => true,
                'message' => 'Por favor ingrese un número entre 1 y 12.'
            )
            ),
            'meta_bsp'=>array(
                'money'=>array(
                    'rule'=>array('money','left'),
                    'required'=>'true',
                    'message'=>'Ingrese un monto válido.'
                )
            ),
            'comision'=>array(
                'number' => array(
                'rule' => array('range',0,101),
                'required' => true,
                'message' => 'Por favor ingrese un porcentaje válido (1-100).'
            ))
        );
	public $belongsTo = array(
		'Airline' => array(
			'className' => 'Airline',
			'foreignKey' => 'airline_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	public function metas_aereolineas($airline_id) {
		return $this->query("SELECT periodo_bsp, fecha_inicio, fecha_fin, meta_bsp, boletos_periodo, total_periodo, faltante, porcentaje, comision, ingreso_comision
		FROM goal_airlines WHERE airline_id = ? AND boletos_periodo <> 0 ORDER BY fecha_inicio", array($airline_id));
	}
}
