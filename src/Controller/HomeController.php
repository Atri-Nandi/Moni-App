<?php

namespace App\Controller;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class HomeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * This is the home page of the application where you can enter email address.
     * and get the meetings of the current week.
     *  
     * @param Request $request Symfony request object
     * 
     * @return Response Symfony Reponse object     * 
     */
    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        // Create form
        $form = $this->getForm();
        $form->handleRequest($request);

        // Handle Submit
        if ($form->isSubmitted()) {
            if ($response = $this->handleSubmit($form)) {
                return $response;
            }            
        }

        return $this->render('index.html.twig', [
            'form' => $form->createView()        
        ]);
    }

    /**
     * Generate Calendar Form
     * 
     * @return Form Symfony Form object
     */
    private function getForm(): Form
    {
        return $this->createFormBuilder()
            ->add('email', TextType::class, [
                'label' => "Email Address", 
                'constraints' =>[
                    new Email(['message' => 'This is a invalid email address']),
                    new NotBlank(['message' => 'This field can not be blank'])
                ],               
                'attr' => ['class' => 'form-control'],                
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Submit",
                'attr' => ['class' => 'btn btn-primary d-grid w-100'],                
            ])
            ->getForm();
    }

    /** 
     * Handle the submit button. 
     * On submit it will display the meetings of the current week.
     * 
     * @param Form $form Symfony form object
     * 
     * @return Response|null Symfony Redirect Response object or null
     */
    private function handleSubmit(Form $form): ?Response
    {
        $data = $form->getData();

        if ($form->get('submit')->isClicked() && $form->isValid()) 
        {            
            if(filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            {
                $employee = $this->em->getRepository(Employee::class)->findOneBy(['email' => $data['email']]);                
                if($employee){
                    return $this->redirectToRoute('meeting-list', ['id' => $employee->getId()]);
                } else {
                    $this->addFlash('error', 'Email address does not exists.');

                    return $this->redirectToRoute('home');
                }
            }
            else {
                $this->addFlash('error', 'Incorrect email address');
            }
        }
        return null;
    }
}
