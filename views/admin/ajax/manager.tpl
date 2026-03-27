{* Champs cachés pour le contexte de la liste (lus par le JS lors de l'ajout) *}
<input type="hidden" id="faq_hashtoken" value="{$hashtoken}">
<input type="hidden" id="faq_module" value="{$item_type}">
<input type="hidden" id="faq_id_module" value="{$item_id}">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 text-gray-800"><i class="bi bi-card-list me-2"></i>Questions existantes</h5>
    <button type="button" class="btn btn-primary btn-sm" onclick="faqApp.addItem()">
        <i class="bi bi-plus-lg me-1"></i> Ajouter une question
    </button>
</div>

{include file="components/ajax-table.tpl"
data=$faq_items
id_key="id_faqmulti"
columns=$ajax_columns
sortable=true
edit_action="faqApp.editItem"
delete_action="faqApp.deleteItem"
empty_msg="Aucune question n'a été créée pour cet élément."}