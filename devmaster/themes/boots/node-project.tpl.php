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
<div id="project-info" class="col-md-12">
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
    <li>
    <li>
      <strong><?php print t('Deploy'); ?></strong>
      <small>
        <?php if ($project->settings->deploy['method'] == 'webhook'): ?>
          <?php if (empty($project->settings->deploy['last_webhook_time'])): ?>

            <span><?php print t('Webhook not received'); ?></span>
            <a type="button" class="btn btn-xs btn-warning" data-toggle="popover" title="<?php print t('Webhook not received'); ?>" href="<?php print $add_webhook_url?>"><i class="fa fa-<?php print $add_webhook_icon?>"></i> Add a Webhook</a> to <input value="<?php print  $project->webhook_url; ?>"></a>
          <?php endif; ?>
        <?php endif; ?>
      </small>
    </li>
  </ul>
</div>

<!-- ENVIRONMENTS-->
<div class="row placeholders col-md-12">
<?php foreach ($node->project->environments as $environment_name => $environment): ?>

  <?php
  if ($environment->site_status == HOSTING_SITE_DISABLED){
    $environment_class = 'disabled';
    $list_item_class = 'disabled';
  }
  elseif ($environment->name == $project->settings->live['live_environment']){
    $environment_class = ' live-environment';
    $list_item_class = 'info active';
  }
  else {
    $environment_class = '';
    $list_item_class = 'info';
  }

  // Active?
  if ($environment->active_tasks > 0) {
    $environment_class .= ' active';
    $list_item_class = 'warning';
  }
  ?>

  <div class="environment-wrapper col-xs-12 col-sm-6 col-md-4 col-lg-3">

    <div class="list-group devshop-environment <?php print $environment_class ?>">
      <div class="environment-header list-group-item list-group-item-<?php print $list_item_class ?>">

        <!-- Environment Status Indicators -->
        <div class="environment-indicators pull-right">
          <?php if ($environment->settings->locked): ?>
            <i class="fa fa-lock" title="Locked"></i>
          <?php endif; ?>

          <?php if ($environment->name == $project->settings->live['live_environment']): ?>
            <i class="fa fa-bolt" title="Live Environment"></i>
          <?php endif; ?>
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

        <div class="progress">
          <div class="progress-bar <?php print $environment->progress_classes ?>"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
            <span class="sr-only">In Progress</span>
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
            <a type="button" class="btn btn-xs pull-right" href="<?php print url('node/' . $node->nid . '/edit/' . $environment->name, array('query'=> drupal_get_destination())); ?>" title="<?php print t("Add Domain"); ?>">
              <i class="fa fa-plus"></i>
            </a>
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
        </div>
      </div>


      <!-- Last  -->
      <div class="list-group-item">
        <!-- Settings -->
        <a href="<?php print url('node/' . $node->nid . '/edit/' . $environment->name, array('query'=> drupal_get_destination())); ?>" class="btn btn-default pull-right settings">
          <i class="fa fa-sliders" ?></i> Settings
        </a>

        <a href="<?php print url("node/$environment->site/logs/commits"); ?>" class="last-commit">
          <?php print $environment->git_current; ?>
        </a>
      </div>

      <div class="environment-tasks list-group-item btn-group btn-group-justified">

        <!-- Deploy: Git Select -->
        <div class="btn-group btn-git">
          <button type="button" class="btn btn-default dropdown-toggle btn-git-ref" data-toggle="dropdown"><i class="fa fa-code"></i>

            <?php print t('Deploy'); ?>

            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu btn-git-ref" role="menu">
            <li><p class="text-muted"><?php print $deploy_label; ?></p></li>

            <?php if (count($git_refs)): ?>
            <li class="divider"></li>

            <?php foreach ($git_refs as $ref => $item): ?>
              <?php if ($ref == $environment->git_ref) continue; ?>
              <li>
                <?php print str_replace('ENV_NID', $environment->site, $item); ?>
              </li>
            <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>

        <!-- Servers -->
        <div class="btn-group btn-tasks">
          <button type="button" class="btn btn-default dropdown-toggle btn-git-ref" data-toggle="dropdown">
            <i class="fa fa-cube" ></i>
            <?php print t('Servers'); ?>
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <li>
              <i class="fa fa-database" ?></i> <strong><?php print t('Database'); ?></strong></database>
              <a href="<?php print url('node/' . $environment->servers['db']['nid']); ?>">
                <?php print $environment->servers['db']['name']; ; ?>
              </a>
            </li>

            <?php if (count($db_servers) > 1): ?>
              <li class="divider"></li>
              <li>
                <p class="bg-warning btn-text"><?php print t('Move database to:'); ?></p>
              </li>
              <?php foreach ($db_servers as $server):
                if ($environment->db_server == $server) continue;
                ?>
                <li>
                  <a href="/node/<?php print $environment->site ?>/site_migrate/?db_server=<?php print $server ?>">
                    <i class="fa fa-database"></i>
                    <?php print $server ?>
                  </a>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>

            <li class="divider"></li>

            <li>
              <i class="fa fa-cube" ?></i> <strong><?php print t('Web'); ?></strong>
              <a href="<?php print url('node/' . $environment->servers['db']['nid']); ?>">
                <?php print $environment->servers['http']['name']; ; ?>
              </a>
            </li>
            <?php if (count($web_servers) > 1): ?>
              <li class="divider"></li>
              <li>
                <p class="bg-warning btn-text"><?php print t('Move database to:'); ?></p>
              </li>
              <?php foreach ($web_servers as $server):
                if ($environment->http_server == $server) continue;
                ?>
                <li>
                  <a href="/node/<?php print $environment->platform ?>/edit/?http_server=<?php print $server ?>">
                    <i class="fa fa-cube"></i>
                    <?php print $server ?>
                  </a>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>


        <!-- Tasks -->
        <div class="btn-group btn-tasks">
          <button type="button" class="btn btn-default dropdown-toggle btn-git-ref" data-toggle="dropdown">
            <i class="fa fa-tasks" ></i>
            <?php print t('Tasks'); ?>
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <?php foreach ($node->environment_actions[$environment->name] as $link): ?>
              <li>
                <a href="<?php print $link['url'] ?>"><?php print $link['title']; ?></a>
              </li>
            <?php endforeach; ?>
            <li class="divider"></li>
            <li class="text-muted"><?php print t('Sync Data:'); ?></li>

            <?php foreach ($project->environments as $env): ?>
              <?php if ($env->settings->locked || $env->name == $environment->name) continue; ?>
              <li><a href="/node/<?php print $node->nid ?>/project_devshop-sync/?source=<?php print $environment->name ?>&dest=<?php print $env->name ?>"><?php print t('Copy data to') . ' ' . $env->name; ?></a></li>
            <?php endforeach; ?>



          </ul>
        </div>

      </div>
      <!-- Tasks -->
      <div class="tasks-button">
        <?php print $environment->tasks_list; ?>
      </div>
    </div>
  </div>
<?php endforeach; ?>

  <div class="placeholder add-project-button col-xs-12 col-sm-6 col-md-4 col-lg-3">
    <a href="/node/<?php print $node->nid; ?>/project_devshop-create" class="btn btn-lg btn-success">
      <i class="fa fa-plus-square"></i><br />
      <?php print t('Create New Environment'); ?></a>
  </div>
</div>
