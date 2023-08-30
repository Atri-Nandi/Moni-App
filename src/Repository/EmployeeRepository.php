<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    /**
     * Get all the planned meetings
     * 
     * @param string $email Email address
     * @param bool $currentWeek True If current week to show
     * 
     * @return array|null List of employees
     */
    public function getMeetingsByEmployee(string $email = null, bool $currentWeek = false): array
    {
        $query =  $this
            ->createQueryBuilder('e')
            ->select('e, p')
            ->leftJoin('e.projectPlannings', 'p');

            if ($email) {
                $query->where('e.email = :email')
                      ->setParameter('email', $email);

                if ($currentWeek) {                    
                    // This will show meetings of the current week
                    $start = new \DateTime();
                    if ('Monday' !== $start->format('l')) {
                        $start->modify(sprintf('- %d days', (int) $start->format('w') - 1));
                    }
                    $end = clone($start);
                    $end->modify('+ 6 days');
                    $start->setTime(0, 0, 0);
                    $end->setTime(23, 59, 59);

                    $query->andWhere('p.startDate >= :start and p.endDate <= :end')
                          ->setParameter('start', $start)
                          ->setParameter('end', $end);
                }
            }             

            return $query ->getQuery()->getResult();      
    }
}
