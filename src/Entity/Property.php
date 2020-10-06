<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PropertyRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=PropertyRepository::class)
 * @Vich\Uploadable
 */
class Property
{

    public function __construct()
    {
        $time = new \DateTime();
        $this->created_at = $time->format('Y-m-d H:i:s');
        $this->sold = false;
        $this->featured = 0;
        $this->options = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    const HEAT = ['Gas', 'Electricity'];
    const CATEGORY = ['Home', 'Apartment', 'Field', 'Garage'];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message = "Please enter title."
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descr;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=10,max=1000)
     * @Assert\NotBlank(
     *     message = "Please enter area."
     * )
     */
    private $surface;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(min=0,max=10)
     */
    private $rooms;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(min=0, max=10)
     */
    private $bedrooms;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(min=0, max=10)
     */
    private $floor;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=10000, max=9999999)
     * @Assert\NotBlank(
     *     message = "Please enter your price."
     * )
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $heat;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 20,
     *      minMessage = "Votre ville doit contenir au moins {{ limit }} caractères",
     *      maxMessage = "Votre ville as un nom trop long : max {{ limit }} caractères"
     * )
     * @Assert\NotBlank(
     *     message = "Please enter city."
     * )
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Regex("/^[0-9]{5}/")
     * @Assert\NotBlank(
     *     message = "Please enter your postal code."
     * )
     *
     */
    private $postal_code;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $sold;

    /**
     * @ORM\Column(type="string")
     */
    private $created_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $featured;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(
     *     message = "Please select category."
     * )
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="author")
     */
    private $author;

    /**
     * @ORM\ManyToMany(targetEntity=Option::class, inversedBy="property")
     */
    private $options;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="property", orphanRemoval=true, cascade={"persist"})
     */
    private $images;

    /**
     * @Assert\All({
     *   @Assert\Image(mimeTypes={"image/png", "image/jpg", "image/jpeg"})
     * })
     */
    private $imageFiles;

    /**
     * @ORM\Column(type="float", scale= 5 , precision=7)
     */
    private $lat;

    /**
     * @ORM\Column(type="float", scale= 5 , precision=8)
     */
    private $lng;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        $slug = new Slugify();
        return $slug->slugify($this->title);
    }

    public function getDescr(): ?string
    {
        return $this->descr;
    }

    public function setDescr(?string $descr): self
    {
        $this->descr = $descr;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(int $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(int $bedrooms): self
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function formatPrice(): string
    {
        return number_format($this->price, 0, '', ' ');
    }

    public function getHeat(): ?int
    {
        return $this->heat;
    }

    public function formatHeat()
    {

        return self::HEAT[$this->heat];

    }

    public function formatCategory()
    {

        return self::CATEGORY[$this->getCategory()];

    }

    public function setHeat(int $heat): self
    {
        $this->heat = $heat;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getSold(): ?bool
    {
        return $this->sold;
    }

    public function setSold(bool $sold): self
    {
        $this->sold = $sold;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): self
    {
        $this->created_at = $created_at;


        return $this;
    }

    public function getFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getCategory(): ?int
    {
        return $this->category;
    }


    public function setCategory(int $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     * @return Property
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Option[]
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->addProperty($this);
        }

        return $this;
    }

    public function removeOption(Option $option): self
    {
        if ($this->options->contains($option)) {
            $this->options->removeElement($option);
            $option->removeProperty($this);
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getUpdateAt(): ?int
    {
        return strtotime($this->updateAt);
    }

    /**
     * @return Property
     */
    public function setUpdateAt(): Property
    {
        $time = new \DateTime('now');
        $this->updateAt = $time->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function getImage(): ?Image
    {
        if ($this->images->isEmpty()) {
            return null;
        }
        return $this->images->first();
    }

    public function addImage(Image $image): self
    {

        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProperty($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getProperty() === $this) {
                $image->setProperty(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageFiles()
    {
        return $this->imageFiles;
    }

    /**
     * @param mixed $imageFiles
     * @return Property
     */
    public function setImageFiles($imageFiles): self
    {
        dump($imageFiles);
        foreach($imageFiles as $imageFile) {
            $img = new Image();
            $img->setImageFile($imageFile);
            $this->addImage($img);
        }
        $this->imageFiles = $imageFiles;
        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }


}
