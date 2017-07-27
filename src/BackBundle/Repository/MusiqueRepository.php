<?php

namespace BackBundle\Repository;

use InvalidArgumentException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MusiqueRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Récupère une liste d'articles triés et paginés.
     *
     * @param int $page Le numéro de la page
     * @param int $nbMaxParPage Nombre maximum d'article par page
     *
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException
     *
     * @return Paginator
     */
    public function findAllPagineEtTrie($page, $nbMaxParPage, User $user = null, $filtre = null, $ordreDeTri = 'DESC')
    {
        if (!is_numeric($page)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $page est incorrecte (valeur : ' . $page . ').'
            );
        }

        if ($page < 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas');
        }

        if (!is_numeric($nbMaxParPage)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $nbMaxParPage est incorrecte (valeur : ' . $nbMaxParPage . ').'
            );
        }

        $qb = $this->createQueryBuilder('a');


        if ($user !== null) {
            $qb->setParameter('author', $user);
        }

        if (isset($filtre)) {
            $mapping = [
                'date' => 'a.date',
            ];

            $qb->orderBy($mapping[$filtre], $ordreDeTri);
        } else {
            $qb->orderBy('a.id', 'DESC');
        }

        $query = $qb->getQuery();


        $premierResultat = ($page - 1) * $nbMaxParPage;
        $query->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
        $paginator = new Paginator($query);

        if (($paginator->count() <= $premierResultat) && $page != 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
        }

        return $paginator;
    }

}