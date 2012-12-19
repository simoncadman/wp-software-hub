<div class="software-hub-view">
    <?php if ( get_option('software_hub_overview_enabled_'.$software['id'], "") ): ?>
    <div class="software-hub-view-overview">
        <h3>Overview</h3>
        <?php
        echo get_option('software_hub_overview_text_'.$software['id'], "");
        ?>
        </div>
    <?php endif; ?>
    
    <?php if ( get_option('software_hub_changelog_enabled_'.$software['id'], "") ): ?>
    <div class="software-hub-view-changelog">
        <h3>Changelog</h3>
        <?php
        echo get_option('software_hub_changelog_text_'.$software['id'], "");
        ?>
    </div>
    <?php endif; ?>
    
    <?php if ( get_option('software_hub_installation_enabled_'.$software['id'], "") ): ?>
    <div class="software-hub-view-installation">
        <h3>Installation</h3>
        <?php
        echo get_option('software_hub_installation_text_'.$software['id'], "");
        ?>
    </div>
    <?php endif; ?>
    
    <?php if ( get_option('software_hub_configuration_enabled_'.$software['id'], "") ): ?>
    <div class="software-hub-view-configuration">
        <h3>Configuration</h3>
        <?php
        echo get_option('software_hub_configuration_text_'.$software['id'], "");
        ?>
    </div>
    <?php endif; ?>
    
    <?php if ( get_option('software_hub_issues_enabled_'.$software['id'], "") ): ?>
    <div class="software-hub-view-issues">
        <h3>Issues</h3>
        <?php
        echo get_option('software_hub_issues_text_'.$software['id'], "");
        ?>
    </div>
    <?php endif; ?>
</div>