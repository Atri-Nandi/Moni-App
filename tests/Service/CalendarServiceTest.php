<?php
namespace App\Tests\Service;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Service\CalendarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalendarServiceTest extends KernelTestCase
{
    private $calendarService;
    private $em;
    private $employeeRepository;

    /**
     * Initilize the calendar service
     */
    public function init()
    {
        self::bootKernel(['environment' => 'test', 'debug' => true]);
        $this->em = $em = $this->createMock(EntityManagerInterface::class);
        $this->employeeRepository = $this->createMock(EmployeeRepository::class);
        $em->expects($this->any())->method('getRepository')->willReturn($this->employeeRepository);

        $this->calendarService = new CalendarService($em);        
    }

    /**
     * Test the planned meeting functionality without email
     */
    public function testMeetingsWithoutEmail(): void
    { 
       $this->init();
       
       $employeeArray = [];
       $employee1 = new Employee();
       $employee1->setFirstName('emp1');
       $employee2 = new Employee();
       $employee2->setFirstName('emp2');
       $employeeArray[] = $employee1;
       $employeeArray[] = $employee2;

       $this->employeeRepository->expects($this->any())->method('getMeetingsByEmployee')->willReturn($employeeArray);
       $result = $this->calendarService->getallPlannedMeetings();
      
       $this->assertCount(2,$result);
    }

    /**
     * Test the planned meeting functionality with email
     */
    public function testMeetingsWithEmail(): void
    { 
       $this->init();

       $email = 'user@example.org';
       $employeeArray = [];
       $employee = new Employee();
       $employee->setEmail($email);
       $employeeArray[] = $employee;

       $this->employeeRepository->expects($this->any())->method('getMeetingsByEmployee')->willReturn($employeeArray);
       $result = $this->calendarService->getallPlannedMeetings($email);
       $this->assertCount(1,$result);
       $this->assertEquals($result[0]->getEmail(),$email);
    }
}