CoshiMediaBundle
=============

MediaBundle - for Symfony 2

Provides
---------

This bundle provides a service and base entity class.
Events are dispached after each media manager service action.


Setup
------

0. Create your media bundle for example AcmeMediaBudle - [optional step - only creation of entity is required]
1. Create Media entity which extends Coshi\MediaBundle\Entity\Media, add id column and mapping of your choice [ if bundle had been created create entity in that bundle ]
2. Add CoshiMediaBundle configuration to your appliaction's config.yml see config reference. Note that you have to create media_path in your web_root and give them proper permissions
3. Add CoshiMediaBundle to your AppKernel.php
4. Update your database schema
5. Add desired entities which have attached media and implement interfaces Coshi\MediaBundle\Model\MediaInterface for desired entity 
6. Now you can use coshi_media.media_manager service to perform file upload as simple as 

        $this->get('coshi_media.media_manager')->create($uploadedFileObject);
        $this->getDoctrine()->getManager()->flush();

Minimal configuration for storing files on local filesystem
-----------------------------------------------------------

    coshi_media:
        media_class: AppBundle\Entity\Media

        #Adapters definiton
        adapters:
            local:
                directory: media
                create: true

        filesystems:
            local:
                adapter: local

        filesystem_default: local

Currentluy only Amazon S3 and Local filesystem are supported.

Configuration reference
----------------

    coshi_media:
        media_class: Webfit\MO\MediaBundle\Entity\Media

        #Adapters definiton
        adapters:
            local:
                directory: media_upload_delete_work 
                create: true
            aws_s3:
                service_id: aws_s3.client #name of amazon service
                bucket_name: test-mediabundle
                options:
                    directory: media_bundle_adapter_upload
        
        #Filesystems/storage definition
        filesystems:
            local:
                adapter: local
            amazon:
                adapter: aws_s3
        
        #Optional parameter
        filesystem_default: amazon

Twig Extension
--------------

This bunlde provides simple extension to render a path to media in Twig
    
       {{ coshi_media_url(media) }}

Where of course media is a instance of mapped media class 

Events
------

Bundle generates couple events they are defined in Coshi\MediaBundle\MediaEvents
Events occurs on create of medium, update and delete. 
This is the way to extend bundle functionality.
        
        MediaEvents::CREATE_MEDIA
        MediaEvents::UPDATE_MEDIA
        MediaEvents::DELETE_MEDIA

