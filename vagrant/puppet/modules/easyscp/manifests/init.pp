# == Class: easyscp
#
# Install EasySCP and depending packages
#
class easyscp {
  exec { "load_src":
    command => "/usr/bin/git clone git://github.com/EasySCP/EasySCP.git /root/EasySCP",
    user    => root,
    require => [ Package["git"] ],
    creates => '/root/EasySCP'
  }

  exec { "update_src":
    command => "/usr/bin/git pull",
    cwd => "/root/EasySCP",
    onlyif => "/usr/bin/test -d /root/EasySCP"
  }

  file { 'easyscp-setup.sh':
    name    => '/root/EasySCP/easyscp-setup.sh',
    mode    => 0777,
    require => Exec['load_src'],
  }

  case $operatingsystem {
    debian, ubuntu: {
      exec { "debconf-${lsbdistcodename}-easyscp":
        command => "/usr/bin/debconf-set-selections puppet:///modules/easyscp/files/$lsbdistcodename/easyscp.preseed",
      }
      exec { "restart-${lsbdistcodename}-mysql":
        command => "/usr/sbin/service mysql restart",
        require => Exec[ "mysql-${lsbdistcodename}-bind" ],
      }
    }
    oraclelinux: {

    }
  }

  case $lsbdistcodename {
    wheezy: {
      $my_cnf = [ '/etc/mysql/my.cnf' ]
      exec { "easyscp-${lsbdistcodename}-pkgs":
        command  => "/usr/bin/debconf-set-selections puppet:///modules/easyscp/files/$lsbdistcodename/easyscp.preseed;
                     /usr/bin/apt-get -y install $(cat /root/EasySCP/docs/Debian/debian-packages-7)",
        require  => [ File[ 'easyscp-setup.sh' ], Exec[ "debconf-${lsbdistcodename}-easyscp" ] ],
        timeout  => -1;
      }
    }
    jessie: {
      $my_cnf = [ '/etc/mysql/my.cnf' ]
      exec { "easyscp-${lsbdistcodename}-pkgs":
        command  => "/usr/bin/debconf-set-selections puppet:///modules/easyscp/files/$lsbdistcodename/easyscp.preseed;
                     /usr/bin/apt-get -y install $(cat /root/EasySCP/docs/Debian/debian-packages-8)",
        require  => [ File[ 'easyscp-setup.sh' ], Exec[ "debconf-${lsbdistcodename}-easyscp" ] ],
        timeout  => -1;
      }
    }
    precise: {
      $my_cnf = [ '/etc/mysql/my.cnf' ]
      exec { "easyscp-${lsbdistcodename}-pkgs":
        command  => "/usr/bin/debconf-set-selections puppet:///modules/easyscp/files/$lsbdistcodename/easyscp.preseed;
                     /usr/bin/apt-get -y install $(cat /root/EasySCP/docs/Ubuntu/ubuntu-packages-1204)",
        require  => [ File[ 'easyscp-setup.sh' ], Exec[ "debconf-${lsbdistcodename}-easyscp" ] ],
        timeout  => -1;
      }
    }
    trusty: {
      $my_cnf = [ '/etc/mysql/my.cnf' ]
      exec { "easyscp-${lsbdistcodename}-pkgs":
        command  => "/usr/bin/debconf-set-selections puppet:///modules/easyscp/files/$lsbdistcodename/easyscp.preseed;
                     /usr/bin/apt-get -y install $(cat /root/EasySCP/docs/Ubuntu/ubuntu-packages-1404)",
        require  => [ File[ 'easyscp-setup.sh' ], Exec[ "debconf-${lsbdistcodename}-easyscp" ] ],
        timeout  => -1;
      }
      exec { "enable-mcrypt":
        command => "/usr/sbin/php5enmod mcrypt",
        before => Exec[ "setup" ],
        require => Exec[ "easyscp-${lsbdistcodename}-pkgs" ]

      }
    }
    xenial: {
      $my_cnf = [ '/etc/mysql/mysql.conf.d/mysqld.cnf' ]
      exec { "easyscp-${lsbdistcodename}-pkgs":
        command  => "/usr/bin/debconf-set-selections puppet:///modules/easyscp/files/$lsbdistcodename/easyscp.preseed;
                     /usr/bin/apt-get -y install $(cat /root/EasySCP/docs/Ubuntu/ubuntu-packages-1604)",
        require  => [ File[ 'easyscp-setup.sh' ], Exec[ "debconf-${lsbdistcodename}-easyscp" ] ],
        timeout  => -1;
      }
    }
  }
  exec { "mysql-${lsbdistcodename}-bind":
    command => "/bin/sed -i \"s/.*bind-address.*/bind-address = 0.0.0.0/\" ${my_cnf}",
    require => Exec["easyscp-${lsbdistcodename}-pkgs"]
  }
  exec { "mysql-password":
    unless => "mysqladmin -uroot -peasyscp status",
    path => "/bin:/usr/bin",
    command => "mysqladmin -uroot password easyscp",
    require =>  Exec[ "easyscp-${lsbdistcodename}-pkgs" ]
  }

  exec { "grant-remote-root":
    command => "/usr/bin/mysql -uroot -peasyscp -e \"grant all on *.* to 'root'@'%' identified by 'easyscp';\"",
    require => Exec["mysql-password"],
  }

  case $operatingsystem {
    centos: {
      exec { 'setup':
        command => "/usr/bin/printf '1\nn\n' | /root/EasySCP/easyscp-setup.sh",
        require => Exec[ "easyscp-${lsbdistcodename}-pkgs"],
      }
    }
    debian: {
      exec { 'setup':
        command => "/usr/bin/printf '2\nn\n' | /root/EasySCP/easyscp-setup.sh",
        require => Exec[ "easyscp-${lsbdistcodename}-pkgs"],
      }
    }
    suse: {
      exec { 'setup':
        command => "/usr/bin/printf '3\nn\n' | /root/EasySCP/easyscp-setup.sh",
        require => Exec[ "easyscp-${lsbdistcodename}-pkgs"],
      }
    }
    oracle: {
      exec { 'setup':
        command => "/usr/bin/printf '4\nn\n' | /root/EasySCP/easyscp-setup.sh",
        require => Exec[ "easyscp-${lsbdistcodename}-pkgs"],
      }
    }
    ubuntu: {
      exec { 'setup':
        command => "/usr/bin/printf '5\nn\n' | /root/EasySCP/easyscp-setup.sh",
        require => Exec[ "easyscp-${lsbdistcodename}-pkgs"],
      }
    }
  }
  exec { 'easyscp-debug':
    command => "/bin/sed -i \"s/.*DEBUG.*/DEBUG = 1/\" /etc/easyscp/easyscp.conf;
                /bin/sed -i \"s/.*<DEBUG>.*/<DEBUG>1<\\/DEBUG>/\" /etc/easyscp/EasySCP_Config.xml
               ",
    require => Exec[ 'setup' ]
  }
}