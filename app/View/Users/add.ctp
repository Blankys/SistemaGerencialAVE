<h1>Ingresar Usuario</h1>
    <?php  
            echo $this->Form->create('User', array('class'=>'form'));
            
            echo '<div class="form-group">';
                echo $this->Form->input('name',array('label'=>'Nombres',
                                        'class'=>'form-control',
                                'placeholder'=>'Ingrese nombres'                
                    ));
            echo '</div>';
            
            echo '<div class="form-group">';
                echo $this->Form->input('last_name',array('label'=>'Apellidos',
                                                          'class'=>'form-control',
                                  'placeholder'=>'Ingrese apellidos'
                    ));
            echo '</div>';
            
            echo '<div class="form-group">';
            echo $this->Form->input('username',array('label'=>'Usuario',
                                                          'class'=>'form-control',
                                  'placeholder'=>'Ingrese nombre de usuario'));
            echo '</div>';
            
            echo '<div class="form-group">';
            echo $this->Form->input('password',array('label'=>'Contraseña',
                                                          'class'=>'form-control',
                                  'placeholder'=>'Ingrese contraseña'));
            echo '</div>';
            
            echo $this->Form->input('role',array(
                    'options' => array('strategic'=>'Estratégico', 'tactic' => 'Táctico','admin' => 'Administrador'),
                    'label'=>'Rol',
                    'class'=>'multiple form-control'
            ));
    ?>
<?php       echo '<br>';
            echo $this->Form->end(array('label'=>'Guardar usuario',
                            'class'=>'btn btn-primary'));