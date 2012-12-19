<div id="software-hub-view-<?php echo $software->id; ?>" class="software-hub-view">
    <ul>
        <?php if ( $software->overview_enabled ): ?>
        <li><a href="#software-hub-view-<?php echo $software->id; ?>-overview">Overview</a></li>
        <?php endif; ?>
        
        <?php if ( $software->changelog_enabled ): ?>
        <li><a href="#software-hub-view-<?php echo $software->id; ?>-changelog">Changelog</a></li>
        <?php endif; ?>
        
        <?php if ( $software->installation_enabled ): ?>
        <li><a href="#software-hub-view-<?php echo $software->id; ?>-installation">Installation</a></li>
        <?php endif; ?>
        
        <?php if ( $software->configuration_enabled ): ?>
        <li><a href="#software-hub-view-<?php echo $software->id; ?>-configuration">Configuration</a></li>
        <?php endif; ?>
        
        <?php if ( $software->issues_enabled ): ?>
        <li><a href="#software-hub-view-<?php echo $software->id; ?>-issues">Issues</a></li>
        <?php endif; ?>
    </ul>
    
    <?php if ( $software->overview_enabled ): ?>
    <div id="software-hub-view-<?php echo $software->id; ?>-overview">
        <?php
        echo $software->overview;
        ?>
    </div>
    <?php endif; ?>
    
    <?php if ( $software->changelog_enabled ): ?>
    <div id="software-hub-view-<?php echo $software->id; ?>-changelog">
        <?php
        echo $software->changelog;
        ?>
    </div>
    <?php endif; ?>
    
    <?php if ( $software->installation_enabled ): ?>
    <div id="software-hub-view-<?php echo $software->id; ?>-installation">
        <?php
        echo $software->installation;
        ?>
    </div>
    <?php endif; ?>
    
    <?php if ( $software->configuration_enabled ): ?>
    <div id="software-hub-view-<?php echo $software->id; ?>-configuration">
        <?php
        echo $software->configuration;
        ?>
    </div>
    <?php endif; ?>
    
    <?php if ( $software->issues_enabled ): ?>
    <div id="software-hub-view-<?php echo $software->id; ?>-issues">
        <?php
        echo $software->issues;
        ?>
    </div>
    <?php endif; ?>
</div>