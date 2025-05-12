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