<div class="accounts index">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php if (!empty($accounts)): ?>
		<table>
			<tr>
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
					<td><?php echo $account['Account']['email']; ?></td>
					<td><?php echo (($account['Account']['activated']) ? __("Yes", true) : __("No", true)); ?></td>
					<td><?php echo (($account['Account']['banned']) ? __("Banned", true) : __("No", true)); ?></td>
					<td><?php echo $account['Account']['access']; ?></td>
					<td><?php echo $account['Account']['last_login']; ?></td>
					<td><?php echo $account['Account']['created']; ?></td>
					<td><?php echo $account['Account']['modified']; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php $paginator->next(); ?>
	<?php else: ?>
		<p>No accounts found.</p>
	<?php endif; ?>
</div>
