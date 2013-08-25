<?php
/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\CategoryBundle\Traits;

/**
 * Class CategoryDocumentTrait
 * @package Black\Bundle\CategoryBundle\Traits
 */
trait CategoryDocumentTrait {

    /**
     * @ODM\String
     * @Assert\Length(max="255")
     * @Assert\Type(type="string")
     * @Gedmo\TreePathSource
     */
    protected $name;

    /**
     * @ODM\String
     * @Assert\Length(max="255")
     * @Assert\Type(type="string")
     * @Gedmo\Slug(fields={"name"})
     */
    protected $slug;

    /**
     * @ODM\String
     * @Assert\Type(type="string")
     */
    protected $description;

    /**
     * @ODM\Field(type="string")
     * @Gedmo\TreePath(separator="|")
     */
    protected $path;

    /**
     * @ODM\ReferenceOne(targetDocument="Category")
     * @Gedmo\TreeParent
     */
    protected $parent;

    /**
     * @ODM\Field(type="int")
     * @Gedmo\TreeLevel
     */
    protected $level;

    /**
     * @ODM\Field(type="date")
     * @Gedmo\TreeLockTime
     */
    protected $lockTime;

    /**
     * @return mixed
     */
    public function getLockTime()
    {
        return $this->lockTime;
    }

}