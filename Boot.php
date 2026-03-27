<?php
declare(strict_types=1);

namespace Plugins\MagixFaqMulti;

use App\Component\Hook\HookManager;
use Magepattern\Component\Tool\SmartyTool;
use Plugins\MagixFaqMulti\db\FaqMultiDb;
use Plugins\MagixFaqMulti\src\FrontendController;

class Boot
{
    private array $targetModules = [
        'product'  => 'id_product',
        'pages'    => 'id_pages',
        'category' => 'id_cat',
        'news'     => 'id_news',
        'about'    => 'id_about'
    ];

    public function register(): void
    {
        // ==========================================
        // 1. HOOKS BACKEND (Administration)
        // ==========================================
        foreach ($this->targetModules as $module => $idKey) {

            // Onglet (Le bouton)
            HookManager::register("{$module}_edit_tab", 'MagixFaqMulti', function(array $params) use ($module) {
                $smarty = SmartyTool::getInstance('admin');
                $file = ROOT_DIR . 'plugins' . DS . 'MagixFaqMulti' . DS . 'views' . DS . 'admin' . DS . 'hooks' . DS . 'tab_button.tpl';
                return $smarty->templateExists($file) ? $smarty->fetch($file) : '';
            });

            // Contenu (Le formulaire et la liste)
            HookManager::register("{$module}_edit_content", 'MagixFaqMulti', function(array $params) use ($module, $idKey) {
                $smarty = SmartyTool::getInstance('admin');
                $itemId = (int)($params[$idKey] ?? 0);

                // On n'affiche pas la FAQ si on est en train de "Créer" un élément (ID = 0)
                if ($itemId <= 0) {
                    return '';
                }

                $db = new FaqMultiDb();

                // On récupère la langue par défaut définie dans Smarty par le contrôleur principal
                $idLangDefault = (int)($smarty->getTemplateVars('defaultLang')['id_lang'] ?? 1);

                $smarty->assign([
                    'faq_item_type' => $module,
                    'faq_item_id'   => $itemId,
                    'faqs'          => $db->fetchFaqByItem($module, $itemId, $idLangDefault),
                    'langs'         => $db->fetchLanguages(),
                    'hashtoken'     => $smarty->getTemplateVars('hashtoken') // On récupère le token déjà généré
                ]);

                $file = ROOT_DIR . 'plugins' . DS . 'MagixFaqMulti' . DS . 'views' . DS . 'admin' . DS . 'hooks' . DS . 'tab_content.tpl';
                return $smarty->templateExists($file) ? $smarty->fetch($file) : '';
            });
        }

        // ==========================================
        // 2. HOOKS FRONTEND (Côté public)
        // ==========================================
        HookManager::register('displayPageBottom', 'MagixFaqMulti', function(array $params) {
            return FrontendController::renderWidget($params, 'pages', 'id_pages');
        });

        HookManager::register('displayProductExtraContent', 'MagixFaqMulti', function(array $params) {
            return FrontendController::renderWidget($params, 'product', 'id_product');
        });

        HookManager::register('displayCategoryBottom', 'MagixFaqMulti', function(array $params) {
            return FrontendController::renderWidget($params, 'category', 'id_cat');
        });

        HookManager::register('displayNewsBottom', 'MagixFaqMulti', function(array $params) {
            return FrontendController::renderWidget($params, 'news', 'id_news');
        });
    }
}