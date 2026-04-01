<?php
declare(strict_types=1);

namespace Plugins\MagixFaqMulti;

use App\Component\Hook\HookManager;
use Magepattern\Component\Tool\SmartyTool;
use Plugins\MagixFaqMulti\db\FaqMultiDb;

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

                if ($itemId <= 0) {
                    return '';
                }

                $db = new FaqMultiDb();
                $idLangDefault = (int)($smarty->getTemplateVars('defaultLang')['id_lang'] ?? 1);

                $smarty->assign([
                    'faq_item_type' => $module,
                    'faq_item_id'   => $itemId,
                    'faqs'          => $db->fetchFaqByItem($module, $itemId, $idLangDefault),
                    'langs'         => $db->fetchLanguages(),
                    'hashtoken'     => $smarty->getTemplateVars('hashtoken')
                ]);

                $file = ROOT_DIR . 'plugins' . DS . 'MagixFaqMulti' . DS . 'views' . DS . 'admin' . DS . 'hooks' . DS . 'tab_content.tpl';
                return $smarty->templateExists($file) ? $smarty->fetch($file) : '';
            });
        }
    }
}