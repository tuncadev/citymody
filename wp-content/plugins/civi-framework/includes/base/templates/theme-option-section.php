<?php if (!empty($list_section)): ?>
	<div class="glf-tab">
		<ul>
			<?php foreach ($list_section as $section):?>
				<li data-id="section_<?php echo esc_attr($section['id']); ?>"><a href="<?php echo esc_attr('#section_' . $section['id']); ?>">
					<i class="<?php echo esc_attr('dashicons ' . $section['icon']); ?>"></i>
					<span><?php echo wp_kses_post($section['title']); ?></span></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>