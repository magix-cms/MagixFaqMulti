<?php
declare(strict_types=1);

namespace Plugins\MagixFaqMulti\src;

use Plugins\MagixFaqMulti\db\FaqMultiFrontDb;
use Magepattern\Component\Tool\SmartyTool;
use App\Frontend\Model\SeoHelper; // 🟢 Import du Helper SEO

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

            // 🟢 4. GÉNÉRATION DU SEO JSON-LD (Formatage générique)
            $seoData = [];
            foreach ($faqs as $faq) {
                $seoData[] = [
                    'question' => $faq['title_faqmulti'],
                    'answer'   => $faq['desc_faqmulti']
                ];
            }
            $faqJsonLd = SeoHelper::generateFaqJsonLd($seoData);

            // 5. Chemin du template
            $template = ROOT_DIR . 'plugins' . DS . 'MagixFaqMulti' . DS . 'views' . DS . 'front' . DS . 'widget.tpl';

            if (!file_exists($template)) return '';

            // 6. Rendu (On ajoute la clé 'seo' aux données transmises)
            return $view->fetch($template, [
                'magix_faqmulti_data' => [
                    'module' => $itemType,
                    'items'  => $faqs,
                    'seo'    => $faqJsonLd // 🟢 Injecté dans le widget
                ]
            ]);

        } catch (\Throwable $e) {
            return "";
        }
    }
}