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
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Mmoreram\GearmanBundle\GearmanBundle(),
        );
    
    	// ...
    }

### Configuration

First configure Gaufrette adapter and filesystem ([Gaufrette configuration](https://github.com/KnpLabs/KnpGaufretteBundle#configuration)). For example:

	# app/config/config.yml	
	
	imports:
        // ...
        - { resource: @StorageBundle/Resources/config/gearman.yml }
	// ...
	
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

Finally add routing and security configuration (for JSON API):

	# app/config/routing.yml
	
	// ...
	
	storage:
	    resource: "@StorageBundle/Controller/"
	    type:     annotation
	    prefix:   itm-storage 

    # app/config/security.yml
    
    providers:
		// ...
		# Custom user provider for search user by "api_key" parameter in query string
		itm_storage_user_provider:
			id: itm.storage.api_key_user_provider
	
		// ...
    
	firewalls:
	    // ...
	    # Firewall for storage JSON API 
		itm_storage:
			pattern: ^/itm-storage
			stateless: true
			simple_preauth:
				authenticator: itm.storage.api_key_authenticator
			provider: itm_storage_user_provider
        
        // ...

Next update your doctrine schema and run Gearman worker:

    php app/console doctrine:schema:update --force
    nohup php app/console gearman:worker:execute ITMStorageBundleWorkersEventWorker -n &

## Usage

### Console commands

ITMStorageBundle add several console commands to Symfony application. For create user call:

	php app/console itm:storage:user-create

This command prints unique user token. 

For view all users use:

	php app/console itm:storage:user-list

For delete user run:

	php app/console itm:storage:user-delete <token>

Store file with attributes in the storage:

	php app/console itm:storage:document-store <filepath> [<attributes>]

Get document info by id:

	itm:storage:document-info <id>

Copy file to local path:

	itm:storage:document-get <id> <target-dir>

### JSON API methods

Document object:

    {
        'id': int,
		'name': string,
		'attributes': string,
		'created_at': timestamp,
    }

Store files:

	URL: /itm-storage/api/store
	Request: Array of files for store with any names
	Response: Document

Get document info:

	URL: /itm-storage/api/load
	Request: id: int - Document id
	Response: Document

Download file:

	URL: /itm-storage/api/get-content
	Request: id: int - Document id
	Response: File content for downloading
	
Register remote event listener:
 
    URL: /itm-storage/api/add-event-listener
    Request: 
        - callback_url: string
        - event: int
    Response: Event id
    Events:
        - 1: Add new document
    
Remove remote event listener:    

    URL: /itm-storage/api/remove-event-listener
    Request: id: int - Event id
    Response: null