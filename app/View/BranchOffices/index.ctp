<div class="branchOffices index">
	<h2><?php echo __('Sucursales'); ?></h2>
        <div class="actions">
            <h3><?php echo __('Opciones'); ?></h3>
            <?php echo $this->Html->link(__('Nueva Sucursal'), array("action"=>"add"),
                                                    array('class'=>'btn btn-primary')); ?>
            
        </div>

	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id','ID'); ?></th>
			<th><?php echo $this->Paginator->sort('name','Nombre de Sucursal'); ?></th>
			<th class="actions"><?php echo __('Acciones'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($branchOffices as $branchOffice): ?>
	<tr>
		<td><?php echo h($branchOffice['BranchOffice']['id']); ?>&nbsp;</td>
		<td><?php echo h($branchOffice['BranchOffice']['name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Ver'), array('action' => 'view', $branchOffice['BranchOffice']['id'])); ?>
			<?php echo $this->Html->link(__('Editar'), array('action' => 'edit', $branchOffice['BranchOffice']['id'])); ?>
			<?php echo $this->Form->postLink(__('Eliminar'), array('action' => 'delete', $branchOffice['BranchOffice']['id']), array('confirm' => __('Está seguro de eliminar la sucursal  %s?', $branchOffice['BranchOffice']['name']))); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array('format'=>'Página {:page} de {:pages}, mostrando {:current} registros de {:count}')
                );
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('anterior'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('siguiente') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
