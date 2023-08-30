<?php 

namespace App\Command;

use App\Service\CalendarService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:export-planned-meetings',
    description: 'Export planned meetings of the consultant.',
    hidden: false,
    aliases: ['app:export-meeting']
)]
class CalendarCommand extends Command
{
    private $calendarService;
    private $parameterBag;

    public function __construct(CalendarService $calendarService, ParameterBagInterface $parameterBag)
    {
        parent::__construct();
        $this->calendarService = $calendarService;
        $this->parameterBag = $parameterBag;
        
    }

    /**
     * Configuration of this command
     */
    protected function configure(): void
    {
        $this
            ->setName('calendar-planned-meetings:export')
            ->setDescription('This command allows you to export all the planned meetings to a file.')
            ->addArgument('email', InputArgument::OPTIONAL, 'Enter the email address');            
    }

    /**
     * Execute the ICal export function
     * 
     * @param InputInterface $input Input
     * @param OutputInterface $output Output
     * 
     * @return int Command enum i.e. SUCESSS
     * 
    */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $employees = $this->calendarService->getallPlannedMeetings($email);

        if ($employees) {
            $this->exportEmployeeMeetings($output, $employees);            
        } else {
            $output->writeln([
                '',
                '',
                '<error>Employee with email - '.$email.' does not exists</error>',
                '',
            ]);
        }

        return Command::SUCCESS;
    }

    /**
     * Export meetings of the employee in ICal formatted files.
     * The folder path of the files is taken from environment file.
     * 
     * @param OutputInterface $output Output
     * @param array $employees List of employees
     * 
    */
    private function exportEmployeeMeetings(OutputInterface $output, array $employees): void 
    {
        $path = $this->parameterBag->get('calendar_path');

        $output->writeln([
            '',
            'Start export calendar',
            '========================',
            '',
        ]);
        
        if (!file_exists($path)) {
            $output->writeln('<comment>Create destination folder</comment>');
            mkdir($path, 0777, true);
        } else {
            $output->writeln('<comment>Destination folder already exists</comment>');
        }

        // Export all meetings of the employee
        foreach($employees as $employee){
            $employeeDetail = $employee->getFirstName().' '.$employee->getLastName().' <'.$employee->getEmail().'>';
            $output->writeln('<info>Export the meeting calendar for '.$employeeDetail.'</info>');
            
            // Create iCal calendar
            $calendar = Calendar::create($employeeDetail);
            $meetings = $employee->getProjectPlannings();
            if($meetings){
                foreach($meetings as $meeting){
                    $calendar
                    ->event(Event::create($meeting->getDescription() ? $meeting->getDescription() : '')
                     ->description($meeting->getNotes() ? $meeting->getNotes() : '')
                     ->startsAt($meeting->getStartDate())
                     ->endsAt($meeting->getEndDate())
                    );
                }    
            }
            $cal = $calendar->get();
            file_put_contents($path.$employee->getEmail().".iCal",$cal);
        }

        $output->writeln([
            '',
            '========================',
            'End export calendar',
            '',
        ]);
    }
}