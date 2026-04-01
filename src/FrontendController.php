<?php
declare(strict_types=1);

namespace Plugins\MagixFaqMulti\src;

use Plugins\MagixFaqMulti\db\FaqMultiFrontDb;
use Magepattern\Component\Tool\SmartyTool;
use App\Frontend\Model\SeoHelper;

class FrontendController
{
    /**
     * Le routeur dynamique appelé par HookManager
     */
    public static function renderWidget(array $params = []): string
    {
        $hookName = $params['name'] ?? '';

        if ($hookName === 'displayPageBottom') {
            return self::processRender($params, 'pages', 'id_pages');
        }
        if ($hookName === 'displayProductExtraContent') {
            return self::processRender($params, 'product', 'id_product');
        }
        if ($hookName === 'displayCategoryBottom') {
            return self::processRender($params, 'category', 'id_cat');
        }
        if ($hookName === 'displayNewsBottom') {
            return self::processRender($params, 'news', 'id_news');
        }

        return '';
    }

    /**
     * Votre méthode métier (rendue privée)
     */
    private static function processRender(array $params, string $itemType, string $idKey): string
    {
        try {
            $view = SmartyTool::getInstance('front');

            $langData = $view->getTemplateVars('current_lang') ?: $view->getTemplateVars('lang') ?: ['id_lang' => 1];
            $idLang = (int)($langData['id_lang'] ?? 1);

            $itemId = (int)($params[$idKey] ?? 0);
            if ($itemId === 0) return '';

            $db = new FaqMultiFrontDb();
            $faqs = $db->getPublishedFaqs($itemType, $itemId, $idLang);

            if (empty($faqs)) return '';

            $seoData = [];
            foreach ($faqs as $faq) {
                $seoData[] = [
                    'question' => $faq['title_faqmulti'],
                    'answer'   => $faq['desc_faqmulti']
                ];
            }
            $faqJsonLd = SeoHelper::generateFaqJsonLd($seoData);

            $template = ROOT_DIR . 'plugins' . DS . 'MagixFaqMulti' . DS . 'views' . DS . 'front' . DS . 'widget.tpl';

            if (!file_exists($template)) return '';

            return $view->fetch($template, [
                'magix_faqmulti_data' => [
                    'module' => $itemType,
                    'items'  => $faqs,
                    'seo'    => $faqJsonLd
                ]
            ]);

        } catch (\Throwable $e) {
            return "";
        }
    }
}