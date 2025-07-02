<?php
    namespace App\Twig;
    use Twig\Extension\AbstractExtension;
    use App\Service\ListService;
    class  GetStatusValueTwig extends AbstractExtension
    {
        
        public function getFilters(): array{
          
            return [
                new \Twig\TwigFilter('getTenderStatus', function (int $value) {
                    return array_search($value,ListService::$tender_status) ?? 'inconnu';
                }),
                new \Twig\TwigFilter('getTenderType', function (int $value) {
                    return array_search($value,ListService::$tender_type) ?? 'inconnu';
                }),
                new \Twig\TwigFilter('getDocumentStatus', function (int $value) {
                    return array_search($value,ListService::$document_status) ?? 'inconnu';
                }),

                new \Twig\TwigFilter('highlight', function ($date) {
                    
                    return $date<new \DateTime()?' fw-light text-decoration-line-through m-0 p-0':'';
                    
                }),

                new \Twig\TwigFilter('getStatusColor', [$this, 'getStatusColor']),
                new \Twig\TwigFilter('getReminderDay', [$this, 'getReminderDay']),
                new \Twig\TwigFilter('getDateType', [$this, 'getDateType']),
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
        public function getReminderDay(int $value){
            $reminders=[
                1=>"1 jour avant",
                2=>"2 jours avant",
                3=>"3 jours avant",
                4=>"4 jours avant",
                5=>"5 jours avant",
                7 =>"1 semaine avant",
                14 =>"2 semaine avant",
                21=>"3 semaine avant",
            ];
            return $reminders[$value]?? 'inconnu';
        }

        public function getDateType(int $value){
            $dates=[
                0=>"Date de soumissions",
                1=>"Date de négociation",
                2=>"Date de réponse",
                3=>"Date d'attribution",
                4=>"Date de début",
                5=>"Date de fin",
            ];
            return $dates[$value];
        }
      

      
    }
?>