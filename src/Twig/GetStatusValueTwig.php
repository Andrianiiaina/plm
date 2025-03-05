<?php
    namespace App\Twig;
    use Twig\Extension\AbstractExtension;
    use Twig\TwigFunction;    
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
                new \Twig\TwigFilter('getStatusColor', [$this, 'getStatusColor']),
                new \Twig\TwigFilter('getrojectStatusColor', [$this, 'getrojectStatusColor']),
            ];
        }
        public function getStatusColor(int $value){
            $colors=[ 
                0=>"secondary",
                1=>"primary",
                2=>"info",
                3=>"success",
                4=>"warning",
                5=>"danger",
                6=>"dark",
            ];
            return $colors[$value] ?? 'inconnu';
        }

        public function getrojectStatusColor(int $value){
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