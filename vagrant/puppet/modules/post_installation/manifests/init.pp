# == Class: easyscp
#
# Perform additional configuration tasks and package installations
#
class post_installation {
  case $operatingsystem {
    debian, ubuntu: {
      case $lsbdistcodename {
        xenial: {
          $post_pkgs = [ "php-xdebug" ]
          $xdebug_ini_path = [ "/etc/php/mods-available/xdebug.ini" ]
        }
        default: {
          $post_pkgs = [ "php5-xdebug" ]
          $xdebug_ini_path = [ '/etc/php5/mods-available/xdebug.ini' ]
        }

      }
      package { $post_pkgs:
        ensure	=> 'installed',
      }

      file { $xdebug_ini_path:
          owner => 'root',
          group => 'root',
          mode  => '0644',
          source => 'puppet:///modules/post_installation/xdebug.ini',
       }

#      exec { "enable_xdebug":
#        command => "/usr/sbin/php5enmod xdebug",
#        require => Package[ "php5-xdebug" ]
#      }
    }
  }
}
