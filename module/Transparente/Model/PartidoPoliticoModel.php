<?php
namespace Transparente\Model;

class PartidoPoliticoModel extends AbstractModel
{
    /**
     * Paginado
     *
     * @return Paginator
     */
    public function getPaginator(\Zend\Stdlib\Parameters $params = null)
    {
        $queryOptions = [
            'order'  => 'PartidoPolitico.iniciales',
            'sort'   => 'ASC',
            'filter' => false,
        ];
        if ($params) {
            $queryOptions = array_merge($queryOptions, $params->toArray());
        }

        $dql = 'SELECT PartidoPolitico
        FROM Transparente\Model\Entity\PartidoPolitico  PartidoPolitico';

        if ($queryOptions['filter']) {
            $dql .= "
                WHERE PartidoPolitico.nombre      LIKE '%{$queryOptions['filter']}%'
                   OR PartidoPolitico.iniciales   LIKE '%{$queryOptions['filter']}%'
            ";
        }
        $dql       .= " ORDER BY {$queryOptions['order']} {$queryOptions['sort']}";

        $paginator  = $this->getPaginatorFromDql($dql);
        return $paginator;
    }
}