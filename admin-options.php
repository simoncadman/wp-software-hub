<div class="wrap">
	<h2><?php _e('Software Hub Options', 'software_hub_control');?></h2>
	<form method="post" action="options.php">
		<?php settings_fields('software_hub_settings'); ?>
		<h3><?php _e('Software', 'software_hub');?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Software', 'software_hub');?></th>
                                <td>
                                        <textarea name="software_hub_instances"><?php echo get_option('software_hub_instances', ""); ?></textarea>
				</td>
				<td>
					<small><?php _e("List of software instances", 'software_hub');?></small>
				</td>
			</tr>
		</table>
                <?php foreach ( software_hub_get_software_instances() as $software ) : ?>
                    <h3><?php echo $software['name']; ?></h3>
                    <h4>Overview</h4>
                    <table class="form-table">
                            <tr valign="top">
                                    <th scope="row"><?php _e('Show Overview', 'software_hub');?></th>
                                    <td>
                                            <input type="checkbox" name="software_hub_overview_enabled_<?php echo $software['id'] ?>" <?php if ( get_option('software_hub_overview_enabled_'.$software['id'], false) ) : ?>checked<?php endif; ?> />
                                    </td>
                            </tr>
                            <tr valign="top">
                                    <th scope="row"><?php _e('Overview', 'software_hub');?></th>
                                    <td>
                                            <?php the_editor(get_option('software_hub_overview_text_'.$software['id'], ""), 'software_hub_overview_text_'.$software['id']); ?>
                                    </td>
                            </tr>
                    </table>
                <?php endforeach; ?>
                
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>