# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  config.vm.box = "ubuntu12.04-givenchy-beauty"

  config.vm.hostname = "Lancaster"

  config.vm.network :forwarded_port, guest: 80, host: 8011
  config.vm.network :forwarded_port, guest: 3306, host: 33071

  config.ssh.username = "vagrant"
  config.ssh.password = "vagrant"

  config.vm.network :private_network, ip: "192.168.33.8"

  config.vm.synced_folder "./", "/vagrant", :nfs => true

  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--memory", "1024"]
    #vb.gui = true
  end

  #config.vm.provision :puppet do |puppet|
  #  puppet.module_path = "puppet/modules"
  #  puppet.manifests_path = "puppet/manifests"
  #  puppet.manifest_file  = "site.pp"
  #end

  #config.vm.provision :shell, :path => "var/init-env.sh"

end
