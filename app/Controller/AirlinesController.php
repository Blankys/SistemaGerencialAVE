<?php
App::uses('AppController', 'Controller');
/**
 * Airlines Controller
 *
 * @property Airline $Airline
 * @property PaginatorComponent $Paginator
 */
class AirlinesController extends AppController {

/**
 * Components
 *
 * @var array
 */
        public $paginate=array(
            'limit'=>10,
            'order'=>array('Airline.id'=>'asc')
        );
	
        

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Airline->recursive = 0;
		$this->set('airlines', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Airline->exists($id)) {
			throw new NotFoundException(__('Aerolínea Inválida'));
		}
		$options = array('conditions' => array('Airline.' . $this->Airline->primaryKey => $id));
		$this->set('airline', $this->Airline->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Airline->create();
			if ($this->Airline->save($this->request->data)) {
				$this->Session->setFlash(__('La aerolínea fue guardada.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('La aerolínea no fue guardada. Porfavor intente de nuevo.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Airline->exists($id)) {
			throw new NotFoundException(__('Aerolínea inválida'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Airline->save($this->request->data)) {
				$this->Session->setFlash(__('La aerolínea fue guardada.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('La aerolínea no fue guardada. Porfavor intente de nuevo.'));
			}
		} else {
			$options = array('conditions' => array('Airline.' . $this->Airline->primaryKey => $id));
			$this->request->data = $this->Airline->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Airline->id = $id;
		if (!$this->Airline->exists()) {
			throw new NotFoundException(__('Aerolínea inválida'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Airline->delete()) {
			$this->Session->setFlash(__('La aerolínea fue eliminada.'));
		} else {
			$this->Session->setFlash(__('La aerolínea no fue guardada. Porfavor intente de nuevo.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
