<?php
	$merchantId = (empty(WP_AZPayCheckout::$config['merchantId'])) ? '' : WP_AZPayCheckout::$config['merchantId'];
	$merchantKey = (empty(WP_AZPayCheckout::$config['merchantKey'])) ? '' : WP_AZPayCheckout::$config['merchantKey'];
	$titlecheckout = (empty(WP_AZPayCheckout::$config['titlecheckout'])) ? '' : WP_AZPayCheckout::$config['titlecheckout'];
	$titlebtn = (empty(WP_AZPayCheckout::$config['titlebtn'])) ? '' : WP_AZPayCheckout::$config['titlebtn'];
?>

<div class="wrap">

	<h2 class="title">Configurando AZPay</h2>

	<form method="post" action="admin-post.php">
		<input type="hidden" name="action" value="wpac_save_option" />
		<?php wp_nonce_field('options_page_nonce', 'options_page_nonce_field'); ?>

		<table class="form-table">
			<tbody>

				<tr>
					<th scope="row">
						<label for="wpac_merchantid">MERCHANT ID*</label>
					</th>
					<td>
						<input name="wpac_merchantid" type="text" id="wpac_merchantid" value="<?php echo $merchantId; ?>" class="regular-text" />
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wpac_merchantkey">MERCHANT KEY*</label>
					</th>
					<td>
						<input name="wpac_merchantkey" type="text" id="wpac_merchantkey" value="<?php echo $merchantKey; ?>" class="regular-text" />
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wpac_rebill">Recorrência</label>
					</th>
					<td>
						<input name="wpac_rebill" type="checkbox" id="wpac_rebill" <?php echo (empty(WP_AZPayCheckout::$config['rebill'])) ? '' : 'checked'; ?> />
					</td>
				</tr>

			</tbody>
		</table>

		<h2 class="title">Configurando bandeiras e operadoras*</h2>

		<table class="form-table">
			<tbody>

				<tr>
					<th scope="row">
						<label for="wpac_flag_visa">Visa</label>
					</th>
					<td>
						<select class="" name="wpac_flag_visa">
							<option value="0">Bandeira desabilitada</option>
							<?php
								foreach($this::$cardOperators['wpac_flag_visa'] as $operator) {
									$selected = ( $this::$config['flags']['visa']['value'] == $operator['value'] ) ? 'selected' : '';
									echo '<option value="'.$operator['value'].'" '.$selected.'>'.$operator['title'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wpac_flag_mastercard">Mastercard</label>
					</th>
					<td>
						<select class="" name="wpac_flag_mastercard">
							<option value="0">Bandeira desabilitada</option>
							<?php
								foreach($this::$cardOperators['wpac_flag_mastercard'] as $operator) {
									$selected = ( $this::$config['flags']['mastercard']['value'] == $operator['value'] ) ? 'selected' : '';
									echo '<option value="'.$operator['value'].'" '.$selected.'>'.$operator['title'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wpac_flag_amex">Amex</label>
					</th>
					<td>
						<select class="" name="wpac_flag_amex">
							<option value="0">Bandeira desabilitada</option>
							<?php
								foreach($this::$cardOperators['wpac_flag_amex'] as $operator) {
									$selected = ( $this::$config['flags']['amex']['value'] == $operator['value'] ) ? 'selected' : '';
									echo '<option value="'.$operator['value'].'" '.$selected.'>'.$operator['title'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wpac_flag_diners">Diners</label>
					</th>
					<td>
						<select class="" name="wpac_flag_diners">
							<option value="0">Bandeira desabilitada</option>
							<?php
								foreach($this::$cardOperators['wpac_flag_diners'] as $operator) {
									$selected = ( $this::$config['flags']['diners']['value'] == $operator['value'] ) ? 'selected' : '';
									echo '<option value="'.$operator['value'].'" '.$selected.'>'.$operator['title'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wpac_flag_discover">Discover</label>
					</th>
					<td>
						<select class="" name="wpac_flag_discover">
							<option value="0">Bandeira desabilitada</option>
							<?php
								foreach($this::$cardOperators['wpac_flag_discover'] as $operator) {
									$selected = ( $this::$config['flags']['discover']['value'] == $operator['value'] ) ? 'selected' : '';
									echo '<option value="'.$operator['value'].'" '.$selected.'>'.$operator['title'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wpac_flag_elo">Elo</label>
					</th>
					<td>
						<select class="" name="wpac_flag_elo">
							<option value="0">Bandeira desabilitada</option>
							<?php
								foreach($this::$cardOperators['wpac_flag_elo'] as $operator) {
									$selected = ( $this::$config['flags']['elo']['value'] == $operator['value'] ) ? 'selected' : '';
									echo '<option value="'.$operator['value'].'" '.$selected.'>'.$operator['title'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wpac_flag_aura">Aura</label>
					</th>
					<td>
						<select class="" name="wpac_flag_aura">
							<option value="0">Bandeira desabilitada</option>
							<?php
								foreach($this::$cardOperators['wpac_flag_aura'] as $operator) {
									$selected = ( $this::$config['flags']['aura']['value'] == $operator['value'] ) ? 'selected' : '';
									echo '<option value="'.$operator['value'].'" '.$selected.'>'.$operator['title'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wpac_flag_jcb">JCB</label>
					</th>
					<td>
						<select class="" name="wpac_flag_jcb">
							<option value="0">Bandeira desabilitada</option>
							<?php
								foreach($this::$cardOperators['wpac_flag_jcb'] as $operator) {
									$selected = ( $this::$config['flags']['jcb']['value'] == $operator['value'] ) ? 'selected' : '';
									echo '<option value="'.$operator['value'].'" '.$selected.'>'.$operator['title'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>

			</tbody>
		</table>

		<h2 class="title">Configurando textos</h2>

		<table class="form-table">
			<tbody>

				<tr>
					<th scope="row">
						<label for="wpac_titlecheckout">Título Checkout</label>
					</th>
					<td>
						<input name="wpac_titlecheckout" type="text" id="wpac_titlecheckout" value="<?php echo $titlecheckout; ?>" class="regular-text" />
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wpac_titlebtn">Título Botão Finalizar</label>
					</th>
					<td>
						<input name="wpac_titlebtn" type="text" id="wpac_titlebtn" value="<?php echo $titlebtn; ?>" class="regular-text" />
					</td>
				</tr>

			</tbody>
		</table>

		<br />
		<span>* Campos obrigatórios</span>

		<p class="submit">
			<input type="submit" name="wpac_options_submit" class="button button-primary" value="Atualizar Configuração">
		</p>

	</form>

</div>
