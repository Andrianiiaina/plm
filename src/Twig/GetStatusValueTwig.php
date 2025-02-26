<?php
    namespace App\Twig;
    use Twig\Extension\AbstractExtension;
    use Twig\TwigFunction;
    use App\Service\ListService;
    class  GetStatusValueTwig extends AbstractExtension
    {
        public function getFunctions():array
        {
            return [
                new TwigFunction('get_project_status', [$this, 'getProjectStatus']),
                new TwigFunction('get_project_type', [$this, 'getProjectType']),
                new TwigFunction('get_document_status', [$this, 'getDocumentStatus']),
            ];
        }

        public function getProjectStatus(int $value):string
        {
            return array_search($value,ListService::$project_status) ?? 'inconnu';
        }

        public function getProjectType(int $value):string
        {
            return array_search($value,ListService::$project_type) ?? 'inconnu';
        }

        public function getDocumentStatus(int $value):string
        {
            return array_search($value,ListService::$document_status) ?? 'inconnu';
        }
    }
?>