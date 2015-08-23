# ITM Storage Bundle #

This Symfony bundle is provides easy API to save files with attributes into storage (filesystem, cloud storages, ftp etc.). It requires [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle).

## Instalation

### With composer

This bundle can be installed using [composer](https://getcomposer.org/):

	php composer.phar require http://stash.itmclient.com/scm/sb/itmextensionsbundle.git
	
### Register the bundle

    <?php
    
    // app/AppKernel.php
    
    public function registerBundles()
    {
        $bundles = array(
    
        	// ...
    		new ITM\StorageBundle\StorageBundle(),
        	new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
			new Knp\DoctrineBehaviors\Bundle\DoctrineBehaviorsBundle(),
        );
    
    	// ...
    }

### Configuration

First configure Gaufrette adapter and filesystem ([Gaufrette configuration](https://github.com/KnpLabs/KnpGaufretteBundle#configuration)). For example:

	# app/config/config.yml	
	
	# Gaufrette Configuration
	knp_gaufrette:
    	adapters:
        	itm:
	            local:
	                directory: "%kernel.root_dir%/../web/uploads"
	    filesystems:
	        itm:
	            adapter:    itm

Then specify filesystem name for ITMStorageBundle:

	# app/config/config.yml	

	# ITMStorageBundle Configuration
	storage:
	    # Gaufrette filesystem name
	    filesystem: itm

Finally add routing configuration:

	# app/config/routing.yml
	
	storage:
	    resource: "@StorageBundle/Controller/"
	    type:     annotation
	    prefix:   itm-storage 

## Usage

### Console commands

ITMStorageBundle add several console commands to Symfony application. For create user call:

	php app/console itm:storage:user-create

This command prints unique user token. 

For view all users use:

	php app/console itm:storage:user-list

For delete user run:

	php app/console itm:storage:user-delete <token>

### Web API

Store files:

	/itm-storage/api/store

Get document info:

	/itm-storage/api/load

Download file:

	/itm-storage/api/get-content

