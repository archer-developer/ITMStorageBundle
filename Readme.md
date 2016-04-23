# ITM Storage Bundle #

This Symfony bundle provides easy API to save files with attributes into storage (filesystem, cloud storages, ftp etc.). It requires [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle).

## Installation

### With composer

This bundle can be installed using [composer](https://getcomposer.org/). Add custom repository to composer.json:

    # ...
    "repositories": [{
        "type": "vcs",
        "url": "git@github.com:archer-developer/ITMStorageBundle.git"
    }],

Install bundle:

	php composer.phar require itm/storage-bundle
	
### Register the bundle

    <?php
    
    // app/AppKernel.php
    
    public function registerBundles()
    {
        $bundles = array(
    
        	// ...
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Knp\DoctrineBehaviors\Bundle\DoctrineBehaviorsBundle(),

            // If you will use remote client for storage (optional)
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Mmoreram\GearmanBundle\GearmanBundle(),

            // ...
            new ITM\StorageBundle\StorageBundle(),
        );
    
    	// ...
    }

### Configuration

First configure Gaufrette adapter and filesystem ([Gaufrette configuration](https://github.com/KnpLabs/KnpGaufretteBundle#configuration)). For example:

	# app/config/config.yml	
	
	imports:
        // ...
        // If you will use remote client for storage (optional)
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
	    # Gaufrette filesystem name (required)
	    filesystem: itm
<<<<<<< HEAD
	    
		# Remote storage servers
		servers:
			- {address: http://localhost:8001, api_key: token1}
			- {address: http://localhost:8002, api_key: token2}
			- {address: http://localhost:8003, api_key: token3}
	
		# Client address
		client_address: http://localhost:8000/app_dev.php
=======


>>>>>>> develop

Finally add routing and security configuration (If you will use storage JSON API):

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
			pattern: ^/itm-storage/api
			stateless: true
			simple_preauth:
				authenticator: itm.storage.api_key_authenticator
			provider: itm_storage_user_provider
        
        // ...

Next update your doctrine schema:

    php app/console doctrine:schema:update --force
    
And run Gearman worker on background:
    
    nohup php app/console gearman:worker:execute ITMStorageBundleWorkersEventWorker -n &

You can use Supervisord ([See documentation](https://github.com/supervisor/supervisor)) for start and reload Gearman worker.

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

	php app/console itm:storage:document-info <id>

Copy file to local path:

	php app/console itm:storage:document-get <id> <target-dir>
	
Register remote listeners (the storage bundle is using as client):

    php app/console itm:storage:client-subscribe

### Connecting listeners

Subscribe to local events:
  
    $dispatcher->addListener(DocumentEvents::ADD_DOCUMENT, function (AddDocumentEvent $event) {
        $document = $event->getDocument();
        // ... do process document
    });
    $dispatcher->addListener(DocumentEvents::DELETE_DOCUMENT, function (DeleteDocumentEvent $event) {});
    $dispatcher->addListener(DocumentEvents::RESTORE_DOCUMENT, function (RestoreDocumentEvent $event) {});
    
Subscribe to remote events:
    
    $dispatcher->addListener(DocumentEvents::REMOTE_ADD_DOCUMENT, function (RemoteDocumentEvent $event) {
		// Read storage document id
		$document_id = $event->getDocumentId();
		// Load document
		$client = $container->get('itm.storage.remote_client')->load($document_id);
		// ... do process document
	});
	$dispatcher->addListener(DocumentEvents::REMOTE_DELETE_DOCUMENT, function (RemoteDocumentEvent $event) {});
	$dispatcher->addListener(DocumentEvents::REMOTE_RESTORE_DOCUMENT, function (RemoteDocumentEvent $event) {});
	

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
	Request: 
	    - Array of files for store with any names
	    - attributes: string - JSON string
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
        - 2: Remove document
    
Remove remote event listener:    

    URL: /itm-storage/api/remove-event-listener
    Request: id: int - Event id
    Response: null
