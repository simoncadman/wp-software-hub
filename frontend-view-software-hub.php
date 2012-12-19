<div id="software-hub-view-<?php echo $software['id']; ?>" class="software-hub-view">
    <ul>
        <li><a href="#software-hub-view-<?php echo $software['id']; ?>-overview">Overview</a></li>
        <li><a href="#software-hub-view-<?php echo $software['id']; ?>-changelog">Changelog</a></li>
        <li><a href="#software-hub-view-<?php echo $software['id']; ?>-installation">Installation</a></li>
        <li><a href="#software-hub-view-<?php echo $software['id']; ?>-configuration">Configuration</a></li>
        <li><a href="#software-hub-view-<?php echo $software['id']; ?>-issues">Issues</a></li>
    </ul>
    
    <?php if ( get_option('software_hub_overview_enabled_'.$software['id'], "") ): ?>
    <div id="software-hub-view-<?php echo $software['id']; ?>-overview">
        <?php
        echo get_option('software_hub_overview_text_'.$software['id'], "");
        ?>
    </div>
    <?php endif; ?>
    
    <?php if ( get_option('software_hub_changelog_enabled_'.$software['id'], "") ): ?>
    <div id="software-hub-view-<?php echo $software['id']; ?>-changelog">
        <?php
        echo get_option('software_hub_changelog_text_'.$software['id'], "");
        ?>
    </div>
    <?php endif; ?>
    
    <?php if ( get_option('software_hub_installation_enabled_'.$software['id'], "") ): ?>
    <div id="software-hub-view-<?php echo $software['id']; ?>-installation">
        <?php
        echo get_option('software_hub_installation_text_'.$software['id'], "");
        ?>
    </div>
    <?php endif; ?>
    
    <?php if ( get_option('software_hub_configuration_enabled_'.$software['id'], "") ): ?>
    <div id="software-hub-view-<?php echo $software['id']; ?>-configuration">
        <?php
        echo get_option('software_hub_configuration_text_'.$software['id'], "");
        ?>
    </div>
    <?php endif; ?>
    
    <?php if ( get_option('software_hub_issues_enabled_'.$software['id'], "") ): ?>
    <div id="software-hub-view-<?php echo $software['id']; ?>-issues">
        <?php
        echo get_option('software_hub_issues_text_'.$software['id'], "");
        ?>
    </div>
    <?php endif; ?>
</div>