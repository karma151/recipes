<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Sluggable\Util\Urlizer;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RecipeRepository")
 */
class Recipe
{
  /**
  * @ORM\Id
  * @ORM\Column(type="integer")
  * @ORM\GeneratedValue(strategy="AUTO")
  */
  private $id;

  /**
  * @ORM\Column(type="string", length=255, unique=false)
  * @Assert\NotBlank()
  */
  private $name;

  /**
  * @ORM\Column(type="text", unique=false)
  */
  private $description;

  /**
  * @var ArrayCollection Ingredient
  * @ORM\OneToMany(targetEntity="AppBundle\Entity\RecipeHasIngredient", mappedBy="recipe",cascade={"all"},orphanRemoval=true )
  * @ORM\OrderBy({"position" = "ASC"})
  */
  private $ingredients;

  /**
  * @var ArrayCollection Steps
  * @ORM\OneToMany(targetEntity="AppBundle\Entity\Step", mappedBy="recipe",cascade={"all"},orphanRemoval=true )
  * @ORM\OrderBy({"position" = "ASC"})
  */
  private $steps;

  /**
  * @var ArrayCollection Parts
  * @ORM\OneToMany(targetEntity="AppBundle\Entity\Part", mappedBy="recipe",cascade={"all"},orphanRemoval=true )
  * @ORM\OrderBy({"position" = "ASC"})
  */
  private $parts;

  /**
  * @ORM\Column(type="integer", nullable=true)
  */
  private $cookTime;

  /**
  * @ORM\Column(type="integer")
  */
  private $preparationTime;

  /**
  * @ORM\Column(type="integer", nullable=true)
  */
  private $restTime;

  /**
  * @ORM\Column(type="integer")
  */
  private $forPerson;

  /**
  * @ORM\Column(type="string", length=255, unique=true)
  */
  private $slug;


  // Constructor
  public function __construct()
  {
    $this->ingredients = new ArrayCollection();
    $this->steps = new ArrayCollection();
  }

  /**
  * Set Ingredients
  *
  * @return Recipe
  */
  public function setIngredients(ArrayCollection $ingredients)
  {
    $this->ingredients = $ingredients;
    return $this;
  }

  /**
  * Get Ingredients
  *
  * @return ArrayCollection
  */
  public function getIngredients()
  {
    return $this->ingredients;
  }

  /**
  * Add Ingredient
  *
  * @param RecipeHasIngredient $ingredient
  * @return $this
  */
  public function addIngredient(RecipeHasIngredient $ingredient)
  {
    $ingredient->setRecipe($this);
    $this->ingredients[] = $ingredient;
    return $this;
  }

  /**
  * Remove Ingerdient
  *
  * @param Ingredient $ingredient
  * @return $this
  */
  public function removeIngredient(Ingredient $ingredient)
  {
    $this->ingredients->removeElement($ingredient);
    return $this;
  }

  public function getIngredientsGroupByPart(){
    $orderedIngredients = array();
    foreach ($this->ingredients as $recipeHasIngredient){
      if ($recipeHasIngredient->getPart() != null){
        $orderedIngredients[$recipeHasIngredient->getPart()->getId()] = $recipeHasIngredient->getPart();
      }
    }
    return $orderedIngredients;
  }



  /**
  * Set Steps
  *
  * @return Recipe
  */
  public function setSteps(ArrayCollection $steps)
  {
    $this->steps = $steps;
    return $this;
  }

  /**
  * Get Steps
  *
  * @return ArrayCollection
  */
  public function getSteps()
  {
    return $this->steps;
  }

  /**
  * Add Step
  *
  * @param Step $step
  * @return $this
  */
  public function addStep(Step $step)
  {
    $step->setRecipe($this);
    $this->steps[] = $step;
    return $this;
  }

  /**
  * Remove Step
  *
  * @param Step $step
  * @return $this
  */
  public function removeStep(Step $step)
  {
    $this->steps->removeElement($step);
    return $this;
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
  *
  * @return Recipe
  */
  public function setName($name)
  {
    $this->setSlug($name);
    $this->name = $name;

    return $this;
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
  * Set cookTime
  *
  * @param integer $cookTime
  *
  * @return Recipe
  */
  public function setCookTime($cookTime)
  {
    $this->cookTime = $cookTime;

    return $this;
  }

  /**
  * Get cookTime
  *
  * @return integer
  */
  public function getCookTime()
  {
    return $this->cookTime;
  }

  /**
  * Set preparationTime
  *
  * @param integer $preparationTime
  *
  * @return Recipe
  */
  public function setPreparationTime($preparationTime)
  {
    $this->preparationTime = $preparationTime;

    return $this;
  }

  /**
  * Get preparationTime
  *
  * @return integer
  */
  public function getPreparationTime()
  {
    return $this->preparationTime;
  }

  public function __toString(){
    return $this->getName();
  }

  /**
  * Set restTime
  *
  * @param integer $restTime
  *
  * @return Recipe
  */
  public function setRestTime($restTime)
  {
    $this->restTime = $restTime;

    return $this;
  }

  /**
  * Get restTime
  *
  * @return integer
  */
  public function getRestTime()
  {
    return $this->restTime;
  }

  /**
  * Set forPerson
  *
  * @param integer $forPerson
  *
  * @return Recipe
  */
  public function setForPerson($forPerson)
  {
    $this->forPerson = $forPerson;

    return $this;
  }

  /**
  * Get forPerson
  *
  * @return integer
  */
  public function getForPerson()
  {
    return $this->forPerson;
  }

  /**
  * Set slug
  *
  * @param string $slug
  *
  * @return Recipe
  */
  public function setSlug($slug)
  {
    // replace non letter or digits by -
    $slug = preg_replace('~[^\pL\d]+~u', '-', $slug);

    // transliterate
    $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);

    // remove unwanted characters
    $slug = preg_replace('~[^-\w]+~', '', $slug);

    // trim
    $slug = trim($slug, '-');

    // remove duplicate -
    $slug = preg_replace('~-+~', '-', $slug);

    // lowercase
    $slug = strtolower($slug);

    $this->slug = $slug;
  }

  /**
  * Get slug
  *
  * @return string
  */
  public function getSlug()
  {
    return $this->slug;
  }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Recipe
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}