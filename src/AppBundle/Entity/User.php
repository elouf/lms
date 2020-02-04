<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"oneUser"})
     */
    protected $id;


    /**
     * @var string
     *
     * @ORM\Column(name="$firstname", type="string", length=255)
     * @Serializer\Groups({"oneUser"})
     */
    protected $firstname;

    /**
     * @var string
     *
     * @Serializer\Groups({"oneUser"})
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     * @Serializer\Groups({"oneUser"})
     */
    protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="nomEtabl", type="string", length=1024, nullable = true)
     */
    protected $nomEtabl;

    /**
     * @var string
     *
     * @ORM\Column(name="commune", type="string", length=255, nullable = true)
     */
    protected $commune;

    /**
     * @var int
     *
     * @ORM\Column(name="codePost", type="integer", nullable = true)
     */
    protected $codePost;

    /**
     * @var string
     *
     * @ORM\Column(name="typeUser", type="string", length=255, nullable = true)
     */
    protected $typeUser;

    /**
     * @var Institut
     *
     * @ORM\ManyToOne(targetEntity="Institut")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Serializer\Groups({"oneUser"})
     */
    protected $institut;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable = true)
     */
    protected $phone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     *@var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Chat", mappedBy="administrateurs")
     */
    private $chats;

    /**
     * @var bool
     *
     * @ORM\Column(name="receiveAutoNotifs", type="boolean", nullable=true)
     */
    private $receiveAutoNotifs = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="confirmedByAdmin", type="boolean", nullable=true)
     */
    private $confirmedByAdmin = false;

    const STATUT_ETUDIANT = 'Etudiant';
    const STATUT_FORMATEUR = 'Formateur';
    const STATUT_PROFSTAGIAIRE = 'Prof_stagiaire';
    const STATUT_RESPONSABLE = 'Responsable';

    /**
     * @var bool
     *
     * @ORM\Column(name="validInscriptionFormateurEngagement", type="boolean", nullable=true)
     */
    private $validInscriptionFormateurEngagement = false;

    /**
     * @ORM\Column(name="statut", type="string")
     * @Serializer\Groups({"oneUser"})
     */

    private $statut = 'Etudiant';

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Section", inversedBy="autorizedUsers")
     * @ORM\JoinTable(name="users_sections")
     */
    private $autorizedSections;

    public function __construct()
    {
        parent::__construct();
        $this->autorizedSections = new ArrayCollection();
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set nomEtabl
     *
     * @param string $nomEtabl
     *
     * @return User
     */
    public function setNomEtabl($nomEtabl)
    {
        $this->nomEtabl = $nomEtabl;

        return $this;
    }

    /**
     * Get nomEtabl
     *
     * @return string
     */
    public function getNomEtabl()
    {
        return $this->nomEtabl;
    }

    /**
     * Set commune
     *
     * @param string $commune
     *
     * @return User
     */
    public function setCommune($commune)
    {
        $this->commune = $commune;

        return $this;
    }

    /**
     * Get commune
     *
     * @return string
     */
    public function getCommune()
    {
        return $this->commune;
    }

    /**
     * Set codePost
     *
     * @param integer $codePost
     *
     * @return User
     */
    public function setCodePost($codePost)
    {
        $this->codePost = $codePost;

        return $this;
    }

    /**
     * Get codePost
     *
     * @return integer
     */
    public function getCodePost()
    {
        return $this->codePost;
    }

    /**
     * Set typeUser
     *
     * @param string $typeUser
     *
     * @return User
     */
    public function setTypeUser($typeUser)
    {
        $this->typeUser = $typeUser;

        return $this;
    }

    /**
     * Get typeUser
     *
     * @return string
     */
    public function getTypeUser()
    {
        return $this->typeUser;
    }

    /**
     * Set Institut
     *
     * @param Institut $institut
     *
     * @return User
     */
    public function setInstitut($institut)
    {
        $this->institut = $institut;

        return $this;
    }

    /**
     * Get institut
     *
     * @return Institut
     */
    public function getInstitut()
    {
        return $this->institut;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    public function setEmail($email)
    {
        parent::setUsername($email);
        return parent::setEmail($email);
    }

    public function setEmailCanonical($emailCanonical)
    {
        parent::setUsernameCanonical($emailCanonical);
        return parent::setEmailCanonical($emailCanonical);
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime("now");
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Add a chat
     *
     * @param Chat $chat
     * @return User
     */
    public function addChat(Chat $chat)
    {
        if(!$this->chats->contains($chat)){
            $this->chats[] = $chat;
        }
        return $this;
    }
    /**
     * Remove a chat
     *
     * @param Chat $chat
     */
    public function removeChat(Chat $chat)
    {
        $this->chats->removeElement($chat);
    }
    /**
     * Get chats
     *
     * @return ArrayCollection
     */
    public function getChats()
    {
        return $this->chats;
    }

    /**
     * Set receiveAutoNotifs
     *
     * @param boolean $receiveAutoNotifs
     *
     * @return User
     */
    public function setReceiveAutoNotifs($receiveAutoNotifs)
    {
        $this->receiveAutoNotifs = $receiveAutoNotifs;

        return $this;
    }

    /**
     * Get receiveAutoNotifs
     *
     * @return bool
     */
    public function getReceiveAutoNotifs()
    {
        return $this->receiveAutoNotifs;
    }

    /**
     * Set confirmedByAdmin
     *
     * @param boolean $confirmedByAdmin
     *
     * @return User
     */
    public function setConfirmedByAdmin($confirmedByAdmin)
    {
        $this->confirmedByAdmin = $confirmedByAdmin;

        return $this;
    }

    /**
     * Get confirmedByAdmin
     *
     * @return bool
     */
    public function getConfirmedByAdmin()
    {
        return $this->confirmedByAdmin;
    }

    /**
     * Set validInscriptionFormateurEngagement
     *
     * @param boolean $validInscriptionFormateurEngagement
     *
     * @return User
     */
    public function setValidInscriptionFormateurEngagement($validInscriptionFormateurEngagement)
    {
        $this->validInscriptionFormateurEngagement = $validInscriptionFormateurEngagement;

        return $this;
    }

    /**
     * Get validInscriptionFormateurEngagement
     *
     * @return bool
     */
    public function getValidInscriptionFormateurEngagement()
    {
        return $this->validInscriptionFormateurEngagement;
    }

    /**
     * Set statut
     *
     * @param string $statut
     *
     * @return User
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Get statuts
     *
     * @return array
     */
    public static function getStatuts()
    {
        return [
            "Étudiant" => self::STATUT_ETUDIANT,
            "Formateur" => self::STATUT_FORMATEUR,
            "Professeur Stagiaire" => self::STATUT_PROFSTAGIAIRE,
            "Responsable" => self::STATUT_RESPONSABLE
        ];
    }

    /**
     * Set autorizedSections
     *
     * @param array $autorizedSections
     *
     * @return User
     */
    public function setAutorizedSections($autorizedSections)
    {
        $this->autorizedSections = $autorizedSections;

        return $this;
    }

    /**
     * Get autorizedSections
     *
     * @return ArrayCollection
     */
    public function getAutorizedSections()
    {
        return $this->autorizedSections;
    }

    /**
     * Add autorizedSection
     *
     * @param Section $autorizedSection
     *
     * @return User
     */
    public function addAutorizedSection($autorizedSection)
    {
        if(!$this->autorizedSections->contains($autorizedSection)){
            $this->autorizedSections[] = $autorizedSection;
            $autorizedSection->addautorizedUser($this);
        }

        return $this;
    }

    /**
     * Remove autorizedSection
     *
     * @param Section $autorizedSection
     */
    public function removeAutorizedSection($autorizedSection)
    {
        if ($this->autorizedSections->contains($autorizedSection)) {
            $this->autorizedSections->removeElement($autorizedSection);
            $autorizedSection->removeAutorizedUser($this);
        }
    }
}
