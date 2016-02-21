# create a new run stage to ensure certain modules are included first
stage { 'pre':
  before => Stage['main']
}
stage { 'post': }
Stage['main'] -> Stage['post']

class { 'deb_common_packages':
  stage => pre,
}
class { 'post_installation':
  stage => post,
}

include common_config, deb_common_packages, easyscp, post_installation