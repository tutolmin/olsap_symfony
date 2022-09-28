# Valuable additions

## Bridge ###
Bridge should be able to route DHCP requests out to pi. Docker rules disallow it.
```
#cat /etc/NetworkManager/dispatcher.d/99-bridge
#!/bin/sh

INTERFACE=$1
ACTION=$2

if [ "$INTERFACE" = "nm-bridge" ]; then
  if [ "$ACTION" = "up" ]; then
    iptables -A FORWARD -i nm-bridge -j ACCEPT
  fi
fi
```
## Populate ENVIRONMENTS ##
```
docker-compose exec php bin/console app:instances:create cricket bionic 20

for i in 2 5 6 7 8; do for j in `seq 3`; do docker-compose exec php bin/console app:environments:create $i; done;done
```

## LXD auth ###
Generate client key and cert in order to communicate with LXD
```
lxc_cert.php
```
Complete instruction: https://stgraber.org/2016/04/18/lxd-api-direct-interaction/

## AWX
Start the AWX containers
```
/root/.awx/awxcompose
docker-compose up -d
```
CLI execution
```
awx -k --conf.host=http://localhost:8080/ projects list
```
Enter AWX container and install
```
ansible-galaxy collection install community.general
ansible-inventory -i lxd.yml --graph
```
where lxd.yml contains
```
plugin: community.general.lxd
#url: unix:/var/snap/lxd/common/lxd/unix.socket
url: https://172.27.72.4:8443/
#trust_password: "mypass"
#state: RUNNING
groupby:
  osUbuntu:
    type: os
    attribute: ubuntu
```
... and /etc/ansible/ansible.cfg contains 
```
[inventory]
enable_plugins = auto
```
... and ~/.config/lxc/ contains client.crt and client.key generated earlier

## Start the containers
```
SERVER_NAME=:80  docker-compose up -d
```

## Container limits
```
lxc profile set cricket limits.memory 256MB
lxc profile set cricket limits.memory.swap false
lxc profile device set cricket root size 1GB
lxc profile set cricket limits.cpu.allowance 10%
```

## Git bunch update
```
for i in `git branch`; do git checkout $i; git checkout set_immune .gitignore; git add .; git commit -m ".gitignore";git push;done
```

## SystemD messenger consumer
```
[Unit]
#Description=Symfony messenger-consume %i
Description=Symfony messenger-consume

[Service]
#ExecStart=php /path/to/your/app/bin/console messenger:consume async --time-limit=3600
ExecStart=/usr/local/bin/docker-compose exec -T php bin/console messenger:consume async --time-limit=3600 --limit=3
Restart=always
WorkingDirectory=/root/olsap
RestartSec=3

[Install]
WantedBy=default.target
```

## PHP LXC lib
https://github.com/ashleyhood/php-lxd/blob/master/docs/configuration.md

## Combo indexes
`CREATE UNIQUE INDEX instance_types_hw_profile_id_os_id_combo ON instance_types(hw_profile_id, os_id);`

## To string convertion
```
    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->name;
    }
```
# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework, with full [HTTP/2](https://symfony.com/doc/current/weblink.html), HTTP/3 and HTTPS support.

![CI](https://github.com/dunglas/symfony-docker/workflows/CI/badge.svg)

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/)
2. Run `docker-compose build --pull --no-cache` to build fresh images
3. Run `docker-compose up` (the logs will be displayed in the current shell)
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker-compose down --remove-orphans` to stop the Docker containers.

## Features

* Production, development and CI ready
* Automatic HTTPS (in dev and in prod!)
* HTTP/2, HTTP/3 and [Preload](https://symfony.com/doc/current/web_link.html) support
* Built-in [Mercure](https://symfony.com/doc/current/mercure.html) hub
* [Vulcain](https://vulcain.rocks) support
* Just 2 services (PHP FPM and Caddy server)
* Super-readable configuration

**Enjoy!**

## Docs

1. [Build options](docs/build.md)
2. [Using Symfony Docker with an existing project](docs/existing-project.md)
3. [Support for extra services](docs/extra-services.md)
4. [Deploying in production](docs/production.md)
5. [Installing Xdebug](docs/xdebug.md)
6. [Using a Makefile](docs/makefile.md)
7. [Troubleshooting](docs/troubleshooting.md)

## Credits

Created by [Kévin Dunglas](https://dunglas.fr), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
