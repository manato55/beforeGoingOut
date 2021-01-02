


<div class="users form">
<?= $this->Flash->render('auth') ?>
<?= $this->Form->create($user) ?>
	<fieldset>
		<legend>emailとpasswordを入力してください。</legend>
		<?= $this->Form->control('email') ?>
		<?= $this->Form->control('password') ?>
	</fieldset>
<?= $this->Form->button('ログイン'); ?>
<?= $this->Form->end() ?>
<?= $this->Html->link('新規登録',"/users/add"); ?>
</div>