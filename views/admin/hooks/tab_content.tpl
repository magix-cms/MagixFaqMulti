<div class="tab-pane fade" id="magix-faqmulti-pane" role="tabpanel" aria-labelledby="magix-faqmulti-tab" tabindex="0">
    <div class="card shadow-sm border-0 mt-3">
        <div class="card-body">

            {* ==========================================
               VUE 1 : LA ZONE AJAX (Pour la liste uniquement)
               ========================================== *}
            <div id="magix-faqmulti-app" data-module="{$faq_item_type}" data-id="{$faq_item_id}">
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p>Chargement de la FAQ...</p>
                </div>
            </div>

            {* ==========================================
               VUE 2 : LE FORMULAIRE STATIQUE MULTILINGUE
               ========================================== *}
            <div id="faq_view_form" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h5 class="mb-0 text-primary" id="faq_form_title">
                        <i class="bi bi-patch-question me-2"></i>Ajouter une question
                    </h5>
                    <div>
                        {if isset($langs)}{include file="components/dropdown-lang.tpl" prefix="faq_"}{/if}
                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="faqApp.showList()">
                            <i class="bi bi-arrow-left"></i> Retour à la liste
                        </button>
                    </div>
                </div>

                {* 🟢 NOUVEAU : On utilise un vrai formulaire pour que le JS puisse tout capturer d'un coup *}
                <form id="faq_form_element">
                    <input type="hidden" id="faq_id_faqmulti" name="id_faqmulti" value="0">

                    <div class="tab-content">
                        {if isset($langs)}
                            {foreach $langs as $idLang => $iso}
                                <div class="tab-pane fade {if $iso@first}show active{/if}" id="faq_lang-{$idLang}" role="tabpanel">

                                    <div class="bg-light p-4 rounded border mb-4">
                                        <div class="row g-3">
                                            <div class="col-md-9">
                                                <label class="form-label fw-medium">Question (Titre)</label>
                                                {* Le nom devient un tableau : title_faqmulti[1] *}
                                                <input type="text" class="form-control" name="title_faqmulti[{$idLang}]" placeholder="Votre question...">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-medium">Statut d'affichage</label>
                                                <div class="form-check form-switch fs-5 mt-1">
                                                    <input type="hidden" name="published_faqmulti[{$idLang}]" value="0">
                                                    <input class="form-check-input" type="checkbox" name="published_faqmulti[{$idLang}]" value="1" checked>
                                                    <label class="form-check-label fs-6 text-muted">En ligne</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-medium">Réponse (Contenu) :</label>
                                        <textarea class="form-control mceEditor"
                                                  name="desc_faqmulti[{$idLang}]"
                                                  id="faq_desc_{$idLang}"
                                                  rows="6"></textarea>
                                    </div>

                                </div>
                            {/foreach}
                        {/if}
                    </div>
                </form>

                <hr class="my-4">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary me-2 px-4" onclick="faqApp.showList()">Annuler</button>
                    <button type="button" class="btn btn-success px-5" onclick="faqApp.save()">
                        <i class="bi bi-save me-2"></i> Enregistrer
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{block name="javascripts" append}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Initialisation de l'App AJAX
            if (typeof MagixAjaxManager !== 'undefined') {
                window.faqApp = new MagixAjaxManager(
                    'magix-faqmulti-app',
                    'magix-faqmulti-tab',
                    'MagixFaqMulti',
                    'faq',
                    'faqmulti'
                );
            }
        });
    </script>
{/block}