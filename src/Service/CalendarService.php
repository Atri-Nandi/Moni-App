<?php

namespace App\Service;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;

class CalendarService 
{
    private $em;

    public function __construct(EntityManagerInterface $em) 
    {
        $this->em = $em;
    }

    /**
     * Get all the planned meetings for an employee by email. 
     * In case email is not provided, all meetings are returned.
     * 
     * @param string $email       Email address of employee
     * @param bool   $currentWeek True if the planned meetings has to be shown for current week otherwise false
     * 
     * @return array List of employees
     */
    public function getallPlannedMeetings(string $email = null, bool $currentWeek = false): array
    {                
      return $this->em->getRepository(Employee::class)->getMeetingsByEmployee($email, $currentWeek);                            
    }    
}