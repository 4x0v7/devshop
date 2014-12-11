
<ul class="nav nav-pills project-stuff">
  <!-- Drush Aliases -->
  <li class="dropdown">
    <a href="#" class="dropdown-toggle drush-aliases" data-toggle="dropdown" title="<?php print t('Drush Aliases'); ?>">
        <i class="fa fa-drupal"></i>
      </span>
      <span class="caret"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
      Save to <pre class="inline">~/.drush/<?php print $project->name; ?>.aliases.drushrc.php</pre>
      <textarea cols="40" rows="10" class='form-control' onlick="this.select()">
<?php print $drush_aliases; ?>
      </textarea>
    </div>
  </li>
</ul>

<nav class="navbar navbar-default navbar-project" role="navigation">
  <div class="container-fluid">
    <!-- First Links -->
    <div class="nav navbar-text main-project-nav">
      <ul class="nav nav-pills">

        <!-- Dashboard -->
        <li><?php print $dashboard_link; ?></li>

        <!-- Settings -->
        <?php if ($settings_link): ?>
        <li><?php print $settings_link; ?></li>
        <?php endif; ?>

        <!-- Logs-->
        <?php if ($logs_link): ?>
          <li><?php print $logs_link; ?></li>
        <?php endif; ?>

      </ul>
    </div>

    <!-- Git Info -->
    <div class="navbar-form navbar-right form-group">
      <div class="input-group">

        <!-- Link to github or an icon -->
        <?php if (isset($github_url)): ?>
          <a class="input-group-addon" href="<?php print $github_url; ?>" title="<?php print t('View on GitHub'); ?>" target="_blank"><i class="fa fa-github-alt"></i></a>
        <?php else: ?>
          <div class="input-group-addon"><i class="fa fa-git"></i></div>
        <?php endif; ?>


        <!-- Git URL -->
        <input type="text" class="form-control" size="26" value="<?php print $node->project->git_url; ?>" onclick="this.select()">

        <!-- Branch & Tag List -->
        <div class="input-group-btn">
          <button type="button" class="btn btn-default dropdown-toggle <?php print $branches_class ?>" data-toggle="dropdown" title="<?php print $branches_label; ?>">

            <?php if ($branches_show_label): ?>
              <i class="fa fa-<?php print $branches_icon; ?>"></i>
              <?php print $branches_label; ?>
            <?php else: ?>
              <small>
                <i class="fa fa-code-fork"></i> <?php print $branches_count; ?>
              </small>

              &nbsp;
              <?php if ($tags_count): ?>
                <small>
                  <i class="fa fa-tag"></i> <?php print $tags_count; ?>
                </small>
              <?php endif; ?>

            <?php endif; // branches_show label ?>

            <span class="caret"></span></button>
          <ul class="dropdown-menu dropdown-menu-right" role="menu">
            <?php foreach ($branches_items as $item): ?>
              <li><?php print $item; ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  </div>
</nav>
