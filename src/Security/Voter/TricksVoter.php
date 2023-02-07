<?php 

namespace App\Security\Voter;

use App\Entity\Trick;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TricksVoter extends Voter
{
    const EDIT = 'TRICK_EDIT';
    const DELETE = 'TRICK_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $trick): bool
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE])){
            return false;
        }
        if(!$trick instanceof Trick){
            return false;
        }
        return true;

        // return in_array($attribute, [self::EDIT, self::DELETE]) && $trick instanceof Trick;
    }

    protected function voteOnAttribute($attribute, $trick, TokenInterface $token): bool
    {
        // on récupère l'utilisateur à partir du token 
        $user = $token->getUser();

        // on vérifie si user est une instance de userinterface 
        if(!$user instanceof UserInterface){
            return false;
        }
    
        // on vérifie si user est admin 
        if($this->security->isGranted('ROLE_ADMIN')){
            return true;
        }

        // on vérifie les permissions 
        switch($attribute){
            case self::EDIT:
                // on vérif si user peut éditer 
                return $this->canEdit();
                break;
            case self::DELETE:
                // on vérif si user peut supprimer
                break;
        }
    }


    private function canEdit()
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}