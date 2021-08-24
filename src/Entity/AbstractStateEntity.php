<?php

/**
 * Entité de gestion d'un pays
 */

namespace App\Entity;

use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints;

use App\Validator\Country\{
    StateCode as StateCodeValidation,
    StateName as StateNameValidation
};

#[
    MappedSuperclass()
]
abstract class AbstractStateEntity
{
    public const 
        TYPE_COUNTRY = 'COUNTRY',
        TYPE_STATE = 'STATE'
    ;

    /**
     * Code ISO
     * @var ?string
     */
    #[
        ORM\Id,
        ORM\Column(type: 'string', length: 2),
    ]
    protected ?string $code = null;

    /**
     * Nom
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 100),
    ]
    protected ?string $name = null;

    /**
     * Nom du fichier de l'image
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 100, nullable: true)
    ]
    private ?string $image = null;

    /**
     * Retourne le code ISO du pays
     * @return ?string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Modifie le code ISO du pays
     * @param ?string $code
     * @return self
     */
    public function setCode(?string $code) : self
    {
        $this->code = strtoupper($code);
        return $this;
    }

    /**
     * Retourne le nom du pays
     * @return ?string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * Modifie le nom du pays
     * @param ?string $name
     * @return self
     */
    public function setName(?string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Retourne le nom du fichier de l'image
     * @return ?string
     */
    public function getImage() : ?string
    {
        return $this->image;
    }

    /**
     * Modifie le nom du fichier de l'image
     * @param ?string $image
     * @return self
     */
    public function setImage(?string $image) : self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Validation du code et du nom
     * On utilise un callback pour pouvoir préciser le préfixe des messages de Validation
     * Cela n'est pas possible avec les annotations
     * @param ExecutionContextInterface $context
     * @return void
     */
    #[
        Constraints\Callback()
    ]
    public function isFieldsValid(ExecutionContextInterface $context) : void
    {
        $constraints = [
            'code' => $this->getCodeValidation(),
            'name' => new StateNameValidation(),
        ];
        $errors = $context->getValidator()->validate($this, $constraints);
        foreach($errors as $error)
        {
            $context    
                ->buildViolation($error->getMessage())
                ->atPath($error->getPropertyPath())
                ->addViolation();
        }
    }

    /**
     * Retourne le type d'état
     * @return string
     */
    abstract public function getStateType() : string;

    /**
     * Retourne la validation du code
     * @return StateCodeValidation
     */
    abstract protected function getCodeValidation() : StateCodeValidation;
}
