;
; This makefile should be used by devmaster-install
; 

core = 6.x
api = 2

projects[drupal][type] = "core"

projects[devshop][type] = "profile"
projects[devshop][download][type] = "git"
projects[devshop][download][tag] = "6.x-1.0-alpha4"