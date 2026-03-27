<?php
declare(strict_types=1);

namespace Plugins\MagixFaqMulti\src;

use App\Backend\Controller\BaseController;
use Plugins\MagixFaqMulti\db\FaqMultiDb;
use Magepattern\Component\HTTP\Request;
use Magepattern\Component\Tool\SmartyTool;
use Magepattern\Component\Tool\FormTool;

class BackendController extends BaseController
{
    public function run(): void
    {
        SmartyTool::addTemplateDir('faqmulti', ROOT_DIR . 'plugins' . DS . 'MagixFaqMulti' . DS . 'views' . DS . 'admin');
        $action = $_GET['action'] ?? null;

        if ($action && method_exists($this, $action)) {
            $this->$action();
        } else {
            $this->jsonResponse(false, 'Action invalide.');
        }
    }

    public function loadList(): void
    {
        if (ob_get_length()) ob_clean();

        $itemType = $_GET['module'] ?? '';
        $itemId = (int)($_GET['id_module'] ?? 0);
        $idLang = (int)($this->defaultLang['id_lang'] ?? 1);

        if (empty($itemType) || $itemId === 0) {
            echo '<div class="alert alert-warning">Paramètres manquants.</div>';
            return;
        }

        $db = new FaqMultiDb();
        $faqs = $db->fetchFaqByItem($itemType, $itemId, $idLang);

        // 🟢 NOUVEAU : On injecte les traductions complètes pour le JS
        foreach ($faqs as &$faq) {
            $fullData = $db->fetchFaqById((int)$faq['id_faqmulti']);
            $faq['content'] = $fullData['content'] ?? [];
        }

        $columns = [
            'title_faqmulti' => ['title' => 'Question', 'type' => 'text', 'class' => 'fw-bold text-dark'],
            'published_faqmulti' => ['title' => 'Statut', 'type' => 'status', 'class' => 'text-center', 'width' => '120px']
        ];

        $this->view->assign([
            'faq_items'    => $faqs,
            'ajax_columns' => $columns,
            'item_type'    => $itemType,
            'item_id'      => $itemId,
            'hashtoken'    => $this->session->getToken(),
            'langs'        => $db->fetchLanguages() // Utile pour d'éventuels sous-menus
        ]);

        $this->view->display('ajax/manager.tpl');
    }

    public function save(): void
    {
        if (ob_get_length()) ob_clean();

        $token = Request::isPost('hashtoken') ? $_POST['hashtoken'] : '';
        if (!$this->session->validateToken($token)) {
            $this->jsonResponse(false, 'Session expirée.');
        }

        $idFaq    = (int)($_POST['id_faqmulti'] ?? 0);
        $itemType = FormTool::simpleClean($_POST['module_faqmulti'] ?? '');
        $itemId   = (int)($_POST['id_module'] ?? 0);

        if (empty($itemType) || $itemId === 0) {
            $this->jsonResponse(false, 'Les références du module sont obligatoires.');
        }

        $db = new FaqMultiDb();

        try {
            if ($idFaq === 0) {
                $idFaq = $db->insertFaqStructure(['item_type' => $itemType, 'item_id' => $itemId]);
                if (!$idFaq) $this->jsonResponse(false, 'Erreur de création de la structure.');
            }

            // 🟢 NOUVEAU : Traitement de la boucle des langues
            if (isset($_POST['title_faqmulti']) && is_array($_POST['title_faqmulti'])) {
                foreach ($_POST['title_faqmulti'] as $idLang => $title) {
                    $cleanTitle = FormTool::simpleClean($title);

                    // On ne sauvegarde que si un titre est défini pour cette langue
                    if (!empty($cleanTitle)) {
                        $db->saveFaqContent($idFaq, (int)$idLang, [
                            'title_faqmulti'     => $cleanTitle,
                            'desc_faqmulti'      => $_POST['desc_faqmulti'][$idLang] ?? '',
                            'published_faqmulti' => isset($_POST['published_faqmulti'][$idLang]) ? 1 : 0
                        ]);
                    }
                }
            }

            $this->jsonResponse(true, 'Enregistrement réussi.');
        } catch (\Exception $e) {
            $this->jsonResponse(false, 'Erreur serveur : ' . $e->getMessage());
        }
    }

    public function delete(): void
    {
        if (ob_get_length()) ob_clean();

        $token = Request::isPost('hashtoken') ? $_POST['hashtoken'] : '';
        if (!$this->session->validateToken($token)) {
            $this->jsonResponse(false, 'Session expirée.');
        }

        $idFaq = (int)($_POST['id_faqmulti'] ?? 0);

        if ($idFaq > 0) {
            $db = new FaqMultiDb();
            if ($db->deleteFaq($idFaq)) {
                $this->jsonResponse(true, 'Question supprimée avec succès.');
            }
        }
        $this->jsonResponse(false, 'Impossible de supprimer cette question.');
    }

    public function reorder(): void
    {
        if (ob_get_length()) ob_clean();

        $token = Request::isPost('hashtoken') ? $_POST['hashtoken'] : '';
        if (!$this->session->validateToken($token)) {
            $this->jsonResponse(false, 'Session expirée.');
        }

        $orderedIds = $_POST['ids'] ?? [];

        if (!empty($orderedIds) && is_array($orderedIds)) {
            $db = new FaqMultiDb();
            if ($db->reorderFaq($orderedIds)) {
                $this->jsonResponse(true, 'Ordre mis à jour.');
            }
        }
        $this->jsonResponse(false, 'Erreur lors du tri.');
    }
}