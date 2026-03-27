<?php

declare(strict_types=1);

namespace Plugins\MagixFaqMulti\db;

use App\Backend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class FaqMultiDb extends BaseDb
{
    /**
     * Récupère toutes les FAQ liées à un élément spécifique (ex: un produit), triées par ordre.
     * Utilisé pour l'affichage en Frontend et le listing dans l'onglet d'édition.
     */
    public function fetchFaqByItem(string $itemType, int $itemId, int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select([
            'f.id_faqmulti',
            'f.item_type',
            'f.item_id',
            'f.order_faqmulti',
            'c.title_faqmulti',
            'c.desc_faqmulti',
            'c.published_faqmulti'
        ])
            ->from('mc_faqmulti', 'f')
            ->join('mc_faqmulti_content', 'c', 'f.id_faqmulti = c.id_faqmulti')
            ->where('f.item_type = :type AND f.item_id = :id AND c.id_lang = :lang', [
                'type' => $itemType,
                'id'   => $itemId,
                'lang' => $idLang
            ])
            ->orderBy('f.order_faqmulti', 'ASC');

        return $this->executeAll($qb) ?: [];
    }

    /**
     * Récupère une FAQ spécifique avec toutes ses traductions (pour le formulaire d'édition).
     */
    public function fetchFaqById(int $idFaq): array|false
    {
        // 1. On récupère la structure
        $qb = new QueryBuilder();
        $qb->select('*')->from('mc_faqmulti')->where('id_faqmulti = :id', ['id' => $idFaq]);
        $faq = $this->executeRow($qb);

        if (!$faq) return false;

        // 2. On récupère les traductions
        $qbContent = new QueryBuilder();
        $qbContent->select('*')->from('mc_faqmulti_content')->where('id_faqmulti = :id', ['id' => $idFaq]);
        $contents = $this->executeAll($qbContent);

        $faq['content'] = [];
        if ($contents) {
            foreach ($contents as $c) {
                $faq['content'][$c['id_lang']] = $c;
            }
        }

        return $faq;
    }

    /**
     * Insère une nouvelle structure de FAQ et retourne son ID.
     */
    public function insertFaqStructure(array $data): int|false
    {
        // On calcule automatiquement le prochain 'order_faqmulti' pour cet item
        $qbCount = new QueryBuilder();
        $qbCount->select(['COUNT(id_faqmulti) as total'])
            ->from('mc_faqmulti')
            ->where('item_type = :type AND item_id = :id', [
                'type' => $data['item_type'],
                'id'   => $data['item_id']
            ]);

        $countResult = $this->executeRow($qbCount);
        $data['order_faqmulti'] = (int)($countResult['total'] ?? 0);

        $qb = new QueryBuilder();
        $qb->insert('mc_faqmulti', $data);

        return $this->executeInsert($qb) ? (int)$this->getLastInsertId() : false;
    }

    /**
     * Sauvegarde ou met à jour le contenu traduit d'une FAQ.
     */
    public function saveFaqContent(int $idFaq, int $idLang, array $data): bool
    {
        $qbCheck = new QueryBuilder();
        $qbCheck->select(['id_faqmulti'])->from('mc_faqmulti_content')
            ->where('id_faqmulti = :faq AND id_lang = :lang', ['faq' => $idFaq, 'lang' => $idLang]);

        $exists = $this->executeRow($qbCheck);
        $qb = new QueryBuilder();

        if ($exists) {
            $qb->update('mc_faqmulti_content', $data)
                ->where('id_faqmulti = :faq AND id_lang = :lang', ['faq' => $idFaq, 'lang' => $idLang]);
            return $this->executeUpdate($qb);
        } else {
            $data['id_faqmulti'] = $idFaq;
            $data['id_lang'] = $idLang;
            $qb->insert('mc_faqmulti_content', $data);
            return $this->executeInsert($qb);
        }
    }

    /**
     * Met à jour l'ordre des FAQ (via Drag & Drop SortableJS).
     */
    public function reorderFaq(array $faqIds): bool
    {
        $success = true;
        foreach ($faqIds as $index => $id) {
            $qb = new QueryBuilder();
            $qb->update('mc_faqmulti', ['order_faqmulti' => $index])
                ->where('id_faqmulti = :id', ['id' => (int)$id]);

            if (!$this->executeUpdate($qb)) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Supprime une FAQ (la suppression en cascade SQL s'occupera de mc_faqmulti_content).
     */
    public function deleteFaq(int $idFaq): bool
    {
        $qb = new QueryBuilder();
        $qb->delete('mc_faqmulti')->where('id_faqmulti = :id', ['id' => $idFaq]);
        return $this->executeDelete($qb);
    }
}