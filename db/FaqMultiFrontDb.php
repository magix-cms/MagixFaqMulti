<?php
declare(strict_types=1);

namespace Plugins\MagixFaqMulti\db;

use App\Frontend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class FaqMultiFrontDb extends BaseDb
{
    /**
     * Récupère les questions/réponses publiées pour un élément précis
     *
     * @param string $itemType Le type d'élément (ex: 'pages', 'product')
     * @param int $itemId L'ID de l'élément
     * @param int $idLang L'ID de la langue courante
     * @return array La liste des FAQ formatées
     */
    public function getPublishedFaqs(string $itemType, int $itemId, int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select([
            'f.id_faqmulti',
            'fc.title_faqmulti',
            'fc.desc_faqmulti'
        ])
            ->from('mc_faqmulti', 'f')
            ->join('mc_faqmulti_content', 'fc', 'f.id_faqmulti = fc.id_faqmulti')
            ->where('f.item_type = :item_type', ['item_type' => $itemType])
            ->where('f.item_id = :item_id', ['item_id' => $itemId])
            ->where('fc.id_lang = :id_lang', ['id_lang' => $idLang])
            ->where('fc.published_faqmulti = 1') // 🟢 Sécurité absolue
            ->orderBy('f.order_faqmulti', 'ASC'); // Tri respectant le Drag&Drop

        return $this->executeAll($qb) ?: [];
    }
}