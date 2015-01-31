<?php
/**
 * @file node.tpl.php
 *
 * Theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: Node body or teaser depending on $teaser flag.
 * - $picture: The authors picture of the node output from
 *   theme_user_picture().
 * -
 * $date: Formatted creation date (use $created to reformat with
 *   format_date()).
 * - $links: Themed links like "Read more", "Add new comment", etc. output
 *   from theme_links().
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $terms: the themed list of taxonomy term links output from theme_links().
 * - $submitted: themed submission information output from
 *   theme_node_submitted().
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type, i.e. story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $teaser: Flag for the teaser state.
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 */
?>

<!-- STATUS/INFO -->
<div id="project-info">
  <ul class="list-inline">
    <?php if ($project->settings->live['live_domain']): ?>
    <li>
      <strong>Live Site</strong>
      <small><a href="http://<?php print $project->settings->live['live_domain']; ?>" target="_blank">http://<?php print $project->settings->live['live_domain']; ?></a></small>
    </li>
    <?php endif; ?>
    <li>
      <strong>Install Profile</strong>
      <small><?php print $project->install_profile ?></small>
    </li>

    <!-- Drush Info -->
    <li class="pull-right">
      <button type="button" class="btn btn-xs btn-link text-muted" data-toggle="modal" data-target="#drush-alias-modal" title="Drush Aliases">
        <i class="fa fa-drupal"></i>
        <?php print t('Drush'); ?>
      </button>

      <!-- Modal -->
      <div class="modal fade" id="drush-alias-modal" tabindex="-1" role="dialog" aria-labelledby="drush-alias-modal" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
              <h4 class="modal-title" id="drush-alias-modal">Project Drush Aliases</h4>
            </div>
            <div class="modal-body">

              <!-- Download button -->
              <p>
                <a href="<?php print $aliases_url; ?>" class="btn btn-primary"><?php print t('Download Alias File'); ?></a> or copy to <code>~/.drush/<?php print $project->name; ?>.aliases.drushrc.php</code>.
              </p>

              <textarea cols="40" rows="10" class='form-control' onlick="this.select()"><?php print $drush_aliases; ?></textarea>

              <p>
                <?php print $access_note; ?>
              </p>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </li>
    <li>
    <?php if ($project->settings->deploy['method'] == 'manual'): ?>
      <strong><?php print t('Manual Deployment Only'); ?></strong>
    <?php else: ?>
        <!-- Webhook -->
        <?php if ($project->settings->deploy['method'] == 'webhook'): ?>


      <strong><?php print t('Last Commit'); ?></strong>
          <small>
          <?php if (empty($project->settings->deploy['last_webhook'])): ?>
            <!-- Not Received -->
            <span class="text-danger"><i class="fa fa-warning"></i> <?php print t('Not Received'); ?></span>
          <?php elseif ($project->settings->deploy['last_webhook_status'] == DEVSHOP_PULL_STATUS_ACCESS_DENIED): ?>
            <!-- Last Received -->
            <span class="text-danger">
              <i class="fa fa-warning"></i> <?php print t('Access Denied'); ?>
            </span>
            <a href="<?php print url('admin/hosting/devshop/pull')?>">
              <?php print t('Configure Webhook Access'); ?>
            </a>
          <?php else: ?>
            <!-- Last Received -->
            <span title="<?php print t('Last webhook receieved.'); ?>"><?php print $webhook_ago; ?></span>
          <?php endif; ?>
          </small>

        <?php elseif ($project->settings->deploy['method'] == 'queue'): ?>
        <!-- Queue -->
        <strong><?php print t('Queue'); ?>:</strong>
        <small>
          <?php print $queued_ago; ?>
        </small>
          <?php if (user_access('administer hosting queues')): ?>
              <?php print $hosting_queue_admin_link; ?>
          <?php endif; ?>
        <?php endif; ?>
    </li>

    <!-- Webhook -->
    <?php if ($project->settings->deploy['method'] == 'webhook'):

        $float = empty($project->settings->deploy['last_webhook'])? 'inline': 'pull-right';
      ?>
      <li class="<?php print $float; ?>"><?php print $webhook_url; ?></li>
    <?php endif; ?>
    <?php endif; ?>

  </ul>
</div>

