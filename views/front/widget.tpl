{if !empty($magix_faqmulti_data.items)}
    {* Injection du JSON-LD (Schema FAQPage) pour le SEO Google *}
    {$magix_faqmulti_data.seo nofilter}

    {* Remplacement de la div par une balise <section> sémantique *}
    <section class="magix-faqmulti-widget my-5 bg-body-tertiary">

        <header class="d-flex align-items-center mb-4">
            {* Ajout de aria-hidden pour masquer l'icône décorative aux robots/lecteurs *}
            <i class="bi bi-patch-question fs-2 text-primary me-3" aria-hidden="true"></i>
            {* Titre principal de la section FAQ *}
            <h2 class="h4 mb-0 fw-bold">{#faq_title#|default:'Foire aux questions'} : {$pages.name}</h2>
        </header>

        <div class="accordion shadow-sm" id="faqAccordion_{$magix_faqmulti_data.module}">

            {foreach $magix_faqmulti_data.items as $faq}
                {* Identifiants uniques pour le fonctionnement de l'accordéon Bootstrap *}
                {assign var="headingId" value="faqHeading_{$magix_faqmulti_data.module}_{$faq.id_faqmulti}"}
                {assign var="collapseId" value="faqCollapse_{$magix_faqmulti_data.module}_{$faq.id_faqmulti}"}

                {* On ouvre le tout premier élément de la liste par défaut *}
                {assign var="isOpen" value=$faq@first}

                {* Utilisation de <article> pour définir un contenu indépendant et lisible par les IA *}
                <article class="accordion-item border-0 border-bottom">

                    {* Correction de la hiérarchie : on passe de h2 à h3 pour respecter le h2 parent *}
                    <h3 class="accordion-header" id="{$headingId}">
                        <button class="accordion-button fw-medium {if !$isOpen}collapsed{/if} bg-transparent"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#{$collapseId}"
                                aria-expanded="{if $isOpen}true{else}false{/if}"
                                aria-controls="{$collapseId}">
                            {$faq.title_faqmulti|escape:'html'}
                        </button>
                    </h3>

                    <div id="{$collapseId}"
                         class="accordion-collapse collapse {if $isOpen}show{/if}"
                         aria-labelledby="{$headingId}"
                         data-bs-parent="#faqAccordion_{$magix_faqmulti_data.module}">

                        <div class="accordion-body text-muted bg-body">
                            {* nofilter est vital ici car la réponse est formatée via TinyMCE *}
                            {$faq.desc_faqmulti nofilter}
                        </div>

                    </div>
                </article>
            {/foreach}

        </div>
    </section>
{/if}