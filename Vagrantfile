VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/trusty32"
  config.vm.network "forwarded_port", guest: 80, host: 1234
  config.vm.provision "shell", path: "provisioner.sh"
end
