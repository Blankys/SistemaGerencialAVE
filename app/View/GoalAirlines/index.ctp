<h2><?php echo __('Metas por Aerolíneas'); ?></h2>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
		<?php echo $this->Html->link(__('Nueva Meta'), array('action' => 'add'),array('class'=>'btn btn-primary')); ?>
		<?php echo $this->Html->link(__('Lista de Aerolíneas'), array('controller' => 'airlines', 'action' => 'index'),array('class'=>'btn btn-primary')); ?>
		<?php echo $this->Html->link(__('Nueva Aerolínea'), array('controller' => 'airlines', 'action' => 'add'),array('class'=>'btn btn-primary')); ?>
</div>
<div class="goalAirlines index">
	
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id','ID'); ?></th>
			<th><?php echo $this->Paginator->sort('airline_id','Aerolínea'); ?></th>
			<th><?php echo $this->Paginator->sort('FECHA_INICIO_D','Fecha de inicio'); ?></th>
			<th><?php echo $this->Paginator->sort('FECHA_FIN','Fecha de Fin'); ?></th>
			<th><?php echo $this->Paginator->sort('META_BSP','Meta BSP'); ?></th>
			<th><?php echo $this->Paginator->sort('VENTA','Venta'); ?></th>
			<th><?php echo $this->Paginator->sort('FALTANTE','Faltante'); ?></th>
			<th><?php echo $this->Paginator->sort('PORCENTAJE','Porcentaje'); ?></th>
			<th class="actions"><?php echo __('Acciones'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($goalAirlines as $goalAirline): ?>
	<tr>
		<td><?php echo h($goalAirline['GoalAirline']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($goalAirline['Airline']['name'], array('controller' => 'airlines', 'action' => 'view', $goalAirline['Airline']['id'])); ?>
		</td>
		<td><?php echo h($goalAirline['GoalAirline']['FECHA_INICIO_D']); ?>&nbsp;</td>
		<td><?php echo h($goalAirline['GoalAirline']['FECHA_FIN']); ?>&nbsp;</td>
		<td><?php echo h($goalAirline['GoalAirline']['META_BSP']); ?>&nbsp;</td>
		<td><?php echo h($goalAirline['GoalAirline']['VENTA']); ?>&nbsp;</td>
		<td><?php echo h($goalAirline['GoalAirline']['FALTANTE']); ?>&nbsp;</td>
		<td><?php echo h($goalAirline['GoalAirline']['PORCENTAJE']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Ver'), array('action' => 'view', $goalAirline['GoalAirline']['id'])); ?>
			<?php echo $this->Html->link(__('Editar'), array('action' => 'edit', $goalAirline['GoalAirline']['id'])); ?>
			<?php echo $this->Form->postLink(__('Eliminar'), array('action' => 'delete', $goalAirline['GoalAirline']['id']), array('confirm' => __('Está seguro de eliminar la meta # %s?', $goalAirline['GoalAirline']['id']))); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array('format'=>'Página {:page} de {:pages}, mostrando {:current} registros de {:count}'));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('anterior'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('siguiente') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>

