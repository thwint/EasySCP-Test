# == Class: common_config
#
# Perform initial configuration tasks for all Linux based Vagrant boxes
#
class common_config {
#  file {
#    '/home/vagrant/.ssh/authorized_keys':
#      owner => 'vagrant',
#      group => 'vagrant',
#      mode  => '0644',
#      source => 'puppet:///modules/common_config/authorized_keys';
#  }

  file { '/root/.ssh':
    ensure => 'directory',
    owner  => 'root',
    group  => 'root',
    mode   => '0700',
  }

  file {
    '/root/.ssh/authorized_keys':
      owner => 'root',
      group => 'root',
      mode  => '0644',
      source => 'puppet:///modules/common_config/authorized_keys',
      require => File['/root/.ssh'];
  }

}