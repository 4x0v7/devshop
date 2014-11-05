#!/bin/bash
#
#  DevShop Standalone Install Script
#  =================================
#
#  Install DevShop with Ansible.
#
#  To clone devshop playbook from source:
#
#    $ sudo ./install.sh
#
#  To use a local playbook: (use the directory path, do not include playbook.yml)
#
#    $ sudo ./install.sh /path/to/playbook
#
#  For example, if using vagrant:
#
#    $ sudo ./install.sh /vagrant/installers/ansible
#
#  If using a playbook path option, the makefile used to build devmaster is defined
#  in the vars.yml file: devshop_makefile.
#
echo "============================================="
echo " Welcome to the DevShop Standalone Installer "
echo "============================================="


if [ -f '/etc/os-release' ]; then
    . /etc/os-release
    OS=$ID
    VERSION="$VERSION_ID"
    HOSTNAME_FQDN=`hostname --fqdn`

elif [ -f '/etc/lsb-release' ]; then
    . /etc/lsb-release
    OS=$DISTRIB_ID
    VERSION="$DISTRIB_RELEASE"
    HOSTNAME_FQDN=`hostname --fqdn`
fi

LINE=---------------------------------------------

echo " OS: $OS"
echo " Version: $VERSION"
echo " Hostname: $HOSTNAME_FQDN"
echo $LINE

# Detect playbook path option
if [ $1 ]; then
    PLAYBOOK_PATH=$1
    echo " Using playbook $1/playbook.yml "
    echo $LINE
else
    PLAYBOOK_PATH=/tmp/devshop-install
fi

# Fail if not running as root (sudo)
if [ $EUID -ne 0 ]; then
    echo "This script must be run as root.  Try 'sudo ./install.sh'." 1>&2
    exit 1
fi

# If ansible command is not available, install it.
if [ ! `which ansible` ]; then
    echo " Installing Ansible..."

    if [ $OS == 'ubuntu' ] || [ $OS == 'debian' ]; then

        # Detect ubuntu version and switch package.
        if [ $VERSION == '12.04' ]; then
            PACKAGE=python-software-properties
        else
            PACKAGE=software-properties-common
        fi

        apt-get install git -y
        apt-get install $PACKAGE -y
        apt-add-repository ppa:ansible/ansible -y
        apt-get update
        apt-get install ansible -y

    elif [ $OS == 'centos' ] || [ $OS == 'redhat' ] || [ $OS == 'fedora'  ]; then

        yum install git -y
        yum install epel-release -y
        yum install ansible -y
    fi

    echo $LINE

else
    echo " Ansible already installed. Skipping installation."
    echo $LINE
fi

# Generate MySQL Password
if [ "$TRAVIS" == "true" ]; then
  echo "TRAVIS DETECTED! Setting 'root' user password."
  MYSQL_ROOT_PASSWORD=''
  echo $MYSQL_ROOT_PASSWORD > /tmp/mysql_root_password
fi

if [ -f '/tmp/mysql_root_password' ]
then
  MYSQL_ROOT_PASSWORD=$(cat /tmp/mysql_root_password)
  echo "Password found, using $MYSQL_ROOT_PASSWORD"
else
  MYSQL_ROOT_PASSWORD=$(< /dev/urandom tr -dc _A-Z-a-z-0-9 | head -c${2:-32};echo;)
  echo "Generating new MySQL root password... $MYSQL_ROOT_PASSWORD"
  echo $MYSQL_ROOT_PASSWORD > /tmp/mysql_root_password
fi

echo $LINE
echo " Hostname: $HOSTNAME_FQDN"
echo " MySQL Root Password: $MYSQL_ROOT_PASSWORD"
echo $LINE

# Clone the installer code if a playbook path was not set.
if [ ! -f "$PLAYBOOK_PATH/playbook.yml" ]; then
  git clone http://git.drupal.org/project/devshop.git $PLAYBOOK_PATH
  PLAYBOOK_PATH=/tmp/devshop-install/installers/ansible
  MAKEFILE_PATH=/tmp/devshop-install/build-devshop.make
  echo $LINE

fi

cd $PLAYBOOK_PATH

# Create inventory file
echo $HOSTNAME_FQDN > inventory

# If ansible playbook fails syntax check, report it and exit.
if [[ ! `ansible-playbook -i inventory --syntax-check playbook.yml` ]]; then
    echo " Ansible syntax check failed! Check installers/ansible/playbook.yml and try again."
    exit 1
fi

# Run the playbook.
echo " Installing with Ansible..."
echo $LINE

ANSIBLE_EXTRA_VARS="server_hostname=$HOSTNAME_FQDN mysql_root_password=$MYSQL_ROOT_PASSWORD devshop_makefile=$MAKEFILE_PATH"

if [ $MAKEFILE_PATH ]; then
  ANSIBLE_EXTRA_VARS="$ANSIBLE_EXTRA_VARS devshop_makefile=$MAKEFILE_PATH"
fi

ansible-playbook -i inventory playbook.yml --connection=local --sudo --extra-vars "$ANSIBLE_EXTRA_VARS"

# DevShop Installed!
if [  ! -f '/var/aegir/.drush/hostmaster.alias.drushrc.php' ]; then

  echo "╔═════════════════════════════════════════════════════════════════════╗"
  echo "║ It appears something failed during installation.                    ║"
  echo "║ There is no '/var/aegir/.drush/hostmaster.alias.drushrc.php' file.  ║"
  echo "╚═════════════════════════════════════════════════════════════════════╝"
else

  echo "╔═══════════════════════════════════════════════════════════════╗"
  echo "║           ____  Welcome to  ____  _                           ║"
  echo "║          |  _ \  _____   __/ ___|| |__   ___  _ __            ║"
  echo "║          | | | |/ _ \ \ / /\___ \| '_ \ / _ \| '_ \           ║"
  echo "║          | |_| |  __/\ V /  ___) | | | | (_) | |_) |          ║"
  echo "║          |____/ \___| \_/  |____/|_| |_|\___/| .__/           ║"
  echo "║                                              |_|              ║"
  echo "╟───────────────────────────────────────────────────────────────╢"
  echo "║ Submit any issues to                                           ║"
  echo "║ http://drupal.org/node/add/project-issue/devshop              ║"
  echo "╟───────────────────────────────────────────────────────────────╢"
  echo "║ NOTES                                                         ║"
  echo "║ Your MySQL root password was set as a long secure string.     ║"
  echo "║   You shouldn't need it again.                                ║"
  echo "║                                                               ║"
  echo "║ An SSH keypair has been created in /var/aegir/.ssh            ║"
  echo "║                                                               ║"
  echo "║ Supervisor is running Hosting Queue Runner.                   ║"
  echo "╠═══════════════════════════════════════════════════════════════╣"
  echo "║ You can use this link to login:                               ║"
  echo "╚═══════════════════════════════════════════════════════════════╝"
  sudo su - aegir -c "drush @hostmaster uli"
fi
