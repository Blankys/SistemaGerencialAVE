<div class="goalBranchOffices form">
<?php echo $this->Form->create('GoalBranchOffice'); ?>
	<fieldset>
		<legend><?php echo __('Add Goal Branch Office'); ?></legend>
	<?php
		echo $this->Form->input('SUCURSAL');
		echo $this->Form->input('MES');
		echo $this->Form->input('IDSUCURSAL');
		echo $this->Form->input('SUCURSAL_C');
		echo $this->Form->input('MES_CUMPLIMIENTO');
		echo $this->Form->input('META_BOLETOS');
		echo $this->Form->input('META_SERVICIOS');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Goal Branch Offices'), array('action' => 'index')); ?></li>
	</ul>
</div>
