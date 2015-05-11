<?php
namespace Coshi\MediaBundle\Model;

/**
 * MediaAttachableInterface
 *
 * @author Krzysztof Ozog <krzysztof.ozog@codesushi.co>
 */
interface MediaAttachableInterface
{
    /**
     * returns instance of link class
     *
     * @access public
     * @return MediaLinkInterface
     */
    public function getMediaLink();
}
