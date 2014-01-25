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
        
        <?php if ( $software->faqs_enabled ): ?>
        <li><a href="#software-hub-view-<?php echo $software->id; ?>-faqs">FAQs</a></li>
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
        <?php foreach ( $releases as $release ) : ?>
        <h4><strong><?php echo $release->name; ?> Release &ndash; <?php echo date('jS F Y', strtotime($release->time)); ?></strong></h4>
        <?php if ( strlen( $release->notes ) > 0 ): ?>
        <?php echo $release->notes; ?>
        <?php endif; ?>
        <ul>
            <?php foreach ( $release->changes as $change ): ?>
            <li><?php echo $change->display_message ?> <a title="<?php echo $change->note ?>" href="<?php echo $githubweb ?>/commit/<?php echo $change->commit; ?>"><img style="width:15px;" src="<?php echo plugins_url() . '/software-hub/images/git.svg' ?>" alt="Git logo" /></a></li>
            <?php endforeach; ?>
        </ul>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <?php if ( $software->installation_enabled ): ?>
    <div id="software-hub-view-<?php echo $software->id; ?>-installation">
        <?php
        $headerdetails = array();
        echo $software->installation;
        ?>
        <select onchange="softwareHubUpdateInstallInstructions();" id="installdropdown">
        <option value="">- Select Operating System -</option>
        <?php foreach ( $osgroups as $osgroup ) : ?>
        <?php if ( $osgroup->child_count > 1 ): ?>
        <optgroup label="  - <?php echo $osgroup->short_name; ?>">
        <?php endif; ?>
            <?php foreach ( $osgroup->oses as $os ) : ?>
            <?php
            $headerdetails[$os->id] = $os->oslist; 
            if ( $os->oscount > 0 ) { 
                if ( $os->oscount > 1 ) {
                    $headerdetails[$os->id] .= ' etc - ';
                } else {
                    $headerdetails[$os->id] .= ' - ';
                }
                $headerdetails[$os->id] .= $os->name;
            } else {
                $headerdetails[$os->id] = $osgroup->name;
            }
            ?>
            <option value="<?php echo $os->id; ?>"><?php echo $headerdetails[$os->id] ?></option>
            <?php endforeach; ?>
        <?php if ( $osgroup->child_count > 1 ): ?>
        </optgroup>
        <?php endif; ?>
        <?php endforeach; ?>
        </select>
        
        <?php foreach ( $installitems as $install ) : ?>
        <div id="install<?= $install->id ?>" class="installtypes">
            <h4><?php echo $headerdetails[$install->id] ?></h4>
            <?php echo do_shortcode($install->content); ?>
        </div>
        <?php endforeach; ?>
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
    
    <?php if ( $software->faqs_enabled ): ?>
    <div id="software-hub-view-<?php echo $software->id; ?>-faqs">
        <?php
        echo $software->faqs;
        ?>
    </div>
    <?php endif; ?>
</div>
