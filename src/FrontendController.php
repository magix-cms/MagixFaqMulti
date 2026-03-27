<?php
declare(strict_types=1);

namespace Plugins\MagixFaqMulti\src;

use Plugins\MagixFaqMulti\db\FaqMultiFrontDb;
use Magepattern\Component\Tool\SmartyTool;

class FrontendController
{
    /**
     * Rendu générique pour tous les hooks Frontend
     */
    public static function renderWidget(array $params, string $itemType, string $idKey): string
    {
        try {
            $view = SmartyTool::getInstance('front');

            // 1. Détection de la langue courante
            $langData = $view->getTemplateVars('current_lang') ?: $view->getTemplateVars('lang') ?: ['id_lang' => 1];
            $idLang = (int)($langData['id_lang'] ?? 1);

            // 2. Récupération de l'ID via la clé dynamique passée par le Boot
            $itemId = (int)($params[$idKey] ?? 0);

            if ($itemId === 0) return '';

            // 3. Récupération depuis la base de données
            $db = new FaqMultiFrontDb();
            $faqs = $db->getPublishedFaqs($itemType, $itemId, $idLang);

            if (empty($faqs)) return '';

            // 4. Chemin du template
            $template = ROOT_DIR . 'plugins' . DS . 'MagixFaqMulti' . DS . 'views' . DS . 'front' . DS . 'widget.tpl';

            if (!file_exists($template)) return '';

            // 5. Rendu (On encapsule les données proprement)
            return $view->fetch($template, [
                'magix_faqmulti_data' => [
                    'module' => $itemType,
                    'items'  => $faqs
                ]
            ]);

        } catch (\Throwable $e) {
            // Silencieux en front, on évite de casser la page en cas d'erreur
            return "";
        }
    }
}