core = 6.x
api = 2

projects[drupal][type] = "core"

; DevShop modules
projects[devshop_hosting][subdir] = devshop
projects[devshop_hosting][download][type] = git
projects[devshop_hosting][download][branch] = 6.x-2.x

; Contrib modules
projects[adminrole][subdir] = contrib
projects[ctools][subdir] = contrib
projects[jquery_update][subdir] = contrib

; Aegir Contrib
projects[hosting_filemanager][subdir] = contrib
projects[hosting_tasks_extra][subdir] = contrib
projects[hosting_queue_runner][subdir] = contrib

; Aegir Contrib maintained by devshop maintainers
projects[hosting_solr][subdir] = contrib
projects[hosting_solr][download][type] = git
projects[hosting_solr][download][branch] = 6.x-1.x

projects[hosting_drush_aliases][subdir] = contrib
projects[hosting_drush_aliases][download][type] = git
projects[hosting_drush_aliases][download][branch] = 6.x-1.x

projects[hosting_logs][subdir] = contrib
projects[hosting_logs][download][type] = git
projects[hosting_logs][download][branch] = 6.x-1.x

; Aegir Hostmaster modules
; This includes all hostmaster contrib and libraries!
projects[hostmaster][type] = profile
projects[hostmaster][version] = 2.x
