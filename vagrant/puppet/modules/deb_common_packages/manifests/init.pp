# == Class: deb_common_packages
#
# Perform system upgrade and install common packages for all deb based distributions
#
class deb_common_packages {
  exec { 'update':
    command => '/usr/bin/apt-get update';
  }

  case $lsbdistcodename {
    jessie: {
      exec { 'debconf-grub':
        command => '/usr/bin/debconf-set-selections puppet:///modules/deb_common_packages/files/jessie/grub.preseed',
        before => Exec[ "upgrade" ],
        require => Exec['update'];
      }
    }
  }

  exec { 'upgrade':
    command => '/usr/bin/apt-get -y dist-upgrade',
    timeout	=> -1;
  }

  $deb_pkgs = [ "lsb-release", "git", "debconf-utils", "facter" ]
  package { $deb_pkgs:
    ensure	=> 'installed',
    require => Exec['upgrade'];
  }
}