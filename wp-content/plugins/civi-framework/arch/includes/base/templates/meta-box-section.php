<?php if ( !empty($list_section) ): ?>
	<div class="glf-tab">
		<ul>
			<?php $section_index = 0; ?>
			<?php foreach( $list_section as $section ) : ?>
				<li class="<?php echo esc_attr($section_index == 0 ? 'active' : ''); ?>" data-id="section_<?php echo esc_attr($section['id']); ?>">
					<a href="<?php echo esc_attr('#section_' . $section['id']); ?>">
						<i class="<?php echo esc_attr('dashicons ' . $section['icon']); ?>"></i>
						<span><?php echo wp_kses_post($section['title']); ?></span>
					</a>
				</li>
				<?php $section_index++; ?>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>