<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\ProjectPlanning;
use App\Service\CalendarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class MeetingController extends AbstractController
{
    public $calendarService;
    private $em;

    public function __construct(CalendarService $calendarService, EntityManagerInterface $em) {
        $this->calendarService = $calendarService;  
        $this->em = $em;
    }

    /**
     * This page shows the meeting details based on the employee email
     * 
     * @param string $id Database ID of the employee object
     * 
     * @return Response Symfony Reponse object
     */
    #[Route('/meeting-list/{id}', name: 'meeting-list')]
    public function index(string $id = ''): Response
    {
        $employee= $this->em->getRepository(Employee::class)->findOneBy(['id' => $id]);
        if(!$employee){
            return $this->redirectToRoute('home');
        }

        // Get all the meetings of the current week.
        $emp = $this->calendarService->getallPlannedMeetings($employee->getEmail(), true);           

        return $this->render('meeting-list.html.twig', [
            'employees' => $emp,
        ]);
    }

    /**
     * In this page you can update the meeting details.
     * 
     * @param Request $request Symfony request object
     * @param string  $id      Database ID of the project planning object
     * 
     * @return Response Symfony Reponse object
     */
    #[Route('/meeting/{id}', name: 'meeting')]
    public function meeting(Request $request, string $id = ''): Response
    {
        $meeting= $this->em->getRepository(ProjectPlanning::class)->findOneBy(['id' => $id]);
        if(!$meeting){
            return $this->redirectToRoute('home');
        }

        // Create form
        $form = $this->getForm($meeting);
        $form->handleRequest($request);

        // Handle Submit
        if($form->isSubmitted()){
            if($response = $this->handleSubmit($form)){
                return $response;                
            }
        }

        return $this->render('meeting.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * Generate meeting Form
     * 
     * @param ProjectPlanning $meeting Project planning object
     * 
     * @return Form Symfony Form object
     */
    private function getForm(ProjectPlanning $meeting): Form
    {
        return $this->createFormBuilder($meeting,  ['csrf_protection' => false])
            ->add('description', TextType::class, [
                'label' => "Meeting Descrpition",             
                'attr' => ['class' => 'form-control'],                
            ])
            ->add('notes', TextareaType::class, [
                'label' => "Notes",            
                'attr' => ['class' => 'form-control'],                
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => "Start Date",
                'widget' => 'single_text',
                'html5' => true,
                'placeholder' => 'Select Start Date',
                'attr' => ['class' => 'form-control'],                
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => "End Date",
                'widget' => 'single_text',
                'html5' => true, 
                'attr' => ['class' => 'form-control'],                
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Submit",
                'attr' => ['class' => 'btn btn-primary'],                
            ])
            ->add('cancel', SubmitType::class, [
                'label' => "Cancel",
                'attr' => ['class' => 'btn btn-secondary'],                
            ])
            ->getForm();
    }

     
    /** 
     * Handle the submit button. This update the meeting details.
     * 
     * @param Form $form Symfony form object
     * 
     * @return Response|null Symfony Response object
     */
    private function handleSubmit(Form $form): Response
    {
        $meeting = $form->getData();

        if ($form->get('submit')->isClicked() && $form->isValid()) {
            $this->em->persist($meeting);
            $this->em->flush();
        } 

        return $this->redirectToRoute('meeting-list', ['id' => $meeting->getEmp()->getId()]);
    }    
}
