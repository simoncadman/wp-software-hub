<div class="wrap">

<h2 class="nav-tab-wrapper">
    <a href="?page=software_hub_menu&tab=software-hub-options" class="nav-tab <?php if ( $_GET['tab'] == '' || $_GET['tab'] == 'software-hub-options' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Software Hub Options', 'software_hub_control');?></a>
    <?php foreach ( software_hub_get_software_instances() as $software ) : ?>
    <a href="?page=software_hub_menu&tab=software-hub-<?php echo $software['id']; ?>" class="nav-tab <?php if ( $_GET['tab'] == 'software-hub-' . $software['id'] ) : ?> nav-tab-active <?php endif; ?>"><?php echo $software['name']; ?></a>
    <?php endforeach; ?>
</h2>
	<form method="post" action="options.php">
        <?php if ( $_GET['tab'] == '' || $_GET['tab'] == 'software-hub-options' ) : ?>
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
        <?php endif; ?>
        
                <?php foreach ( software_hub_get_software_instances() as $software ) : ?>
                    <?php if ( $_GET['tab'] == 'software-hub-' . $software['id'] ) : ?>
                    <h4>Shortcode: [software_hub_view id="<?php echo $software['id']; ?>"]</h4>
                    
                    <h5 class="nav-tab-wrapper">
                        <a href="?page=software_hub_menu&tab=software-hub-<?= $software['id'] ?>&tab2=overview" class="nav-tab <?php if ( $_GET['tab2'] == '' || $_GET['tab2'] == 'overview' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Overview', 'software_hub_control');?></a>
                        <a href="?page=software_hub_menu&tab=software-hub-<?= $software['id'] ?>&tab2=changelog" class="nav-tab <?php if ( $_GET['tab2'] == 'changelog' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Changelog', 'software_hub_control');?></a>
                        <a href="?page=software_hub_menu&tab=software-hub-<?= $software['id'] ?>&tab2=installation" class="nav-tab <?php if ( $_GET['tab2'] == 'installation' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Installation', 'software_hub_control');?></a>
                        <a href="?page=software_hub_menu&tab=software-hub-<?= $software['id'] ?>&tab2=configuration" class="nav-tab <?php if ( $_GET['tab2'] == 'configuration' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Configuration', 'software_hub_control');?></a>
                        <a href="?page=software_hub_menu&tab=software-hub-<?= $software['id'] ?>&tab2=issues" class="nav-tab <?php if ( $_GET['tab2'] == 'issues' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Issues', 'software_hub_control');?></a>
                    </h5>
                    
                    <?php if ( $_GET['tab2'] == '' || $_GET['tab2'] == 'overview' ) : ?>
                    <?php settings_fields('software_hub_settings-overview-' . $software['id']); ?>
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
                    <?php endif; ?>
                    
                    <?php if ( $_GET['tab2'] == 'changelog' ) : ?>
                    <?php settings_fields('software_hub_settings-changelog-' . $software['id']); ?>
                    <table class="form-table">
                            <tr valign="top">
                                    <th scope="row"><?php _e('Show Changelog', 'software_hub');?></th>
                                    <td>
                                            <input type="checkbox" name="software_hub_changelog_enabled_<?php echo $software['id'] ?>" <?php if ( get_option('software_hub_changelog_enabled_'.$software['id'], false) ) : ?>checked<?php endif; ?> />
                                    </td>
                            </tr>
                            <tr valign="top">
                                    <th scope="row"><?php _e('Changelog', 'software_hub');?></th>
                                    <td>
                                            <?php the_editor(get_option('software_hub_changelog_text_'.$software['id'], ""), 'software_hub_changelog_text_'.$software['id']); ?>
                                    </td>
                            </tr>
                    </table>
                    <?php endif; ?>
                    
                    <?php if ( $_GET['tab2'] == 'installation' ) : ?>
                    <?php settings_fields('software_hub_settings-installation-' . $software['id']); ?>
                    <table class="form-table">
                            <tr valign="top">
                                    <th scope="row"><?php _e('Show Installation', 'software_hub');?></th>
                                    <td>
                                            <input type="checkbox" name="software_hub_installation_enabled_<?php echo $software['id'] ?>" <?php if ( get_option('software_hub_installation_enabled_'.$software['id'], false) ) : ?>checked<?php endif; ?> />
                                    </td>
                            </tr>
                            <tr valign="top">
                                    <th scope="row"><?php _e('Installation', 'software_hub');?></th>
                                    <td>
                                            <?php the_editor(get_option('software_hub_installation_text_'.$software['id'], ""), 'software_hub_installation_text_'.$software['id']); ?>
                                    </td>
                            </tr>
                    </table>
                    <?php endif; ?>
                    
                    <?php if ( $_GET['tab2'] == 'configuration' ) : ?>
                    <?php settings_fields('software_hub_settings-configuration-' . $software['id']); ?>
                    <table class="form-table">
                            <tr valign="top">
                                    <th scope="row"><?php _e('Show Configuration', 'software_hub');?></th>
                                    <td>
                                            <input type="checkbox" name="software_hub_configuration_enabled_<?php echo $software['id'] ?>" <?php if ( get_option('software_hub_configuration_enabled_'.$software['id'], false) ) : ?>checked<?php endif; ?> />
                                    </td>
                            </tr>
                            <tr valign="top">
                                    <th scope="row"><?php _e('Configuration', 'software_hub');?></th>
                                    <td>
                                            <?php the_editor(get_option('software_hub_configuration_text_'.$software['id'], ""), 'software_hub_configuration_text_'.$software['id']); ?>
                                    </td>
                            </tr>
                    </table>
                    <?php endif; ?>
                    
                    <?php if ( $_GET['tab2'] == 'issues' ) : ?>
                    <?php settings_fields('software_hub_settings-issues-' . $software['id']); ?>
                    <table class="form-table">
                            <tr valign="top">
                                    <th scope="row"><?php _e('Show Issues', 'software_hub');?></th>
                                    <td>
                                            <input type="checkbox" name="software_hub_issues_enabled_<?php echo $software['id'] ?>" <?php if ( get_option('software_hub_issues_enabled_'.$software['id'], false) ) : ?>checked<?php endif; ?> />
                                    </td>
                            </tr>
                            <tr valign="top">
                                    <th scope="row"><?php _e('Issues', 'software_hub');?></th>
                                    <td>
                                            <?php the_editor(get_option('software_hub_issues_text_'.$software['id'], ""), 'software_hub_issues_text_'.$software['id']); ?>
                                    </td>
                            </tr>
                    </table>
                    <?php endif; ?>
                    
                    <?php break; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>