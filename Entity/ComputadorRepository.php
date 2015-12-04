<?php

namespace Swpb\Bundle\CocarBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ComputadorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ComputadorRepository extends EntityRepository
{
    /**
     * Máquinas que não estão no Cacic
     *
     * @return mixed
     */
    public function cacicNull() {
        $start = isset($start) ? $start : (time() - ((60*60*24)*30));
        $start = new \DateTime(date("Y-m-d", $start));
        $end   = isset($end) ? $end : time();
        $end = new \DateTime(date("Y-m-d", $end));

        $qb = $this->createQueryBuilder('c')
            ->select("count(DISTINCT c.id)")
            ->innerJoin("CocarBundle:PingComputador", "p")
            ->andWhere("c.cacic_id is null")
            ->andWhere("p.date BETWEEN :start AND :end")
            ->setParameter("start", $start)
            ->setParameter("end", $end);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Total de computadores que pingaram nos últimos 30 dias
     *
     * @return mixed
     */
    public function totalComp() {
        $start = isset($start) ? $start : (time() - ((60*60*24)*30));
        $start = new \DateTime(date("Y-m-d", $start));
        $end   = isset($end) ? $end : time();
        $end = new \DateTime(date("Y-m-d", $end));

        $qb = $this->createQueryBuilder('c')
            ->select("count(DISTINCT c.id)")
            ->innerJoin("CocarBundle:PingComputador", "p")
            ->andWhere("p.date BETWEEN :start AND :end")
            ->setParameter("start", $start)
            ->setParameter("end", $end);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