<!-- ENVIRONMENTS-->
<div class="row">
<?php foreach ($node->project->environments as $environment_name => $environment): ?>

  <div class="environment-wrapper col-xs-12 col-sm-6 col-md-4 col-lg-3">

    <div class="list-group environment <?php print $environment->class  ?>">
      <div class="environment-header list-group-item list-group-item-<?php print $environment->list_item_class ?>">


        <div class="environment-dropdowns pull-right">

          <!-- Environment Tasks -->
          <div class="environment-tasks btn-group ">
            <?php print $environment->tasks_list; ?>
          </div>
        </div>

        <!-- Environment Links -->
        <a href="<?php print $environment->site? url("node/$environment->site"): url("node/$environment->platform"); ?>" class="environment-link">
          <?php print $environment->name; ?></a>

        <a href="<?php print url("node/$environment->site/logs/commits"); ?>" class="environment-meta-data btn btn-text">
          <i class='fa fa-<?php print $environment->git_ref_type == 'tag'? 'tag': 'code-fork'; ?>'></i><?php print $environment->git_ref; ?>
        </a>

        <?php if ($environment->version): ?>
          <a href="<?php print url("node/$environment->platform"); ?>"  title="Drupal version <?php print $environment->version; ?>" class="environment-meta-data btn btn-text">
          <i class="fa fa-drupal"></i><?php print $environment->version; ?>
        </a>

        <?php endif; ?>

        <?php if ($environment->site_status == HOSTING_SITE_DISABLED): ?>
          <span class="environment-meta-data">Disabled</span>
        <?php endif; ?>

        <!-- Environment Status Indicators -->
        <div class="environment-indicators">
          <?php if ($environment->settings->locked): ?>
            <span class="environment-meta-data text-muted" title="<?php print t('This database is locked.'); ?>">
              <i class="fa fa-lock"></i><?php print t('Locked') ?>
            </span>
          <?php endif; ?>

          <?php if ($environment->name == $project->settings->live['live_environment']): ?>
          <span class="environment-meta-data text-muted" title="<?php print t('This is the live environment.'); ?>">
            <i class="fa fa-bolt"></i>Live
          </span>
          <?php endif; ?>
        </div>


        <div class="progress">
          <div class="progress-bar progress-bar-striped progress-bar-warning active"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
            <span class="sr-only"><?php print $environment->progress_output ?></span>
          </div>
        </div>
      </div>
      <!-- URLs -->
      <div class="environment-domains list-group-item btn-group btn-group-justified">
        <div class="btn-group">
          <?php if (count($environment->domains) > 1): ?>

            <a type="button" class="btn btn-xs" href="<?php print $environment->url ?>" target="_blank">
              <i class="fa fa-globe"></i> <?php print $environment->url ?>
            </a>
            <button type="button" class="btn btn-xs dropdown-toggle pull-right" data-toggle="dropdown" aria-expanded="false">
              <i class="fa fa-globe"></i>
              <?php print count($environment->domains); ?>
              <span class="caret"></span>
              <span class="sr-only">Domains</span>
            </button>
          <?php else: ?>
            <?php if (!empty($environment->url)): ?>
              <a type="button" class="btn btn-xs" href="<?php print $environment->url ?>" target="_blank">
                <i class="fa fa-globe"></i>
                <?php print $environment->url ?>
              </a>
            <?php else: ?>
              <button class="btn btn-xs">
                <i class="fa fa-globe"></i>
                <em>&nbsp;</em>
              </button>
            <?php endif;?>
            <?php /*
            <a type="button" class="btn btn-xs pull-right" href="<?php print url('node/' . $node->nid . '/edit/' . $environment->name, array('query'=> drupal_get_destination())); ?>" title="<?php print t("Add Domain"); ?>">
              <i class="fa fa-plus"></i>
            </a>
            */ ?>
          <?php endif;?>

          <?php if (count($environment->domains) > 1): ?>
          <ul class="dropdown-menu pull-right" role="menu">
            <?php foreach ($environment->domains as $domain): ?>
            <li><a href="<?php print 'http://' . $domain; ?>" target="_blank"><?php print 'http://' . $domain; ?></a></li>
            <?php endforeach; ?>
            <li class="divider">&nbsp;</li>
            <li><?php print l(t('Edit Domains'), 'node/' . $node->nid . '/edit/' . $environment->name, array('query'=> drupal_get_destination())); ?></li>
          </ul>
          <?php endif; ?>

          <?php if ($environment->login_url): ?>
          <a href="<?php print $environment->login_url; ?>" class="btn btn-link pull-right"><?php print $environment->login_text; ?></a>
          <?php endif; ?>
        </div>
      </div>


      <div class="environment-deploy list-group-item">

        <!-- Deploy: Git Select -->
        <label><?php print t('Deploy'); ?></label>
        <div class="btn-group btn-toolbar" role="toolbar">
          <div class="btn-group btn-deploy-code" role="group">
            <button type="button" class="btn btn-default dropdown-toggle btn-git-ref" data-toggle="dropdown"><i class="fa fa-code"></i>
              <?php print t('Code'); ?>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu btn-git-ref" role="menu">
              <li><label><?php print t('Deploy branch or tag'); ?></label></li>
              <?php if (count($git_refs)): ?>
              <?php foreach ($git_refs as $ref => $item): ?>
                <li>
                  <?php print str_replace('ENV_NID', $environment->site, $item); ?>
                </li>
              <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </div>
          <div class="btn-group btn-deploy-database" role="group">

            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-database"></i>
              <?php print t('Data'); ?>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
              <?php if ($environment->settings->locked): ?>
                <li><label><?php print t('This environment is locked. You cannot deploy data here.'); ?></label></li>
              <?php elseif (count($target_environments) == 1): ?>
                <li><label><?php print t('No other environments available.'); ?></label></li>
              <?php else: ?>
                <li><label><?php print t('Deploy data from'); ?></label></li>
                <?php foreach ($source_environments as $source): ?>
                  <?php if ($source->name == $environment->name) continue; ?>
                  <li><a href="/node/<?php print $environment->site ?>/site_sync/?source=<?php print $source->site ?>&dest=<?php print $source->name ?>">
                    <?php if ($project->settings->live['live_environment'] == $source->name): ?>
                      <i class="fa fa-bolt deploy-db-indicator"></i>
                    <?php elseif ($source->settings->locked): ?>
                      <i class="fa fa-lock deploy-db-indicator"></i>
                    <?php endif; ?>

                      <strong class="btn-block"><?php print $source->name ?></strong>
                      <small><?php print $source->url; ?></small>
                    </a>
                  </li>
                <?php endforeach; ?>
                <li class="divider"></li>
                   <li><a href="/node/<?php print $environment->site ?>/site_sync/?source=other&dest=<?php print $source->name ?>">
                       <strong class="btn-block"><?php print t('Other...'); ?></strong>
                       <small><?php print t('Enter a drush alias to deploy from.'); ?></small>
                     </a>
                   </li>
              <?php endif; ?>
            </ul>
          </div>
          <div class="btn-group btn-deploy-servers" role="group">

            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i>
              <?php print t('Stack'); ?>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu devshop-stack" role="menu">
              <li><label><?php print t('Deploy Services'); ?></label></li>
              <?php foreach ($environment->servers as $type => $server):
                  // DB: Migrate Task
                  if ($type == 'db') {
                    $icon = 'database';
                    $url = "node/{$environment->site}/site_migrate";
                  }
                  // HTTP: Edit Platform
                  elseif ($type == 'http') {
                    $icon = 'cube';
                    $url = "node/{$environment->platform}/edit";
                  }
                  // SOLR: Edit Site
                  elseif ($type == 'solr') {
                    $icon = 'sun-o';
                    $url = "node/{$environment->project_nid}/edit/{$environment->name}";
                  }

                  // Build http query.
                  $query = array();
                  $query['destination'] = $_GET['q'];
                  $query['deploy'] = 'stack';

                  $full_url = url($url, array('query' => $query));

                  // @TODO: Not sure why nid is localhost here.
                  $server_url = $server['nid'] == 'localhost'?
                    'server_localhost':
                    url('node/' . $server['nid']);
                  ?>
                  <li class="inline">
                    <a href="<?php print $server_url; ?>" title="<?php print $type .': ' . $server['name']; ?>">
                      <strong class="btn-block"><i class="fa fa-<?php print $icon; ?>"></i> <?php print $type; ?></strong>
                      <small><?php print $server['name']; ?></small>
                    </a>
                    <?php if ($full_url) :?>
                    <a href="<?php print $full_url;?>" title="<?php print t('Change !type server...', array('!type' => $type)); ?>"><i class="fa fa-angle-right"></i></a>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
        <div class="list-group-item">

          <!-- Last Commit -->
          <a href="<?php print url("node/$environment->site/logs/commits"); ?>" class="last-commit">
            <?php print $environment->git_current; ?>
          </a>
        </div>

      <?php if ($environment->test): ?>
        <div class="environment-tests list-group-item list-group-item-<?php print $environment->test->status_class ?>">
          <label><?php print t('Tests'); ?></label>
          <div class="btn-group btn-toolbar" role="toolbar">
            <button type="button" class="btn" data-toggle="modal" data-target="#test-results-modal-<?php print $environment->name; ?>" title="<?php print t('View Results'); ?>">
              <?php print $environment->test->status_message ?>

              <small>
                <?php print $environment->test->duration ?>
                <br />
                <em>
                  <?php print $environment->test->ago ?>
                </em>
              </small>
            </button>

            <!--- TEST RESULTS MODAL -->
            <div class="modal fade" id="test-results-modal-<?php print $environment->name; ?>" tabindex="-1" role="dialog" aria-labelledby="test-results-modal-<?php print $environment->name; ?>" aria-hidden="true">
              <div class="modal-dialog modal-results modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="drush-alias-modal"><?php print t('Test Results'); ?></h4>
                  </div>
                  <div class="modal-body">
                    <?php print $environment->test->results; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="btn-group" role="group">

            <a href="<?php print $environment->test->run_tests_url; ?>" type="button" class="btn">
              <?php print t('Run Tests'); ?>
              <i class="fa fa-caret-right"></i>
            </a>
          </div>
        </div>
      <?php endif; ?>

      </div>
    </div>
<?php endforeach; ?>

  <div class="placeholder add-project-button col-xs-12 col-sm-6 col-md-4 col-lg-3">
    <a href="/node/<?php print $node->nid; ?>/project_devshop-create" class="btn btn-lg btn-success">
      <i class="fa fa-plus-square"></i><br />
      <?php print t('Create New Environment'); ?></a>
  </div>
</div>
