<?php

namespace Andy\Bundle\BugTrackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;


/**
 * Class Resolution
 *
 * @ORM\Entity()
 * @ORM\Table(name="bug_track_resolution")
 * @Config(
 *      routeName="resolution_index",
 *      defaultValues={
 *          "security"={
 *              "type"="ACL",
 *              "permissions"="All",
 *              "group_name"=""
 *          }
 *      }
 * )
 */
class Resolution
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", unique=true)
     * @ConfigField(
     *  defaultValues={
     *      "dataaudit"={"auditable"=true},
     *      "importexport"={
     *          "identity"=true,
     *          "order"=10
     *      }
     *  }
     * )
     */
    protected $name;


    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getName();
    }
}
