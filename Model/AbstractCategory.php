<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\CategoryBundle\Model;

/**
 * Class AbstractCategory
 *
 * @package Black\Bundle\CategoryBundle\Model
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
abstract class AbstractCategory implements CategoryInterface
{
    /**
     * @var
     */
    protected $path;

    /**
     * @var
     */
    protected $parent;

    /**
     * @var
     */
    protected $level;

    /**
     * @var
     */
    protected $children;

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param CategoryInterface $parent
     */
    public function setParent(CategoryInterface $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }
}
