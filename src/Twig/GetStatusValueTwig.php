<?php
    namespace App\Twig;
    use Twig\Extension\AbstractExtension;
    use Twig\TwigFunction;
    use App\Service\ListService;
    class  GetStatusValueTwig extends AbstractExtension
    {
        public function getFunctions()
        {
            return [
                new TwigFunction('get_project_status', [$this, 'getProjectStatus']),
                new TwigFunction('get_project_type', [$this, 'getProjectType']),
            ];
        }

        public function getProjectStatus(int $value)
        {
            return array_search($value,ListService::$project_status) ?? 'inconnu';
        }

        public function getProjectType(int $value)
        {
            return array_search($value,ListService::$project_type) ?? 'inconnu';
        }
    }
?>