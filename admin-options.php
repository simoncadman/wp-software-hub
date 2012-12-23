<div class="wrap">

<h2 class="nav-tab-wrapper">
    <a href="?page=software_hub_menu&tab=software-hub-options" class="nav-tab <?php if ( !isset($_GET['tab']) || $_GET['tab'] == '' || isset($_GET['tab']) && $_GET['tab'] == 'software-hub-options' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Software Hub Options', 'software_hub_control');?></a>
    <?php foreach ( $softwareInstances as $softwareItem ) : ?>
    <a href="?page=software_hub_menu&tab=<?php echo $softwareItem->id; ?>" class="nav-tab <?php if ( isset($_GET['tab']) && $_GET['tab'] == $softwareItem->id ) : ?> nav-tab-active <?php endif; ?>"><?php echo $softwareItem->name; ?></a>
    <?php endforeach; ?>
</h2>
        <?php if ( !isset($_GET['tab']) || $_GET['tab'] == '' || isset($_GET['tab']) && $_GET['tab'] == 'software-hub-options' ) : ?>
        <?php if ( count($softwareInstances) > 0 ): ?>
        <table class="form-table">
                <tr valign="top">
                        <th scope="row"><?php _e('Software', 'software_hub');?></th>
                        <td>
                            <ul>
                                <?php foreach ( $softwareInstances as $instance ) : ?>
                                <li>
                                    <form method="post" action="">
                                    <input type="hidden" name="software_hub_backend_page_type" value="delete_software" />
                                    <input type="hidden" name="software_id" value="<?php echo $instance->id; ?>" />
                                    <?php echo $instance->name; ?> <input type="submit" value="Delete" />
                                    </form>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                </tr>
        </table>
        <?php endif; ?>
        <table class="form-table">
         <form method="post" action="">
                <tr valign="top">
                        <th scope="row"><?php _e('Add New Software', 'software_hub');?></th>
                        <td>
                            <input type="hidden" name="software_hub_backend_page_type" value="hub" />
                            <input type="text" name="software_hub_new" />
                        </td>
                </tr>
        </table>
        <?php endif; ?>
                    <?php if ( isset($_GET['tab']) && $_GET['tab'] != 'software-hub-options' ) : ?>
	<form id="software_hub_form" method="post" action="">
                    <input type="hidden" name="software_id" value="<?php echo $software->id; ?>" />
                    <h4>Shortcode: [software_hub_view id="<?php echo $software->id; ?>"]</h4>
                    
                    <h3 class="nav-tab-wrapper">
                        <a href="?page=software_hub_menu&tab=<?= $software->id ?>&tab2=overview" class="nav-tab <?php if ( !isset($_GET['tab2']) || $_GET['tab2'] == '' || $_GET['tab2'] == 'overview' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Overview', 'software_hub_control');?></a>
                        <a href="?page=software_hub_menu&tab=<?= $software->id ?>&tab2=releases" class="nav-tab <?php if ( isset($_GET['tab2']) && $_GET['tab2'] == 'releases' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Releases', 'software_hub_control');?></a>
                        <a href="?page=software_hub_menu&tab=<?= $software->id ?>&tab2=changes" class="nav-tab <?php if ( isset($_GET['tab2']) && $_GET['tab2'] == 'changes' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Changes', 'software_hub_control');?></a>
                        <a href="?page=software_hub_menu&tab=<?= $software->id ?>&tab2=changelog" class="nav-tab <?php if ( isset($_GET['tab2']) && $_GET['tab2'] == 'changelog' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Changelog', 'software_hub_control');?></a>
                        <a href="?page=software_hub_menu&tab=<?= $software->id ?>&tab2=installation" class="nav-tab <?php if ( isset($_GET['tab2']) && $_GET['tab2'] == 'installation' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Installation', 'software_hub_control');?></a>
                        <a href="?page=software_hub_menu&tab=<?= $software->id ?>&tab2=configuration" class="nav-tab <?php if ( isset($_GET['tab2']) && $_GET['tab2'] == 'configuration' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Configuration', 'software_hub_control');?></a>
                        <a href="?page=software_hub_menu&tab=<?= $software->id ?>&tab2=issues" class="nav-tab <?php if ( isset($_GET['tab2']) && $_GET['tab2'] == 'issues' ): ?> nav-tab-active <?php endif; ?>"><?php _e('Issues', 'software_hub_control');?></a>
                    </h3>
                    
                    <?php if ( !isset($_GET['tab2']) || $_GET['tab2'] == '' || $_GET['tab2'] == 'overview' ) : ?>
                    <input type="hidden" name="software_hub_backend_page_type" value="overview" />
                    <table class="form-table">
                            <tr valign="top">
                                    <th scope="row"><?php _e('Show Overview', 'software_hub');?></th>
                                    <td>
                                            <input type="checkbox" name="software_hub_overview_enabled" <?php if ( $software->overview_enabled == 1 ) : ?>checked<?php endif; ?> />
                                    </td>
                            </tr>
                            <tr valign="top">
                                    <th scope="row"><?php _e('Overview', 'software_hub');?></th>
                                    <td>
                                            <?php wp_editor($software->overview, 'software_hub_overview_text'); ?>
                                    </td>
                            </tr>
                    </table>
                    <?php endif; ?>
                    
                    <?php if ( isset($_GET['tab2']) && $_GET['tab2'] == 'releases' ) : ?>
                    <input type="hidden" name="software_hub_backend_page_type" value="releases" />
                    <input type="button" class="button-primary" onclick="document.getElementById('addSoftwareRelease').style.display='';" value="Create New Release" />
                    <table class="form-table">
                        <tr valign="top">
                            <th>
                                Release Name
                            </th>
                            <th>
                                Date
                            </th>
                            <th>
                                Notes
                            </th>
                            <th>
                                Changes
                            </th>
                            <th>
                                Actions
                            </th>
                        </tr>
                        <?php foreach ( $releases as $release ) : ?>
                        <tr valign="top">
                            <td>
                                    <?php echo $release->name; ?>
                            </td>
                            <td>
                                    <?php echo $release->time; ?>
                            </td>
                            <td>
                                    <?php echo $release->notes; ?>
                            </td>
                            <td>
                                <ul>
                                    <?php foreach ( software_hub_changes($release->id) as $change ) : ?>
                                    <li><?php echo substr($change->commit, 0, 10); ?> - <?php echo $change->display_message; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td>
                                    Delete
                                    Modify
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php endif; ?>
                    
                    <?php if ( isset($_GET['tab2']) && $_GET['tab2'] == 'changes' ) : ?>
                    <input type="hidden" id="software_hub_backend_page_type" name="software_hub_backend_page_type" value="changes" />
                    <input type="button" class="button-primary" onclick="document.getElementById('software_hub_backend_page_type').value = 'sync'; document.getElementById('software_hub_form').submit();" value="Sync" />
                    <table class="form-table">
                        <tr valign="top">
                            <th>
                                Time
                            </th>
                            <th>
                                Commit
                            </th>
                            <th>
                                Note
                            </th>
                        </tr>
                        <?php foreach ( $changes as $change ) : ?>
                        <tr valign="top">
                            <td>
                                    <?php echo $change->time; ?>
                            </td>
                            <td>
                                    <?php echo $change->commit; ?>
                            </td>
                            <td>
                                    <?php echo $change->display_message; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php endif; ?>
                    
                    <?php if ( isset($_GET['tab2']) && $_GET['tab2'] == 'changelog' ) : ?>
                    <input type="hidden" name="software_hub_backend_page_type" value="changelog" />
                    <table class="form-table">
                            <tr valign="top">
                                    <th scope="row"><?php _e('Show Changelog', 'software_hub');?></th>
                                    <td>
                                            <input type="checkbox" name="software_hub_changelog_enabled" <?php if ( $software->changelog_enabled ) : ?>checked<?php endif; ?> />
                                    </td>
                            </tr>
                            <tr valign="top">
                                    <th scope="row"><?php _e('Changelog', 'software_hub');?></th>
                                    <td>
                                            <?php wp_editor($software->changelog, 'software_hub_changelog_text'); ?>
                                    </td>
                            </tr>
                    </table>
                    <?php endif; ?>
                    
                    <?php if ( isset($_GET['tab2']) && $_GET['tab2'] == 'installation' ) : ?>
                    <input type="hidden" name="software_hub_backend_page_type" value="installation" />
                    <table class="form-table">
                            <tr valign="top">
                                    <th scope="row"><?php _e('Show Installation', 'software_hub');?></th>
                                    <td>
                                            <input type="checkbox" name="software_hub_installation_enabled" <?php if ( $software->installation_enabled ) : ?>checked<?php endif; ?> />
                                    </td>
                            </tr>
                            <tr valign="top">
                                    <th scope="row"><?php _e('Installation', 'software_hub');?></th>
                                    <td>
                                            <?php wp_editor($software->installation, 'software_hub_installation_text'); ?>
                                    </td>
                            </tr>
                    </table>
                    <?php endif; ?>
                    
                    <?php if ( isset($_GET['tab2']) && $_GET['tab2'] == 'configuration' ) : ?>
                    <input type="hidden" name="software_hub_backend_page_type" value="configuration" />
                    <table class="form-table">
                            <tr valign="top">
                                    <th scope="row"><?php _e('Show Configuration', 'software_hub');?></th>
                                    <td>
                                            <input type="checkbox" name="software_hub_configuration_enabled" <?php if ( $software->configuration_enabled ) : ?>checked<?php endif; ?> />
                                    </td>
                            </tr>
                            <tr valign="top">
                                    <th scope="row"><?php _e('Configuration', 'software_hub');?></th>
                                    <td>
                                            <?php wp_editor($software->configuration, 'software_hub_configuration_text'); ?>
                                    </td>
                            </tr>
                    </table>
                    <?php endif; ?>
                    
                    <?php if ( isset($_GET['tab2']) && $_GET['tab2'] == 'issues' ) : ?>
                    <input type="hidden" name="software_hub_backend_page_type" value="issues" />
                    <table class="form-table">
                            <tr valign="top">
                                    <th scope="row"><?php _e('Show Issues', 'software_hub');?></th>
                                    <td>
                                            <input type="checkbox" name="software_hub_issues_enabled" <?php if ( $software->issues_enabled ) : ?>checked<?php endif; ?> />
                                    </td>
                            </tr>
                            <tr valign="top">
                                    <th scope="row"><?php _e('Issues', 'software_hub');?></th>
                                    <td>
                                            <?php wp_editor($software->issues, 'software_hub_issues_text'); ?>
                                    </td>
                            </tr>
                    </table>
                    <?php endif; ?>
                    <?php endif; ?>
                
                    <?php if ( isset($_GET['tab2']) && ( $_GET['tab2'] == 'releases' || $_GET['tab2'] == 'changes' ) ) : ?>
                    
                    <?php else: ?>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
                    <?php endif; ?>
	</form>
</div>