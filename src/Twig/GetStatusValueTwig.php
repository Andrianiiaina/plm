<?php
    namespace App\Twig;
    use Twig\Extension\AbstractExtension;
    use App\Service\ListService;
use Symfony\Component\Validator\Constraints\Valid;

    class  GetStatusValueTwig extends AbstractExtension
    {
        
        public function getFilters(): array{
          
            return [
                new \Twig\TwigFilter('getTenderStatus', function (int $value) {
                    return array_flip(ListService::$tender_status)[$value] ?? 'inconnu';
                }),
                new \Twig\TwigFilter('getTenderType', function (int $value) {
                    return array_flip(ListService::$tender_type)[$value] ?? 'inconnu';
                }),
                new \Twig\TwigFilter('getDocumentStatus', function (int $value) {
                    return array_flip(ListService::$document_status)[$value] ?? 'inconnu';
                }),

                new \Twig\TwigFilter('highlight', function ($date) {
                    return $date<new \DateTime()?' fw-light text-decoration-line-through m-0 p-0':'';
                }),

                new \Twig\TwigFilter('getStatusColor', [$this, 'getStatusColor']),
                new \Twig\TwigFilter('getReminderDay', function($value){
                    return array_flip(ListService::$reminders)[$value] ?? 'inconnu';  
                }),
                new \Twig\TwigFilter('getDateType', function($value){
                    return array_flip(ListService::$tender_date_type)[$value];
                }),
            ];
        }
 

        public function getStatusColor(int $value){
            $colors=[ 
                0=>"primary",
                1=>"success",
                2=>"warning",
                3=>"danger",
                4=>"dark",
            ];
            return $colors[$value] ?? 'inconnu';
        }
    }
?>