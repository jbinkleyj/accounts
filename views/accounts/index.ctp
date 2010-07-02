<div class="accounts index">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php if (!empty($accounts)): ?>
		<table>
			<tr>
				<?php if (Configure::read('accounts.fields.username') != Configure::read('accounts.fields.email')): ?>
					<th><?php echo $paginator->sort(__("Username", true), 'email'); ?></th>
				<?php endif; ?>
				<th><?php echo $paginator->sort(__("Email", true), 'email'); ?></th>
				<th><?php echo $paginator->sort(__("Activated", true), 'activated'); ?></th>
				<th><?php echo $paginator->sort(__("Banned", true), 'banned'); ?></th>
				<th><?php echo $paginator->sort(__("Access", true), 'access'); ?></th>
				<th><?php echo $paginator->sort(__("Last Login", true), 'last_login'); ?></th>
				<th><?php echo $paginator->sort(__("Created", true), 'created'); ?></th>
				<th><?php echo $paginator->sort(__("Modified", true), 'modified'); ?></th>
			</tr>
			<?php
			$i = 0;
			foreach ($accounts as $account):
				$class = null;
				if ($i++ % 2 == 0) {
					$class = ' class="altrow"';
				}
				?>
				<tr<?php echo $class; ?>>
					<?php if (Configure::read('accounts.fields.username') != Configure::read('accounts.fields.email')): ?>
						<td><?php echo $account[Configure::read('accounts.modelName')][Configure::read('accounts.fields.username')]; ?></td>
					<?php endif; ?>
					<td><?php echo $account[Configure::read('accounts.modelName')][Configure::read('accounts.fields.email')]; ?></td>
					<td><?php echo (($account[Configure::read('accounts.modelName')][Configure::read('accounts.fields.activated')]) ? __("Yes", true) : __("No", true)); ?></td>
					<td><?php echo (($account[Configure::read('accounts.modelName')][Configure::read('accounts.fields.banned')]) ? __("Banned", true) : __("No", true)); ?></td>
					<td><?php echo $account[Configure::read('accounts.modelName')][Configure::read('accounts.fields.last_login')]; ?></td>
					<td><?php echo $account[Configure::read('accounts.modelName')]['created']; ?></td>
					<td><?php echo $account[Configure::read('accounts.modelName')]['modified']; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php $paginator->next(); ?>
	<?php else: ?>
		<p>No accounts found.</p>
	<?php endif; ?>
</div>
