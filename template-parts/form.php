<form class="tw-contact-form" enctype="multipart/form-data">
	<?php if ( $title ) : ?>
		<h2><?php echo $title; ?></h2>
	<?php endif; ?>

	<div class="tw-row">
		<div class="tw-col">
			<label>
				<?php esc_html_e( 'First name', 'tw' ); ?>*
				<input type="text" name="first_name" class="tw-input" required>
			</label>
		</div>
		<div class="tw-col">
			<label>
				<?php esc_html_e( 'Last name', 'tw' ); ?>*
				<input type="text" name="last_name" class="tw-input" required>
			</label>
		</div>
	</div>

	<div class="tw-row">
		<div class="tw-col">
			<label>
				<?php esc_html_e( 'Email address', 'tw' ); ?>*
				<input type="email" name="email" class="tw-input" required>
			</label>
		</div>
		<div class="tw-col">
			<label>
				<?php esc_html_e( 'Choose date', 'tw' ); ?>
				<input type="text" name="date" class="tw-input tw-datepicker">
			</label>
		</div>
	</div>

	<div class="tw-row">
		<div class="tw-col">
			<label>
				<?php esc_html_e( 'Choose colors', 'tw' ); ?>
				<select name="colors" class="tw-select" multiple>
				  	<option value="red">Red</option>
				  	<option value="green">Green</option>
				  	<option value="blue">Blue</option>
				</select>
			</label>
		</div>
		<div class="tw-col">
			<label>
				<?php esc_html_e( 'Picture', 'tw' ); ?>
				<input type="file" name="file" class="tw-file" accept="image/*">
			</label>
		</div>
	</div>

	<div class="tw-text-center">
		<p class="tw-error-container"></p>
		<p class="tw-success-container"></p>

		<button type="submit" class="tw-btn">
			<?php esc_html_e( 'Submit', 'tw' ); ?>
		</button>
	</div>
</form>