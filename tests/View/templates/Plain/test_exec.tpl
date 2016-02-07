<?php $this->var1 = 'some value' ?>
exec: <?= $this->exec(['controller' => 'exec_test', 'action' => 'inner', 'param1' => 'a string', 'param2' => 42]) ?>

exec2: <?= $this->exec(['controller' => 'exec_test', 'action' => 'inner_with_response', 'param1' => 'hello']) ?>

<?= $this->var1 ?> <?= $this->assigned1 ?> <?= $this->assigned2 ?>