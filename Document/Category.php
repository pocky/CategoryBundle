<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\CategoryBundle\Document;

use Black\Bundle\CategoryBundle\Model\AbstractCategory;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class Category
 *
 * @ODM\MappedSuperclass()
 * @Gedmo\Tree(type="materializedPath", activateLocking=true)
 *
 * @package Black\Bundle\CategoryBundle\Document
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
abstract class Category extends AbstractCategory
{
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
