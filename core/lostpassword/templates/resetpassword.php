<?php
/**
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */
style('lostpassword', 'resetpassword');
script('core', 'lostpassword');
?>

<form action="<?php print_unescaped($_['link']) ?>" id="reset-password" method="post">
	<fieldset>
		<p>
			<label for="password" class="infield"><?php p($l->t('New password')); ?></label>
			<input type="password" name="password" id="password" value="" placeholder="<?php p($l->t('New Password')); ?>" required />
			<img class="svg" id="password-icon" src="<?php print_unescaped(image_path('', 'actions/password.svg')); ?>" alt=""/>
		</p>
		<input type="submit" id="submit" value="<?php p($l->t('Reset password')); ?>" />
		<p class="text-center">
			<img class="hidden" id="float-spinner" src="<?php p(\OCP\Util::imagePath('core', 'loading-dark.gif'));?>"/>
		</p>
	</fieldset>
</form>
