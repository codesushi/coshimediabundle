CoshiMediaBundle
=============

MediaBundle - for symfony 2

Provides
---------

This bundle provides a service and base entity class.

Setup
------

1. Create your media bundle for example AcmeMediaBudle
2. In this bundle create Media entity which extends Coshi\MediaBundle\Entity\Media, add id column and mapping of your choice
3. Add CoshiMediaBundle configuration to your appliaction's config.yml see config reference. Note that you have to create media_path in your web_root and give them proper permissions
4. Add CoshiMediaBundle to your AppKernel.php
5. Update your database schema
6. Add desired entities which have attached media and implement 2 interfaces Coshi\MediaBundle\Model\MediaLinkInteface for desired entity and Coshi\MediaBundle\Model\MediaLinkInteface for Link class. Note if entity has only one media it has to implement both and getLinkObject have to return current object (return $this)

Config reference
----------------

coshi_media:
    media_class: Acme\MediaBundle\Entity\Media
    uploader:
        www_root: web
        media_path: media

