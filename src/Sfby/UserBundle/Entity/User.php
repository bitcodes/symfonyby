<?php

namespace Sfby\UserBundle\Entity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sfby\BlogBundle\Entity\Blog;
use FOS\UserBundle\Model\GroupInterface;
/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{

    /**
     * @var int $id
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank(groups={"Registration", "Profile"}, message="profile.error.name_is_blank")
     * @Assert\MinLength(limit=2, groups={"Registration", "Profile"}, message="profile.error.name_short")
     * @Assert\MaxLength(limit=255, groups={"Registration", "Profile"}, message="profile.error.name_long")
     */
    protected $name;

    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    protected $image;
    
    /**
     * @var string $old_image
     *
     */
    protected $old_image;
    
    /**
     * @var UploadedFile $file
     * 
     * @Assert\Image(
     *     groups={"Profile"}, 
     *     maxSize = "100k", 
     *     mimeTypesMessage = "profile.error.image_type",
     *     maxSizeMessage = "profile.error.image_size"
     * )
     */
    protected $file;

    /**
     * @var string $about
     *
     * @ORM\Column(name="about", type="text", nullable=true)
     */
    protected $about;

    /**
     * @var string $facebookID
     *
     * @ORM\Column(name="facebook_id", type="string", length=50, nullable=true)
     */
    protected $facebookId;
    
    /**
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="users")
     * @ORM\JoinTable(name="sfby_user2group")
     */
    protected $groups;
    /**
     * 
     * @ORM\OneToMany(targetEntity="Sfby\BlogBundle\Entity\Blog", mappedBy="user", cascade={"all"})
     */
    protected $blogs;

    public function __construct()
    {
        parent::__construct();
        $this->blogs = new ArrayCollection();
        $this->groups = new ArrayCollection();
        // your own logic
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function preUpdate()
    {
        if (null === $this->file) return;
        $this->file->move($this->getAbsoluteUploadDir(), $this->image);
        $this->file = null;
        if ($this->old_image && $file = $this->getAbsolutePath($this->old_image)) 
        {
            @unlink($file);
        }
        $this->old_image = null;
    }
    /**
     * @ORM\PostRemove()
     */
    public function postRemove()
    {
        if ($file = $this->getAbsolutePath()) 
        {
            unlink($file);
        }
    }
    
    public function getAbsolutePath($filename = null)
    {
        if(!$filename) $filename = $this->image;
        return null === $filename ? null : $this->getAbsoluteUploadDir().'/'.$filename;
    }

    public function getWebPath()
    {
        return null === $this->image ? null : $this->getUploadDir().'/'.$this->image;
    }

    protected function getAbsoluteUploadDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'uploads/users';
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set image
     *
     * @param string $image
     */
    public function setImage($image)
    {
        $this->old_image = $this->image;
        $this->image = $image;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * Get file
     *
     * @return UploadedFile 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file
     *
     * @param UploadedFile $file
     */
    public function setFile($file)
    {
        $this->setImage(uniqid().'.'.$file->guessExtension());
        $this->file = $file;
    }

    /**
     * Set about
     *
     * @param text $about
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }

    /**
     * Get about
     *
     * @return text 
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Add blogs
     *
     * @param Sfby\BlogBundle\Entity\Blog $blogs
     */
    public function addBlog(\Sfby\BlogBundle\Entity\Blog $blogs)
    {
        $this->blogs[] = $blogs;
    }

    /**
     * Get blogs
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getBlogs()
    {
        return $this->blogs;
    }

    /**
     * Add groups
     *
     * @param Sfby\UserBundle\Entity\Group $groups
     */
    public function addGroup(GroupInterface $groups)
    {
        parent::addGroup($groups);
    }

    /**
     * Get groups
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }
    
    /**
     * @param Array
     */
    public function setFBData($fbdata)
    {
        if (isset($fbdata['id'])) 
        {
            $this->setFacebookID($fbdata['id']);
            $this->addRole('ROLE_FACEBOOK');
        }
        $name = array();
        if (isset($fbdata['name'])) 
        {
            $name[] = $fbdata['name'];
        }
        else
        {
            if (isset($fbdata['first_name'])) 
            {
                $name[] = $fbdata['first_name'];
            }
            if (isset($fbdata['last_name'])) 
            {
                $name[] = $fbdata['last_name'];
            }
        }
        $this->setName(join(' ', $name));
        
        if (isset($fbdata['email'])) 
        {
            $this->setEmail($fbdata['email']);
        }
    }
    
    public function serialize()
    {
        return serialize(array($this->facebookId, parent::serialize()));
    }

    public function unserialize($data)
    {
        list($this->facebookId, $parentData) = unserialize($data);
        parent::unserialize($parentData);
    }



    /**
     * Set facebookID
     *
     * @param string $facebookID
     */
    public function setFacebookID($facebookID)
    {
        $this->facebookId = $facebookID;
        $this->setUsername('fb_'.$facebookID);
    }

    /**
     * Get facebookID
     *
     * @return string 
     */
    public function getFacebookID()
    {
        return $this->facebookId;
    }
}