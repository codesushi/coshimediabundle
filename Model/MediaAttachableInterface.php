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
     * getMediaLink
     *
     * returns instance of link class
     *
     * @access public
     * @return Coshi\MediaBundle\Model\MediaLinkInteface
     */
    public function getMediaLink();

}
